import TestOne from './TestOne.vue'
import TestTwo from './TestTwo.vue'
import NotFound from './NotFound.vue'

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

