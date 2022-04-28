<template>
  <InertiaHead title="Dodawanie CV" />
  <div class="mx-auto w-full max-w-7xl bg-white shadow-md">
    <div class="p-4 sm:px-6">
      <h2 class="text-lg font-medium leading-6 text-gray-900">
        Dodaj CV
      </h2>
    </div>
    <form class="flex flex-col justify-center py-8 px-6 space-y-8 border-t border-gray-200 divide-y divide-gray-200">
      <div class="space-y-8 sm:space-y-5">
        <div>
          <h3 class="text-lg font-medium leading-6 text-gray-900">
            Dane podstawowe
          </h3>
          <div class="px-8">
            <div class="items-center py-4 sm:grid sm:grid-cols-2">
              <label
                for="firstName"
                class="block text-sm font-medium text-gray-700 sm:mt-px"
              >
                Imię
              </label>
              <div class="mt-1 sm:mt-0">
                <input
                  id="firstName"
                  v-model="form.firstName"
                  type="text"
                  class="block w-full rounded-md shadow-sm sm:text-sm"
                  :class="{ 'border-red-300 text-red-900 focus:outline-none focus:ring-red-500 focus:border-red-500': form.errors.firstName, 'focus:ring-blumilk-500 focus:border-blumilk-500 sm:text-sm border-gray-300': !form.errors.firstName }"
                >
                <p
                  v-if="form.errors.firstName"
                  class="mt-2 text-sm text-red-600"
                >
                  {{ form.errors.firstName }}
                </p>
              </div>
            </div>
            <div class="items-center py-4 sm:grid sm:grid-cols-2">
              <label
                for="lastName"
                class="block text-sm font-medium text-gray-700 sm:mt-px"
              >
                Nazwisko
              </label>
              <div class="mt-1 sm:mt-0">
                <input
                  id="lastName"
                  v-model="form.lastName"
                  type="text"
                  class="block w-full rounded-md shadow-sm sm:text-sm"
                  :class="{ 'border-red-300 text-red-900 focus:outline-none focus:ring-red-500 focus:border-red-500': form.errors.lastName, 'focus:ring-blumilk-500 focus:border-blumilk-500 sm:text-sm border-gray-300': !form.errors.lastName }"
                >
                <p
                  v-if="form.errors.lastName"
                  class="mt-2 text-sm text-red-600"
                >
                  {{ form.errors.lastName }}
                </p>
              </div>
            </div>
          </div>
        </div>
        <div>
          <h3 class="text-lg font-medium leading-6 text-gray-900">
            Edukacja
          </h3>
          <Draggable
            v-model="form.educations"
            class="pt-4 space-y-4"
            tag="transition-group"
            ghost-class="opacity-50"
            handle=".handle"
            :animation="200"
            :component-data="{tag: 'div', type: 'transition-group'}"
            item-key="index"
          >
            <template #item="{ element, index }">
              <div class="group flex items-start space-x-3">
                <button
                  class="py-4 text-red-500 hover:text-gray-600 opacity-100 group-hover:opacity-100 transition-opacity hover:scale-110 lg:opacity-0 handle"
                  type="button"
                >
                  <ViewGridIcon class="w-5 h-5 text-gray-500" />
                </button>
                <Disclosure
                  v-slot="{ open }"
                  default-open
                  as="div"
                  class="flex-1 border border-gray-200"
                >
                  <div class="flex">
                    <DisclosureButton
                      :class="['transition transition-colors rounded-md group w-full max-w-full overflow-hidden flex items-center justify-between p-4 font-semibold text-gray-500 hover:text-blumilk-500 transition transition-colors rounded-md focus:outline-none']"
                    >
                      <div class="break-all line-clamp-1 text-md">
                        {{ element.school ? element.school : '(Nieokreślony)' }}
                      </div>
                      <div class="ml-2">
                        <svg
                          :class="[open ? '-rotate-90' : 'rotate-90', 'h-6 w-6 transform transition-transform ease-in-out duration-150']"
                          viewBox="0 0 20 20"
                        >
                          <path
                            d="M6 6L14 10L6 14V6Z"
                            fill="currentColor"
                          />
                        </svg>
                      </div>
                    </DisclosureButton>
                  </div>
                  <DisclosurePanel
                    as="div"
                    class="py-2 px-4 border-t border-gray-200"
                  >
                    <div>
                      <div class="items-center py-4 sm:grid sm:grid-cols-2">
                        <label
                          :for="`education-school-${index}`"
                          class="block text-sm font-medium text-gray-700 sm:mt-px"
                        >
                          Szkoła
                        </label>
                        <div class="mt-1 sm:mt-0">
                          <input
                            :id="`education-school-${index}`"
                            v-model="element.school"
                            type="text"
                            class="block w-full rounded-md shadow-sm sm:text-sm"
                            :class="{ 'border-red-300 text-red-900 focus:outline-none focus:ring-red-500 focus:border-red-500': form.errors[`educations.${index}.school`], 'focus:ring-blumilk-500 focus:border-blumilk-500 sm:text-sm border-gray-300': !form.errors[`educations.${index}.school`] }"
                          >
                          <p
                            v-if="form.errors[`educations.${index}.school`]"
                            class="mt-2 text-sm text-red-600"
                          >
                            {{ form.errors[`educations.${index}.school`] }}
                          </p>
                        </div>
                      </div>
                      <div class="items-center py-4 sm:grid sm:grid-cols-2">
                        <label
                          :for="`education-degree-${index}`"
                          class="block text-sm font-medium text-gray-700 sm:mt-px"
                        >
                          Stopień
                        </label>
                        <div class="mt-1 sm:mt-0">
                          <input
                            :id="`education-degree-${index}`"
                            v-model="element.degree"
                            type="text"
                            class="block w-full rounded-md shadow-sm sm:text-sm"
                            :class="{ 'border-red-300 text-red-900 focus:outline-none focus:ring-red-500 focus:border-red-500': form.errors[`educations.${index}.degree`], 'focus:ring-blumilk-500 focus:border-blumilk-500 sm:text-sm border-gray-300': !form.errors[`educations.${index}.degree`] }"
                          >
                          <p
                            v-if="form.errors[`educations.${index}.degree`]"
                            class="mt-2 text-sm text-red-600"
                          >
                            {{ form.errors[`educations.${index}.degree`] }}
                          </p>
                        </div>
                      </div>
                      <div class="items-center py-4 sm:grid sm:grid-cols-2">
                        <label
                          :for="`education-fieldOfStudy-${index}`"
                          class="block text-sm font-medium text-gray-700 sm:mt-px"
                        >
                          Kierunek/Specjalizacja
                        </label>
                        <div class="mt-1 sm:mt-0">
                          <input
                            :id="`education-fieldOfStudy-${index}`"
                            v-model="element.fieldOfStudy"
                            type="text"
                            class="block w-full rounded-md shadow-sm sm:text-sm"
                            :class="{ 'border-red-300 text-red-900 focus:outline-none focus:ring-red-500 focus:border-red-500': form.errors[`educations.${index}.fieldOfStudy`], 'focus:ring-blumilk-500 focus:border-blumilk-500 sm:text-sm border-gray-300': !form.errors[`educations.${index}.fieldOfStudy`] }"
                          >
                          <p
                            v-if="form.errors[`educations.${index}.fieldOfStudy`]"
                            class="mt-2 text-sm text-red-600"
                          >
                            {{ form.errors[`educations.${index}.fieldOfStudy`] }}
                          </p>
                        </div>
                      </div>
                      <div class="items-center py-4 sm:grid sm:grid-cols-2">
                        <label
                          :for="`education-startDate-${index}`"
                          class="block text-sm font-medium text-gray-700 sm:mt-px"
                        >
                          Data rozpoczęcia
                        </label>
                        <div class="mt-1 sm:mt-0">
                          <FlatPickr
                            :id="`education-startDate-${index}`"
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
                          :for="`education-endDate-${index}`"
                          class="block text-sm font-medium text-gray-700 sm:mt-px"
                        >
                          Data zakończenia
                        </label>
                        <div class="mt-1 sm:mt-0">
                          <FlatPickr
                            :id="`education-endDate-${index}`"
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
                    </div>
                  </DisclosurePanel>
                </Disclosure>
                <button
                  class="py-4 text-red-500 hover:text-red-600 opacity-100 group-hover:opacity-100 transition-opacity hover:scale-110 lg:opacity-0"
                  type="button"
                  @click="form.educations.splice(index, 1)"
                >
                  <TrashIcon class="w-5 h-5 text-red-500" />
                </button>
              </div>
            </template>
          </Draggable>
          <div class="px-8">
            <button
              type="button"
              class="p-4 mx-auto mt-4 w-full font-semibold text-center text-blumilk-600 hover:bg-blumilk-25 focus:outline-none transition-colors"
              @click="form.educations.push({
                index: form.educations.length,
                school: null,
                degree: null,
                fieldOfStudy: null,
                startDate: null,
                endDate: null,
              })"
            >
              Dodaj element
            </button>
          </div>
        </div>
        <div>
          <h3 class="text-lg font-medium leading-6 text-gray-900">
            Technologie
          </h3>
          <Draggable
            v-model="form.technologies"
            class="pt-4 space-y-4"
            tag="transition-group"
            ghost-class="opacity-50"
            handle=".handle"
            :animation="200"
            :component-data="{tag: 'div', type: 'transition-group' }"
            item-key="index"
          >
            <template #item="{ element, index }">
              <div class="group flex items-start space-x-3">
                <button
                  class="py-4 text-red-500 hover:text-gray-600 opacity-100 group-hover:opacity-100 transition-opacity hover:scale-110 lg:opacity-0 handle"
                  type="button"
                >
                  <ViewGridIcon class="w-5 h-5 text-gray-500" />
                </button>
                <Disclosure
                  v-slot="{ open }"
                  default-open
                  as="div"
                  class="flex-1 border border-gray-200"
                >
                  <div class="flex">
                    <DisclosureButton
                      :class="['transition transition-colors rounded-md group w-full max-w-full overflow-hidden flex items-center justify-between p-4 font-semibold text-gray-500 hover:text-blumilk-500 transition transition-colors rounded-md focus:outline-none']"
                    >
                      <div class="break-all line-clamp-1 text-md">
                        {{ element.name ? element.name : '(Nieokreślony)' }}
                      </div>
                      <div class="ml-2">
                        <svg
                          :class="[open ? '-rotate-90' : 'rotate-90', 'h-6 w-6 transform transition-transform ease-in-out duration-150']"
                          viewBox="0 0 20 20"
                        >
                          <path
                            d="M6 6L14 10L6 14V6Z"
                            fill="currentColor"
                          />
                        </svg>
                      </div>
                    </DisclosureButton>
                  </div>
                  <DisclosurePanel
                    as="div"
                    class="py-2 px-4 border-t border-gray-200"
                  >
                    <div class="gap-4 md:grid md:grid-cols-2 ">
                      <div class="py-4">
                        <label
                          :for="`technology-name-${index}`"
                          class="block text-sm font-medium text-gray-700"
                        >
                          Technologia
                        </label>
                        <div class="mt-3">
                          <input
                            :id="`technology-name-${index}`"
                            v-model="element.name"
                            type="text"
                            class="block w-full h-10 rounded-md shadow-sm sm:text-sm"
                            :class="{ 'border-red-300 text-red-900 focus:outline-none focus:ring-red-500 focus:border-red-500': form.errors[`technologies.${index}.name`], 'focus:ring-blumilk-500 focus:border-blumilk-500 sm:text-sm border-gray-300': !form.errors[`technologies.${index}.name`] }"
                          >
                          <p
                            v-if="form.errors[`technologies.${index}.name`]"
                            class="mt-2 text-sm text-red-600"
                          >
                            {{ form.errors[`technologies.${index}.name`] }}
                          </p>
                        </div>
                      </div>
                      <div class="py-4">
                        <label
                          :for="`technology-level-${index}`"
                          class="block text-sm font-medium text-gray-700"
                        >
                          Poziom - <span :class="element.level.textColor">{{ element.level.name }}</span>
                        </label>
                        <div class="mt-2">
                          <RadioGroup v-model="element.level">
                            <div
                              :class="`relative overflow-hidden flex h-12 rounded-l-md rounded-r-md space-x-px ${element.level.activeColor} transition-colors duration-200 easy-in-out`"
                            >
                              <RadioGroupOption
                                v-for="level in levels"
                                :key="level.index"
                                v-slot="{ active, checked }"
                                as="template"
                                :value="level"
                              >
                                <div
                                  :class="`${element.level.backgroundColor} hover:opacity-80 cursor-pointer transition-colors duration-200 easy-in-out focus:outline-none flex-1`"
                                />
                              </RadioGroupOption>
                              <div
                                :class="`absolute transform transition-transform  duration-200 easy-in-out`"
                                :style="`width: ${100/levels.length}%; transform: translateX(calc(${100 * element.level.index}% - 1px))`"
                              >
                                <div :class="`h-12 ${element.level.activeColor} transition-colors duration-300 easy-in-out`" />
                              </div>
                            </div>
                          </RadioGroup>
                          <p
                            v-if="form.errors[`technologies.${index}.level`]"
                            class="mt-2 text-sm text-red-600"
                          >
                            {{ form.errors[`technologies.${index}.level`] }}
                          </p>
                        </div>
                      </div>
                    </div>
                  </DisclosurePanel>
                </Disclosure>
                <button
                  class="py-4 text-red-500 hover:text-red-600 opacity-100 group-hover:opacity-100 transition-opacity hover:scale-110 lg:opacity-0"
                  type="button"
                  @click="form.technologies.splice(index, 1)"
                >
                  <TrashIcon class="w-5 h-5 text-red-500" />
                </button>
              </div>
            </template>
          </Draggable>
          <div class="px-8">
            <button
              type="button"
              class="p-4 mx-auto mt-4 w-full font-semibold text-center text-blumilk-600 hover:bg-blumilk-25 focus:outline-none transition-colors"
              @click="form.technologies.push({
                index: form.technologies.length,
                name: null,
                level: levels[0]
              })"
            >
              Dodaj element
            </button>
          </div>
        </div>
        <div>
          <h3 class="text-lg font-medium leading-6 text-gray-900">
            Projekty
          </h3>
          <Draggable
            v-model="form.projects"
            class="pt-4 space-y-4"
            tag="transition-group"
            ghost-class="opacity-50"
            handle=".handle"
            :animation="200"
            :component-data="{tag: 'div', type: 'transition-group' }"
            item-key="index"
          >
            <template #item="{ element, index }">
              <div class="group flex items-start space-x-3">
                <button
                  class="py-4 text-red-500 hover:text-gray-600 opacity-100 group-hover:opacity-100 transition-opacity hover:scale-110 lg:opacity-0 handle"
                  type="button"
                >
                  <ViewGridIcon class="w-5 h-5 text-gray-500" />
                </button>
                <Disclosure
                  v-slot="{ open }"
                  default-open
                  as="div"
                  class="flex-1 border border-gray-200"
                >
                  <div class="flex">
                    <DisclosureButton
                      :class="['transition transition-colors rounded-md group w-full max-w-full overflow-hidden flex items-center justify-between p-4 font-semibold text-gray-500 hover:text-blumilk-500 transition transition-colors rounded-md focus:outline-none']"
                    >
                      <div class="break-all line-clamp-1 text-md">
                        {{ element.description ? element.description : '(Nieokreślony)' }}
                      </div>
                      <div class="ml-2">
                        <svg
                          :class="[open ? '-rotate-90' : 'rotate-90', 'h-6 w-6 transform transition-transform ease-in-out duration-150']"
                          viewBox="0 0 20 20"
                        >
                          <path
                            d="M6 6L14 10L6 14V6Z"
                            fill="currentColor"
                          />
                        </svg>
                      </div>
                    </DisclosureButton>
                  </div>
                  <DisclosurePanel
                    as="div"
                    class="py-2 px-4 border-t border-gray-200"
                  >
                    <div>
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
                          :for="`project-tags-${index}`"
                          class="block text-sm font-medium text-gray-700 sm:mt-px"
                        >
                          Dodatkowe informacje
                        </label>
                        <div class="mt-1 sm:mt-0">
                          <input
                            :id="`project-tags-${index}`"
                            v-model="element.tags"
                            type="text"
                            class="block w-full rounded-md shadow-sm sm:text-sm"
                            :class="{ 'border-red-300 text-red-900 focus:outline-none focus:ring-red-500 focus:border-red-500': form.errors[`projects.${index}.tags`], 'focus:ring-blumilk-500 focus:border-blumilk-500 sm:text-sm border-gray-300': !form.errors[`projects.${index}.tags`] }"
                          >
                          <p
                            v-if="form.errors[`projects.${index}.tags`]"
                            class="mt-2 text-sm text-red-600"
                          >
                            {{ form.errors[`projects.${index}.tags`] }}
                          </p>
                        </div>
                      </div>
                    </div>
                  </DisclosurePanel>
                </Disclosure>
                <button
                  class="py-4 text-red-500 hover:text-red-600 opacity-100 group-hover:opacity-100 transition-opacity hover:scale-110 lg:opacity-0"
                  type="button"
                  @click="form.projects.splice(index, 1)"
                >
                  <TrashIcon class="w-5 h-5 text-red-500" />
                </button>
              </div>
            </template>
          </Draggable>
          <div class="px-8">
            <button
              type="button"
              class="p-4 mx-auto mt-4 w-full font-semibold text-center text-blumilk-600 hover:bg-blumilk-25 focus:outline-none transition-colors"
              @click="form.projects.push({
                index: form.projects.length,
                school: null,
                degree: null,
                fieldOfStudy: null,
                startDate: null,
                endDate: null,
              })"
            >
              Dodaj element
            </button>
          </div>
        </div>
      </div>

      <div class="pt-5">
        <div class="flex justify-end">
          <button
            type="button"
            class="py-2 px-4 text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 rounded-md border border-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 shadow-sm"
          >
            Cancel
          </button>
          <button
            type="submit"
            class="inline-flex justify-center py-2 px-4 ml-3 text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded-md border border-transparent focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 shadow-sm"
          >
            Save
          </button>
        </div>
      </div>
    </form>
  </div>
</template>

<script setup>
import { Disclosure, DisclosureButton, DisclosurePanel } from '@headlessui/vue'
import { RadioGroup, RadioGroupOption } from '@headlessui/vue'
import { TrashIcon, ViewGridIcon } from '@heroicons/vue/outline'
import { useForm } from '@inertiajs/inertia-vue3'
import FlatPickr from 'vue-flatpickr-component'
import Draggable from 'vuedraggable'

const levels = [
  {
    index: 0,
    name: 'Poczatkujący',
    activeColor: 'bg-rose-400',
    backgroundColor: 'bg-rose-100',
    textColor: 'text-rose-400',
  },
  {
    index: 1,
    name: 'Zaawansowany',
    activeColor: 'bg-orange-400',
    backgroundColor: 'bg-orange-100',
    textColor: 'text-orange-400',
  },
  {
    index: 2,
    name: 'Doświadczony',
    activeColor: 'bg-amber-400',
    backgroundColor: 'bg-amber-100',
    textColor: 'text-yellow-500',
  },
  {
    index: 3,
    name: 'Ekspert',
    activeColor: 'bg-emerald-400',
    backgroundColor: 'bg-emerald-100',
    textColor: 'text-emerald-400',
  },
  {
    index: 4,
    name: 'Chad',
    activeColor: 'bg-blumilk-400',
    backgroundColor: 'bg-blumilk-100',
    textColor: 'text-blumilk-400',
  },
]

const form = useForm({
  educations: [],
  projects: [],
  technologies: [{
    index: 0,
    name: 'Laravel',
    level: levels[0],
  }],
})

</script>