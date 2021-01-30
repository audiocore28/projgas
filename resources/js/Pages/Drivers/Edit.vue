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
        <li @click="openTab = 3" :class="{ '-mb-px': openTab === 3 }" class="mr-1" v-show="deliveryDetails.data.length">
          <a :class="openTab === 3 ? activeClasses : inactiveClasses" class="bg-white inline-block py-2 px-4 font-semibold">Deliveries</a>
        </li>
        <li @click="openTab = 4" :class="{ '-mb-px': openTab === 4 }" class="mr-1" v-show="haulDetails.data.length">
          <a :class="openTab === 4 ? activeClasses : inactiveClasses" class="bg-white inline-block py-2 px-4 font-semibold">Hauling</a>
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
                <button v-if="!loadDetails.data.length && !deliveryDetails.data.length && !haulDetails.data.length && !driver.deleted_at" class="text-red-600 hover:underline" tabindex="-1" type="button" @click="destroy">Delete Driver</button>
                <loading-button :loading="sending" class="btn-indigo ml-auto" type="submit">Update Driver</loading-button>
              </div>
            </form>
          </div>
        </div>

        <!-- Tab 2 -->
        <div v-show="openTab === 2">
          <div class="bg-white rounded shadow overflow-x-auto mb-8 -mt-4">
            <div class="rounded shadow overflow-x-auto mb-8" v-for="load in localLoadDetails" :key="load.id" :value="load.id">
              <div class="ml-5 mt-5 mb-1">
                <inertia-link :href="route('tanker-loads.edit', load.id)" tabindex="-1">
                  <p class="text-sm font-bold text-blue-700 mb-2">{{ load.trip_no }}. {{ load.driver.name }} & {{ load.helper.name }} ({{ load.tanker.plate_no }})</p>
                </inertia-link>
                <p class="text-sm font-medium text-gray-600 mb-4 ml-1">{{ load.date }} / {{ load.purchase.purchase_no }}</p>
              </div>
              <table class="min-w-full divide-y divide-gray-200">
                <tbody class="bg-white divide-y divide-gray-200">
                  <tr v-for="detail in load.loads">
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                      <div class="text-sm text-gray-900">{{ detail.product.name }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                      <div class="text-sm text-gray-900">{{ quantityFormat(detail.quantity) }}</div>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
          <button @click="loadMoreLoad">Load more...</button>
        </div>

        <!-- Tab 3 -->
        <div v-show="openTab === 3">
          <div class="bg-white rounded shadow overflow-x-auto mb-8 -mt-4">
            <div class="rounded shadow overflow-x-auto mb-8" v-for="delivery in localDeliveryDetails">
              <div class="ml-5 mt-5 mb-1">
                <inertia-link :href="route('deliveries.edit', delivery.id)" tabindex="-1">
                  <p class="text-sm font-bold text-blue-700 mb-2">{{ delivery.driver.name }} & {{ delivery.helper.name }} ({{ delivery.tanker.plate_no }})</p>
                </inertia-link>
              </div>
              <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                  <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Date
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      DR No.
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Client
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Product
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Quantity
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Unit Price
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Amount
                    </th>
                  </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                  <tr v-for="detail in delivery.deliveries" :key="detail.id" :value="detail.id">
                    <td class="px-6 py-4 whitespace-nowrap">
                      <div class="text-sm font-medium text-gray-900">
                        {{ detail.date }}
                      </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      <div class="text-sm font-medium text-gray-900">
                        {{ detail.dr_no }}
                      </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      <div class="text-sm font-medium text-gray-900">
                        {{ detail.client.name  }}
                      </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      <div class="text-sm font-medium text-gray-900">
                        {{ detail.product.name }}
                      </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                      <div class="text-sm font-medium text-gray-900">
                        {{ quantityFormat(detail.quantity) }}
                      </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                      <div class="text-sm font-medium text-gray-900">
                        {{ toPHP(detail.unit_price) }}
                      </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                      <div class="text-sm font-medium text-gray-900">
                        {{ totalCurrency(detail.quantity, detail.unit_price) }}
                      </div>
                    </td>
                  </tr>
                  <!-- Total -->
                  <tr class="bg-gray-200">
                    <td class="px-6 py-4 whitespace-nowrap">
                      <div class="text-left text-xs font-medium text-gray-500 uppercase">Total:</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      <div class="text-sm text-gray-900"></div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      <div class="text-sm text-gray-900"></div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      <div class="text-sm text-gray-900"></div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      <div class="text-sm text-gray-900">{{ totalDeliveriesQty(delivery.id) }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      <div class="text-sm text-gray-900"></div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      <div class="text-sm text-gray-900">{{ totalDeliveriesAmount(delivery.id) }}</div>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
          <button @click="loadMoreDelivery">Load more...</button>
        </div>

        <!-- Tab 4 -->
        <div v-show="openTab === 4">
          <div class="bg-white rounded shadow overflow-x-auto mb-8 -mt-4">
            <div class="rounded shadow overflow-x-auto mb-8" v-for="haul in localHaulDetails">
              <div class="ml-5 mt-5 mb-1">
                <inertia-link :href="route('hauls.edit', haul.id)" tabindex="-1">
                  <p class="text-sm font-bold text-blue-700 mb-2">{{ haul.driver.name }} & {{ haul.helper.name }} ({{ haul.tanker.plate_no }})</p>
                </inertia-link>
              </div>
              <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                  <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Date
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Client
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Product
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Quantity
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Unit Price
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Amount
                    </th>
                  </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                  <tr v-for="detail in haul.hauls" :key="detail.id" :value="detail.id">
                    <td class="px-6 py-4 whitespace-nowrap">
                      <div class="text-sm font-medium text-gray-900">
                        {{ detail.date }}
                      </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      <div class="text-sm font-medium text-gray-900">
                        {{ detail.client.name  }}
                      </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      <div class="text-sm font-medium text-gray-900">
                        {{ detail.product.name }}
                      </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                      <div class="text-sm font-medium text-gray-900">
                        {{ quantityFormat(detail.quantity) }}
                      </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                      <div class="text-sm font-medium text-gray-900">
                        {{ toPHP(detail.unit_price) }}
                      </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                      <div class="text-sm font-medium text-gray-900">
                        {{ totalCurrency(detail.quantity, detail.unit_price) }}
                      </div>
                    </td>
                  </tr>
                  <!-- Total -->
                  <tr class="bg-gray-200">
                    <td class="px-6 py-4 whitespace-nowrap">
                      <div class="text-left text-xs font-medium text-gray-500 uppercase">Total:</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      <div class="text-sm text-gray-900"></div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      <div class="text-sm text-gray-900"></div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      <div class="text-sm text-gray-900">{{ totalHaulsQty(haul.id) }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      <div class="text-sm text-gray-900"></div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      <div class="text-sm text-gray-900">{{ totalHaulsAmount(haul.id) }}</div>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
          <!-- <button @click="loadMore" :disabled="loadingMore">Load more...</button> -->
          <button @click="loadMoreHaul">Load more...</button>
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
import moment from 'moment'
import axios from 'axios'
import { loadMoreLoadMixin } from '@/Mixins/loadMoreLoadMixin'
import { loadMoreHaulMixin } from '@/Mixins/loadMoreHaulMixin'
import { loadMoreDeliveryMixin } from '@/Mixins/loadMoreDeliveryMixin'

export default {
  mixins: [
    loadMoreLoadMixin('drivers'),
    loadMoreHaulMixin('drivers'),
    loadMoreDeliveryMixin('drivers'),
  ],
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
  },
  props: {
    errors: Object,
    driver: Object,
  },
  remember: 'form',
  data() {
    return {
      sending: false,
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

    // Helpers
    quantityFormat(value) {
      return Number(value).toLocaleString()
    },

    toPHP(value) {
      return `₱${Number(value).toLocaleString()}`;
    },

    totalCurrency(quantity, unitPrice) {
      return this.toPHP(quantity * unitPrice);
    },

    // Haul
    totalHaulsAmount(haulId) {
      for (var i = 0; i < this.localHaulDetails.length; i++) {
        if (this.localHaulDetails[i].id === haulId) {

          var totalAmt = this.localHaulDetails[i].hauls.reduce(function (acc, haul) {
            acc += parseFloat(haul.quantity) * parseFloat(haul.unit_price);
            return acc;
          }, 0);

          return this.toPHP(totalAmt);
        }
      }
    },

    totalHaulsQty(haulId) {
      for (var i = 0; i < this.localHaulDetails.length; i++) {
        if (this.localHaulDetails[i].id === haulId) {

          var totalQty = this.localHaulDetails[i].hauls.reduce(function (acc, haul) {
            acc += parseFloat(haul.quantity);
            return acc;
          }, 0);

          return this.quantityFormat(totalQty);
        }
      }
    },

    // Delivery
    totalDeliveriesAmount(deliveryId) {
      for (var i = 0; i < this.localDeliveryDetails.length; i++) {
        if (this.localDeliveryDetails[i].id === deliveryId) {

          var totalAmt = this.localDeliveryDetails[i].deliveries.reduce(function (acc, delivery) {
            acc += parseFloat(delivery.quantity) * parseFloat(delivery.unit_price);
            return acc;
          }, 0);

          return this.toPHP(totalAmt);
        }
      }
    },

    totalDeliveriesQty(deliveryId) {
      for (var i = 0; i < this.localDeliveryDetails.length; i++) {
        if (this.localDeliveryDetails[i].id === deliveryId) {

          var totalQty = this.localDeliveryDetails[i].deliveries.reduce(function (acc, delivery) {
            acc += parseFloat(delivery.quantity);
            return acc;
          }, 0);

          return this.quantityFormat(totalQty);
        }
      }
    },

  },
}
</script>