<script>
  import TestComponent from '../components/TestComponent.svelte'

  export let context = {}
</script>

<section class="test-one">
  <header>
    <h2>Counter Demo</h2>
    <p>Use the interactive counter below to verify hydration.</p>
  </header>

  <TestComponent {context} />
</section>

<style>
  .test-one {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
  }
</style>

