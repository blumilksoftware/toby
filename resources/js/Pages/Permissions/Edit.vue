<script setup>
import { Switch } from '@headlessui/vue'
import { usePermissionInfo } from '@/Composables/permissionInfo.js'
import { useForm } from '@inertiajs/vue3'
import InertiaLink from '@/Shared/InertiaLink.vue'
import AppLayout from '@/Shared/Layout/AppLayout.vue'

const props = defineProps({
  user: Object,
  permissions: Object,
})

const form = useForm({
  permissions: props.permissions,
})

const { findGroupedPermissions } = usePermissionInfo()
const groupedPermissions = findGroupedPermissions(props.permissions)

function editPermissions() {
  form.patch(`/users/${props.user.id}/permissions`)
}
</script>

<template>
  <AppLayout>
    <template #title>Edycja uprawnień</template>
    <div class="bg-white shadow-md">
      <div class="p-6">
        <h2 class="text-lg font-medium leading-6 text-gray-900">
          Edytuj uprawnienia
        </h2>
      </div>
      <div
        class="bg-gray-50 w-full py-3 px-4 text-xs font-semibold tracking-wider text-left text-gray-500 uppercase"
      >
        Użytkownik
      </div>
      <div class="items-center p-4 flex">
        <div class="flex">
          <img
            class="size-14 mt-1 rounded-full"
            :src="user.avatar"
          >
          <div class="ml-3">
            <p class="text-md font-medium text-gray-900">
              {{ user.name }}
            </p>
            <p class="text-sm text-gray-500">
              {{ user.role }}
            </p>
            <p class="text-sm text-gray-500">
              {{ user.email }}
            </p>
          </div>
        </div>
      </div>
      <div class="bg-gray-50 w-full py-3 px-4 text-xs font-semibold tracking-wider text-left text-gray-500 uppercase">
        Uprawnienia
      </div>
      <form
        class="w-full py-2 px-4"
        @submit.prevent="editPermissions"
      >
        <template
          v-for="[section, sectionPermissions] in groupedPermissions"
          :key="section"
        >
          <div
            v-if="sectionPermissions.length > 0"
            class="w-full my-4 border border-gray-200 items-start"
          >
            <div
              class="group w-full max-w-full overflow-hidden flex items-center justify-between p-4 font-semibold text-gray-500"
            >
              <div class="break-all line-clamp-1 text-md">
                {{ section }}
              </div>
            </div>
            <div class="border-t border-gray-200">
              <table class="min-w-full divide-y divide-gray-200 table-fixed">
                <thead class="bg-gray-50">
                  <tr>
                    <th
                      scope="col"
                      class="py-3 px-4 text-xs font-semibold tracking-wider text-left text-gray-500 uppercase whitespace-nowrap"
                    >
                      Uprawnienie
                    </th>
                    <th
                      scope="col"
                      class="py-3 px-4 text-xs font-semibold tracking-wider text-left text-gray-500 uppercase whitespace-nowrap"
                    >
                      Czy przyznano?
                    </th>
                  </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                  <tr
                    v-for="permission in sectionPermissions"
                    :key="permission.value"
                  >
                    <td class="p-4 text-sm text-gray-500 w-1/2">
                      {{ permission.name }}
                    </td>
                    <td class="p-4 text-sm text-gray-500">
                      <Switch
                        v-model="form.permissions[permission.value]"
                        :class="[form.permissions[permission.value] ? 'bg-blumilk-500' : 'bg-gray-200', 'relative inline-flex shrink-0 h-6 w-11 border-2 border-transparent rounded-full cursor-pointer transition-colors ease-in-out duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blumilk-500']"
                      >
                        <span
                          :class="[form.permissions[permission.value] ? 'translate-x-5' : 'translate-x-0', 'pointer-events-none inline-block size-5 rounded-full bg-white shadow ring-0 transition ease-in-out duration-200']"
                        />
                      </Switch>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </template>
        <div class="flex justify-end py-3">
          <div class="space-x-3">
            <InertiaLink
              href="/users"
              class="py-2 px-4 text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 rounded-md border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blumilk-500 focus:ring-offset-2 shadow-sm"
            >
              Anuluj
            </InertiaLink>
            <button
              type="submit"
              class="inline-flex justify-center py-2 px-4 text-sm font-medium text-white bg-blumilk-600 rounded-md border border-transparent focus:outline-none focus:ring-2 focus:ring-blumilk-500 focus:ring-offset-2 shadow-sm"
              :class="[form.processing || !form.isDirty ? 'disabled:opacity-60' : 'hover:bg-blumilk-700']"
              :disabled="form.processing || !form.isDirty"
            >
              Zapisz
            </button>
          </div>
        </div>
      </form>
    </div>
  </AppLayout>
</template>
