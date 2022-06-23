function isViewModeKey(key) {
  return this.key === key.value
}

const modes = [
  { key: 'week', name: 'Widok tygodnia', shortcut: 'Tydzień', is: isViewModeKey },
  { key: 'month', name: 'Widok miesiąca', shortcut: 'Miesiąc', is: isViewModeKey },
]

export default {
  all: modes,
  find: whereMode => modes.find(mode => mode.key === whereMode),
}
