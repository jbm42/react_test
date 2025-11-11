import AppShell from '../components/AppShell.jsx'
import TestComponent from '../components/TestComponent.jsx'
import styles from './test-one.module.css'

export default function TestOnePage() {
  return (
    <AppShell>
      <section className={styles.container}>
        <header>
          <h2 className={styles.heading}>Counter Demo</h2>
          <p className={styles.lead}>Use the interactive counter below to verify hydration.</p>
        </header>

        <TestComponent />
      </section>
    </AppShell>
  )
}

