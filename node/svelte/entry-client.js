import { createApp } from './createApp.js'
import { DEFAULT_PAGE } from './pages/index.js'

function parseJsonAttribute(value, fallback) {
  if (typeof value !== 'string' || value.length === 0) {
    return fallback
  }

  try {
    const parsed = JSON.parse(value)
    return parsed ?? fallback
  } catch (_err) {
    console.warn('[reactive] failed to parse JSON attribute', value)
    return fallback
  }
}

function ensureHost(shadowRoot, rootElement) {
  if (!shadowRoot) {
    throw new Error('[reactive] shadowRoot not available')
  }

  let host = shadowRoot.querySelector('[data-reactive-host]')
  if (host) {
    return host
  }

  host = rootElement.querySelector('[data-reactive-host]')
  if (host) {
    shadowRoot.appendChild(host)
    return host
  }

  const fallback = document.createElement('div')
  fallback.setAttribute('data-reactive-host', '')
  shadowRoot.appendChild(fallback)
  return fallback
}

function ensureStyles(shadowRoot, hrefs) {
  if (!Array.isArray(hrefs)) {
    return
  }

  hrefs.forEach((href) => {
    if (typeof href !== 'string' || href.length === 0) {
      return
    }
    const existing = Array.from(shadowRoot.querySelectorAll('link[data-reactive-css]')).find(
      (node) => node.dataset.reactiveCss === href,
    )
    if (existing) {
      return
    }
    const link = document.createElement('link')
    link.rel = 'stylesheet'
    link.href = href
    link.dataset.reactiveCss = href
    shadowRoot.appendChild(link)
  })
}

class ReactiveRootElement extends HTMLElement {
  connectedCallback() {
    if (this._mounted) {
      return
    }

    let shadow = this.shadowRoot

    if (!shadow) {
      const template = this.querySelector('template[shadowroot]')
      shadow = this.attachShadow({ mode: 'open' })
      if (template) {
        shadow.appendChild(template.content.cloneNode(true))
        template.remove()
      }
    }

    const host = ensureHost(shadow, this)

    const cssAssets = parseJsonAttribute(this.dataset.css, [])
    ensureStyles(shadow, cssAssets)

    const pageName = this.dataset.page || DEFAULT_PAGE
    const context = parseJsonAttribute(this.dataset.context, {})
    const ssrEnabled = this.dataset.ssr !== '0'

    const { app } = createApp(pageName, context)
    if (ssrEnabled) {
      app.mount(host, true)
    } else {
      host.innerHTML = ''
      app.mount(host)
    }
    this._mounted = true
    this._app = app
  }

  disconnectedCallback() {
    if (this._app) {
      this._app.unmount()
      this._app = undefined
    }
    this._mounted = false
  }
}

if (!customElements.get('reactive-root')) {
  customElements.define('reactive-root', ReactiveRootElement)
}

