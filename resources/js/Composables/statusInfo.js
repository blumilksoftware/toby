import {
  CheckIcon as OutlineCheckIcon,
  ClockIcon as OutlineClockIcon,
  DocumentTextIcon as OutlineDocumentTextIcon,
  ThumbDownIcon as OutlineThumbDownIcon,
  ThumbUpIcon as OutlineThumbUpIcon,
  XIcon as OutlineXIcon,
} from '@heroicons/vue/outline'

import {
  CheckIcon as SolidCheckIcon,
  ClockIcon as SolidClockIcon,
  DocumentTextIcon as SolidDocumentTextIcon,
  ThumbDownIcon as SolidThumbDownIcon,
  ThumbUpIcon as SolidThumbUpIcon,
  XIcon as SolidXIcon,
} from '@heroicons/vue/solid'

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
      icon: OutlineThumbDownIcon,
      foreground: 'text-white',
      background: 'bg-rose-600',
    },
    solid: {
      icon: SolidThumbDownIcon,
      color: 'text-rose-600',
    },
  },
  {
    text: 'Zaakceptowany przez przełożonego technicznego',
    value: 'accepted_by_technical',
    outline: {
      icon: OutlineThumbUpIcon,
      foreground: 'text-white',
      background: 'bg-green-500',
    },
    solid: {
      icon: SolidThumbUpIcon,
      color: 'text-green-500',
    },
  },
  {
    text: 'Zaakceptowany przez przełożonego administracyjnego',
    value: 'accepted_by_administrative',
    outline: {
      icon: OutlineThumbUpIcon,
      foreground: 'text-white',
      background: 'bg-green-500',
    },
    solid: {
      icon: SolidThumbUpIcon,
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
      icon: OutlineXIcon,
      foreground: 'text-white',
      background: 'bg-gray-900',
    },
    solid: {
      icon: SolidXIcon,
      color: 'text-gray-900',
    },
  },
]

export function useStatusInfo(status) {
  return statuses.find(statusInfo => statusInfo.value === status)
}
