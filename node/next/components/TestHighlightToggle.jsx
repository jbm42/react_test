import { useState } from 'react'
import TestButton from './TestButton.jsx'
import styles from './TestHighlightToggle.module.css'

const items = ['Alpha', 'Bravo', 'Charlie', 'Delta']

export default function TestHighlightToggle() {
  const [highlighted, setHighlighted] = useState(false)

  function toggleHighlight() {
    setHighlighted((value) => !value)
  }

  return (
    <section className={styles.panel}>
      <header>
        <h2>Highlight Toggle</h2>
        <p>Toggle the highlight state to confirm reactivity.</p>
      </header>

      <ul className={highlighted ? `${styles.list} ${styles.highlighted}` : styles.list}>
        {items.map((item, index) => (
          <li key={index}>{item}</li>
        ))}
      </ul>

      <TestButton
        label={highlighted ? 'Remove highlight' : 'Highlight list'}
        onClick={toggleHighlight}
      />
    </section>
  )
}

