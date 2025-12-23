import { onMount, onCleanup, createSignal } from 'solid-js'
import TestButton from './TestButton.jsx'
import styles from './TestComponent.module.css'

export default function TestComponent(props) {
  const [count, setCount] = createSignal(0)
  let captureListener = null

  const increment = (event) => {
    console.info('[TestComponent] increment handler fired', {
      current: count(),
      next: count() + 1,
      eventType: event?.type ?? null,
      eventDetail: event?.detail ?? null,
      timeStamp: event?.timeStamp ?? null,
      isTrusted: event?.isTrusted ?? null,
    })
    setCount(count() + 1)
  }

  onMount(() => {
    console.info('[TestComponent] mounted', {
      componentId: Math.random().toString(36).slice(2, 8),
      initial: count(),
      context: props.context,
    })

    captureListener = (evt) => {
      if (!evt.isTrusted) {
        console.info('[TestComponent] observed non-trusted click on window', {
          type: evt.type,
          detail: evt.detail,
          timeStamp: evt.timeStamp,
        })
      }
    }

    window.addEventListener('click', captureListener, { capture: true })
  })

  onCleanup(() => {
    if (captureListener) {
      window.removeEventListener('click', captureListener, { capture: true })
      captureListener = null
    }
  })

  return (
    <section class={styles.test}>
      <p>This is a simple test component. Click the button to increment the counter.</p>
      <TestButton label={`Clicked ${count()} times`} onClick={increment} />
    </section>
  )
}

