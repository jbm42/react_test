import TestOne from './TestOne.jsx'
import TestTwo from './TestTwo.jsx'
import NotFound from './NotFound.jsx'

export const DEFAULT_PAGE = 'test-one'

const registry = {
  'test-one': TestOne,
  'test-two': TestTwo,
}

export function resolvePage(name) {
  return registry[name] ?? NotFound
}

export function listPages() {
  return Object.keys(registry)
}

