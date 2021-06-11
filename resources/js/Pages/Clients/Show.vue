<template>
  <div>
    <h1 class="mb-8 font-bold text-3xl">
      <inertia-link class="text-blue-600 hover:text-blue-800" :href="route('clients.index')">Clients</inertia-link>
      <span class="text-blue-600 font-medium">/</span> {{ client.name }}
    </h1>

    <trashed-message v-if="client.deleted_at" class="mb-6" @restore="restore">
      This client has been deleted.
    </trashed-message>

    <div class="p-1">
      <ul class="flex border-b">
        <li @click="openTab = 2" :class="{ '-mb-px': openTab === 2 }" class="mr-1" v-show="batangasDetails">
          <a :class="openTab === 2 ? activeClasses : inactiveClasses" class="bg-white inline-block py-2 px-4 font-semibold">Batangas</a>
        </li>
        <li @click="openTab = 3" :class="{ '-mb-px': openTab === 3 }" class="mr-1" v-show="mindoroDetails">
          <a :class="openTab === 3 ? activeClasses : inactiveClasses" class="bg-white inline-block py-2 px-4 font-semibold">Mindoro</a>
        </li>
      </ul>


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
            <div class="rounded shadow overflow-x-auto mx-4 my-8" v-for="(months, year) in localBatangasDetails">
              <h1 class="font-semibold text-center">{{ year }}</h1>
              <div class="mt-6 mb-1" v-for="(transactionDetails, month) in months">
                <div class="text-sm font-bold bg-gradient-to-r from-yellow-500 to-blue-600 text-white px-4 py-1 flex justify-between">
                  <h2>{{ month }}</h2>
                  <!-- <span class="mx-2 px-2 py-2 text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">{{ transactions.length }}</span> -->
                  <div>
                    <span class="text-xs px-3 bg-yellow-500 text-white rounded-full">{{ `Dsl: ${totalBatangasQty(year, month, 'Diesel')}` }}</span>
                    <span class="text-xs px-3 bg-green-500 text-white rounded-full">{{ `Reg: ${totalBatangasQty(year, month, 'Regular')}` }}</span>
                    <span class="text-xs px-3 bg-red-500 text-white rounded-full">{{ `Prem: ${totalBatangasQty(year, month, 'Premium')}` }}</span>
                    <span class="text-xs px-3 bg-white text-blue-600 rounded-full">{{ `Amt: ${totalBatangasAmt(year, month)}` }}</span>
                  </div>
                </div>
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
                        Code
                      </th>
                      <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Client
                      </th>
                      <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Remarks
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
                    <tr v-for="(detail, transactionDetailIndex) in transactionDetails">
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
                        <a class="text-sm font-medium text-blue-600" target="_blank" :href="`/monthly-batangas-transactions/${detail.monthly_batangas_transaction_id}/edit#transaction-${detail.batangas_transaction_id}`">
                          {{ detail.trip_no }}
                        </a>
                      </td>
                      <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-medium text-gray-900">
                          {{ detail.client.name }}
                        </div>
                      </td>
                      <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-medium text-gray-900">
                          {{ detail.remarks }}
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
            </div>
          </div>
          <!-- <button @click="loadMoreBatangasTransaction">Load more...</button> -->
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
            <div class="rounded shadow overflow-x-auto mx-4 my-8" v-for="(months, year) in localMindoroDetails">
              <h1 class="font-semibold text-center">{{ year }}</h1>
              <div class="mt-6 mb-1" v-for="(transactionDetails, month) in months">
                <div class="text-sm font-bold bg-gradient-to-r from-yellow-500 to-blue-600 text-white px-4 py-1 flex justify-between">
                  <h2>{{ month }}</h2>
                  <!-- <span class="mx-2 px-2 py-2 text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">{{ transactions.length }}</span> -->
                  <div>
                    <span class="text-xs px-3 bg-yellow-500 text-white rounded-full">{{ `Dsl: ${totalMindoroQty(year, month, 'Diesel')}` }}</span>
                    <span class="text-xs px-3 bg-green-500 text-white rounded-full">{{ `Reg: ${totalMindoroQty(year, month, 'Regular')}` }}</span>
                    <span class="text-xs px-3 bg-red-500 text-white rounded-full">{{ `Prem: ${totalMindoroQty(year, month, 'Premium')}` }}</span>
                    <span class="text-xs px-3 bg-white text-blue-600 rounded-full">{{ `Amt: ${totalMindoroAmt(year, month)}` }}</span>
                  </div>
                </div>
                <div>
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
                          Code
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                          Client
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                          Remarks
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
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                          DR#
                        </th>
                      </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                      <tr v-for="(detail, transactionDetailIndex) in transactionDetails">
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
                          <a class="text-sm font-medium text-blue-600" target="_blank" :href="`/monthly-mindoro-transactions/${detail.monthly_mindoro_transaction_id}/edit#transaction-${detail.mindoro_transaction_id}`">
                              {{ detail.trip_no }}
                          </a>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                          <div class="text-sm font-medium text-gray-900">
                            {{ detail.client.name }}
                          </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                          <div class="text-sm font-medium text-gray-900">
                            {{ detail.remarks }}
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
                        <td class="px-6 py-4 whitespace-nowrap">
                          <div class="text-sm font-medium text-gray-900">
                            {{ detail.dr_no }}
                          </div>
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>
          <!-- <button @click="loadMoreMindoroTransaction">Load more...</button> -->
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
import { loadMoreMindoroTransactionMixin } from '@/Mixins/loadMoreMindoroTransactionMixin'
import { loadMoreBatangasTransactionMixin } from '@/Mixins/loadMoreBatangasTransactionMixin'
import { numberFormatsMixin } from '@/Mixins/numberFormatsMixin'

export default {
  mixins: [
    loadMoreMindoroTransactionMixin('clients'),
    loadMoreBatangasTransactionMixin('clients'),
    numberFormatsMixin,
  ],
  metaInfo() {
    return { title: this.client.name }
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
      openTab: 2,
      activeClasses: 'border-l border-t border-r rounded-t text-blue-600',
      inactiveClasses: 'text-blue-500 hover:text-blue-800',
    }
  },
  methods: {
    restore() {
      if (confirm('Are you sure you want to restore this client?')) {
        this.$inertia.put(this.route('clients.restore', this.client.id))
      }
    },
    // BatangasTransactionDetail
    totalBatangasQty(yr, mo, product) {
      var totalQty = Object.entries(this.localBatangasDetails).reduce((acc, [year, months]) => {
        if(year === yr) {
          for (let [month, details] of Object.entries(months)) {
            if(month === mo) {
              for (let [key, detail] of Object.entries(details)) {
                if(detail.product.name === product) {
                  acc += parseFloat(detail.quantity);
                }
              }
            }
          }
        }
        return parseFloat(acc);
      }, 0);

      return this.quantityFormat(totalQty);
    },

    totalBatangasAmt(yr, mo) {
      var totalAmt = Object.entries(this.localBatangasDetails).reduce((acc, [year, months]) => {
        if(year === yr) {
          for (let [month, details] of Object.entries(months)) {
            if(month === mo) {
              for (let [key, detail] of Object.entries(details)) {
                acc += parseFloat(detail.quantity * detail.unit_price);
              }
            }
          }
        }
        return parseFloat(acc);
      }, 0);

      return this.toPHP(totalAmt);
    },

    // MindoroTransactionDetail
    totalMindoroQty(yr, mo, product) {
      var totalQty = Object.entries(this.localMindoroDetails).reduce((acc, [year, months]) => {
        if(year === yr) {
          for (let [month, details] of Object.entries(months)) {
            if(month === mo) {
              for (let [key, detail] of Object.entries(details)) {
                if(detail.product.name === product) {
                  acc += parseFloat(detail.quantity);
                }
              }
            }
          }
        }
        return parseFloat(acc);
      }, 0);

      return this.quantityFormat(totalQty);
    },

    totalMindoroAmt(yr, mo) {
      var totalAmt = Object.entries(this.localMindoroDetails).reduce((acc, [year, months]) => {
        if(year === yr) {
          for (let [month, details] of Object.entries(months)) {
            if(month === mo) {
              for (let [key, detail] of Object.entries(details)) {
                acc += parseFloat(detail.quantity * detail.unit_price);
              }
            }
          }
        }
        return parseFloat(acc);
      }, 0);

      return this.toPHP(totalAmt);
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

  },
}
</script>

<style src="vue2-datepicker/index.css"></style>
