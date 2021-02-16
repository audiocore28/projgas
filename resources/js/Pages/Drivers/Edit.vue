<template>
  <div>
    <h1 class="mb-8 font-bold text-3xl">
      <inertia-link class="text-blue-600 hover:text-blue-800" :href="route('drivers.index')">Drivers</inertia-link>
      <span class="text-blue-600 font-medium">/</span> {{ form.name }}
    </h1>

    <trashed-message v-if="driver.deleted_at" class="mb-6" @restore="restore">
      This driver has been deleted.
    </trashed-message>

    <div class="p-1">
      <ul class="flex border-b">
        <li @click="openTab = 1" :class="{ '-mb-px': openTab === 1 }" class="-mb-px mr-1">
          <a :class="openTab === 1 ? activeClasses : inactiveClasses" class="bg-white inline-block py-2 px-4 font-semibold">Info</a>
        </li>
        <li @click="openTab = 2" :class="{ '-mb-px': openTab === 2 }" class="mr-1" v-show="loadDetails.data.length">
          <a :class="openTab === 2 ? activeClasses : inactiveClasses" class="bg-white inline-block py-2 px-4 font-semibold">Loads</a>
        </li>
        <li @click="openTab = 3" :class="{ '-mb-px': openTab === 3 }" class="mr-1" v-show="haulDetails.data.length">
          <a :class="openTab === 3 ? activeClasses : inactiveClasses" class="bg-white inline-block py-2 px-4 font-semibold">Hauling</a>
        </li>
        <li @click="openTab = 4" :class="{ '-mb-px': openTab === 4 }" class="mr-1" v-show="deliveryDetails.data.length">
          <a :class="openTab === 4 ? activeClasses : inactiveClasses" class="bg-white inline-block py-2 px-4 font-semibold">Deliveries</a>
        </li>
<!--         <li @click="openTab = 5" :class="{ '-mb-px': openTab === 5 }" class="mr-1">
          <a :class="openTab === 5 ? activeClasses : inactiveClasses" class="bg-white inline-block py-2 px-4 font-semibold">Salaries</a>
        </li>
 -->
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
                <button v-if="!loadDetails.data.length && !deliveryDetails.data.length && !haulDetails.data.length && !driver.deleted_at" class="text-red-600 hover:underline" tabindex="-1" type="button" @click="destroy">Delete Driver</button>
                <loading-button :loading="sending" class="btn-indigo ml-auto" type="submit">Update Driver</loading-button>
              </div>
            </form>
          </div>
        </div>

        <!-- Tab 2 -->
        <div v-show="openTab === 2">
          <tanker-loads :loadDetails="loadDetails" record="drivers"></tanker-loads>
        </div>

        <!-- Tab 3 -->
        <div v-show="openTab === 3">
          <hauling :haulDetails="haulDetails" record="drivers"></hauling>
        </div>

        <!-- Tab 4 -->
        <div v-show="openTab === 4">
          <deliveries :deliveryDetails="deliveryDetails" record="drivers"></deliveries>
        </div>

        <!-- Tab 5 -->
        <!-- <div v-show="openTab === 5" class="bg-white -mt-4">
          <div class="ml-5 pt-6">
            <text-input v-model="rate" class="pr-6 pb-8 w-full lg:w-1/6" label="Salary Rate" />
          </div>
          <div class="bg-white rounded shadow overflow-x-auto mb-8 -mt-4">
            <div class="rounded shadow overflow-x-auto mb-8">
              <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                  <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Year
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Month
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Loaded
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Amount
                    </th>
                  </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                  <tr v-for="trip in trips" :key="trip.id" :value="trip.id">
                    <td class="px-6 py-4 whitespace-nowrap">
                      <div class="text-sm font-medium text-gray-900">
                        {{ trip.year }}
                      </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      <div class="text-sm font-medium text-gray-900">
                        {{ trip.month }}
                      </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      <div class="text-sm font-medium text-gray-900">
                        {{ trip.loaded }}
                      </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      <div class="text-sm font-medium text-gray-900">
                        {{ trip.loaded * rate }}
                      </div>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div> -->

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
import Deliveries from '@/Shared/Deliveries'
import Hauling from '@/Shared/Hauling'
import TankerLoads from '@/Shared/TankerLoads'
import moment from 'moment'

export default {
  metaInfo() {
    return { title: this.form.name }
  },
  layout: Layout,
  components: {
    Icon,
    LoadingButton,
    SelectInput,
    TextInput,
    TrashedMessage,
    Deliveries,
    Hauling,
    TankerLoads,
  },
  props: {
    errors: Object,
    driver: Object,
    deliveryDetails: Object,
    haulDetails: Object,
    loadDetails: Object,
    // trips: Array,
  },
  remember: 'form',
  data() {
    return {
      sending: false,
      // rate: 3500,
      momentFormat: {
        //[optional] Date to String
        stringify: (date) => {
          return date ? moment(date).format('ll') : ''
        },
        //[optional]  String to Date
        parse: (value) => {
          return value ? moment(value, 'll').toDate() : null
        },
        // [optional] getWeekNumber
        getWeek: (date) => {
          return // a number
        }
      },
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