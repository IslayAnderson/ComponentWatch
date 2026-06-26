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
    // Handle screenshot token before normal analytics flow
    await handleScreenshotRequest(apiUrl, siteKey)

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

    let discoveryIds
    try {
      const res = await fetch(`${apiUrl}/api/discoveries`, {
        method: 'POST',
        headers,
        body: JSON.stringify({
          session_id: sessionId,
          discoveries: found.map(({ component, htmlHash, stackPosition }) => ({
            component_id: component.id,
            page_url: window.location.href,
            html_hash: htmlHash,
            stack_position: stackPosition,
          })),
        }),
      })
      if (!res.ok) throw new Error(`HTTP ${res.status}`)
      const data = await res.json()
      discoveryIds = data.discovery_ids
    } catch (e) {
      console.error('[ComponentWatcher] Failed to report discoveries', e)
      return
    }

    const analytics = new Analytics(apiUrl, headers)
    analytics.attach(
      found.map(({ element }, i) => ({ element, discoveryId: discoveryIds[i] }))
    )
  },
}

export default ComponentWatcher
