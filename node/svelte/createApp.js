import App from './App.svelte'
import { DEFAULT_PAGE } from './pages/index.js'

export function createApp(pageName = DEFAULT_PAGE, context = {}) {
  let instance = null

  return {
    app: {
      mount(target, hydrate) {
        if (!target) {
          throw new Error('[reactive] mount target is required for Svelte app')
        }

        if (instance) {
          instance.$destroy()
        }

        instance = new App({
          target,
          hydrate: Boolean(hydrate),
          props: {
            pageName,
            context,
          },
        })
      },
      unmount() {
        if (instance) {
          instance.$destroy()
          instance = null
        }
      },
    },
  }
}

