<template>
  <div>
    <h1 class="mb-8 font-bold text-3xl">
      <inertia-link class="text-blue-600 hover:text-blue-800" :href="route('client-payments.index')">Client Payments</inertia-link>
      <span class="text-blue-600 font-medium">/</span> Create
    </h1>
    <div class="bg-white rounded shadow overflow-hidden w-full">
      <form @submit.prevent="submit">
        <div class="p-8 -mr-6 -mb-8 flex flex-wrap">
          <div class="w-full mb-2">
            <label class="form-label block mr-5">Date:<span class="text-red-500">*</span></label>
            <div class="pr-6 pb-4 my-3">
              <date-picker v-model="form.date" :error="errors.date" lang="en" value-type="format" :formatter="momentFormat"></date-picker>
              <div v-if="errors.date" class="form-error">{{ errors.date }}</div>
            </div>
          </div>

          <div class="pr-6 pb-8 w-full -mt-1 lg:w-1/3">
            <label class="form-label block">Client:</label>
             <multiselect
               id="client_id"
               v-model="form.selected_client"
               placeholder=""
               class="mt-3 text-xs z-50"
               :options="clients"
               label="name"
               track-by="id"
               @search-change="onSearchClientChange"
               @input="onSelectedClient"
               :show-labels="false"
               :allow-empty="false"
            ></multiselect>
          </div>

          <select-input v-model="form.mode" :error="errors.mode" class="pr-6 pb-4 w-full lg:w-1/3" label="Mode of Payment">
            <option :value="null"/>
            <option v-for="(mode, index) in modes" :key="index" :value="mode">{{ mode }}</option>
          </select-input>

          <text-input type="number" step="any" v-model="form.amount" :error="errors.amount" class="pr-6 pb-8 w-full lg:w-1/3" label="Amount"/>
          <text-input v-model="form.remarks" :error="errors.remarks" class="pr-6 pb-8 w-full" label="Remarks"/>
        </div>


        <!-- SOA / List of Transactions -->
        <div class="bg-white rounded overflow-x-auto mb-8 -mt-4">
          <h1 class="font-semibold text-center text-blue-600" v-if="form.transactions.batangasDetails">Batangas</h1>
          <div class="rounded shadow overflow-x-auto mx-4 my-8" v-for="(months, year) in form.transactions.batangasDetails">
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
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      <!-- <input type="checkbox" v-model="statementForm.selectAll" @click="select"> -->
                    </th>
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
                    <td class="px-6 py-4 whitespace-nowrap">
                      <div class="text-sm font-medium text-gray-900">
                        <input type="checkbox" :value="detail.id" v-model="form.selectedBatangas">
                      </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      <div class="text-sm font-medium text-gray-900">
                        {{ detail.date }}
                      </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      <a class="text-sm font-medium text-blue-600" target="_blank" :href="`/monthly-batangas-transactions/${detail.monthly_batangas_transaction_id}/edit#transaction-${detail.batangas_transaction_id}`" v-if="$page.auth.user.can.batangasTransaction.update">
                        {{ detail.trip_no }}
                      </a>
                      <span class="text-sm font-medium text-gray-900" v-else>
                        {{ detail.trip_no }}
                      </span>
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

        <div class="bg-white rounded overflow-x-auto mb-8 -mt-4">
          <h1 class="font-semibold text-center text-yellow-600" v-if="form.transactions.mindoroDetails">Mindoro</h1>
          <div class="rounded shadow overflow-x-auto mx-4 my-8" v-for="(months, year) in form.transactions.mindoroDetails">
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
                      <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        <!-- <input type="checkbox" v-model="statementForm.selectAll" @click="select"> -->
                      </th>
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
                      <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-medium text-gray-900">
                          <input type="checkbox" :value="detail.id" v-model="form.selectedMindoro">
                        </div>
                      </td>
                      <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-medium text-gray-900">
                          {{ detail.date }}
                        </div>
                      </td>
                      <td class="px-6 py-4 whitespace-nowrap">
                        <a class="text-sm font-medium text-blue-600" target="_blank" :href="`/monthly-mindoro-transactions/${detail.monthly_mindoro_transaction_id}/edit#transaction-${detail.mindoro_transaction_id}`" v-if="$page.auth.user.can.mindoroTransaction.update">
                            {{ detail.trip_no }}
                        </a>
                        <span class="text-sm font-medium text-gray-900" v-else>
                            {{ detail.trip_no }}
                        </span>
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

        <div class="px-8 py-4 bg-gray-100 border-t border-gray-200 flex justify-end items-center">
          <loading-button :loading="sending" class="btn-indigo" type="submit">Save</loading-button>
        </div>
      </form>
    </div>
  </div>
</template>

<script>
import Layout from '@/Shared/Layout'
import LoadingButton from '@/Shared/LoadingButton'
import TextInput from '@/Shared/TextInput'
import Multiselect from 'vue-multiselect'
import DatePicker from 'vue2-datepicker'
import SelectInput from '@/Shared/SelectInput'
import moment from 'moment'
import {throttle} from 'lodash'
import { numberFormatsMixin } from '@/Mixins/numberFormatsMixin'

export default {
  mixins: [
    numberFormatsMixin,
  ],
  metaInfo: { title: 'Create Client Payment' },
  layout: Layout,
  components: {
    LoadingButton,
    TextInput,
    Multiselect,
    DatePicker,
    SelectInput,
  },
  props: {
    errors: Object,
    clients: {
      type: Array,
      default: () => [],
    },
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
      modes: ['Cash', 'Cheque'],
      form: {
        selectedBatangas: [],
        selectedMindoro: [],
        transactions: [],
        date: null,
        client: {
          id: null,
        },
        selected_client: null,
        mode: null,
        amount: null,
        remarks: null,
      },
    }
  },
  methods: {
    submit() {
      this.$inertia.post(this.route('client-payments.store'), this.form, {
        onStart: () => this.sending = true,
        onFinish: () => this.sending = false,
      })
    },

    // BatangasTransactionDetail
    totalBatangasQty(yr, mo, product) {
      var totalQty = Object.entries(this.form.transactions.batangasDetails).reduce((acc, [year, months]) => {
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
      var totalAmt = Object.entries(this.form.transactions.batangasDetails).reduce((acc, [year, months]) => {
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
      var totalQty = Object.entries(this.form.transactions.mindoroDetails).reduce((acc, [year, months]) => {
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
      var totalAmt = Object.entries(this.form.transactions.mindoroDetails).reduce((acc, [year, months]) => {
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

    onSearchClientChange: throttle(function(term) {
      this.$inertia.get(this.route('client-payments.create'), {term}, {
        preserveState: true,
        preserveScroll: true,
        replace: true,
      })
    }, 300),
    onSelectedClient(client) {
      this.form.client.id = client.id

      this.getTransactions(client.id);
    },

    getTransactions(clientId) {
      axios.get(`/clients/${clientId}`)
        .then(({ data }) => {
            this.form.transactions = data;
         });
    },
  },
}
</script>

<style src="vue2-datepicker/index.css"></style>

<style>
  .highlight-yellow label.form-label {
    @apply text-white;
  }

  .multiselect__single, .multiselect__option {
    font-size: 0.875rem;
  }

  .multiselect__tag {
    @apply bg-blue-500;
  }

  .multiselect__tag-icon:after {
    @apply text-white;
  }

  .multiselect__tag-icon:hover {
    @apply bg-blue-600;
  }

  .multiselect__option--highlight {
    @apply bg-blue-500;
  }

  /*.multiselect__element span {}*/

  .multiselect--active {
    position: relative;
    z-index: 100;
  }

  .month-picker__container, .month-picker__container  {
    position: relative;
    z-index: 100;
    background-color: #fff;
  }
</style>
