import { createApp } from './createApp.js'
import { DEFAULT_PAGE } from './pages/index.js'

const container = document.getElementById('reactive')
const pageName = container?.dataset.page || DEFAULT_PAGE

const { app } = createApp(pageName)
app.mount(container ?? '#reactive', true)

