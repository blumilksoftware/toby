import WhiteBalanceSunnyIcon from 'vue-material-design-icons/WhiteBalanceSunny.vue'
import CommentAlertIcon from 'vue-material-design-icons/CommentAlert.vue'
import StarShootingIcon from 'vue-material-design-icons/StarShooting.vue'
import BabyCarriageIcon from 'vue-material-design-icons/BabyCarriage.vue'
import HumanMaleBoardIcon from 'vue-material-design-icons/HumanMaleBoard.vue'
import CurrencyUsdOffIcon from 'vue-material-design-icons/CurrencyUsdOff.vue'
import HandHeartOutlineIcon from 'vue-material-design-icons/HandHeartOutline.vue'
import CalendarCheckIcon from 'vue-material-design-icons/CalendarCheck.vue'
import MedicalBagIcon from 'vue-material-design-icons/MedicalBag.vue'

const statuses = [
  {
    text: 'Urlop wypoczynkowy',
    value: 'vacation',
    outline: {
      icon: WhiteBalanceSunnyIcon,
      background: 'bg-white',
      foreground: 'text-amber-500',
    },
    solid: {
      icon: WhiteBalanceSunnyIcon,
      color: 'text-amber-500',
    },
  },
  {
    text: 'Urlop na żądanie',
    value: 'vacation_on_request',
    outline: {
      icon: CommentAlertIcon,
      background: 'bg-white',
      foreground: 'text-slate-500',
    },
    solid: {
      icon: CommentAlertIcon,
      color: 'text-slate-500',
    },
  },
  {
    text: 'Urlop okolicznościowy',
    value: 'special_vacation',
    outline: {
      icon: StarShootingIcon,
      background: 'bg-white',
      foreground: 'text-orange-500',
    },
    solid: {
      icon: StarShootingIcon,
      color: 'text-orange-500',
    },
  },
  {
    text: 'Opieka nad dzieckiem art 188 kp',
    value: 'childcare_vacation',
    outline: {
      icon: BabyCarriageIcon,
      background: 'bg-white',
      foreground: 'text-purple-500',
    },
    solid: {
      icon: BabyCarriageIcon,
      color: 'text-purple-500',
    },
  },
  {
    text: 'Urlop szkoleniowy',
    value: 'training_vacation',
    outline: {
      icon: HumanMaleBoardIcon,
      background: 'bg-white',
      foreground: 'text-blumilk-500',
    },
    solid: {
      icon: HumanMaleBoardIcon,
      color: 'text-blumilk-500',
    },
  },
  {
    text: 'Urlop bezpłatny',
    value: 'unpaid_vacation',
    outline: {
      icon: CurrencyUsdOffIcon,
      background: 'bg-white',
      foreground: 'text-emerald-500',
    },
    solid: {
      icon: CurrencyUsdOffIcon,
      color: 'text-emerald-500',
    },
  },
  {
    text: 'Wolontariat',
    value: 'volunteering_vacation',
    outline: {
      icon: HandHeartOutlineIcon,
      background: 'bg-white',
      foreground: 'text-pink-500',
    },
    solid: {
      icon: HandHeartOutlineIcon,
      color: 'text-pink-500',
    },
  },
  {
    text: 'Odbiór za święto',
    value: 'time_in_lieu',
    outline: {
      icon: CalendarCheckIcon,
      background: 'bg-white',
      foreground: 'text-stone-500',
    },
    solid: {
      icon: CalendarCheckIcon,
      color: 'text-stone-500',
    },
  },
  {
    text: 'Zwolnienie lekarskie',
    value: 'sick_vacation',
    outline: {
      icon: MedicalBagIcon,
      background: 'bg-white',
      foreground: 'text-rose-500',
    },
    solid: {
      icon: MedicalBagIcon,
      color: 'text-rose-500',
    },
  },
]

export function useVacationTypeInfo() {
  const getStatues = () => statuses
  const findStatus = value => statuses.find(month => month.value === value)

  return {
    getStatues,
    findStatus,
  }
}
