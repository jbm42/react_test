<script>
  import { onDestroy, onMount } from 'svelte'
  import TestButton from './TestButton.svelte'

  export let context = {}

  let count = 0
  let captureListener = null

  function increment(event) {
    console.info('[TestComponent] increment handler fired', {
      current: count,
      next: count + 1,
      eventType: event?.type ?? null,
      eventDetail: event?.detail ?? null,
      timeStamp: event?.timeStamp ?? null,
      isTrusted: event?.isTrusted ?? null,
    })
    count += 1
  }

  onMount(() => {
    console.info('[TestComponent] mounted', {
      componentId: Math.random().toString(36).slice(2, 8),
      initial: count,
      context,
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

  onDestroy(() => {
    if (captureListener) {
      window.removeEventListener('click', captureListener, { capture: true })
      captureListener = null
    }
  })
</script>

<section class="test">
  <p>This is a simple test component. Click the button to increment the counter.</p>
  <TestButton label={`Clicked ${count} times`} on:click={increment} />
</section>

<style>
  .test {
    padding: 1.75rem;
    border-radius: 18px;
    border: 1px solid #76a9fa;
    background: rgba(19, 75, 138, 0.9);
    color: #f0f9ff;
    box-shadow:
      inset 0 1px 0 rgba(255, 255, 255, 0.12),
      0 12px 24px rgba(13, 71, 161, 0.25);
  }

  p {
    margin-bottom: 1.25rem;
  }
</style>

