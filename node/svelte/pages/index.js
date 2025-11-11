import TestOne from './TestOne.svelte'
import TestTwo from './TestTwo.svelte'
import NotFound from './NotFound.svelte'

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

