import { renderToString } from 'react-dom/server'
import App from './App.jsx'
import { DEFAULT_PAGE } from './pages/index.js'

export async function render(pageName = DEFAULT_PAGE, context = {}) {
  const html = renderToString(<App pageName={pageName} context={context} />)
  return {
    html,
    head: '',
    css: '',
    page: pageName,
    context,
  }
}

