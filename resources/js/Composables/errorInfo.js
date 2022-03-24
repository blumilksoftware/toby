const errors = [
  {
    'title': '404',
    'description': 'Nie znaleziono strony',
  },
]

export function useErrorInfo() {
  const getErrors = () => errors
  const findErrors = value => errors.find(error => error.value === value)

  return {
    getErrors,
    findErrors,
  }
}
