function isViewModeKey(key) {
  return this.key === key.value
}

export const viewModes = [
  { key: 'week', name: 'Widok tygodnia', shortcut: 'Tydzień', is: isViewModeKey },
  { key: 'month', name: 'Widok miesiąca', shortcut: 'Miesiąc', is: isViewModeKey },
]

export function find(modeKey) {
  return viewModes.find(mode => mode.key === modeKey)
}
