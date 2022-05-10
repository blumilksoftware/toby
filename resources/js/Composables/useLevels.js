const technologyLevels = [
  {
    level: 1,
    name: 'Beginner',
    activeColor: 'bg-rose-400',
    backgroundColor: 'bg-rose-100',
    textColor: 'text-rose-400',
  },
  {
    level: 2,
    name: 'Junior',
    activeColor: 'bg-orange-400',
    backgroundColor: 'bg-orange-100',
    textColor: 'text-orange-400',
  },
  {
    level: 3,
    name: 'Regular',
    activeColor: 'bg-amber-400',
    backgroundColor: 'bg-amber-100',
    textColor: 'text-yellow-500',
  },
  {
    level: 4,
    name: 'Advanced',
    activeColor: 'bg-emerald-400',
    backgroundColor: 'bg-emerald-100',
    textColor: 'text-emerald-400',
  },
  {
    level: 5,
    name: 'Master',
    activeColor: 'bg-blumilk-400',
    backgroundColor: 'bg-blumilk-100',
    textColor: 'text-blumilk-400',
  },
]
const languageLevels = [
  {
    level: 1,
    name: 'A1',
    activeColor: 'bg-rose-400',
    backgroundColor: 'bg-rose-100',
    textColor: 'text-rose-400',
  },
  {
    level: 2,
    name: 'A2',
    activeColor: 'bg-orange-400',
    backgroundColor: 'bg-orange-100',
    textColor: 'text-orange-400',
  },
  {
    level: 3,
    name: 'B1',
    activeColor: 'bg-amber-400',
    backgroundColor: 'bg-amber-100',
    textColor: 'text-yellow-500',
  },
  {
    level: 4,
    name: 'B2',
    activeColor: 'bg-emerald-400',
    backgroundColor: 'bg-emerald-100',
    textColor: 'text-emerald-400',
  },
  {
    level: 5,
    name: 'C1',
    activeColor: 'bg-blumilk-400',
    backgroundColor: 'bg-blumilk-100',
    textColor: 'text-blumilk-400',
  },
  {
    level: 6,
    name: 'C2',
    activeColor: 'bg-gray-600',
    backgroundColor: 'bg-gray-200',
    textColor: 'text-gray-600',
  },
]

export default function () {
  return {
    technologyLevels,
    languageLevels,
  }
}