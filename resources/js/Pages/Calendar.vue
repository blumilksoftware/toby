<template>
  <InertiaHead title="Kalendarz urlopów" />
  <div class="bg-white sm:rounded-lg shadow-md">
    <div class="p-4 sm:px-6">
      <h2 class="text-lg leading-6 font-medium text-gray-900">
        Kalendarz urlopów
      </h2>
    </div>
    <div class="overflow-x-auto">
      <table class="w-full text-center border border-gray-300">
        <thead>
          <tr>
            <th class="w-64 py-2 border border-gray-300">
              <Menu
                as="div"
                class="relative inline-block text-left"
              >
                <div>
                  <MenuButton
                    class="inline-flex justify-center w-full rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-gray-100 focus:ring-blumilk-500"
                  >
                    Styczeń
                    <ChevronDownIcon
                      class="-mr-1 ml-2 h-5 w-5"
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
                    class="origin-top-right absolute right-0 mt-2 w-56 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 focus:outline-none"
                  >
                    <div class="py-1">
                      <MenuItem>
                        <a
                          href="#"
                          class="text-gray-900 block px-4 py-2 text-sm"
                        >Styczeń</a>
                      </MenuItem>
                      <MenuItem>
                        <a
                          href="#"
                          class="text-gray-900 block px-4 py-2 text-sm"
                        >Luty</a>
                      </MenuItem>
                      <MenuItem>
                        <a
                          href="#"
                          class="text-gray-900 block px-4 py-2 text-sm"
                        >Marzec</a>
                      </MenuItem>
                    </div>
                  </MenuItems>
                </transition>
              </Menu>
            </th>
            <th
              v-for="i in 31"
              :key="i"
              class="border border-gray-300 font-semibold text-gray-900 py-4 px-2 bg-gray-50"
            >
              {{ i }}
              <p class="text-gray-800 font-normal mt-1">
                Pt
              </p>
            </th>
          </tr>
        </thead>
        <tbody>
          <tr
            v-for="user in users.data"
            :key="user.id"
          >
            <th class="border border-gray-300 py-2 px-4">
              <div class="flex justify-start items-center">
                <span class="inline-flex items-center justify-center h-10 w-10 rounded-full">
                  <img
                    class="h-10 w-10 rounded-full"
                    :src="user.avatar"
                    alt=""
                  >
                </span>
                <div class="ml-3">
                  <div class="text-sm font-medium text-gray-900">
                    {{ user.name }}
                  </div>
                </div>
              </div>
            </th>
            <td
              v-for="i in 31"
              :key="i"
              class="border border-gray-300 bg-blumilk-25"
            />
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script>
import {Menu, MenuButton, MenuItem, MenuItems} from '@headlessui/vue'
import {ChevronDownIcon} from '@heroicons/vue/solid'

export default {
  name: 'VacationCalendar',
  components: {
    Menu,
    MenuButton,
    MenuItem,
    MenuItems,
    ChevronDownIcon,
  },
  props: {
    users: {
      type: Object,
      default: () => null,
    },
  },
}
</script>
