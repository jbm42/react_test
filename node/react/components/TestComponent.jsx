import { useEffect, useState, useRef } from 'react'
import TestButton from './TestButton'
import styles from './TestComponent.module.css'

export default function TestComponent({ context = {} }) {
  const [count, setCount] = useState(0)
  const captureListenerRef = useRef(null)

  function increment(event) {
    console.info('[TestComponent] increment handler fired', {
      current: count,
      next: count + 1,
      eventType: event?.type ?? null,
      eventDetail: event?.detail ?? null,
      timeStamp: event?.timeStamp ?? null,
      isTrusted: event?.isTrusted ?? null,
    })
    setCount((prev) => prev + 1)
  }

  useEffect(() => {
    console.info('[TestComponent] mounted', {
      componentId: Math.random().toString(36).slice(2, 8),
      initial: count,
      context,
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
    captureListenerRef.current = captureListener

    return () => {
      if (captureListenerRef.current) {
        window.removeEventListener('click', captureListenerRef.current, { capture: true })
        captureListenerRef.current = null
      }
    }
  }, [])

  return (
    <section className={styles.test}>
      <p>This is a simple test component. Click the button to increment the counter.</p>
      <TestButton label={`Clicked ${count} times`} onClick={increment} />
    </section>
  )
}

