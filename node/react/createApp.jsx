import { createRoot, hydrateRoot } from 'react-dom/client'
import App from './App.jsx'
import { DEFAULT_PAGE } from './pages/index.js'

export function createApp(pageName = DEFAULT_PAGE, context = {}) {
  let root = null
  let container = null

  return {
    app: {
      mount(target, hydrate) {
        if (!target) {
          throw new Error('[reactive] mount target is required for React app')
        }

        if (root) {
          root.unmount()
          root = null
        }

        container = target

        if (hydrate) {
          root = hydrateRoot(container, <App pageName={pageName} context={context} />)
        } else {
          root = createRoot(container)
          root.render(<App pageName={pageName} context={context} />)
        }
      },
      unmount() {
        if (root) {
          root.unmount()
          root = null
        }
        container = null
      },
    },
  }
}

