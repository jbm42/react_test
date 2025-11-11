<script>
  import { onMount } from 'svelte'
  import TestButton from './TestButton.svelte'

  export let context = {}

  const items = ['Alpha', 'Bravo', 'Charlie', 'Delta']
  let highlighted = false

  function toggleHighlight() {
    highlighted = !highlighted
  }

  onMount(() => {
    if (context && Object.keys(context).length > 0) {
      console.info('[TestHighlightToggle] received context', context)
    }
  })
</script>

<section class="test-two">
  <header>
    <h2>Highlight Toggle</h2>
    <p>Toggle the highlight state to confirm reactivity.</p>
  </header>

  <ul class:highlighted={highlighted}>
    {#each items as item, index}
      <li>{item}</li>
    {/each}
  </ul>

  <TestButton
    label={highlighted ? 'Remove highlight' : 'Highlight list'}
    on:click={toggleHighlight}
  />
</section>

<style>
  .test-two {
    display: flex;
    flex-direction: column;
    gap: 1.65rem;
    background: rgba(18, 52, 100, 0.9);
    padding: 1.75rem;
    border-radius: 18px;
    border: 1px solid #8fb3ff;
    box-shadow:
      inset 0 1px 0 rgba(255, 255, 255, 0.12),
      0 16px 30px rgba(30, 58, 138, 0.3);
  }

  ul {
    margin: 0;
    padding: 1.25rem;
    list-style: square inside;
    border: 1px solid #c4d9ff;
    border-radius: 14px;
    background: rgba(12, 41, 85, 0.9);
    transition: background-color 0.2s ease-in-out, border-color 0.2s ease-in-out,
      transform 0.2s ease-in-out;
  }

  ul.highlighted {
    background-color: #ffe483;
    border-color: #facc15;
    transform: translateY(-2px);
    color: #2c1810;
  }
</style>

