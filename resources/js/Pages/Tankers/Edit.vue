<template>
  <div>
    <h1 class="mb-8 font-bold text-3xl">
      <inertia-link class="text-blue-600 hover:text-blue-800" :href="route('tankers.index')">Tankers</inertia-link>
      <span class="text-blue-600 font-medium">/</span> {{ form.plate_no }}
    </h1>

    <div class="p-1">
      <ul class="flex border-b">
        <li @click="openTab = 1" :class="{ '-mb-px': openTab === 1 }" class="-mb-px mr-1">
          <a :class="openTab === 1 ? activeClasses : inactiveClasses" class="bg-white inline-block py-2 px-4 font-semibold" href="#">Information</a>
        </li>
        <li @click="openTab = 2" :class="{ '-mb-px': openTab === 2 }" class="mr-1">
          <a :class="openTab === 2 ? activeClasses : inactiveClasses" class="bg-white inline-block py-2 px-4 font-semibold" href="#">Personnel</a>
        </li>
        <li @click="openTab = 3" :class="{ '-mb-px': openTab === 3 }" class="mr-1">
          <a :class="openTab === 3 ? activeClasses : inactiveClasses" class="bg-white inline-block py-2 px-4 font-semibold" href="#">Loads</a>
        </li>
        <li @click="openTab = 4" :class="{ '-mb-px': openTab === 4 }" class="mr-1">
          <a :class="openTab === 4 ? activeClasses : inactiveClasses" class="bg-white inline-block py-2 px-4 font-semibold" href="#">Deliveries</a>
        </li>
      </ul>

      <!-- Tab 1 -->
      <div class="w-full pt-4">
        <div v-show="openTab === 1">
          <div class="bg-white rounded shadow overflow-hidden max-w-3xl -mt-4">
            <form @submit.prevent="submit">
              <div class="p-8 -mr-6 -mb-8 flex flex-wrap">
                <text-input v-model="form.plate_no" :error="errors.plate_no" class="pr-6 pb-8 w-full lg:w-1/2" label="Plate No" />
                <text-input v-model="form.compartment" :error="errors.compartment" class="pr-6 pb-8 w-full lg:w-1/2" label="Compartment" />
              </div>
              <div class="px-8 py-4 bg-gray-100 border-t border-gray-200 flex justify-end items-center">
                <button v-if="!tanker.deleted_at" class="text-red-600 hover:underline" tabindex="-1" type="button" @click="destroy">Delete Tanker</button>
                <loading-button :loading="sending" class="btn-indigo ml-auto" type="submit">Update Tanker</loading-button>
              </div>
            </form>
          </div>
        </div>

        <!-- Tab 2 -->
        <div v-show="openTab === 2">
    <!-- <div v-for="driver in drivers" :key="driver.id" :value="driver.id">{{ driver.fname }}</div> -->
          <h1 class="mt-8 mb-8 ml-4 font-bold text-2xl">Driver(s)</h1>

          <div class="bg-white rounded shadow overflow-x-auto">
            <table class="w-full whitespace-no-wrap">
              <tr class="text-left font-bold">
                <th class="px-6 pt-6 pb-4">ID</th>
                <th class="px-6 pt-6 pb-4">Name</th>
                <th class="px-6 pt-6 pb-4">License No.</th>
              </tr>
              <tr v-for="driver in drivers" :key="driver.id" class="hover:bg-gray-100 focus-within:bg-gray-100">
                <!-- table columns -->
                <td class="border-t">
                  <inertia-link class="px-6 py-4 flex items-center" :href="route('drivers.edit', driver.id)" tabindex="-1">
                    {{ driver.id }}
                  </inertia-link>
                </td>
                <td class="border-t">
                  <inertia-link class="px-6 py-4 flex items-center" :href="route('drivers.edit', driver.id)" tabindex="-1">
                    {{ driver.name }}
                  </inertia-link>
                </td>
                <td class="border-t">
                  <inertia-link class="px-6 py-4 flex items-center" :href="route('drivers.edit', driver.id)" tabindex="-1">
                    {{ driver.license_no }}
                  </inertia-link>
                </td>
              </tr>
              <tr v-if="drivers.length === 0">
                <td class="border-t px-6 py-4" colspan="4">No drivers found.</td>
              </tr>
            </table>
          </div>

          <h1 class="mt-8 mb-8 ml-4 font-bold text-2xl">Helper(s)</h1>

          <div class="bg-white rounded shadow overflow-x-auto">
            <table class="w-full whitespace-no-wrap">
              <tr class="text-left font-bold">
                <th class="px-6 pt-6 pb-4">ID</th>
                <th class="px-6 pt-6 pb-4">Name</th>
              </tr>
              <tr v-for="helper in helpers" :key="helper.id" class="hover:bg-gray-100 focus-within:bg-gray-100">
                <!-- table columns -->
                <td class="border-t">
                  <inertia-link class="px-6 py-4 flex items-center" :href="route('helpers.edit', helper.id)" tabindex="-1">
                    {{ helper.id }}
                  </inertia-link>
                </td>
                <td class="border-t">
                  <inertia-link class="px-6 py-4 flex items-center" :href="route('helpers.edit', helper.id)" tabindex="-1">
                    {{ helper.name }}
                  </inertia-link>
                </td>
              </tr>
              <tr v-if="helpers.length === 0">
                <td class="border-t px-6 py-4" colspan="4">No helpers found.</td>
              </tr>
            </table>
          </div>
        </div>

        <!-- Tab 3 -->
        <div v-show="openTab === 3">
          Loads
        </div>

        <!-- Tab 4 -->
        <div v-show="openTab === 4">
          Deliveries
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import Icon from '@/Shared/Icon'
import Layout from '@/Shared/Layout'
import LoadingButton from '@/Shared/LoadingButton'
import SelectInput from '@/Shared/SelectInput'
import TextInput from '@/Shared/TextInput'
import TrashedMessage from '@/Shared/TrashedMessage'

export default {
  // metaInfo() {
  //   return { title: this.form.name }
  // },
  layout: Layout,
  components: {
    Icon,
    LoadingButton,
    SelectInput,
    TextInput,
    TrashedMessage,
  },
  props: {
    errors: Object,
    tanker: Object,
    drivers: Array,
    helpers: Array,
  },
  remember: 'form',
  data() {
    return {
      sending: false,
      form: {
        plate_no: this.tanker.plate_no,
        compartment: this.tanker.compartment,
      },
      // Tabs
      openTab: 1,
      activeClasses: 'border-l border-t border-r rounded-t text-blue-600',
      inactiveClasses: 'text-blue-500 hover:text-blue-800',
    }
  },
  methods: {
    submit() {
      this.$inertia.put(this.route('tankers.update', this.tanker.id), this.form, {
        onStart: () => this.sending = true,
        onFinish: () => this.sending = false,
      })
    },
    destroy() {
      if (confirm('Are you sure you want to delete this tanker?')) {
        this.$inertia.delete(this.route('tankers.destroy', this.tanker.id))
      }
    },
    restore() {
      if (confirm('Are you sure you want to restore this tanker?')) {
        this.$inertia.put(this.route('tankers.restore', this.tanker.id))
      }
    },
  },
}
</script>
