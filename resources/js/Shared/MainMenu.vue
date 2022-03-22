<template>
  <TransitionRoot
    as="template"
    :show="sidebarOpen"
  >
    <Dialog
      as="div"
      class="fixed inset-0 flex z-40 lg:hidden"
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
        <div class="relative flex-1 flex flex-col max-w-xs w-full pt-5 pb-4 bg-blumilk-700">
          <TransitionChild
            as="template"
            enter="ease-in-out duration-300"
            enter-from="opacity-0"
            enter-to="opacity-100"
            leave="ease-in-out duration-300"
            leave-from="opacity-100"
            leave-to="opacity-0"
          >
            <div class="absolute top-0 right-0 -mr-12 pt-2">
              <button
                type="button"
                class="ml-1 flex items-center justify-center h-10 w-10 rounded-full focus:outline-none focus:ring-2 focus:ring-inset focus:ring-white"
                @click="sidebarOpen = false"
              >
                <span class="sr-only">Close sidebar</span>
                <XIcon class="h-6 w-6 text-white" />
              </button>
            </div>
          </TransitionChild>
          <div class="flex-shrink-0 flex items-center px-4">
            <InertiaLink href="/">
              <img
                class="h-8 w-auto"
                src="/img/logo-white.png"
                alt="Workflow"
              >
            </InertiaLink>
          </div>
          <nav class="mt-5 flex-shrink-0 h-full divide-y divide-blumilk-800 overflow-y-auto">
            <div class="px-2 space-y-1">
              <InertiaLink
                href="/"
                :class="[$page.component === 'Dashboard' ? 'bg-blumilk-800 text-white' : 'text-blumilk-100 hover:text-white hover:bg-blumilk-600', 'group flex items-center px-2 py-2 text-sm leading-6 font-medium rounded-md']"
              >
                <HomeIcon class="mr-4 flex-shrink-0 h-6 w-6 text-blumilk-200" />
                Strona główna
              </InertiaLink>
            </div>
            <div class="mt-3 pt-3">
              <div class="px-2 space-y-1">
                <InertiaLink
                  v-for="item in navigation"
                  :key="item.name"
                  :href="item.href"
                  :class="[$page.component === item.component ? 'bg-blumilk-800 text-white' : 'text-blumilk-100 hover:text-white hover:bg-blumilk-600', 'group flex items-center px-2 py-2 text-base font-medium rounded-md']"
                >
                  <component
                    :is="item.icon"
                    class="mr-4 flex-shrink-0 h-6 w-6 text-blumilk-200"
                  />
                  {{ item.name }}
                </InertiaLink>
              </div>
            </div>
          </nav>
        </div>
      </TransitionChild>
      <div class="flex-shrink-0 w-14" />
    </Dialog>
  </TransitionRoot>

  <!-- Static sidebar for desktop -->
  <div class="hidden lg:flex lg:w-64 lg:flex-col lg:fixed lg:inset-y-0">
    <div class="flex flex-col flex-grow bg-blumilk-700 pt-5 pb-4 overflow-y-auto">
      <div class="flex items-center flex-shrink-0 px-4">
        <InertiaLink href="/">
          <img
            class="h-8 w-auto"
            src="/img/logo-white.png"
            alt="Workflow"
          >
        </InertiaLink>
      </div>
      <nav class="mt-5 flex-1 flex flex-col divide-y divide-blumilk-800 overflow-y-auto">
        <div class="px-2 space-y-1">
          <InertiaLink
            href="/"
            :class="[$page.component === 'Dashboard' ? 'bg-blumilk-800 text-white' : 'text-blumilk-100 hover:text-white hover:bg-blumilk-600', 'group flex items-center px-2 py-2 text-sm leading-6 font-medium rounded-md']"
          >
            <HomeIcon class="mr-4 flex-shrink-0 h-6 w-6 text-blumilk-200" />
            Strona główna
          </InertiaLink>
        </div>
        <div class="mt-4 pt-4">
          <div class="px-2 space-y-1">
            <InertiaLink
              v-for="item in navigation"
              :key="item.name"
              :href="item.href"
              :class="[$page.component === item.component ? 'bg-blumilk-800 text-white' : 'text-blumilk-100 hover:text-white hover:bg-blumilk-600', 'group flex items-center px-2 py-2 text-sm leading-6 font-medium rounded-md']"
            >
              <component
                :is="item.icon"
                class="mr-4 flex-shrink-0 h-6 w-6 text-blumilk-200"
              />
              {{ item.name }}
            </InertiaLink>
          </div>
        </div>
      </nav>
    </div>
  </div>

  <div class="lg:pl-64 flex flex-col flex-1">
    <div class="relative z-10 flex-shrink-0 flex h-16 bg-white border-b border-gray-200">
      <button
        type="button"
        class="px-4 border-r border-gray-200 text-gray-400 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-blumilk-500 lg:hidden"
        @click="sidebarOpen = true"
      >
        <span class="sr-only">Open sidebar</span>
        <MenuAlt1Icon class="h-6 w-6" />
      </button>
      <div class="flex-1 px-4 flex justify-between sm:px-6 lg:px-8">
        <div class="flex items-center">
          <div>
            <Menu
              as="div"
              class="relative inline-block text-left"
            >
              <div class="flex justify-center items-center">
                <div class="mr-4">
                  Wybrany rok:
                </div>
                <div>
                  <MenuButton
                    class="inline-flex justify-center w-full rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blumilk-500"
                  >
                    {{ years.current }}
                    <ChevronDownIcon class="-mr-1 ml-2 h-5 w-5" />
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
                  class="origin-top-right absolute right-0 mt-2 w-32 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 focus:outline-none"
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
                          v-if="item.year === years.current"
                          class="h-5 w-5 text-blumilk-500 ml-2"
                        />
                      </InertiaLink>
                    </MenuItem>
                  </div>
                </MenuItems>
              </transition>
            </Menu>
          </div>
        </div>
        <div class="ml-4 flex items-center md:ml-6">
          <!-- Profile dropdown -->
          <Menu
            as="div"
            class="ml-3 relative"
          >
            <div>
              <MenuButton
                class="max-w-xs bg-white rounded-full flex items-center text-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blumilk-500 lg:p-2 lg:rounded-md lg:hover:bg-gray-50"
              >
                <img
                  class="h-8 w-8 rounded-full"
                  :src="auth.user.avatar"
                  alt="Avatar"
                >
                <span class="hidden ml-3 text-gray-700 text-sm font-medium lg:block">
                  <span class="sr-only">Open user menu for </span>
                  {{ auth.user.name }}
                </span>
                <ChevronDownIcon
                  class="hidden flex-shrink-0 ml-1 h-5 w-5 text-gray-400 lg:block"
                  aria-hidden="true"
                />
              </MenuButton>
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
                class="origin-top-right absolute right-0 mt-2 w-48 rounded-md shadow-lg py-1 bg-white ring-1 ring-black ring-opacity-5 focus:outline-none"
              >
                <MenuItem
                  v-for="item in userNavigation"
                  :key="item.name"
                  v-slot="{ active }"
                >
                  <InertiaLink
                    :href="item.href"
                    :method="item.method"
                    :as="item.as"
                    :class="[active ? 'bg-gray-100' : '', 'block w-full text-left px-4 py-2 text-sm text-gray-700']"
                  >
                    {{ item.name }}
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

<script>
import {ref} from 'vue'
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
  BellIcon,
  HomeIcon,
  CollectionIcon,
  MenuAlt1Icon,
  UserGroupIcon,
  XIcon,
  SunIcon,
  StarIcon,
  CalendarIcon,
  DocumentTextIcon,
  AdjustmentsIcon,
} from '@heroicons/vue/outline'
import {
  CashIcon,
  CheckCircleIcon,
  CheckIcon,
  ChevronDownIcon,
  ChevronRightIcon,
  OfficeBuildingIcon,
  SearchIcon,
} from '@heroicons/vue/solid'
import {computed} from 'vue'
import {usePage} from '@inertiajs/inertia-vue3'

export default {
  components: {
    Dialog,
    DialogOverlay,
    Menu,
    MenuButton,
    MenuItem,
    MenuItems,
    TransitionChild,
    TransitionRoot,
    BellIcon,
    CashIcon,
    CheckCircleIcon,
    ChevronDownIcon,
    ChevronRightIcon,
    MenuAlt1Icon,
    OfficeBuildingIcon,
    SearchIcon,
    XIcon,
    StarIcon,
    HomeIcon,
    CheckIcon,
    UserGroupIcon,
    SunIcon,
    CalendarIcon,
    AdjustmentsIcon,
  },
  setup() {
    const sidebarOpen = ref(false)

    const auth = computed(() => usePage().props.value.auth)
    const years = computed(() => usePage().props.value.years)

    const navigation = computed(() =>
      [
        {name: 'Moje wnioski', href: '/vacation-requests/me', component: 'VacationRequest/Index' , icon: DocumentTextIcon, can: true},
        {name: 'Wnioski urlopowe', href: '/vacation-requests', component: 'VacationRequest/IndexForApprovers', icon: CollectionIcon, can: auth.value.can.listAllVacationRequests},
        {name: 'Kalendarz urlopów', href: '/vacation-calendar', component: 'Calendar', icon: CalendarIcon, can: true},
        {name: 'Wykorzystanie urlopu', href: '/monthly-usage', component: 'MonthlyUsage', icon: AdjustmentsIcon, can: auth.value.can.listMonthlyUsage},
        {name: 'Dni wolne', href: '/holidays', component: 'Holidays/Index', icon: StarIcon, can: true},
        {name: 'Limity urlopów', href: '/vacation-limits', component: 'VacationLimits', icon: SunIcon, can: auth.value.can.manageVacationLimits},
        {name: 'Użytkownicy', href: '/users', component: 'Users/Index', icon: UserGroupIcon, can: auth.value.can.manageUsers},
      ].filter(item => item.can))

    const userNavigation = [
      {name: 'Wyloguj się', href: '/logout', method: 'post', as: 'button'},
    ]

    return {
      auth,
      years,
      navigation,
      userNavigation,
      sidebarOpen,
    }
  },
}
</script>
