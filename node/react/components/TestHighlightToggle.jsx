import { useEffect, useState } from 'react'
import TestButton from './TestButton'
import styles from './TestHighlightToggle.module.css'

export default function TestHighlightToggle({ context = {} }) {
  const [highlighted, setHighlighted] = useState(false)
  const items = ['Alpha', 'Bravo', 'Charlie', 'Delta']

  function toggleHighlight() {
    setHighlighted((prev) => !prev)
  }

  useEffect(() => {
    if (context && Object.keys(context).length > 0) {
      console.info('[TestHighlightToggle] received context', context)
    }
  }, [context])

  return (
    <section className={styles.container}>
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

