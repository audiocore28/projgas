<template>
  <div>
    <h1 class="mb-8 font-bold text-3xl">
      <inertia-link class="text-blue-600 hover:text-blue-800" :href="route('drivers.index')">Drivers</inertia-link>
      <span class="text-blue-600 font-medium">/</span> {{ form.name }}
    </h1>
    <div class="p-1">
      <ul class="flex border-b">
        <li @click="openTab = 1" :class="{ '-mb-px': openTab === 1 }" class="-mb-px mr-1">
          <a :class="openTab === 1 ? activeClasses : inactiveClasses" class="bg-white inline-block py-2 px-4 font-semibold" href="#">Information</a>
        </li>
        <li @click="openTab = 2" :class="{ '-mb-px': openTab === 2 }" class="mr-1">
          <a :class="openTab === 2 ? activeClasses : inactiveClasses" class="bg-white inline-block py-2 px-4 font-semibold" href="#">Tanker | Helper</a>
        </li>
        <li @click="openTab = 3" :class="{ '-mb-px': openTab === 3 }" class="mr-1">
          <a :class="openTab === 3 ? activeClasses : inactiveClasses" class="bg-white inline-block py-2 px-4 font-semibold" href="#">Loads</a>
        </li>
        <li @click="openTab = 4" :class="{ '-mb-px': openTab === 4 }" class="mr-1">
          <a :class="openTab === 4 ? activeClasses : inactiveClasses" class="bg-white inline-block py-2 px-4 font-semibold" href="#">Deliveries</a>
        </li>
        <li @click="openTab = 5" :class="{ '-mb-px': openTab === 5 }" class="mr-1">
          <a :class="openTab === 5 ? activeClasses : inactiveClasses" class="bg-white inline-block py-2 px-4 font-semibold" href="#">Salaries</a>
        </li>
      </ul>

      <!-- Tab 1 -->
      <div class="w-full pt-4">
        <div v-show="openTab === 1">
          <div class="bg-white rounded shadow overflow-hidden max-w-3xl -mt-4">
            <form @submit.prevent="submit">
              <div class="p-8 -mr-6 -mb-8 flex flex-wrap">
                <text-input v-model="form.name" :error="errors.name" class="pr-6 pb-8 w-full lg:w-1/2" label="Full Name" />
                <text-input v-model="form.nickname" :error="errors.nickname" class="pr-6 pb-8 w-full lg:w-1/2" label="Nickname" />
                <text-input v-model="form.address" :error="errors.address" class="pr-6 pb-8 w-full lg:w-1/2" label="Address" />
                <text-input v-model="form.license_no" :error="errors.license_no" class="pr-6 pb-8 w-full lg:w-1/2" label="License No." />
                <text-input v-model="form.contact_no" :error="errors.contact_no" class="pr-6 pb-8 w-full lg:w-1/2" label="Contact No" />
              </div>
              <div class="px-8 py-4 bg-gray-100 border-t border-gray-200 flex justify-end items-center">
                <button v-if="!driver.deleted_at" class="text-red-600 hover:underline" tabindex="-1" type="button" @click="destroy">Delete Driver</button>
                <loading-button :loading="sending" class="btn-indigo ml-auto" type="submit">Update Driver</loading-button>
              </div>
            </form>
          </div>

          <h1 class="mt-20 mb-8 font-bold text-2xl">Tanker(s)</h1>

          <div class="bg-white rounded shadow overflow-x-auto">
            <table class="w-full whitespace-no-wrap">
              <tr class="text-left font-bold">
                <th class="px-6 pt-6 pb-4">ID</th>
                <th class="px-6 pt-6 pb-4">Plate No.</th>
                <th class="px-6 pt-6 pb-4">Compartment</th>
              </tr>
              <tr v-for="tanker in tankers" :key="tanker.id" class="hover:bg-gray-100 focus-within:bg-gray-100">
                <!-- table columns -->
                <td class="border-t">
                  <inertia-link class="px-6 py-4 flex items-center" :href="route('tankers.edit', tanker.id)" tabindex="-1">
                    {{ tanker.id }}
                  </inertia-link>
                </td>
                <td class="border-t">
                  <inertia-link class="px-6 py-4 flex items-center" :href="route('tankers.edit', tanker.id)" tabindex="-1">
                    {{ tanker.plate_no }}
                  </inertia-link>
                </td>
                <td class="border-t">
                  <inertia-link class="px-6 py-4 flex items-center" :href="route('tankers.edit', tanker.id)" tabindex="-1">
                    {{ tanker.compartment }}
                  </inertia-link>
                </td>
              </tr>
              <tr v-if="tankers.length === 0">
                <td class="border-t px-6 py-4" colspan="4">No tankers found.</td>
              </tr>
            </table>
          </div>
        </div>
        <!-- Tab 2 -->
        <div v-show="openTab === 2">
          Tanker | Helper
        </div>
        <!-- Tab 3 -->
        <div v-show="openTab === 3">
          Loads
        </div>
        <!-- Tab 4 -->
        <div v-show="openTab === 4">
          Deliveries
        </div>
        <!-- Tab 5 -->
        <div v-show="openTab === 5">
          Salaries
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
    driver: Object,
    tankers: Array,
  },
  remember: 'form',
  data() {
    return {
      sending: false,
      form: {
        name: this.driver.name,
        nickname: this.driver.nickname,
        address: this.driver.address,
        license_no: this.driver.license_no,
        contact_no: this.driver.contact_no,
      },
      // Tabs
      openTab: 1,
      activeClasses: 'border-l border-t border-r rounded-t text-blue-600',
      inactiveClasses: 'text-blue-500 hover:text-blue-800',
    }
  },
  methods: {
    submit() {
      this.$inertia.put(this.route('drivers.update', this.driver.id), this.form, {
        onStart: () => this.sending = true,
        onFinish: () => this.sending = false,
      })
    },
    destroy() {
      if (confirm('Are you sure you want to delete this driver?')) {
        this.$inertia.delete(this.route('drivers.destroy', this.driver.id))
      }
    },
    restore() {
      if (confirm('Are you sure you want to restore this driver?')) {
        this.$inertia.put(this.route('drivers.restore', this.driver.id))
      }
    },
  },
}
</script>