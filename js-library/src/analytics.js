/**
 * Attach event listeners to discovered elements.
 * Buffers events and flushes in batches.
 */
export class Analytics {
  #apiUrl
  #headers
  #discoveryMap = new Map() // element -> discoveryId
  #screenBlankElements = new Set() // elements with screen_blank enabled
  #visibleElements = new Set() // elements currently in the viewport
  #buffer = []
  #flushInterval = null
  #observer = null
  #screenshotHandler = null

  constructor(apiUrl, headers) {
    this.#apiUrl = apiUrl
    this.#headers = headers
  }

  attach(discoveries) {
    for (const { element, discoveryId, screenBlank } of discoveries) {
      this.#discoveryMap.set(element, discoveryId)
      if (screenBlank) this.#screenBlankElements.add(element)
      this.#listen(element)
    }

    this.#watchViewport()
    this.#watchScreenshotShortcuts()

    this.#flushInterval = setInterval(() => this.#flush(), 5000)
    document.addEventListener('visibilitychange', () => {
      if (document.visibilityState === 'hidden') this.#flush()
    })
  }

  #listen(el) {
    el.addEventListener('click', () => this.#record(el, 'click'))
    el.addEventListener('mouseover', () => this.#record(el, 'mouseover'))

    let hoverStart = null
    el.addEventListener('mouseenter', () => { hoverStart = Date.now() })
    el.addEventListener('mouseleave', () => {
      if (hoverStart !== null) {
        this.#record(el, 'hover_time', { ms: Date.now() - hoverStart })
        hoverStart = null
      }
    })
  }

  #watchViewport() {
    this.#observer = new IntersectionObserver((entries) => {
      for (const entry of entries) {
        if (entry.isIntersecting) {
          this.#visibleElements.add(entry.target)
        } else {
          this.#visibleElements.delete(entry.target)
        }
      }
    }, { threshold: 0.1 })

    for (const el of this.#discoveryMap.keys()) {
      this.#observer.observe(el)
    }
  }

  #watchScreenshotShortcuts() {
    this.#screenshotHandler = (e) => {
      const mac = e.metaKey && e.shiftKey && ['3', '4', '5'].includes(e.key)
      const win = e.key === 'PrintScreen'

      if (!mac && !win) return

      const shortcut = e.key === 'PrintScreen' ? 'PrintScreen' : `Cmd+Shift+${e.key}`
      let shouldBlank = false

      for (const el of this.#visibleElements) {
        this.#record(el, 'screenshot', { shortcut })
        if (this.#screenBlankElements.has(el)) shouldBlank = true
      }

      if (shouldBlank) this.#flashBlankOverlay()
    }

    document.addEventListener('keydown', this.#screenshotHandler)
  }

  #flashBlankOverlay() {
    const overlay = document.createElement('div')
    overlay.style.cssText = 'position:fixed;inset:0;background:#fff;z-index:2147483647;pointer-events:none'
    document.body.appendChild(overlay)
    setTimeout(() => overlay.remove(), 800)
  }

  #record(el, type, payload = null) {
    const discoveryId = this.#discoveryMap.get(el)
    if (!discoveryId) return
    this.#buffer.push({
      discovery_id: discoveryId,
      type,
      payload,
      occurred_at: new Date().toISOString(),
    })
  }

  async #flush() {
    if (this.#buffer.length === 0) return
    const batch = this.#buffer.splice(0)
    try {
      await fetch(`${this.#apiUrl}/api/events`, {
        method: 'POST',
        headers: this.#headers,
        body: JSON.stringify({ events: batch }),
        keepalive: true,
      })
    } catch (e) {
      this.#buffer.unshift(...batch)
      console.warn('[ComponentWatcher] Event flush failed', e)
    }
  }

  destroy() {
    clearInterval(this.#flushInterval)
    this.#observer?.disconnect()
    if (this.#screenshotHandler) {
      document.removeEventListener('keydown', this.#screenshotHandler)
    }
    this.#flush()
  }
}
