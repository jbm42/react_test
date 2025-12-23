import { useEffect, useMemo } from 'react'
import { DEFAULT_PAGE, resolvePage } from './pages/index.js'
import styles from './App.module.css'

export default function App({ pageName = DEFAULT_PAGE, context = {} }) {
  const Component = useMemo(() => resolvePage(pageName), [pageName])

  useEffect(() => {
    if (typeof window !== 'undefined') {
      console.info('[hydrate] props.pageName', pageName, 'context', context)
    }
  }, [pageName, context])

  if (typeof window === 'undefined') {
    console.info('[ssr] rendering page', pageName)
  }

  return (
    <main className={styles.page}>
      <div className={styles.rendererLabel}>React Renderer</div>
      <Component context={context} />
    </main>
  )
}

