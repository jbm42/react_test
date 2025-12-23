import { onMount } from 'solid-js'
import { DEFAULT_PAGE, resolvePage } from './pages/index.js'
import styles from './App.module.css'

export default function App(props) {
  const pageName = () => props.pageName ?? DEFAULT_PAGE
  const context = () => props.context ?? {}

  const component = () => resolvePage(pageName())

  if (import.meta.env.SSR) {
    console.info('[ssr] rendering page', pageName())
  }

  onMount(() => {
    console.info('[hydrate] props.pageName', pageName(), 'context', context())
  })

  return (
    <main class={styles.page}>
      <div class={styles.rendererLabel}>Solid Renderer</div>
      {(() => {
        const Component = component()
        return <Component context={context()} />
      })()}
    </main>
  )
}

