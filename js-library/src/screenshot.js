import html2canvas from 'html2canvas'

/**
 * Check for a screenshot token in the URL, validate it, capture the
 * matching component, and POST the image back to the Watcher API.
 */
export async function handleScreenshotRequest(apiUrl, siteKey) {
  const params = new URLSearchParams(window.location.search)
  const token = params.get('cw_screenshot')
  if (!token) return false

  // Validate token and get component definition
  let component
  try {
    const res = await fetch(`${apiUrl}/api/screenshot/validate?token=${token}`)
    if (!res.ok) {
      console.warn('[ComponentWatcher] Screenshot token invalid or expired.')
      return
    }
    const data = await res.json()
    component = data.component
  } catch (e) {
    console.error('[ComponentWatcher] Screenshot validation failed', e)
    return
  }

  // Find the element using the component's macros
  const element = findElement(component.macros)
  if (!element) {
    console.warn(`[ComponentWatcher] Screenshot: no element found for component "${component.name}"`)
    return
  }

  // Wait for fonts/images to settle
  await new Promise(r => setTimeout(r, 500))

  let imageDataUrl
  try {
    const canvas = await html2canvas(element, { useCORS: true, logging: false })
    imageDataUrl = canvas.toDataURL('image/png')
  } catch (e) {
    console.error('[ComponentWatcher] html2canvas failed', e)
    return
  }

  try {
    await fetch(`${apiUrl}/api/screenshot`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'X-Site-Key': siteKey },
      body: JSON.stringify({
        token,
        image: imageDataUrl,
        page_url: window.location.href,
      }),
    })
  } catch (e) {
    console.error('[ComponentWatcher] Screenshot upload failed', e)
    return
  }

  console.info(`[ComponentWatcher] Screenshot captured for "${component.name}".`)
  return true
}

function findElement(macros) {
  const sorted = [...macros].sort((a, b) => a.priority - b.priority)
  for (const macro of sorted) {
    try {
      if (macro.type === 'id') {
        const el = document.getElementById(macro.value.replace(/^#/, ''))
        if (el) return el
      } else if (macro.type === 'css') {
        const el = document.querySelector(macro.value)
        if (el) return el
      } else if (macro.type === 'js') {
        // eslint-disable-next-line no-new-func
        const result = new Function(`return (${macro.value})`)()
        if (result instanceof Element) return result
      }
    } catch (e) {
      console.warn(`[ComponentWatcher] Screenshot macro failed (${macro.type})`, e)
    }
  }
  return null
}
