/**
 * Attach event listeners to discovered elements.
 * Buffers events and flushes in batches.
 */
export class Analytics {
  #apiUrl
  #headers
  #discoveryMap = new Map() // element -> discoveryId
  #buffer = []
  #flushInterval = null

  constructor(apiUrl, headers) {
    this.#apiUrl = apiUrl
    this.#headers = headers
  }

  attach(discoveries) {
    for (const { element, discoveryId } of discoveries) {
      this.#discoveryMap.set(element, discoveryId)
      this.#listen(element)
    }

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
    this.#flush()
  }
}
