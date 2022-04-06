import WhiteBalanceSunnyIcon from 'vue-material-design-icons/WhiteBalanceSunny.vue'
import CommentAlertIcon from 'vue-material-design-icons/CommentAlert.vue'
import StarShootingIcon from 'vue-material-design-icons/StarShooting.vue'
import BabyCarriageIcon from 'vue-material-design-icons/BabyCarriage.vue'
import HumanMaleBoardIcon from 'vue-material-design-icons/HumanMaleBoard.vue'
import CurrencyUsdOffIcon from 'vue-material-design-icons/CurrencyUsdOff.vue'
import HandHeartOutlineIcon from 'vue-material-design-icons/HandHeartOutline.vue'
import CalendarCheckIcon from 'vue-material-design-icons/CalendarCheck.vue'
import MedicalBagIcon from 'vue-material-design-icons/MedicalBag.vue'
import CalendarRemoveIcon from 'vue-material-design-icons/CalendarRemove.vue'

const types = [
  {
    text: 'Urlop wypoczynkowy',
    value: 'vacation',
    icon: WhiteBalanceSunnyIcon,
    color: 'text-amber-500',
    border: {
      approved: 'border-amber-500',
      pending: 'border-amber-300',
    },
  },
  {
    text: 'Urlop na żądanie',
    value: 'vacation_on_request',
    icon: CommentAlertIcon,
    color: 'text-slate-500',
    border: {
      approved: 'border-slate-500',
      pending: 'border-slate-300',
    },
  },
  {
    text: 'Urlop okolicznościowy',
    value: 'special_vacation',
    icon: StarShootingIcon,
    color: 'text-orange-500',
    border: {
      approved: 'border-orange-500',
      pending: 'border-orange-300',
    },
  },
  {
    text: 'Opieka nad dzieckiem art 188 kp',
    value: 'childcare_vacation',
    icon: BabyCarriageIcon,
    color: 'text-purple-500',
    border: {
      approved: 'border-purple-500',
      pending: 'border-purple-300',
    },
  },
  {
    text: 'Urlop szkoleniowy',
    value: 'training_vacation',
    icon: HumanMaleBoardIcon,
    color: 'text-blumilk-500',
    border: {
      approved: 'border-blumilk-500',
      pending: 'border-blumilk-300',
    },
  },
  {
    text: 'Urlop bezpłatny',
    value: 'unpaid_vacation',
    icon: CurrencyUsdOffIcon,
    color: 'text-emerald-500',
    border: {
      approved: 'border-emerald-500',
      pending: 'border-emerald-300',
    },
  },
  {
    text: 'Wolontariat',
    value: 'volunteering_vacation',
    icon: HandHeartOutlineIcon,
    color: 'text-pink-500',
    border: {
      approved: 'border-pink-500',
      pending: 'border-pink-300',
    },
  },
  {
    text: 'Odbiór za święto',
    value: 'time_in_lieu',
    icon: CalendarCheckIcon,
    color: 'text-stone-500',
    border: {
      approved: 'border-stone-500',
      pending: 'border-stone-300',
    },
  },
  {
    text: 'Zwolnienie lekarskie',
    value: 'sick_vacation',
    icon: MedicalBagIcon,
    color: 'text-rose-500',
    border: {
      approved: 'border-rose-500',
      pending: 'border-rose-300',
    },
  },
  {
    text: 'Nieobecność',
    value: 'absence',
    icon: CalendarRemoveIcon,
    color: 'text-cyan-500',
  },
]

export default function useVacationTypeInfo() {
  const getTypes = () => types
  const findType = value => types.find(type => type.value === value)

  return {
    getTypes,
    findType,
  }
}
