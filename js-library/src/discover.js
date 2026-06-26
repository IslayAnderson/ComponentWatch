/**
 * Run component macros against the DOM.
 * Returns an array of { component, element, htmlHash, stackPosition }.
 */
export function discover(components) {
  const results = []

  for (const component of components) {
    const sorted = [...component.macros].sort((a, b) => a.priority - b.priority)

    for (const macro of sorted) {
      const elements = runMacro(macro)
      for (let i = 0; i < elements.length; i++) {
        results.push({
          component,
          element: elements[i],
          htmlHash: hashHtml(elements[i]),
          stackPosition: i,
        })
      }
      if (elements.length > 0) break // first matching macro wins
    }
  }

  return results
}

function runMacro(macro) {
  try {
    if (macro.type === 'id') {
      const el = document.getElementById(macro.value.replace(/^#/, ''))
      return el ? [el] : []
    }

    if (macro.type === 'css') {
      return Array.from(document.querySelectorAll(macro.value))
    }

    if (macro.type === 'js') {
      // eslint-disable-next-line no-new-func
      const result = new Function(`return (${macro.value})`)()
      if (result instanceof Element) return [result]
      if (result instanceof NodeList || Array.isArray(result)) return Array.from(result)
      return []
    }
  } catch (e) {
    console.warn(`[ComponentWatcher] Macro failed (${macro.type}: ${macro.value})`, e)
  }
  return []
}

/**
 * Normalise and hash element HTML to detect duplicate content.
 * Strips volatile attributes (id, class order, data-* dynamic values)
 * to produce a stable fingerprint.
 */
function hashHtml(el) {
  const clone = el.cloneNode(true)
  normalise(clone)
  const html = clone.outerHTML
  // djb2 hash — fast, no crypto needed
  let hash = 5381
  for (let i = 0; i < html.length; i++) {
    hash = ((hash << 5) + hash) ^ html.charCodeAt(i)
    hash = hash >>> 0 // keep unsigned 32-bit
  }
  return hash.toString(16)
}

function normalise(el) {
  el.removeAttribute('id')
  if (el.hasAttribute('class')) {
    const sorted = el.className.trim().split(/\s+/).sort().join(' ')
    el.setAttribute('class', sorted)
  }
  for (const attr of [...el.attributes]) {
    if (attr.name.startsWith('data-') && /\d{10,}/.test(attr.value)) {
      el.removeAttribute(attr.name)
    }
  }
  for (const child of el.children) normalise(child)
}
