import TestComponent from '../components/TestComponent'
import styles from './TestOne.module.css'

export default function TestOne({ context = {} }) {
  return (
    <section className={styles.testOne}>
      <header>
        <h2>Counter Demo</h2>
        <p>Use the interactive counter below to verify hydration.</p>
      </header>

      <TestComponent context={context} />
    </section>
  )
}

