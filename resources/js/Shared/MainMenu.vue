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
  ArrowPathIcon,
  AdjustmentsVerticalIcon,
  BanknotesIcon,
  Bars3CenterLeftIcon,
  BeakerIcon,
  CakeIcon,
  CalendarIcon,
  ClipboardDocumentListIcon,
  ComputerDesktopIcon,
  DocumentDuplicateIcon,
  DocumentTextIcon,
  GiftIcon,
  HomeIcon,
  KeyIcon,
  RectangleGroupIcon,
  RectangleStackIcon,
  StarIcon,
  SunIcon,
  UserGroupIcon,
  XMarkIcon,
  ClockIcon,
} from '@heroicons/vue/24/outline'
import { ChevronDownIcon } from '@heroicons/vue/24/solid'
import InertiaLink from '@/Shared/InertiaLink.vue'
import { useGlobalProps } from '@/Composables/useGlobalProps'

const props = defineProps({
  showRefreshButton: Boolean,
})

const { auth, vacationRequestsCount, overtimeRequestsCount } = useGlobalProps()

const sidebarOpen = ref(false)

const vacationNavigation = computed(() =>
  [
    {
      name: 'Moje wnioski',
      href: '/vacation/requests/me',
      section: 'VacationRequest',
      icon: DocumentTextIcon,
      can: !auth.value.can.listAllRequests,
    },
    {
      name: 'Wnioski',
      href: '/vacation/requests',
      section: 'VacationRequest',
      icon: RectangleStackIcon,
      can: auth.value.can.listAllRequests,
      badge: vacationRequestsCount.value,
    },
    {
      name: 'Nadgodziny',
      href: '/overtime/requests',
      section: 'OvertimeRequest',
      icon: ClockIcon,
      can: auth.value.can.listAllOvertimeRequests,
      badge: overtimeRequestsCount.value,
    },
    {
      name: 'Moje nadgodziny',
      href: '/overtime/requests/me',
      section: 'OvertimeRequest',
      icon: ClockIcon,
      can: !auth.value.can.listAllOvertimeRequests && auth.value.overtimeEnabled,
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
      can: auth.value.can.listMonthlyUsage,
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
      can: auth.value.can.manageVacationLimits,
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
    can: auth.value.can.manageUsers,
  },
  {
    name: 'Jubileusze',
    href: '/employees-milestones',
    section: 'EmployeesMilestones',
    icon: CakeIcon,
    can: true,
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
    can: auth.value.can.manageResumes,
  },
  {
    name: 'CV',
    href: '/resumes',
    section: 'Resumes',
    icon: RectangleGroupIcon,
    can: auth.value.can.manageResumes,
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
    can: auth.value.can.manageBenefits,
  },
  {
    name: 'Raporty benefitowe',
    href: '/benefits-reports',
    section: 'BenefitsReport',
    icon: DocumentDuplicateIcon,
    can: auth.value.can.manageBenefits,
  },
  {
    name: 'Sprzęt',
    href: '/equipment-items',
    section: 'Equipment',
    icon: ComputerDesktopIcon,
    can: auth.value.can.manageEquipment,
  },
  {
    name: 'Mój sprzęt',
    href: '/equipment-items/me',
    section: 'Equipment',
    icon: ComputerDesktopIcon,
    can: !auth.value.can.manageEquipment,
  },
].filter(item => item.can))

const reloadPage = () => {
  window.location.reload()
}

const emit = defineEmits(['open'])
</script>

<template>
  <TransitionRoot
    :show="sidebarOpen"
    as="template"
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
        <DialogOverlay class="fixed inset-0 bg-gray-600/75" />
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
                class="flex justify-center items-center ml-1 size-10 rounded-full focus:outline-none focus:ring-2 focus:ring-inset focus:ring-white"
                type="button"
                @click="sidebarOpen = false"
              >
                <XMarkIcon class="size-6 text-white" />
              </button>
            </div>
          </TransitionChild>
          <div class="flex shrink-0 items-center px-4">
            <InertiaLink
              href="/"
              @click="sidebarOpen = false;emit('open')"
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
                :class="[$page.component === 'Dashboard' ? 'bg-blumilk-800 text-white' : 'text-blumilk-100 hover:text-white hover:bg-blumilk-600', 'group flex items-center p-2 text-base leading-6 font-medium rounded-md']"
                href="/"
                @click="sidebarOpen = false;emit('open')"
              >
                <HomeIcon class="shrink-0 mr-4 size-6 text-blumilk-200" />
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
                :class="[$page.component.startsWith(item.section) ? 'bg-blumilk-800 text-white' : 'text-blumilk-100 hover:text-white hover:bg-blumilk-600', 'group flex items-center p-2 text-base font-medium rounded-md']"
                :href="item.href"
                @click="sidebarOpen = false;item.href === '/vacation/requests' ? emit('open') : null"
              >
                <component
                  :is="item.icon"
                  class="shrink-0 mr-4 size-6 text-blumilk-200"
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
                :class="[$page.component.startsWith(item.section) ? 'bg-blumilk-800 text-white' : 'text-blumilk-100 hover:text-white hover:bg-blumilk-600', 'group flex items-center p-2 text-base font-medium rounded-md']"
                :href="item.href"
                @click="sidebarOpen = false"
              >
                <component
                  :is="item.icon"
                  class="shrink-0 mr-4 size-6 text-blumilk-200"
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

  <div class="hidden lg:flex lg:fixed lg:inset-y-0 lg:flex-col lg:w-60">
    <div class="flex overflow-y-auto flex-col grow pt-5 pb-4 bg-blumilk-700">
      <div class="flex shrink-0 items-center px-4">
        <InertiaLink href="/">
          <img
            class="w-auto h-8"
            src="/images/logo-white.svg"
            @click="emit('open')"
          >
        </InertiaLink>
      </div>
      <nav class="flex overflow-y-auto flex-col flex-1 px-2 mt-5 space-y-4">
        <InertiaLink
          :class="[$page.component === 'Dashboard' ? 'bg-blumilk-800 text-white' : 'text-blumilk-100 hover:text-white hover:bg-blumilk-600', 'group flex items-center p-2 mt-1 text-sm leading-6 font-medium rounded-md']"
          href="/"
          @click="emit('open')"
        >
          <HomeIcon class="shrink-0 mr-4 size-6 text-blumilk-200" />
          Strona główna
        </InertiaLink>
        <div
          v-if="vacationNavigation.length"
          class="pt-1 mt-1 space-y-1"
        >
          <InertiaLink
            v-for="item in vacationNavigation"
            :key="item.name"
            :class="[$page.component.startsWith(item.section) ? 'bg-blumilk-800 text-white' : 'text-blumilk-100 hover:text-white hover:bg-blumilk-600', 'group flex items-center p-2 text-sm leading-6 font-medium rounded-md']"
            :href="item.href"
          >
            <component
              :is="item.icon"
              class="shrink-0 mr-4 size-6 text-blumilk-200"
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
            :class="[$page.component.startsWith(item.section) ? 'bg-blumilk-800 text-white' : 'text-blumilk-100 hover:text-white hover:bg-blumilk-600', 'group flex items-center p-2 text-sm leading-6 font-medium rounded-md']"
            :href="item.href"
          >
            <component
              :is="item.icon"
              class="shrink-0 mr-4 size-6 text-blumilk-200"
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

  <div class="flex flex-col flex-1 lg:pl-60">
    <div class="flex relative z-10 shrink-0 h-16 bg-white border-b border-gray-200">
      <button
        class="px-4 text-gray-400 border-r border-gray-200 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-blumilk-500 lg:hidden"
        type="button"
        @click="sidebarOpen = true"
      >
        <Bars3CenterLeftIcon class="size-6" />
      </button>
      <div class="flex flex-1 justify-between items-center px-4 sm:px-6 lg:px-8">
        <div>
          <button
            v-if="showRefreshButton"
            class="inline-flex items-center py-2.5 px-4 text-sm font-medium leading-4 text-white bg-blumilk-600 hover:bg-blumilk-700 rounded-md border border-transparent focus:outline-none focus:ring-2 focus:ring-blumilk-500 focus:ring-offset-2 shadow-sm ml-3"
            @click="reloadPage"
          >
            <div class="hidden sm:block">
              Odśwież
            </div>
            <div class="sm:hidden">
              <ArrowPathIcon class="size-4" />
            </div>
          </button>
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
                :src="auth.user.avatar"
                class="size-8 rounded-full"
              >
              <span class="hidden ml-3 text-sm font-medium text-gray-700 lg:block">
                {{ auth.user.name }}
              </span>
              <ChevronDownIcon class="hidden shrink-0 ml-1 size-5 text-gray-400 lg:block" />
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
                class="absolute right-0 py-1 mt-2 w-48 bg-white rounded-md focus:outline-none ring-1 ring-black/5 shadow-lg origin-top-right"
              >
                <MenuItem v-slot="{ active }">
                  <InertiaLink
                    :class="[active ? 'bg-gray-100' : '', 'block w-full text-left px-4 py-2 text-sm text-gray-700']"
                    as="button"
                    href="/logout"
                    method="POST"
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
