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
import HomeCityIcon from 'vue-material-design-icons/HomeCity.vue'
import PlusIcon from 'vue-material-design-icons/Plus.vue'

const types = [
  {
    text: 'Urlop wypoczynkowy',
    value: 'vacation',
    icon: WhiteBalanceSunnyIcon,
    color: 'text-yellow-500',
    border: 'border-yellow-500',
  },
  {
    text: 'Urlop na żądanie',
    value: 'vacation_on_request',
    icon: CommentAlertIcon,
    color: 'text-slate-500',
    border: 'border-slate-500',
  },
  {
    text: 'Urlop okolicznościowy',
    value: 'special_vacation',
    icon: StarShootingIcon,
    color: 'text-orange-500',
    border: 'border-orange-500',
  },
  {
    text: 'Opieka nad dzieckiem (art. 188 kp)',
    value: 'childcare_vacation',
    icon: BabyCarriageIcon,
    color: 'text-purple-500',
    border: 'border-purple-500',
  },
  {
    text: 'Urlop szkoleniowy',
    value: 'training_vacation',
    icon: HumanMaleBoardIcon,
    color: 'text-indigo-500',
    border: 'border-indigo-500',
  },
  {
    text: 'Urlop bezpłatny',
    value: 'unpaid_vacation',
    icon: CurrencyUsdOffIcon,
    color: 'text-emerald-500',
    border: 'border-emerald-500',
  },
  {
    text: 'Wolontariat',
    value: 'volunteering_vacation',
    icon: HandHeartOutlineIcon,
    color: 'text-pink-500',
    border: 'border-pink-500',
  },
  {
    text: 'Odbiór za święto',
    value: 'time_in_lieu',
    icon: CalendarCheckIcon,
    color: 'text-stone-500',
    border: 'border-stone-500',
  },
  {
    text: 'Zwolnienie lekarskie',
    value: 'sick_vacation',
    icon: MedicalBagIcon,
    color: 'text-rose-500',
    border: 'border-rose-500',
  },
  {
    text: 'Nieobecność',
    value: 'absence',
    icon: CalendarRemoveIcon,
    color: 'text-cyan-500',
    border: 'border-cyan-500',
  },
  {
    text: 'Praca zdalna',
    value: 'remote_work',
    icon: HomeCityIcon,
    color: 'text-lime-500',
    border: 'border-lime-500',
  },
  {
    text: 'Dodaj wniosek',
    value: 'create',
    icon: PlusIcon,
    color: 'text-blumilk-700',
    border: 'border-lime-500',
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
