<template>
  <InertiaHead title="CV" />
  <div class="bg-white shadow-md">
    <div class="flex justify-between items-center p-4 sm:px-6">
      <h2 class="text-lg font-medium leading-6 text-gray-900">
        Lista CV
      </h2>
      <div>
        <InertiaLink
          href="resumes/create"
          class="inline-flex items-center py-3 px-4 text-sm font-medium leading-4 text-white bg-blumilk-600 hover:bg-blumilk-700 rounded-md border border-transparent focus:outline-none focus:ring-2 focus:ring-blumilk-500 focus:ring-offset-2 shadow-sm"
        >
          Dodaj CV
        </InertiaLink>
      </div>
    </div>
    <div class="border-t border-gray-200">
      <div class="overflow-auto xl:overflow-visible">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th
                scope="col"
                class="py-3 px-4 text-xs font-semibold tracking-wider text-left text-gray-500 uppercase whitespace-nowrap"
              >
                Użytkownik
              </th>
              <th
                scope="col"
                class="py-3 px-4 text-xs font-semibold tracking-wider text-left text-gray-500 uppercase whitespace-nowrap"
              >
                Data utworzenia
              </th>
              <th
                scope="col"
                class="py-3 px-4 text-xs font-semibold tracking-wider text-left text-gray-500 uppercase whitespace-nowrap"
              >
                Data aktualizacji
              </th>
              <th
                scope="col"
                class="py-3 px-4 text-xs font-semibold tracking-wider text-left text-gray-500 uppercase whitespace-nowrap"
              >
                Szkoły
              </th>
              <th
                scope="col"
                class="py-3 px-4 text-xs font-semibold tracking-wider text-left text-gray-500 uppercase whitespace-nowrap"
              >
                Języki
              </th>
              <th
                scope="col"
                class="py-3 px-4 text-xs font-semibold tracking-wider text-left text-gray-500 uppercase whitespace-nowrap"
              >
                Technologie
              </th>
              <th
                scope="col"
                class="py-3 px-4 text-xs font-semibold tracking-wider text-left text-gray-500 uppercase whitespace-nowrap"
              >
                Projekty
              </th>
              <th
                scope="col"
                class="py-3 px-4 text-xs font-semibold tracking-wider text-left text-gray-500 uppercase whitespace-nowrap"
              />
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-100">
            <tr
              v-for="resume in resumes.data"
              :key="resume.id"
              class="hover:bg-blumilk-25"
            >
              <td class="p-4 text-sm text-gray-500 whitespace-nowrap">
                <div
                  v-if="resume.user"
                  class="flex"
                >
                  <span class="inline-flex justify-center items-center w-10 h-10 rounded-full">
                    <img
                      class="w-10 h-10 rounded-full"
                      :src="resume.user.avatar"
                    >
                  </span>
                  <div class="ml-3">
                    <p class="text-sm font-medium text-gray-900 break-all">
                      {{ resume.user.name }}
                    </p>
                    <p class="text-sm text-gray-500 break-all">
                      {{ resume.user.email }}
                    </p>
                  </div>
                </div>
                <template v-else>
                  <span class="text-sm font-medium text-gray-900 break-all">{{ resume.name }}</span>
                </template>
              </td>
              <td class="p-4 text-sm text-gray-500 whitespace-nowrap">
                {{ resume.createdAt }}
              </td>
              <td class="p-4 text-sm text-gray-500 whitespace-nowrap">
                {{ resume.updatedAt }}
              </td>
              <td class="p-4 text-sm text-gray-500 whitespace-nowrap">
                {{ resume.educationCount }}
              </td>
              <td class="p-4 text-sm text-gray-500 whitespace-nowrap">
                {{ resume.languageCount }}
              </td>
              <td class="p-4 text-sm text-gray-500 whitespace-nowrap">
                {{ resume.technologyCount }}
              </td>
              <td class="p-4 text-sm text-gray-500 whitespace-nowrap">
                {{ resume.projectCount }}
              </td>
              <td class="p-4 text-sm text-right text-gray-500 whitespace-nowrap">
                <Menu
                  as="div"
                  class="inline-block relative text-left"
                >
                  <MenuButton class="flex items-center text-gray-400 hover:text-gray-600 rounded-full focus:outline-none focus:ring-2 focus:ring-blumilk-500 focus:ring-offset-2 focus:ring-offset-gray-100">
                    <EllipsisVerticalIcon class="w-5 h-5" />
                  </MenuButton>

                  <transition
                    enter-active-class="transition ease-out duration-100"
                    enter-from-class="transform opacity-0 scale-95"
                    enter-to-class="transform opacity-100 scale-100"
                    leave-active-class="transition ease-in duration-75"
                    leave-from-class="transform opacity-100 scale-100"
                    leave-to-class="transform opacity-0 scale-95"
                  >
                    <MenuItems class="absolute right-0 z-10 mt-2 w-56 bg-white rounded-md focus:outline-none ring-1 ring-black ring-opacity-5 shadow-lg origin-top-right">
                      <div class="py-1">
                        <MenuItem v-slot="{ active }">
                          <InertiaLink
                            :href="`/resumes/${resume.id}/edit`"
                            :class="[active ? 'bg-gray-100 text-gray-900' : 'text-gray-700', 'font-medium block px-4 py-2 flex text-sm']"
                          >
                            <PencilIcon class="mr-2 w-5 h-5 text-blue-500" /> Edytuj
                          </InertiaLink>
                        </MenuItem>
                        <MenuItem v-slot="{ active }">
                          <a
                            :href="`/resumes/${resume.id}`"
                            :class="[active ? 'bg-gray-100 text-gray-900' : 'text-gray-700', 'block w-full text-left font-medium px-4 py-2 flex text-sm']"
                          >
                            <ArrowDownTrayIcon class="mr-2 w-5 h-5 text-blumilk-500" /> Pobierz
                          </a>
                        </MenuItem>
                        <MenuItem
                          v-slot="{ active }"
                          class="flex"
                        >
                          <InertiaLink
                            as="button"
                            method="delete"
                            :preserve-scroll="true"
                            :href="`/resumes/${resume.id}`"
                            :class="[active ? 'bg-gray-100 text-gray-900' : 'text-gray-700', 'block w-full text-left font-medium px-4 py-2 text-sm']"
                          >
                            <TrashIcon class="mr-2 w-5 h-5 text-red-500" /> Usuń
                          </InertiaLink>
                        </MenuItem>
                      </div>
                    </MenuItems>
                  </transition>
                </Menu>
              </td>
            </tr>
            <tr v-if="!resumes.data.length">
              <td
                colspan="100%"
                class="py-4 text-xl leading-5 text-center text-gray-700"
              >
                <EmptyState>
                  <template #title>
                    Brak CV
                  </template>
                  <template #text>
                    Brak wpisów dotyczących CV
                  </template>
                </EmptyState>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
      <Pagination :pagination="resumes.meta" />
    </div>
  </div>
</template>

<script setup>
import { EllipsisVerticalIcon } from '@heroicons/vue/24/outline'
import { ArrowDownTrayIcon, PencilIcon, TrashIcon } from '@heroicons/vue/24/solid'
import { Menu, MenuButton, MenuItem, MenuItems } from '@headlessui/vue'
import Pagination from '@/Shared/Pagination.vue'
import EmptyState from '@/Shared/Feedbacks/EmptyState.vue'

defineProps({
  resumes: Object,
  can: Object,
})
</script>
