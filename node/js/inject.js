import { createApp, ref } from 'vue'

const TestButton = {
  name: 'TestButton',
  props: {
    label: {
      type: String,
      default: 'Click me',
    },
  },
  emits: ['click'],
  template: `
    <button class="test-button" type="button" @click="$emit('click')">
      {{ label }}
    </button>
  `,
}

const TestComponent = {
  name: 'TestComponent',
  components: {
    TestButton,
  },
  setup() {
    const count = ref(0)
    function increment() {
      count.value += 1
    }
    return { count, increment }
  },
  template: `
    <section class="test-card">
      <p>This is a simple test component. Click the button to increment the counter.</p>
      <TestButton :label="\`Clicked \${count} times\`" @click="increment" />
    </section>
  `,
}

const App = {
  name: 'TestApp',
  components: {
    TestComponent,
  },
  template: `
    <main class="test-page">
      <h1>Test Page</h1>
      <TestComponent />
    </main>
  `,
}

const styleId = 'vue-test-component-style'
if (!document.getElementById(styleId)) {
  const style = document.createElement('style')
  style.id = styleId
  style.textContent = `
    .test-page {
      font-family: Avenir, Helvetica, Arial, sans-serif;
      padding: 2rem;
      max-width: 640px;
      margin: 0 auto;
      line-height: 1.5;
    }

    .test-page h1 {
      margin-bottom: 1.5rem;
    }

    .test-card {
      padding: 1.5rem;
      border-radius: 12px;
      border: 1px solid #d0d7de;
      background-color: #f6f8fa;
      color: #24292f;
    }

    .test-card p {
      margin-bottom: 1rem;
    }

    .test-button {
      padding: 0.5rem 1rem;
      border: none;
      border-radius: 8px;
      background-color: #2f81f7;
      color: white;
      font-size: 1rem;
      cursor: pointer;
      transition: background-color 0.2s ease-in-out;
    }

    .test-button:hover {
      background-color: #1b64d7;
    }

    .test-button:active {
      background-color: #124ea7;
    }
  `
  document.head.appendChild(style)
}

createApp(App).mount('#reactive')
