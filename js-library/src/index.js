import html2canvas from 'html2canvas'
import { discover } from './discover.js'
import { Analytics } from './analytics.js'
import { handleScreenshotRequest } from './screenshot.js'

/**
 * ComponentWatcher
 *
 * Usage:
 *   <script src="component-watcher.umd.cjs"></script>
 *   <script>
 *     ComponentWatcher.init({
 *       apiUrl: 'https://your-watcher-api.com',
 *       siteKey: 'your-site-key',
 *     })
 *   </script>
 */
const ComponentWatcher = {
  async init({ apiUrl, siteKey }) {
    // Handle screenshot token before normal analytics flow — bail out on screenshot pages
    if (await handleScreenshotRequest(apiUrl, siteKey)) return

    const headers = {
      'Content-Type': 'application/json',
      'X-Site-Key': siteKey,
    }

    let components
    try {
      const res = await fetch(`${apiUrl}/api/components`, { headers })
      if (!res.ok) throw new Error(`HTTP ${res.status}`)
      const data = await res.json()
      components = data.components
    } catch (e) {
      console.error('[ComponentWatcher] Failed to fetch components', e)
      return
    }

    if (!components?.length) return

    const found = discover(components)
    if (!found.length) return

    const sessionId = crypto.randomUUID()
    const pageUrl = (() => {
      const u = new URL(window.location.href)
      u.searchParams.delete('cw_screenshot')
      return u.toString()
    })()

    let discoveryIds
    try {
      const res = await fetch(`${apiUrl}/api/discoveries`, {
        method: 'POST',
        headers,
        body: JSON.stringify({
          session_id: sessionId,
          discoveries: found.map(({ component, htmlHash, stackPosition }) => ({
            component_id: component.id,
            page_url: pageUrl,
            html_hash: htmlHash,
            stack_position: stackPosition,
          })),
        }),
      })
      if (!res.ok) throw new Error(`HTTP ${res.status}`)
      const data = await res.json()
      discoveryIds = data.discovery_ids
      // Fire-and-forget auto-screenshots for new hash instances
      if (data.needs_screenshot) {
        data.needs_screenshot.forEach((needed, i) => {
          if (!needed) return
          const { element } = found[i]
          html2canvas(element, { useCORS: true, logging: false, scale: 1 })
            .then(canvas => fetch(`${apiUrl}/api/auto-screenshot`, {
              method: 'POST',
              headers,
              body: JSON.stringify({
                discovery_id: discoveryIds[i],
                image: canvas.toDataURL('image/jpeg', 0.7),
                page_url: pageUrl,
              }),
            }))
            .catch(() => {})
        })
      }
    } catch (e) {
      console.error('[ComponentWatcher] Failed to report discoveries', e)
      return
    }

    const analytics = new Analytics(apiUrl, headers)
    analytics.attach(
      found.map(({ element, component }, i) => ({
        element,
        discoveryId: discoveryIds[i],
        screenBlank: !!component.screen_blank,
      }))
    )
  },
}

export default ComponentWatcher
