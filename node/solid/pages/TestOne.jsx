import TestComponent from '../components/TestComponent.jsx'
import styles from './TestOne.module.css'

export default function TestOne(props) {
  return (
    <section class={styles.testOne}>
      <header>
        <h2>Counter Demo</h2>
        <p>Use the interactive counter below to verify hydration.</p>
      </header>

      <TestComponent context={props.context} />
    </section>
  )
}

