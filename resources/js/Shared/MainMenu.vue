<script setup>
import { computed, ref } from 'vue'
import {
  Dialog,
  DialogOverlay,
  Menu,
  MenuButton,
  MenuItem,
  MenuItems,
  TransitionChild,
  TransitionRoot,
} from '@headlessui/vue'
import {
  HomeIcon,
  RectangleStackIcon,
  Bars3CenterLeftIcon,
  UserGroupIcon,
  XMarkIcon,
  SunIcon,
  ClipboardDocumentListIcon,
  StarIcon,
  CalendarIcon,
  DocumentTextIcon,
  AdjustmentsVerticalIcon,
  KeyIcon,
  RectangleGroupIcon,
  BeakerIcon,
  GiftIcon,
  BanknotesIcon,
  ComputerDesktopIcon,
  DocumentDuplicateIcon,
} from '@heroicons/vue/24/outline'
import { CheckIcon, ChevronDownIcon } from '@heroicons/vue/24/solid'

const props = defineProps({
  auth: Object,
  years: Object,
  vacationRequestsCount: Number,
})

const sidebarOpen = ref(false)

const vacationNavigation = computed(() =>
  [
    {
      name: 'Moje wnioski',
      href: '/vacation/requests/me',
      section: 'VacationRequest',
      icon: DocumentTextIcon,
      can: !props.auth.can.listAllRequests,
    },
    {
      name: 'Wnioski',
      href: '/vacation/requests',
      section: 'VacationRequest',
      icon: RectangleStackIcon,
      can: props.auth.can.listAllRequests,
      badge: props.vacationRequestsCount,
    },
    {
      name: 'Kalendarz',
      href: '/calendar',
      section: 'Calendar',
      icon: CalendarIcon,
      can: true,
    },
    {
      name: 'Wykorzystanie urlopu',
      href: '/vacation/monthly-usage',
      section: 'MonthlyUsage',
      icon: AdjustmentsVerticalIcon,
      can: props.auth.can.listMonthlyUsage,
    },
    {
      name: 'Dni wolne',
      href: '/holidays',
      section: 'Holidays/',
      icon: StarIcon,
      can: true,
    },
    {
      name: 'Limity urlopów',
      href: '/vacation/limits',
      section: 'VacationLimits',
      icon: SunIcon,
      can: props.auth.can.manageVacationLimits,
    },
    {
      name: 'Podsumowanie roczne',
      href: '/vacation/annual-summary',
      section: 'AnnualSummary',
      icon: ClipboardDocumentListIcon,
      can: true,
    },
  ].filter(item => item.can))

const miscNavigation = computed(() => [
  {
    name: 'Użytkownicy',
    href: '/users',
    section: 'Users',
    icon: UserGroupIcon,
    can: props.auth.can.manageUsers,
  },
  {
    name: 'Klucze',
    href: '/keys',
    section: 'Keys',
    icon: KeyIcon,
    can: true,
  },
  {
    name: 'Technologie',
    href: '/technologies',
    section: 'Technologies',
    icon: BeakerIcon,
    can: props.auth.can.manageResumes,
  },
  {
    name: 'CV',
    href: '/resumes',
    section: 'Resumes',
    icon: RectangleGroupIcon,
    can: props.auth.can.manageResumes,
  },
  {
    name: 'Benefity',
    href: '/benefits',
    section: 'Benefits/',
    icon: GiftIcon,
    can: true,
  },
  {
    name: 'Aktualne benefity',
    href: '/assigned-benefits',
    section: 'AssignedBenefits',
    icon: BanknotesIcon,
    can: props.auth.can.manageBenefits,
  },
  {
    name: 'Raporty benefitowe',
    href: '/benefits-reports',
    section: 'BenefitsReport',
    icon: DocumentDuplicateIcon,
    can: props.auth.can.manageBenefits,
  },
  {
    name: 'Sprzęt',
    href: '/equipment-items',
    section: 'Equipment',
    icon: ComputerDesktopIcon,
    can: props.auth.can.manageEquipment,
  },
  {
    name: 'Mój sprzęt',
    href: '/equipment-items/me',
    section: 'Equipment',
    icon: ComputerDesktopIcon,
    can: !props.auth.can.manageEquipment,
  },
].filter(item => item.can))
</script>

<template>
  <TransitionRoot
    as="template"
    :show="sidebarOpen"
  >
    <Dialog
      as="div"
      class="flex fixed inset-0 z-40 lg:hidden"
      @close="sidebarOpen = false"
    >
      <TransitionChild
        as="template"
        enter="transition-opacity ease-linear duration-300"
        enter-from="opacity-0"
        enter-to="opacity-100"
        leave="transition-opacity ease-linear duration-300"
        leave-from="opacity-100"
        leave-to="opacity-0"
      >
        <DialogOverlay class="fixed inset-0 bg-gray-600 bg-opacity-75" />
      </TransitionChild>
      <TransitionChild
        as="template"
        enter="transition ease-in-out duration-300 transform"
        enter-from="-translate-x-full"
        enter-to="translate-x-0"
        leave="transition ease-in-out duration-300 transform"
        leave-from="translate-x-0"
        leave-to="-translate-x-full"
      >
        <div class="flex relative flex-col flex-1 pt-5 pb-4 w-full max-w-xs bg-blumilk-700">
          <TransitionChild
            as="template"
            enter="ease-in-out duration-300"
            enter-from="opacity-0"
            enter-to="opacity-100"
            leave="ease-in-out duration-300"
            leave-from="opacity-100"
            leave-to="opacity-0"
          >
            <div class="absolute top-0 right-0 pt-2 -mr-12">
              <button
                type="button"
                class="flex justify-center items-center ml-1 w-10 h-10 rounded-full focus:outline-none focus:ring-2 focus:ring-inset focus:ring-white"
                @click="sidebarOpen = false"
              >
                <XMarkIcon class="w-6 h-6 text-white" />
              </button>
            </div>
          </TransitionChild>
          <div class="flex shrink-0 items-center px-4">
            <InertiaLink
              href="/"
              @click="sidebarOpen = false;"
            >
              <img
                class="w-auto h-8"
                src="/images/logo-white.svg"
              >
            </InertiaLink>
          </div>
          <nav class="overflow-y-auto shrink-0 py-5 h-full space-y-5">
            <div class="px-2 space-y-1">
              <InertiaLink
                href="/"
                :class="[$page.component === 'Dashboard' ? 'bg-blumilk-800 text-white' : 'text-blumilk-100 hover:text-white hover:bg-blumilk-600', 'group flex items-center px-2 py-2 text-base leading-6 font-medium rounded-md']"
                @click="sidebarOpen = false;"
              >
                <HomeIcon class="shrink-0 mr-4 w-6 h-6 text-blumilk-200" />
                Strona główna
              </InertiaLink>
            </div>
            <div
              v-if="vacationNavigation.length"
              class="py-1 px-2 space-y-1"
            >
              <InertiaLink
                v-for="item in vacationNavigation"
                :key="item.name"
                :href="item.href"
                :class="[$page.component.startsWith(item.section) ? 'bg-blumilk-800 text-white' : 'text-blumilk-100 hover:text-white hover:bg-blumilk-600', 'group flex items-center px-2 py-2 text-base font-medium rounded-md']"
                @click="sidebarOpen = false;"
              >
                <component
                  :is="item.icon"
                  class="shrink-0 mr-4 w-6 h-6 text-blumilk-200"
                />
                {{ item.name }}
                <span
                  v-if="item.badge"
                  class="py-0.5 px-2.5 ml-3 text-xs font-semibold text-gray-600 bg-gray-100 rounded-full 2xl:inline-block"
                >
                  {{ item.badge }}
                </span>
              </InertiaLink>
            </div>
            <div
              v-if="miscNavigation.length"
              class="py-1 px-2 space-y-1"
            >
              <InertiaLink
                v-for="item in miscNavigation"
                :key="item.name"
                :href="item.href"
                :class="[$page.component.startsWith(item.section) ? 'bg-blumilk-800 text-white' : 'text-blumilk-100 hover:text-white hover:bg-blumilk-600', 'group flex items-center px-2 py-2 text-base font-medium rounded-md']"
                @click="sidebarOpen = false;"
              >
                <component
                  :is="item.icon"
                  class="shrink-0 mr-4 w-6 h-6 text-blumilk-200"
                />
                {{ item.name }}
                <span
                  v-if="item.badge"
                  class="py-0.5 px-2.5 ml-3 text-xs font-semibold text-gray-600 bg-gray-100 rounded-full 2xl:inline-block"
                >
                  {{ item.badge }}
                </span>
              </InertiaLink>
            </div>
          </nav>
        </div>
      </TransitionChild>
      <div class="shrink-0 w-14" />
    </Dialog>
  </TransitionRoot>

  <div class="hidden lg:flex lg:fixed lg:inset-y-0 lg:flex-col lg:w-64">
    <div class="flex overflow-y-auto flex-col grow pt-5 pb-4 bg-blumilk-700">
      <div class="flex shrink-0 items-center px-4">
        <InertiaLink href="/">
          <img
            src="/images/logo-white.svg"
            class="w-auto h-8"
          >
        </InertiaLink>
      </div>
      <nav class="flex overflow-y-auto flex-col flex-1 px-2 mt-5 space-y-4">
        <InertiaLink
          href="/"
          :class="[$page.component === 'Dashboard' ? 'bg-blumilk-800 text-white' : 'text-blumilk-100 hover:text-white hover:bg-blumilk-600', 'group flex items-center px-2 py-2 mt-1 text-sm leading-6 font-medium rounded-md']"
        >
          <HomeIcon class="shrink-0 mr-4 w-6 h-6 text-blumilk-200" />
          Strona główna
        </InertiaLink>
        <div
          v-if="vacationNavigation.length"
          class="pt-1 mt-1 space-y-1"
        >
          <InertiaLink
            v-for="item in vacationNavigation"
            :key="item.name"
            :href="item.href"
            :class="[$page.component.startsWith(item.section) ? 'bg-blumilk-800 text-white' : 'text-blumilk-100 hover:text-white hover:bg-blumilk-600', 'group flex items-center px-2 py-2 text-sm leading-6 font-medium rounded-md']"
          >
            <component
              :is="item.icon"
              class="shrink-0 mr-4 w-6 h-6 text-blumilk-200"
            />
            {{ item.name }}
            <span
              v-if="item.badge"
              class="py-0.5 px-2.5 ml-3 text-xs font-semibold text-gray-600 bg-gray-100 rounded-full 2xl:inline-block"
            >
              {{ item.badge }}
            </span>
          </InertiaLink>
        </div>
        <div
          v-if="miscNavigation.length"
          class="pt-1 mt-1 space-y-1"
        >
          <InertiaLink
            v-for="item in miscNavigation"
            :key="item.name"
            :href="item.href"
            :class="[$page.component.startsWith(item.section) ? 'bg-blumilk-800 text-white' : 'text-blumilk-100 hover:text-white hover:bg-blumilk-600', 'group flex items-center px-2 py-2 text-sm leading-6 font-medium rounded-md']"
          >
            <component
              :is="item.icon"
              class="shrink-0 mr-4 w-6 h-6 text-blumilk-200"
            />
            {{ item.name }}
            <span
              v-if="item.badge"
              class="py-0.5 px-2.5 ml-3 text-xs font-semibold text-gray-600 bg-gray-100 rounded-full 2xl:inline-block"
            >
              {{ item.badge }}
            </span>
          </InertiaLink>
        </div>
      </nav>
    </div>
  </div>

  <div class="flex flex-col flex-1 lg:pl-64">
    <div class="flex relative z-10 shrink-0 h-16 bg-white border-b border-gray-200">
      <button
        type="button"
        class="px-4 text-gray-400 border-r border-gray-200 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-blumilk-500 lg:hidden"
        @click="sidebarOpen = true"
      >
        <Bars3CenterLeftIcon class="w-6 h-6" />
      </button>
      <div class="flex flex-1 justify-between px-4 sm:px-6 lg:px-8">
        <div class="flex items-center">
          <div>
            <Menu
              as="div"
              class="inline-block relative text-left"
            >
              <div class="flex justify-center items-center">
                <div class="mr-4 text-sm">
                  Wybrany rok:
                </div>
                <div>
                  <MenuButton
                    class="inline-flex justify-center py-2 px-4 w-full text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 rounded-md border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blumilk-500  shadow-sm"
                  >
                    {{ years.selected.year }}
                    <ChevronDownIcon class="-mr-1 ml-2 w-5 h-5" />
                  </MenuButton>
                </div>
              </div>

              <transition
                enter-active-class="transition ease-out duration-100"
                enter-from-class="transform opacity-0 scale-95"
                enter-to-class="transform opacity-100 scale-100"
                leave-active-class="transition ease-in duration-75"
                leave-from-class="transform opacity-100 scale-100"
                leave-to-class="transform opacity-0 scale-95"
              >
                <MenuItems
                  class="absolute right-0 mt-2 w-24 bg-white rounded-md focus:outline-none ring-1 ring-black ring-opacity-5 shadow-lg origin-top-right"
                >
                  <div class="py-1">
                    <MenuItem
                      v-for="(item, index) in years.navigation"
                      :key="index"
                      v-slot="{ active }"
                    >
                      <InertiaLink
                        :href="item.link"
                        as="button"
                        method="post"
                        :preserve-state="false"
                        :class="[active ? 'bg-gray-100 text-gray-900' : 'text-gray-700', 'flex w-full px-4 py-2 text-sm']"
                      >
                        {{ item.year }}
                        <CheckIcon
                          v-if="item.year === years.selected.year"
                          class="ml-2 w-5 h-5 text-blumilk-500"
                        />
                      </InertiaLink>
                    </MenuItem>
                  </div>
                </MenuItems>
              </transition>
            </Menu>
          </div>
          <div
            v-if="years.current.year !== years.selected.year"
            class="hidden ml-3 text-sm sm:block"
          >
            <InertiaLink
              :href="years.current.link"
              as="button"
              method="post"
              :preserve-state="false"
              class="font-semibold text-blumilk-600 hover:text-blumilk-500"
            >
              Wróć do obecnego roku
            </inertialink>
          </div>
        </div>
        <div class="flex items-center ml-4 md:ml-6">
          <Menu
            as="div"
            class="relative ml-3"
          >
            <MenuButton
              class="flex items-center max-w-xs text-sm bg-white rounded-full focus:outline-none focus:ring-2 focus:ring-blumilk-500 focus:ring-offset-2 lg:p-2 lg:hover:bg-gray-50 lg:rounded-md"
            >
              <img
                class="w-8 h-8 rounded-full"
                :src="auth.user.avatar"
              >
              <span class="hidden ml-3 text-sm font-medium text-gray-700 lg:block">
                {{ auth.user.name }}
              </span>
              <ChevronDownIcon class="hidden shrink-0 ml-1 w-5 h-5 text-gray-400 lg:block" />
            </MenuButton>
            <transition
              enter-active-class="transition ease-out duration-100"
              enter-from-class="transform opacity-0 scale-95"
              enter-to-class="transform opacity-100 scale-100"
              leave-active-class="transition ease-in duration-75"
              leave-from-class="transform opacity-100 scale-100"
              leave-to-class="transform opacity-0 scale-95"
            >
              <MenuItems
                class="absolute right-0 py-1 mt-2 w-48 bg-white rounded-md focus:outline-none ring-1 ring-black ring-opacity-5 shadow-lg origin-top-right"
              >
                <MenuItem v-slot="{ active }">
                  <InertiaLink
                    href="/logout"
                    method="POST"
                    as="button"
                    :class="[active ? 'bg-gray-100' : '', 'block w-full text-left px-4 py-2 text-sm text-gray-700']"
                  >
                    Wyloguj się
                  </InertiaLink>
                </MenuItem>
              </MenuItems>
            </transition>
          </Menu>
        </div>
      </div>
    </div>
  </div>
</template>
