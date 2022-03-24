const months = [
  {
    'name': 'Styczeń',
    'value': 'january',
    'shortcut': 'Sty',
  },
  {
    'name': 'Luty',
    'value': 'february',
    'shortcut': 'Lut',
  },
  {
    'name': 'Marzec',
    'value': 'march',
    'shortcut': 'Mar',
  },
  {
    'name': 'Kwiecień',
    'value': 'april',
    'shortcut': 'Kwi',
  },
  {
    'name': 'Maj',
    'value': 'may',
    'shortcut':'Maj',
  },
  {
    'name': 'Czerwiec',
    'value': 'june',
    'shortcut': 'Cze',
  },
  {
    'name': 'Lipiec',
    'value': 'july',
    'shortcut': 'Lip',
  },
  {
    'name': 'Sierpień',
    'value': 'august',
    'shortcut': 'Sie',
  },
  {
    'name': 'Wrzesień',
    'value': 'september',
    'shortcut': 'Wrz',
  },
  {
    'name': 'Październik',
    'value': 'october',
    'shortcut': 'Paź',
  },
  {
    'name': 'Listopad',
    'value': 'november',
    'shortcut': 'Lis',
  },
  {
    'name': 'Grudzień',
    'value': 'december',
    'shortcut': 'Gru',
  },
]

export function useMonthInfo() {
  const getMonths = () => months
  const findMonth = value => months.find(month => month.value === value)

  return {
    getMonths,
    findMonth,
  }
}
