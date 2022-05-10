<template>
  <InertiaHead title="Dodawanie CV" />
  <div class="mx-auto w-full max-w-7xl bg-white shadow-md">
    <div class="p-4 sm:px-6">
      <h2 class="text-lg font-medium leading-6 text-gray-900">
        Edytuj CV
      </h2>
    </div>
    <form
      class="flex flex-col justify-center py-8 px-6 space-y-8 border-t border-gray-200"
      @submit.prevent="submitResume"
    >
      <div class="space-y-8 sm:space-y-5">
        <div>
          <h3 class="text-lg font-medium leading-6 text-gray-900">
            Dane podstawowe
          </h3>
          <div class="grid grid-cols-2 gap-8 py-4">
            <Listbox
              v-model="form.user"
              as="div"
            >
              <ListboxLabel class="block text-sm font-medium text-gray-700">
                Użytkownik
              </ListboxLabel>
              <div class="relative mt-2">
                <ListboxButton
                  class="relative py-2 pr-10 pl-3 w-full max-w-md h-10 text-left bg-white rounded-md border border-gray-300 focus:border-blumilk-500 focus:outline-none focus:ring-1 focus:ring-blumilk-500 shadow-sm cursor-default sm:text-sm"
                >
                  <span v-if="form.user === null">
                    Nie istnieje w bazie
                  </span>
                  <span
                    v-else
                    class="flex items-center"
                  >
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
                      v-slot="{ active }"
                      as="template"
                      :value="null"
                    >
                      <li
                        :class="[active ? 'bg-gray-100' : 'text-gray-900', 'cursor-default select-none relative py-2 pl-3 pr-9']"
                      >
                        <div class="flex items-center">
                          Nie istnieje w bazie
                        </div>

                        <span
                          v-if="form.user === null"
                          :class="['text-blumilk-600 absolute inset-y-0 right-0 flex items-center pr-4']"
                        >
                          <CheckIcon class="w-5 h-5" />
                        </span>
                      </li>
                    </ListboxOption>
                    <ListboxOption
                      v-for="user in users.data"
                      :key="user.id"
                      v-slot="{ active }"
                      as="template"
                      :value="user"
                    >
                      <li
                        :class="[active ? 'bg-gray-100' : 'text-gray-900', 'cursor-default select-none relative py-2 pl-3 pr-9']"
                      >
                        <div class="flex items-center">
                          <img
                            :src="user.avatar"
                            class="shrink-0 w-6 h-6 rounded-full"
                          >
                          <span
                            :class="[form.user?.id === user.id ? 'font-semibold' : 'font-normal', 'ml-3 block truncate']"
                          >
                            {{ user.name }}
                          </span>
                        </div>
                        <span
                          v-if="form.user?.id === user.id"
                          :class="['text-blumilk-600 absolute inset-y-0 right-0 flex items-center pr-4']"
                        >
                          <CheckIcon class="w-5 h-5" />
                        </span>
                      </li>
                    </ListboxOption>
                  </ListboxOptions>
                </transition>
              </div>
            </Listbox>
            <div v-if="form.user === null">
              <label
                for="name"
                class="block text-sm font-medium text-gray-700 sm:mt-px"
              >
                Imię i nazwisko
              </label>
              <div class="mt-2">
                <input
                  id="name"
                  v-model="form.name"
                  type="text"
                  class="block w-full max-w-md rounded-md shadow-sm sm:text-sm"
                  :class="{ 'border-red-300 text-red-900 focus:outline-none focus:ring-red-500 focus:border-red-500': form.errors.name, 'focus:ring-blumilk-500 focus:border-blumilk-500 sm:text-sm border-gray-300': !form.errors.name }"
                >
                <p
                  v-if="form.errors.name"
                  class="mt-2 text-sm text-red-600"
                >
                  {{ form.errors.name }}
                </p>
              </div>
            </div>
          </div>
        </div>
        <DynamicSection
          v-model="form.educations"
          header="Edukacja"
          add-label="Dodaj szkołę"
          @add-item="addEducation"
          @remove-item="(index) => form.educations.splice(index, 1)"
        >
          <template #itemHeader="{ element, index }">
            <template v-if="hasAnyErrorInSection('education', index)">
              <ExclamationCircleIcon class="h-6 w-6 mr-2 text-red-600 inline-block" />
            </template>
            {{ element.school ? element.school : '(Nieokreślony)' }}
          </template>
          <template #form="{ element, index }">
            <div class="items-center py-4 sm:grid sm:grid-cols-2">
              <label class="block text-sm font-medium text-gray-700 sm:mt-px">
                Szkoła
              </label>
              <div class="mt-1 sm:mt-0">
                <input
                  v-model="element.school"
                  type="text"
                  class="block w-full rounded-md shadow-sm sm:text-sm"
                  :class="{ 'border-red-300 text-red-900 focus:outline-none focus:ring-red-500 focus:border-red-500': form.errors[`education.${index}.school`], 'focus:ring-blumilk-500 focus:border-blumilk-500 sm:text-sm border-gray-300': !form.errors[`education.${index}.school`] }"
                >
                <p
                  v-if="form.errors[`education.${index}.school`]"
                  class="mt-2 text-sm text-red-600"
                >
                  {{ form.errors[`education.${index}.school`] }}
                </p>
              </div>
            </div>
            <div class="items-center py-4 sm:grid sm:grid-cols-2">
              <label class="block text-sm font-medium text-gray-700 sm:mt-px">
                Stopień
              </label>
              <div class="mt-1 sm:mt-0">
                <input
                  v-model="element.degree"
                  type="text"
                  class="block w-full rounded-md shadow-sm sm:text-sm"
                  :class="{ 'border-red-300 text-red-900 focus:outline-none focus:ring-red-500 focus:border-red-500': form.errors[`education.${index}.degree`], 'focus:ring-blumilk-500 focus:border-blumilk-500 sm:text-sm border-gray-300': !form.errors[`education.${index}.degree`] }"
                >
                <p
                  v-if="form.errors[`education.${index}.degree`]"
                  class="mt-2 text-sm text-red-600"
                >
                  {{ form.errors[`education.${index}.degree`] }}
                </p>
              </div>
            </div>
            <div class="items-center py-4 sm:grid sm:grid-cols-2">
              <label class="block text-sm font-medium text-gray-700 sm:mt-px">
                Kierunek/Specjalizacja
              </label>
              <div class="mt-1 sm:mt-0">
                <input
                  v-model="element.fieldOfStudy"
                  type="text"
                  class="block w-full rounded-md shadow-sm sm:text-sm"
                  :class="{ 'border-red-300 text-red-900 focus:outline-none focus:ring-red-500 focus:border-red-500': form.errors[`education.${index}.fieldOfStudy`], 'focus:ring-blumilk-500 focus:border-blumilk-500 sm:text-sm border-gray-300': !form.errors[`education.${index}.fieldOfStudy`] }"
                >
                <p
                  v-if="form.errors[`education.${index}.fieldOfStudy`]"
                  class="mt-2 text-sm text-red-600"
                >
                  {{ form.errors[`education.${index}.fieldOfStudy`] }}
                </p>
              </div>
            </div>
            <div class="items-center py-4 sm:grid sm:grid-cols-2">
              <label class="block text-sm font-medium text-gray-700 sm:mt-px">
                Data rozpoczęcia
              </label>
              <div class="mt-1 sm:mt-0">
                <FlatPickr
                  v-model="element.startDate"
                  placeholder="Wybierz datę"
                  class="block w-full rounded-md shadow-sm sm:text-sm"
                  :class="{ 'border-red-300 text-red-900 focus:outline-none focus:ring-red-500 focus:border-red-500': form.errors[`education.${index}.startDate`], 'focus:ring-blumilk-500 focus:border-blumilk-500 sm:text-sm border-gray-300': !form.errors[`education.${index}.startDate`] }"
                />
                <p
                  v-if="form.errors[`education.${index}.startDate`]"
                  class="mt-2 text-sm text-red-600"
                >
                  {{ form.errors[`education.${index}.startDate`] }}
                </p>
              </div>
            </div>
            <div class="items-center py-4 sm:grid sm:grid-cols-2">
              <label class="block text-sm font-medium text-gray-700 sm:mt-px">
                Data zakończenia
              </label>
              <div class="mt-1 sm:mt-0">
                <FlatPickr
                  v-model="element.endDate"
                  placeholder="Wybierz datę"
                  class="block w-full rounded-md shadow-sm sm:text-sm"
                  :class="{ 'border-red-300 text-red-900 focus:outline-none focus:ring-red-500 focus:border-red-500': form.errors[`education.${index}.endDate`], 'focus:ring-blumilk-500 focus:border-blumilk-500 sm:text-sm border-gray-300': !form.errors[`education.${index}.endDate`] }"
                />
                <p
                  v-if="form.errors[`education.${index}.endDate`]"
                  class="mt-2 text-sm text-red-600"
                >
                  {{ form.errors[`education.${index}.endDate`] }}
                </p>
              </div>
            </div>
          </template>
        </DynamicSection>
        <DynamicSection
          v-model="form.languages"
          header="Języki"
          add-label="Dodaj język"
          @add-item="addLanguage"
          @remove-item="(index) => form.languages.splice(index, 1)"
        >
          <template #itemHeader="{ element, index }">
            <template v-if="hasAnyErrorInSection('languages', index)">
              <ExclamationCircleIcon class="h-6 w-6 mr-2 text-red-600 inline-block" />
            </template>
            <template v-if="element.name">
              {{ element.name }} - <span :class="element.level.textColor">{{ element.level.name }}</span>
            </template>
            <template v-else>
              (Nieokreślony)
            </template>
          </template>
          <template #form="{ element, index }">
            <div class="gap-4 md:grid md:grid-cols-2 ">
              <div class="py-4">
                <Combobox
                  v-model="element.name"
                  label="Język"
                  :items="languages"
                />
                <p
                  v-if="form.errors[`languages.${index}.name`]"
                  class="mt-2 text-sm text-red-600"
                >
                  {{ form.errors[`languages.${index}.name`] }}
                </p>
              </div>
              <div class="py-4">
                <label
                  :for="`language-level-${index}`"
                  class="block text-sm font-medium text-gray-700"
                >
                  Poziom - <span :class="element.level.textColor">{{ element.level.name }}</span>
                </label>
                <div class="mt-2">
                  <LevelPicker
                    v-model.number="element.level"
                    :levels="languageLevels"
                  />
                  <p
                    v-if="form.errors[`languages.${index}.level`]"
                    class="mt-2 text-sm text-red-600"
                  >
                    {{ form.errors[`languages.${index}.level`] }}
                  </p>
                </div>
              </div>
            </div>
          </template>
        </DynamicSection>
        <DynamicSection
          v-model="form.technologies"
          header="Technologie"
          add-label="Dodaj technologie"
          @add-item="addTechnology"
          @remove-item="(index) => form.technologies.splice(index, 1)"
        >
          <template #itemHeader="{ element, index }">
            <template v-if="hasAnyErrorInSection('technologies', index)">
              <ExclamationCircleIcon class="h-6 w-6 mr-2 text-red-600 inline-block" />
            </template>
            <template v-if="element.name">
              {{ element.name }} - <span :class="element.level.textColor">{{ element.level.name }}</span>
            </template>
            <template v-else>
              (Nieokreślony)
            </template>
          </template>
          <template #form="{ element, index }">
            <div class="gap-4 md:grid md:grid-cols-2 ">
              <div class="py-4">
                <Combobox
                  v-model="element.name"
                  label="Technologia"
                  :items="technologies"
                />
                <p
                  v-if="form.errors[`technologies.${index}.name`]"
                  class="mt-2 text-sm text-red-600"
                >
                  {{ form.errors[`technologies.${index}.name`] }}
                </p>
              </div>
              <div class="py-4">
                <label
                  :for="`technology-level-${index}`"
                  class="block text-sm font-medium text-gray-700"
                >
                  Poziom - <span :class="element.level.textColor">{{ element.level.name }}</span>
                </label>
                <div class="mt-2">
                  <LevelPicker
                    v-model.number="element.level"
                    :levels="technologyLevels"
                  />
                  <p
                    v-if="form.errors[`technologies.${index}.level`]"
                    class="mt-2 text-sm text-red-600"
                  >
                    {{ form.errors[`technologies.${index}.level`] }}
                  </p>
                </div>
              </div>
            </div>
          </template>
        </DynamicSection>
        <DynamicSection
          v-model="form.projects"
          header="Projekty"
          add-label="Dodaj projekt"
          @add-item="addProject"
          @remove-item="(index) => form.projects.splice(index, 1)"
        >
          <template #itemHeader="{ element, index }">
            <template v-if="hasAnyErrorInSection('projects', index)">
              <ExclamationCircleIcon class="h-6 w-6 mr-2 text-red-600 inline-block" />
            </template>
            {{ element.description ? element.description : '(Nieokreślony)' }}
          </template>
          <template #form="{ element, index }">
            <div class="items-center py-4 sm:grid sm:grid-cols-2">
              <label
                :for="`project-description-${index}`"
                class="block text-sm font-medium text-gray-700 sm:mt-px"
              >
                Opis projektu
              </label>
              <div class="mt-1 sm:mt-0">
                <textarea
                  :id="`project-description-${index}`"
                  v-model="element.description"
                  class="block w-full rounded-md shadow-sm sm:text-sm"
                  :class="{ 'border-red-300 text-red-900 focus:outline-none focus:ring-red-500 focus:border-red-500': form.errors[`projects.${index}.description`], 'focus:ring-blumilk-500 focus:border-blumilk-500 sm:text-sm border-gray-300': !form.errors[`projects.${index}.description`] }"
                />
                <p
                  v-if="form.errors[`projects.${index}.description`]"
                  class="mt-2 text-sm text-red-600"
                >
                  {{ form.errors[`projects.${index}.description`] }}
                </p>
              </div>
            </div>
            <div class="items-center py-4 sm:grid sm:grid-cols-2">
              <label
                :for="`project-technologies-${index}`"
                class="block text-sm font-medium text-gray-700 sm:mt-px"
              >
                Technologie
              </label>
              <div class="mt-1 sm:mt-0">
                <input
                  :id="`project-technologies-${index}`"
                  v-model="element.technologies"
                  type="text"
                  class="block w-full rounded-md shadow-sm sm:text-sm"
                  :class="{ 'border-red-300 text-red-900 focus:outline-none focus:ring-red-500 focus:border-red-500': form.errors[`projects.${index}.technologies`], 'focus:ring-blumilk-500 focus:border-blumilk-500 sm:text-sm border-gray-300': !form.errors[`projects.${index}.technologies`] }"
                >
                <p
                  v-if="form.errors[`projects.${index}.technologies`]"
                  class="mt-2 text-sm text-red-600"
                >
                  {{ form.errors[`projects.${index}.technologies`] }}
                </p>
              </div>
            </div>
            <div class="items-center py-4 sm:grid sm:grid-cols-2">
              <label
                :for="`project-startDate-${index}`"
                class="block text-sm font-medium text-gray-700 sm:mt-px"
              >
                Data rozpoczęcia
              </label>
              <div class="mt-1 sm:mt-0">
                <FlatPickr
                  :id="`project-startDate-${index}`"
                  v-model="element.startDate"
                  placeholder="Wybierz datę"
                  class="block w-full rounded-md shadow-sm sm:text-sm"
                  :class="{ 'border-red-300 text-red-900 focus:outline-none focus:ring-red-500 focus:border-red-500': form.errors[`educations.${index}.startDate`], 'focus:ring-blumilk-500 focus:border-blumilk-500 sm:text-sm border-gray-300': !form.errors[`educations.${index}.startDate`] }"
                />
                <p
                  v-if="form.errors[`educations.${index}.startDate`]"
                  class="mt-2 text-sm text-red-600"
                >
                  {{ form.errors[`educations.${index}.startDate`] }}
                </p>
              </div>
            </div>
            <div class="items-center py-4 sm:grid sm:grid-cols-2">
              <label
                :for="`project-endDate-${index}`"
                class="block text-sm font-medium text-gray-700 sm:mt-px"
              >
                Data zakończenia
              </label>
              <div class="mt-1 sm:mt-0">
                <FlatPickr
                  :id="`project-endDate-${index}`"
                  v-model="element.endDate"
                  placeholder="Wybierz datę"
                  class="block w-full rounded-md shadow-sm sm:text-sm"
                  :class="{ 'border-red-300 text-red-900 focus:outline-none focus:ring-red-500 focus:border-red-500': form.errors[`educations.${index}.endDate`], 'focus:ring-blumilk-500 focus:border-blumilk-500 sm:text-sm border-gray-300': !form.errors[`educations.${index}.endDate`] }"
                />
                <p
                  v-if="form.errors[`educations.${index}.endDate`]"
                  class="mt-2 text-sm text-red-600"
                >
                  {{ form.errors[`educations.${index}.endDate`] }}
                </p>
              </div>
            </div>
            <div class="items-center py-4 sm:grid sm:grid-cols-2">
              <label
                :for="`project-tasks-${index}`"
                class="block text-sm font-medium text-gray-700 sm:mt-px"
              >
                Zadania
              </label>
              <div class="mt-1 sm:mt-0">
                <textarea
                  :id="`project-tasks-${index}`"
                  v-model="element.tasks"
                  rows="3"
                  class="block w-full rounded-md shadow-sm sm:text-sm"
                  :class="{ 'border-red-300 text-red-900 focus:outline-none focus:ring-red-500 focus:border-red-500': form.errors[`projects.${index}.tasks`], 'focus:ring-blumilk-500 focus:border-blumilk-500 sm:text-sm border-gray-300': !form.errors[`projects.${index}.tasks`] }"
                />
                <p
                  v-if="form.errors[`projects.${index}.tasks`]"
                  class="mt-2 text-sm text-red-600"
                >
                  {{ form.errors[`projects.${index}.tasks`] }}
                </p>
              </div>
            </div>
          </template>
        </DynamicSection>
        <div class="pt-5">
          <div class="flex justify-end">
            <InertiaLink
              href="/resumes"
              class="py-2 px-4 text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 rounded-md border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blumilk-500 focus:ring-offset-2 shadow-sm"
            >
              Anuluj
            </InertiaLink>
            <button
              type="submit"
              class="inline-flex justify-center py-2 px-4 ml-3 text-sm font-medium text-white bg-blumilk-600 hover:bg-blumilk-700 rounded-md border border-transparent focus:outline-none focus:ring-2 focus:ring-blumilk-500 focus:ring-offset-2 shadow-sm"
            >
              Zapisz
            </button>
          </div>
        </div>
      </div>
    </form>
  </div>
</template>

<script setup>
import { Listbox, ListboxOption, ListboxOptions, ListboxLabel, ListboxButton } from '@headlessui/vue'
import { SelectorIcon, CheckIcon } from '@heroicons/vue/outline'
import { ExclamationCircleIcon } from '@heroicons/vue/solid'
import { useForm } from '@inertiajs/inertia-vue3'
import FlatPickr from 'vue-flatpickr-component'
import DynamicSection from '@/Shared/Forms/DynamicSection'
import Combobox from '@/Shared/Forms/Combobox'
import LevelPicker from '@/Shared/Forms/LevelPicker'
import useLevels from '@/Composables/useLevels'

const props = defineProps({
  users: Object,
  technologies: Array,
  resume: Object,
})

const { technologyLevels, languageLevels } = useLevels()

const languages = [
  'Polish',
  'English',
  'German',
]

const form = useForm({
  user: props.users.data.find((user) => user.id === props.resume.user) ?? null,
  name: props.resume.name ?? null ,
  educations: props.resume.education ?? [],
  projects: props.resume.projects ?? [],
  technologies: props.resume.technologies.map((technology) => ({
    name: props.technologies.find((tech) => tech === technology.name),
    level: technologyLevels.find((level) => level.level === technology.level),
  })) ?? [],
  languages: props.resume.languages.map((language) => ({
    name: languages.find((lang) => lang === language.name),
    level: languageLevels.find((level) => level.level === language.level),
  })) ?? [],
})

function addProject() {
  form.projects.push({
    description: null,
    technologies: null,
    tasks: null,
    startDate: null,
    endDate: null,
  })
}

function addTechnology() {
  form.technologies.push({
    name: null,
    level: technologyLevels[0],
  })
}

function addEducation() {
  form.educations.push({
    school: null,
    degree: null,
    fieldOfStudy: null,
    startDate: null,
    endDate: null,
  })
}

function addLanguage() {
  form.languages.push({
    name: null,
    level: languageLevels[0],
  })
}

function hasAnyErrorInSection(section, index) {
  return Object
    .keys(form.errors)
    .some((error) => error.startsWith(`${section}.${index}.`))
}

function submitResume() {
  form
    .transform((data) => ({
      user: data.user.id,
      name: data.name,
      education: data.educations,
      languages: data.languages.map(language => ({
        name: language.name,
        level: language.level.level,
      })),
      technologies: data.technologies.map(technology => ({
        name: technology.name,
        level: technology.level.level,
      })),
      projects: data.projects,
    }))
    .put(`/resumes/${props.resume.id}`)
}
</script>