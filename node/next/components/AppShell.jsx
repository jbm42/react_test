import styles from './AppShell.module.css'

export default function AppShell({ children }) {
  return (
    <main className={styles.page}>
      <div className={styles.rendererLabel}>Next.js Renderer</div>
      {children}
    </main>
  )
}

