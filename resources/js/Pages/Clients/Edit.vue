<template>
  <div>
    <h1 class="mb-8 font-bold text-3xl">
      <inertia-link class="text-blue-600 hover:text-blue-800" :href="route('clients.index')">Clients</inertia-link>
      <span class="text-blue-600 font-medium">/</span> {{ form.name }}
    </h1>

    <trashed-message v-if="client.deleted_at" class="mb-6" @restore="restore">
      This client has been deleted.
    </trashed-message>

    <div class="p-1">
      <ul class="flex border-b">
        <li @click="openTab = 1" :class="{ '-mb-px': openTab === 1 }" class="-mb-px mr-1">
          <a :class="openTab === 1 ? activeClasses : inactiveClasses" class="bg-white inline-block py-2 px-4 font-semibold">Info</a>
        </li>
        <li @click="openTab = 2" :class="{ '-mb-px': openTab === 2 }" class="mr-1" v-show="deliveryDetails.data.length">
          <a :class="openTab === 2 ? activeClasses : inactiveClasses" class="bg-white inline-block py-2 px-4 font-semibold">Deliveries</a>
        </li>
        <li @click="openTab = 3" :class="{ '-mb-px': openTab === 3 }" class="mr-1" v-show="haulDetails.data.length">
          <a :class="openTab === 3 ? activeClasses : inactiveClasses" class="bg-white inline-block py-2 px-4 font-semibold">Hauling</a>
        </li>
      </ul>

      <!-- Tab 1 -->
      <div v-show="openTab === 1">
        <div class="bg-white rounded shadow overflow-hidden max-w-4xl">
          <form @submit.prevent="submit">
            <div class="p-8 -mr-6 -mb-8 flex flex-wrap">
              <text-input v-model="form.name" :error="errors.name" class="pr-6 pb-8 w-full lg:w-1/2" label="Company Name" />
              <text-input v-model="form.office" :error="errors.office" class="pr-6 pb-8 w-full lg:w-1/2" label="Office" />
              <text-input v-model="form.contact_person" :error="errors.contact_person" class="pr-6 pb-8 w-full lg:w-1/2" label="Contact Person" />
              <text-input v-model="form.contact_no" :error="errors.contact_no" class="pr-6 pb-8 w-full lg:w-1/2" label="Contact No" />
              <text-input v-model="form.email_address" :error="errors.email_address" class="pr-6 pb-8 w-full lg:w-1/2" label="Email Address" />
            </div>
            <div class="px-8 py-4 bg-gray-100 border-t border-gray-200 flex justify-end items-center">
              <button v-if="!deliveryDetails.data.length && !haulDetails.data.length && !client.deleted_at" class="text-red-600 hover:underline" tabindex="-1" type="button" @click="destroy">Delete Client</button>
              <loading-button :loading="sending" class="btn-indigo ml-auto" type="submit">Update Client</loading-button>
            </div>
          </form>
        </div>
      </div>

      <!-- Tab 2 -->
      <div class="w-full pt-4">
        <div v-show="openTab === 2">
          <!-- <div class="bg-white rounded shadow overflow-hidden max-w-3xl mb-6 -mt-4">
            <form @submit.prevent="statementSubmit">
              <div class="p-8 -mr-6 -mb-8 flex flex-wrap">
                <label class="form-label block mr-5">Date</label>
                <div class="pr-6 pb-8 w-full">
                  <date-picker v-model="statementForm.date" lang="en" value-type="format" :formatter="momentFormat"></date-picker>
                </div>

                <text-input v-model="statementForm.soa_no" :error="errors.soa_no" class="pr-6 pb-8 w-full lg:w-1/2" label="SOA No." />
                <text-input type="number" step="any" v-model="statementForm.payment" :error="errors.payment" class="pr-6 pb-8 w-full lg:w-1/2" label="Payment" />
                <text-input v-model="statementForm.check_no" :error="errors.check_no" class="pr-6 pb-8 w-full lg:w-1/2" label="Check No." />
              </div>

              <div class="px-8 py-4 bg-gray-100 border-t border-gray-200 flex justify-end items-center">
                <loading-button :loading="sending" class="btn-indigo" type="submit">Save</loading-button>
              </div>
            </form>
          </div> -->

<!--           id selected: {{statementForm.selected}}
          <h1 class="mt-12 mb-5 font-bold text-2xl">Delivered</h1>
 -->
          <div class="bg-white rounded shadow overflow-x-auto mb-8 -mt-4">
            <table class="min-w-full divide-y divide-gray-200">
              <thead class="bg-gray-50">
                <tr>
<!--                   <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    <input type="checkbox" v-model="statementForm.selectAll" @click="select">
                  </th>
 -->
                  <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Date
                  </th>
                  <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    DR No.
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
                <tr v-for="detail in localDeliveryDetails">
<!--                   <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm font-medium text-gray-900">
                      <input type="checkbox" :value="detail.id" v-model="statementForm.selected">
                    </div>
                  </td>
 -->
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm font-medium text-gray-900">
                      {{ detail.date }}
                    </div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm font-medium text-gray-900">
                      {{ detail.dr_no  }}
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
              </tbody>
            </table>
          </div>
          <button @click="loadMoreDelivery">Load more...</button>
        </div>


        <div v-show="openTab === 3">
          <!-- <div class="bg-white rounded shadow overflow-hidden max-w-3xl mb-6 -mt-4">
            <form @submit.prevent="statementSubmit">
              <div class="p-8 -mr-6 -mb-8 flex flex-wrap">
                <label class="form-label block mr-5">Date</label>
                <div class="pr-6 pb-8 w-full">
                  <date-picker v-model="statementForm.date" lang="en" value-type="format" :formatter="momentFormat"></date-picker>
                </div>

                <text-input v-model="statementForm.soa_no" :error="errors.soa_no" class="pr-6 pb-8 w-full lg:w-1/2" label="SOA No." />
                <text-input type="number" step="any" v-model="statementForm.payment" :error="errors.payment" class="pr-6 pb-8 w-full lg:w-1/2" label="Payment" />
                <text-input v-model="statementForm.check_no" :error="errors.check_no" class="pr-6 pb-8 w-full lg:w-1/2" label="Check No." />
              </div>

              <div class="px-8 py-4 bg-gray-100 border-t border-gray-200 flex justify-end items-center">
                <loading-button :loading="sending" class="btn-indigo" type="submit">Save</loading-button>
              </div>
            </form>
          </div> -->

<!--           id selected: {{statementForm.selected}}
          <h1 class="mt-12 mb-5 font-bold text-2xl">Delivered</h1>
 -->
          <div class="bg-white rounded shadow overflow-x-auto mb-8 -mt-4">
            <table class="min-w-full divide-y divide-gray-200">
              <thead class="bg-gray-50">
                <tr>
<!--                   <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    <input type="checkbox" v-model="statementForm.selectAll" @click="select">
                  </th>
 -->
                  <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Date
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
                <tr v-for="detail in localHaulDetails">
<!--                   <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm font-medium text-gray-900">
                      <input type="checkbox" :value="detail.id" v-model="statementForm.selected">
                    </div>
                  </td>
 -->
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm font-medium text-gray-900">
                      {{ detail.date }}
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
              </tbody>
            </table>
          </div>
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
import DatePicker from 'vue2-datepicker'
import moment from 'moment'
import axios from 'axios'
import { loadMoreHaulMixin } from '@/Mixins/loadMoreHaulMixin'
import { loadMoreDeliveryMixin } from '@/Mixins/loadMoreDeliveryMixin'

export default {
  mixins: [
    loadMoreHaulMixin('clients'),
    loadMoreDeliveryMixin('clients'),
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
    DatePicker,
  },
  props: {
    errors: Object,
    client: Object,
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
        name: this.client.name,
        office: this.client.office,
        contact_person: this.client.contact_person,
        contact_no: this.client.contact_no,
        email_address: this.client.email_address,
      },
      // statementForm: {
      //   date: null,
      //   client_id: this.client.id,
      //   payment: null,
      //   check_no: null,
      //   soa_no: null,
        // Checkbox
      //   selectAll: false,
      //   selected: [],
      // },
      // Tabs
      openTab: 1,
      activeClasses: 'border-l border-t border-r rounded-t text-blue-600',
      inactiveClasses: 'text-blue-500 hover:text-blue-800',
    }
  },
  methods: {
    submit() {
      this.$inertia.put(this.route('clients.update', this.client.id), this.form, {
        onStart: () => this.sending = true,
        onFinish: () => this.sending = false,
      })
    },
    destroy() {
      if (confirm('Are you sure you want to delete this client?')) {
        this.$inertia.delete(this.route('clients.destroy', this.client.id))
      }
    },
    restore() {
      if (confirm('Are you sure you want to restore this client?')) {
        this.$inertia.put(this.route('clients.restore', this.client.id))
      }
    },

    // SOA
    // statementSubmit() {
    //   this.$inertia.post(this.route('statements.store'), this.statementForm, {
    //     onStart: () => this.sending = true,
    //     onFinish: () => this.sending = false,
    //   })
    // },

    // select() {
    //   this.statementForm.selected = [];
    //   if (!this.statementForm.selectAll) {
    //     for (let i in this.deliveryDetails.data) {
    //       this.statementForm.selected.push(this.deliveryDetails.data[i].id);
    //     }
    //   }
    // },

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
  },
}
</script>

<style src="vue2-datepicker/index.css"></style>
