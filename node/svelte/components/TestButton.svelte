<script>
  import { createEventDispatcher } from 'svelte'

  export let label = 'Click me'

  const dispatch = createEventDispatcher()

  function handleClick(event) {
    dispatch('click', event)
  }
</script>

<button class="button" type="button" on:click={handleClick}>
  {label}
</button>

<style>
  .button {
    padding: 0.65rem 1.4rem;
    border: 1px solid rgba(240, 249, 255, 0.45);
    border-radius: 999px;
    background: radial-gradient(circle at top left, #38bdf8, #2563eb);
    color: #f8fafc;
    font-size: 1rem;
    cursor: pointer;
    letter-spacing: 0.03em;
    text-transform: uppercase;
    transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out, filter 0.2s ease-in-out;
    box-shadow:
      0 10px 25px rgba(14, 116, 144, 0.35),
      inset 0 1px 0 rgba(255, 255, 255, 0.3);
  }

  .button:hover {
    transform: translateY(-2px);
    filter: brightness(1.08);
    box-shadow:
      0 14px 30px rgba(37, 99, 235, 0.4),
      inset 0 1px 0 rgba(255, 255, 255, 0.35);
  }

  .button:active {
    transform: translateY(0);
    filter: brightness(0.95);
  }
</style>

