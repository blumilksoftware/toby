import ClockTimeFourOutlineIcon from 'vue-material-design-icons/ClockTimeFourOutline.vue'
import CurrencyUsdIcon from 'vue-material-design-icons/CurrencyUsd.vue'

const types = [
  {
    text: 'Godzinowe',
    value: 'hours',
    icon: ClockTimeFourOutlineIcon,
    color: 'text-blue-500',
    border: 'border-blue-500',
  },
  {
    text: 'Pieniężne',
    value: 'money',
    icon: CurrencyUsdIcon,
    color: 'text-green-500',
    border: 'border-green-500',
  },
]

export default function useSettlementTypeInfo() {
  const getTypes = () => types
  const findType = value => types.find(type => type.value === value)

  return {
    getTypes,
    findType,
  }
}
