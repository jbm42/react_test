import { onMount, createSignal } from 'solid-js'
import TestButton from './TestButton.jsx'
import styles from './TestHighlightToggle.module.css'

export default function TestHighlightToggle(props) {
  const items = ['Alpha', 'Bravo', 'Charlie', 'Delta']
  const [highlighted, setHighlighted] = createSignal(false)

  const toggleHighlight = () => {
    setHighlighted(!highlighted())
  }

  onMount(() => {
    if (props.context && Object.keys(props.context).length > 0) {
      console.info('[TestHighlightToggle] received context', props.context)
    }
  })

  return (
    <section class={styles.testTwo}>
      <header>
        <h2>Highlight Toggle</h2>
        <p>Toggle the highlight state to confirm reactivity.</p>
      </header>

      <ul classList={{ [styles.highlighted]: highlighted() }}>
        {items.map((item) => (
          <li>{item}</li>
        ))}
      </ul>

      <TestButton
        label={highlighted() ? 'Remove highlight' : 'Highlight list'}
        onClick={toggleHighlight}
      />
    </section>
  )
}

