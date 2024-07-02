import {
  CheckIcon as OutlineCheckIcon,
  ClockIcon as OutlineClockIcon,
  DocumentTextIcon as OutlineDocumentTextIcon,
  HandThumbDownIcon as OutlineHandThumbDownIcon,
  HandThumbUpIcon as OutlineHandThumbUpIcon,
  XMarkIcon as OutlineXMarkIcon,
  BanknotesIcon as OutlineBanknotesIcon,
} from '@heroicons/vue/24/outline'

import {
  CheckIcon as SolidCheckIcon,
  ClockIcon as SolidClockIcon,
  DocumentTextIcon as SolidDocumentTextIcon,
  HandThumbDownIcon as SolidHandThumbDownIcon,
  HandThumbUpIcon as SolidHandThumbUpIcon,
  XMarkIcon as SolidXMarkIcon,
  BanknotesIcon as BanknotesIcon,
} from '@heroicons/vue/24/solid'

const statuses = [
  {
    text: 'Utworzony',
    value: 'created',
    outline: {
      icon: OutlineDocumentTextIcon,
      foreground: 'text-white',
      background: 'bg-gray-400',
    },
    solid: {
      icon: SolidDocumentTextIcon,
      color: 'text-gray-400',
    },
  },
  {
    text: 'Czeka na akceptację od przełożonego technicznego',
    value: 'waiting_for_technical',
    outline: {
      icon: OutlineClockIcon,
      foreground: 'text-white',
      background: 'bg-amber-400',
    },
    solid: {
      icon: SolidClockIcon,
      color: 'text-amber-400',
    },
  },
  {
    text: 'Czeka na akceptację od przełożonego administracyjnego',
    value: 'waiting_for_administrative',
    outline: {
      icon: OutlineClockIcon,
      foreground: 'text-white',
      background: 'bg-amber-400',
    },
    solid: {
      icon: SolidClockIcon,
      color: 'text-amber-400',
    },
  },
  {
    text: 'Odrzucony',
    value: 'rejected',
    outline: {
      icon: OutlineHandThumbDownIcon,
      foreground: 'text-white',
      background: 'bg-rose-600',
    },
    solid: {
      icon: SolidHandThumbDownIcon,
      color: 'text-rose-600',
    },
  },
  {
    text: 'Zaakceptowany przez przełożonego technicznego',
    value: 'accepted_by_technical',
    outline: {
      icon: OutlineHandThumbUpIcon,
      foreground: 'text-white',
      background: 'bg-green-500',
    },
    solid: {
      icon: SolidHandThumbUpIcon,
      color: 'text-green-500',
    },
  },
  {
    text: 'Zaakceptowany przez przełożonego administracyjnego',
    value: 'accepted_by_administrative',
    outline: {
      icon: OutlineHandThumbUpIcon,
      foreground: 'text-white',
      background: 'bg-green-500',
    },
    solid: {
      icon: SolidHandThumbUpIcon,
      color: 'text-green-500',
    },
  },
  {
    text: 'Zatwierdzony',
    value: 'approved',
    outline: {
      icon: OutlineCheckIcon,
      foreground: 'text-white',
      background: 'bg-blumilk-500',
    },
    solid: {
      icon: SolidCheckIcon,
      color: 'text-blumilk-500',
    },
  },
  {
    text: 'Anulowany',
    value: 'cancelled',
    outline: {
      icon: OutlineXMarkIcon,
      foreground: 'text-white',
      background: 'bg-gray-900',
    },
    solid: {
      icon: SolidXMarkIcon,
      color: 'text-gray-900',
    },
  },
  {
    text: 'Rozliczony',
    value: 'settled',
    outline: {
      icon: OutlineBanknotesIcon,
      foreground: 'text-white',
      background: 'bg-green-500',
    },
    solid: {
      icon: BanknotesIcon,
      color: 'text-green-500',
    },
  },
]

export function useStatusInfo() {
  const getStatues = () => statuses
  const findStatus = value => statuses.find(status => status.value === value)

  return {
    getStatues,
    findStatus,
  }
}
