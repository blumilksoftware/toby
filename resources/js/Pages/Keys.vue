<template>
  <InertiaHead title="Klucze" />
  <div class="bg-white shadow-md">
    <div class="flex justify-between items-center p-4 sm:px-6">
      <div>
        <h2 class="text-lg font-medium leading-6 text-gray-900">
          Klucze
        </h2>
      </div>
      <div v-if="can.manageKeys">
        <InertiaLink
          as="button"
          href="/keys"
          method="post"
          class="inline-flex items-center py-3 px-4 text-sm font-medium leading-4 text-white bg-blumilk-600 hover:bg-blumilk-700 rounded-md border border-transparent focus:outline-none focus:ring-2 focus:ring-blumilk-500 focus:ring-offset-2 shadow-sm"
        >
          Dodaj klucz
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
                Klucz
              </th>
              <th
                scope="col"
                class="py-3 px-4 text-xs font-semibold tracking-wider text-left text-gray-500 uppercase whitespace-nowrap"
              >
                Kto ma
              </th>
              <th
                scope="col"
                class="py-3 px-4 text-xs font-semibold tracking-wider text-left text-gray-500 uppercase whitespace-nowrap"
              >
                Od kiedy
              </th>
              <th
                scope="col"
                class="py-3 px-4 text-xs font-semibold tracking-wider text-left text-gray-500 uppercase whitespace-nowrap"
              />
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-100">
            <tr
              v-for="key in keys.data"
              :key="key.id"
              class="hover:bg-blumilk-25"
            >
              <td class="p-4 text-sm text-gray-500 whitespace-nowrap">
                Klucz nr {{ key.id }}
              </td>
              <td class="p-4 text-sm text-gray-500 whitespace-nowrap">
                <div v-if="key.user" class="flex">
                  <span class="inline-flex justify-center items-center w-10 h-10 rounded-full">
                    <img
                      class="w-10 h-10 rounded-full"
                      :src="key.user.avatar"
                    >
                  </span>
                  <div class="ml-3">
                    <p class="text-sm font-medium text-gray-900 break-all">
                      {{ key.user.name }}
                    </p>
                    <p class="text-sm text-gray-500 break-all">
                      {{ key.user.email }}
                    </p>
                  </div>
                </div>
                <div v-else class="flex">
                  <div class="ml-3">
                    <p class="text-sm font-medium text-gray-900 break-all">
                      Nikt
                    </p>
                  </div>
                </div>
              </td>
              <td class="p-4 text-sm text-gray-500 whitespace-nowrap">
                {{ key.updatedAt }}
              </td>
              <td class="p-4 text-sm text-right text-gray-500 whitespace-nowrap">
                <Menu
                  as="div"
                  class="inline-block relative text-left"
                >
                  <MenuButton
                    class="flex items-center text-gray-400 hover:text-gray-600 rounded-full focus:outline-none focus:ring-2 focus:ring-blumilk-500 focus:ring-offset-2 focus:ring-offset-gray-100"
                  >
                    <DotsVerticalIcon class="w-5 h-5" />
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
                      class="absolute right-0 z-10 mt-2 w-56 bg-white rounded-md focus:outline-none ring-1 ring-black ring-opacity-5 shadow-lg origin-top-right"
                    >
                      <div class="py-1">
                        <MenuItem
                          v-if="key.can.take"
                          v-slot="{ active }"
                          class="flex"
                        >
                          <InertiaLink
                            as="button"
                            method="post"
                            preserve-scroll
                            :href="`/keys/${key.id}/take`"
                            :class="[active ? 'bg-gray-100 text-gray-900' : 'text-gray-700', 'block w-full text-left font-medium px-4 py-2 text-sm']"
                          >
                            <DominoMaskIcon class="mr-2 w-5 h-5 text-purple-500" />
                            Zabierz klucze
                          </InertiaLink>
                        </MenuItem>
                        <MenuItem
                          v-if="key.can.give"
                          v-slot="{ active }"
                          class="flex"
                        >
                          <button
                            :class="[active ? 'bg-gray-100 text-gray-900' : 'text-gray-700', 'block w-full text-left font-medium px-4 py-2 text-sm']"
                            @click="giveKey(key)"
                          >
                            <HandshakeIcon class="mr-2 w-5 h-5 text-emerald-500" />
                            Przekaż klucze
                          </button>
                        </MenuItem>
                        <MenuItem
                          v-if="can.manageKeys"
                          v-slot="{ active }"
                          class="flex"
                        >
                          <InertiaLink
                            as="button"
                            method="delete"
                            preserve-scroll
                            :href="`/keys/${key.id}`"
                            :class="[active ? 'bg-gray-100 text-gray-900' : 'text-gray-700', 'block w-full text-left font-medium px-4 py-2 text-sm']"
                          >
                            <TrashIcon class="mr-2 w-5 h-5 text-red-500" />
                            Usuń
                          </InertiaLink>
                        </MenuItem>
                      </div>
                    </MenuItems>
                  </transition>
                </Menu>
              </td>
            </tr>
            <tr v-if="!keys.data.length">
              <td
                colspan="100%"
                class="py-4 text-xl leading-5 text-center text-gray-700"
              >
                <EmptyState class="text-gray-700">
                  <template #head>
                    <KeyIcon class="mx-auto w-12 h-12" />
                  </template>
                  <template #title>
                    Brak kluczy
                  </template>
                  <template #text>
                    Nie dodano ani jednego klucza
                  </template>
                </EmptyState>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
  <TransitionRoot
    as="template"
    :show="giving"
  >
    <Dialog
      is="div"
      class="overflow-y-auto fixed inset-0 z-10"
      @close="giving = false"
    >
      <div class="flex justify-center items-end px-4 pt-4 pb-20 min-h-screen text-center sm:block sm:p-0">
        <TransitionChild
          as="template"
          enter="ease-out duration-300"
          enter-from="opacity-0"
          enter-to="opacity-100"
          leave="ease-in duration-200"
          leave-from="opacity-100"
          leave-to="opacity-0"
        >
          <DialogOverlay class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" />
        </TransitionChild>

        <span class="hidden sm:inline-block sm:h-screen sm:align-middle">&#8203;</span>
        <TransitionChild
          as="template"
          enter="ease-out duration-300"
          enter-from="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
          enter-to="opacity-100 translate-y-0 sm:scale-100"
          leave="ease-in duration-200"
          leave-from="opacity-100 translate-y-0 sm:scale-100"
          leave-to="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
        >
          <form
            class="inline-block relative px-4 pt-5 pb-4 text-left align-bottom bg-white rounded-lg shadow-xl transition-all transform sm:p-6 sm:my-8 sm:w-full sm:max-w-sm sm:align-middle"
            @submit.prevent="submitGiveKey"
          >
            <div>
              <div>
                <DialogTitle
                  as="h3"
                  class="text-lg font-medium leading-6 text-center text-gray-900 font-sembiold"
                >
                  Przekaż klucz nr {{ keyToGive?.id }}
                </DialogTitle>
                <div class="mt-5">
                  <Listbox
                    v-model="form.user"
                    as="div"
                  >
                    <ListboxLabel class="block text-sm font-medium text-left text-gray-700">
                      Użytkownik
                    </ListboxLabel>
                    <div class="relative mt-2">
                      <ListboxButton
                        class="relative py-2 pr-10 pl-3 w-full max-w-lg text-left bg-white rounded-md border focus:outline-none focus:ring-1 shadow-sm cursor-default sm:text-sm"
                        :class="{ 'border-red-300 text-red-900 focus:ring-red-500 focus:border-red-500': form.errors.user, 'focus:ring-blumilk-500 focus:border-blumilk-500 sm:text-sm border-gray-300': !form.errors.user }"
                      >
                        <span class="flex items-center">
                          <img
                            :src="form.user.avatar"
                            class="shrink-0 w-6 h-6 rounded-full"
                          >
                          <span class="block ml-3 truncate">{{ form.user.name }}</span>
                        </span>
                        <span class="flex absolute inset-y-0 right-0 items-center pr-2 pointer-events-none">
                          <SelectorIcon class="w-5 h-5 text-gray-400" />
                        </span>
                      </ListboxButton>

                      <transition
                        leave-active-class="transition ease-in duration-100"
                        leave-from-class="opacity-100"
                        leave-to-class="opacity-0"
                      >
                        <ListboxOptions
                          class="overflow-auto absolute z-10 py-1 mt-1 w-full max-w-lg max-h-60 text-base bg-white rounded-md focus:outline-none ring-1 ring-black ring-opacity-5 shadow-lg sm:text-sm"
                        >
                          <ListboxOption
                            v-for="user in filteredUsers"
                            :key="user.id"
                            v-slot="{ active, selected }"
                            as="template"
                            :value="user"
                          >
                            <li :class="[active ? 'bg-gray-100' : 'text-gray-900', 'cursor-default select-none relative py-2 pl-3 pr-9']">
                              <div class="flex items-center">
                                <img
                                  :src="user.avatar"
                                  alt=""
                                  class="shrink-0 w-6 h-6 rounded-full"
                                >
                                <span :class="[selected ? 'font-semibold' : 'font-normal', 'ml-3 block truncate']">
                                  {{ user.name }}
                                </span>
                              </div>

                              <span
                                v-if="selected"
                                :class="['text-blumilk-600 absolute inset-y-0 right-0 flex items-center pr-4']"
                              >
                                <CheckIcon class="w-5 h-5" />
                              </span>
                            </li>
                          </ListboxOption>
                        </ListboxOptions>
                      </transition>
                      <p
                        v-if="form.errors.type"
                        class="mt-2 text-sm text-red-600"
                      >
                        {{ form.errors.type }}
                      </p>
                    </div>
                  </Listbox>
                </div>
              </div>
            </div>
            <div class="mt-5 sm:mt-6">
              <div class="flex justify-end space-x-3">
                <button
                  type="button"
                  class="py-2 px-4 text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 rounded-md border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blumilk-500 focus:ring-offset-2 shadow-sm"
                  @click="giving = false"
                >
                  Anuluj
                </button>
                <button
                  type="submit"
                  :disabled="form.processing"
                  class="inline-flex justify-center py-2 px-4 text-base font-medium text-white bg-blumilk-600 hover:bg-blumilk-700 rounded-md border border-transparent focus:outline-none focus:ring-2 focus:ring-blumilk-500 focus:ring-offset-2 shadow-sm sm:text-sm"
                >
                  Przekaż
                </button>
              </div>
            </div>
          </form>
        </TransitionChild>
      </div>
    </Dialog>
  </TransitionRoot>
</template>

<script setup>
import { DotsVerticalIcon, TrashIcon, CheckIcon, SelectorIcon, KeyIcon } from '@heroicons/vue/solid'
import DominoMaskIcon from 'vue-material-design-icons/DominoMask.vue'
import HandshakeIcon from 'vue-material-design-icons/Handshake.vue'
import { Menu, MenuButton, MenuItem, MenuItems } from '@headlessui/vue'
import { computed, ref } from 'vue'
import { Dialog, DialogOverlay, DialogTitle, TransitionChild, TransitionRoot } from '@headlessui/vue'
import { Listbox, ListboxButton, ListboxLabel, ListboxOption, ListboxOptions } from '@headlessui/vue'
import { useForm } from '@inertiajs/inertia-vue3'
import EmptyState from '@/Shared/Feedbacks/EmptyState.vue'

const props = defineProps({
  keys: Object,
  users: Object,
  can: Object,
})

const keyToGive = ref(null)
const giving = ref(false)

const form = useForm({
  user: null,
})

const filteredUsers = computed(() => props.users.data.filter(user => user.id !== keyToGive.value.user.id ))

function giveKey(key) {
  keyToGive.value = key
  form.user = filteredUsers.value[0]
  giving.value = true
}

function submitGiveKey() {
  form
    .transform(data => ({
      user: data.user.id,
    }))
    .post(`/keys/${keyToGive.value.id}/give`, {
      preserveState: (page) => Object.keys(page.props.errors).length,
      preserveScroll: true,
    })
}

</script>
