import { useCallback, useEffect, useState } from 'react'
import TestButton from './TestButton.jsx'
import styles from './TestComponent.module.css'

export default function TestComponent() {
  const [count, setCount] = useState(0)

  const increment = useCallback((event) => {
    setCount((current) => {
      console.info('[TestComponent] increment handler fired', {
        current,
        next: current + 1,
        eventType: event?.type ?? null,
        eventDetail: event?.detail ?? null,
        timeStamp: event?.timeStamp ?? null,
        isTrusted: event?.isTrusted ?? null,
      })
      return current + 1
    })
  }, [])

  useEffect(() => {
    console.info('[TestComponent] mounted', {
      componentId: Math.random().toString(36).slice(2, 8),
      initial: 0,
    })

    const captureListener = (evt) => {
      if (!evt.isTrusted) {
        console.info('[TestComponent] observed non-trusted click on window', {
          type: evt.type,
          detail: evt.detail,
          timeStamp: evt.timeStamp,
        })
      }
    }

    window.addEventListener('click', captureListener, { capture: true })
    return () => {
      window.removeEventListener('click', captureListener, { capture: true })
    }
  }, [])

  return (
    <section className={styles.test}>
      <p className={styles.copy}>
        This is a simple test component. Click the button to increment the counter.
      </p>
      <TestButton label={`Clicked ${count} times`} onClick={increment} />
    </section>
  )
}

