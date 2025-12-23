import { render, hydrate } from 'solid-js/web'
import App from './App.jsx'
import { DEFAULT_PAGE } from './pages/index.js'

export function createApp(pageName = DEFAULT_PAGE, context = {}) {
  let dispose = null

  return {
    app: {
      mount(target, shouldHydrate) {
        if (!target) {
          throw new Error('[reactive] mount target is required for Solid app')
        }

        if (dispose) {
          dispose()
          dispose = null
        }

        if (shouldHydrate) {
          dispose = hydrate(
            () => <App pageName={pageName} context={context} />,
            target
          )
        } else {
          target.innerHTML = ''
          dispose = render(
            () => <App pageName={pageName} context={context} />,
            target
          )
        }
      },
      unmount() {
        if (dispose) {
          dispose()
          dispose = null
        }
      },
    },
  }
}

