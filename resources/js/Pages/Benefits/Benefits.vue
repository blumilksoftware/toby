<script setup>
import { EllipsisVerticalIcon, TrashIcon } from '@heroicons/vue/24/solid'
import { Menu, MenuButton, MenuItem, MenuItems } from '@headlessui/vue'
import { ref } from 'vue'
import { Dialog, DialogOverlay, DialogTitle, TransitionChild, TransitionRoot } from '@headlessui/vue'
import { useForm } from '@inertiajs/inertia-vue3'
import EmptyState from '@/Shared/Feedbacks/EmptyState.vue'

defineProps({
  benefits: Object,
  can: Object,
})

const creating = ref(false)

const form = useForm({
  name: null,
  companion: false,
})

function submitCreateBenefit() {
  form.post('benefits', {
    preserveState: (page) => Object.keys(page.props.errors).length,
    preserveScroll: true,
    onSuccess: () => form.reset(),
  })
}
</script>

<template>
  <InertiaHead title="Benefity" />
  <div class="bg-white shadow-md">
    <div class="flex justify-between items-center p-4 sm:px-6">
      <div>
        <h2 class="text-lg font-medium leading-6 text-gray-900">
          Benefity
        </h2>
      </div>
      <div v-if="can.manageBenefits">
        <button
          type="button"
          class="inline-flex items-center py-3 px-4 text-sm font-medium leading-4 text-white bg-blumilk-600 hover:bg-blumilk-700 rounded-md border border-transparent focus:outline-none focus:ring-2 focus:ring-blumilk-500 focus:ring-offset-2 shadow-sm"
          dusk="create-benefit-button"
          @click="creating = true"
        >
          Dodaj benefit
        </button>
      </div>
    </div>
    <div class="border-t border-gray-200">
      <div class="overflow-auto xl:overflow-visible">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th
                scope="col"
                class="py-3 px-4 text-xs font-semibold text-left text-gray-500 uppercase whitespace-nowrap"
              >
                Benefit
              </th>
              <th
                scope="col"
                class="py-3 px-4 text-xs font-semibold tracking-wider text-left text-gray-500 uppercase whitespace-nowrap"
              >
                Przeznaczenie
              </th>
              <th
                scope="col"
                class="py-3 px-4 text-xs font-semibold tracking-wider text-left text-gray-500 uppercase whitespace-nowrap"
              />
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-100">
            <tr
              v-for="benefit in benefits.data"
              :key="benefit.id"
              class="hover:bg-blumilk-25"
            >
              <td class="px-4 py-2 text-sm text-gray-500 whitespace-nowrap">
                {{ benefit.name }}
              </td>
              <td class="px-4 py-2 text-sm text-gray-500 whitespace-nowrap">
                <span
                  :class="[benefit.companion ? 'bg-green-100 text-green-800':'bg-amber-100 text-amber-800']"
                  class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                >
                  {{ benefit.companion ? "Dla osoby towarzyszącej" : "Dla pracownika" }}
                </span>
              </td>
              <td class="px-4 py-2 text-sm text-right text-gray-500 whitespace-nowrap">
                <Menu
                  v-if="can.manageBenefits"
                  as="div"
                  class="inline-block relative text-left"
                >
                  <MenuButton
                    class="flex items-center text-gray-400 hover:text-gray-600 rounded-full focus:outline-none focus:ring-2 focus:ring-blumilk-500 focus:ring-offset-2 focus:ring-offset-gray-100"
                    dusk="benefit-button"
                  >
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
                    <MenuItems
                      class="absolute right-0 z-10 mt-2 w-56 bg-white rounded-md focus:outline-none ring-1 ring-black ring-opacity-5 shadow-lg origin-top-right"
                    >
                      <div class="py-1">
                        <MenuItem
                          v-slot="{ active }"
                          class="flex"
                        >
                          <InertiaLink
                            as="button"
                            method="delete"
                            preserve-scroll
                            :href="`/benefits/${benefit.id}`"
                            :class="[active ? 'bg-gray-100 text-gray-900' : 'text-gray-700', 'block w-full text-left font-medium px-4 py-2 text-sm']"
                            dusk="benefit-delete-button"
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
            <tr v-if="!benefits.data.length">
              <td
                colspan="100%"
                class="py-4 text-xl leading-5 text-center text-gray-700"
              >
                <EmptyState>
                  <template #title>
                    Brak benefitów
                  </template>
                  <template #text>
                    Brak wpisów dotyczących benefitów
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
    :show="creating"
  >
    <Dialog
      is="div"
      class="overflow-y-auto fixed inset-0 z-10"
      @close="creating = false"
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
            @submit.prevent="submitCreateBenefit"
          >
            <div>
              <div>
                <DialogTitle
                  as="h3"
                  class="text-lg font-medium leading-6 text-center text-gray-900 font-sembiold"
                >
                  Dodaj benefit
                </DialogTitle>
                <div class="mt-5">
                  <label
                    for="name"
                    class="block text-sm font-medium text-gray-700 sm:mt-px"
                  >
                    Nazwa
                  </label>
                  <div class="mt-2">
                    <input
                      id="name"
                      v-model="form.name"
                      type="text"
                      class="block w-full max-w-lg rounded-md shadow-sm sm:text-sm"
                      :class="{ 'border-red-300 text-red-900 focus:outline-none focus:ring-red-500 focus:border-red-500': form.errors.name, 'focus:ring-blumilk-500 focus:border-blumilk-500 sm:text-sm border-gray-300': !form.errors.name }"
                    >
                    <p
                      v-if="form.errors.name"
                      class="mt-2 text-sm text-red-600"
                    >
                      {{ form.errors.name }}
                    </p>
                  </div>
                  <div class="flex items-center my-7 space-x-2">
                    <input
                      id="companion"
                      v-model="form.companion"
                      type="checkbox"
                      class="rounded h-4 w-4 border-gray-300 text-blumilk-600 focus:ring-blumilk-500"
                      :class="{ 'border-red-300 text-red-900 focus:outline-none focus:ring-red-500 focus:border-red-500': form.errors.companion, 'focus:ring-blumilk-500 focus:border-blumilk-500 sm:text-sm border-gray-300': !form.errors.companion }"
                    >
                    <label
                      for="companion"
                      class="block text-sm font-medium text-gray-700"
                    >
                      Dla osoby towarzyszącej
                    </label>
                  </div>
                  <p
                    v-if="form.errors.companion"
                    class="mt-2 text-sm text-red-600"
                  >
                    {{ form.errors.companion }}
                  </p>
                </div>
              </div>
            </div>
            <div class="mt-5 sm:mt-6">
              <div class="flex justify-end space-x-3">
                <button
                  type="button"
                  class="py-2 px-4 text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 rounded-md border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blumilk-500 focus:ring-offset-2 shadow-sm"
                  @click="creating = false"
                >
                  Anuluj
                </button>
                <button
                  type="submit"
                  class="inline-flex justify-center py-2 px-4 text-base font-medium text-white bg-blumilk-600 rounded-md border border-transparent focus:outline-none focus:ring-2 focus:ring-blumilk-500 focus:ring-offset-2 shadow-sm sm:text-sm"
                  :class="[form.processing || !form.isDirty ? 'disabled:opacity-60' : 'hover:bg-blumilk-700']"
                  :disabled="form.processing || !form.isDirty"
                  dusk="save-benefit-button"
                >
                  Dodaj
                </button>
              </div>
            </div>
          </form>
        </TransitionChild>
      </div>
    </Dialog>
  </TransitionRoot>
</template>
