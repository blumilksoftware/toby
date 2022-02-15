const months = [
  {
    'name': 'Styczeń',
    'value': 'january',
  },
  {
    'name': 'Luty',
    'value': 'february',
  },
  {
    'name': 'Marzec',
    'value': 'march',
  },
  {
    'name': 'Kwiecień',
    'value': 'april',
  },
  {
    'name': 'Maj',
    'value': 'may',
  },
  {
    'name': 'Czerwiec',
    'value': 'june',
  },
  {
    'name': 'Lipiec',
    'value': 'july',
  },
  {
    'name': 'Sierpień',
    'value': 'august',
  },
  {
    'name': 'Wrzesień',
    'value': 'september',
  },
  {
    'name': 'Październik',
    'value': 'october',
  },
  {
    'name': 'Listopad',
    'value': 'november',
  },
  {
    'name': 'Grudzień',
    'value': 'december',
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
