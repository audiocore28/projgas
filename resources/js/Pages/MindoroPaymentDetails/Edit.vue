<template>
  <div>
    <h1 class="mb-6 font-bold text-3xl">
      <inertia-link class="text-blue-600 hover:text-blue-800" :href="route('clients.index')">Clients</inertia-link>
      <span class="text-blue-600 font-medium">/</span> {{ client.name }}
    </h1>

    <trashed-message v-if="client.deleted_at" class="mb-6" @restore="restore" :canRestore="$page.auth.user.can.client.restore">
      This client has been deleted.
    </trashed-message>

    <div class="p-1">
      <form @submit.prevent="updateMindoroPayment">
        <loading-button :loading="sending" class="btn-indigo mb-10" type="submit" v-if="$page.auth.user.can.client.updatePayment">
          Update Payment
        </loading-button>
        <div class="bg-white rounded shadow overflow-x-auto mb-8 -mt-4">
          <div class="rounded shadow overflow-x-auto mx-4 my-8" v-for="(months, year) in form.mindoroDetails">
            <h1 class="font-semibold text-center">{{ year }}</h1>
            <div class="mt-6 mb-1" v-for="(transactionDetails, month) in months">
              <div class="text-sm font-bold bg-gradient-to-r from-yellow-500 to-blue-600 text-white px-4 py-1 flex justify-between">
                <h2>{{ month }}</h2>
                <div>
                  <span class="text-xs px-3 bg-yellow-500 text-white rounded-full">{{ `Dsl: ${totalMindoroQty(year, month, 'Diesel')}` }}</span>
                  <span class="text-xs px-3 bg-green-500 text-white rounded-full">{{ `Reg: ${totalMindoroQty(year, month, 'Regular')}` }}</span>
                  <span class="text-xs px-3 bg-red-500 text-white rounded-full">{{ `Prem: ${totalMindoroQty(year, month, 'Premium')}` }}</span>
                  <span class="text-xs px-3 bg-white text-blue-600 rounded-full">{{ `Amt: ${totalMindoroAmt(year, month)}` }}</span>
                </div>
              </div>
              <div>
                <table class="min-w-full divide-y divide-gray-200">
                  <colgroup>
                    <col span="1" style="width: 10%;">
                    <col span="1" style="width: 5%;">
                    <col span="1" style="width: 10%;">
                    <col span="1" style="width: 10%;">
                    <col span="1" style="width: 10%;">
                    <col span="1" style="width: 10%;">
                    <col span="1" style="width: 10%;">
                    <col span="1" style="width: 10%;">
                    <col span="1" style="width: 10%;">
                    <col span="1" style="width: 10%;">
                    <col span="1" style="width: 5%;">
                  </colgroup>
                  <thead class="bg-gray-50">
                    <tr>
                      <th align="center" scope="col" class="px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Date
                      </th>
                      <th align="center" scope="col" class="px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Code
                      </th>
                      <th align="center" scope="col" class="px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Client
                      </th>
                      <th align="center" scope="col" class="px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Remarks
                      </th>
                      <th align="center" scope="col" class="px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Product
                      </th>
                      <th align="center" scope="col" class="px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Quantity
                      </th>
                      <th align="center" scope="col" class="px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Unit Price
                      </th>
                      <th align="center" scope="col" class="px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Amount
                      </th>
                      <th align="center" scope="col" class="px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">
                        DR#
                      </th>
                      <th align="center" scope="col" class="px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">
                        SI#
                      </th>
                      <th align="center" scope="col" class="px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Payment
                      </th>
                    </tr>
                  </thead>
                  <tbody class="bg-white divide-y divide-gray-200" v-for="(detail, transactionDetailIndex) in transactionDetails" :key="transactionDetailIndex">
                    <tr>
                      <td class="py-2 whitespace-nowrap" align="center">
                        <div class="text-sm font-medium text-gray-900">
                          {{ detail.date }}
                        </div>
                      </td>
                      <td align="center" class="py-2 whitespace-nowrap">
                        <a class="text-sm font-medium text-blue-600" target="_blank" :href="`/monthly-mindoro-transactions/${detail.mindoro_transaction.monthly_mindoro_transaction_id}/edit#transaction-${detail.mindoro_transaction_id}`" v-if="$page.auth.user.can.mindoroTransaction.update">
                            {{ detail.mindoro_transaction.trip_no }}
                        </a>
                        <span class="text-sm font-medium text-gray-900" v-else>
                            {{ detail.mindoro_transaction.trip_no }}
                        </span>
                      </td>
                      <td align="center" class="py-2 whitespace-nowrap">
                        <div class="text-sm font-medium text-gray-900">
                          {{ detail.client.name }}
                        </div>
                      </td>
                      <td align="center" class="py-2 whitespace-nowrap">
                        <div class="text-sm font-medium text-gray-900">
                          {{ detail.remarks }}
                        </div>
                      </td>
                      <td align="center" class="py-2 whitespace-nowrap">
                        <div class="text-sm font-medium text-gray-900">
                          {{ detail.product.name }}
                        </div>
                      </td>
                      <td align="center" class="py-2 whitespace-nowrap text-sm text-gray-500">
                        <div class="text-sm font-medium text-gray-900">
                          {{ quantityFormat(detail.quantity) }}
                        </div>
                      </td>
                      <td align="center" class="py-2 whitespace-nowrap text-sm text-gray-500">
                        <div class="text-sm font-medium text-gray-900">
                          {{ toPHP(detail.unit_price) }}
                        </div>
                      </td>
                      <td align="center" class="py-2 whitespace-nowrap text-sm text-gray-500">
                        <div class="text-sm font-medium text-gray-900">
                          {{ totalCurrency(detail.quantity, detail.unit_price) }}
                        </div>
                      </td>
                      <td align="center" class="py-2 whitespace-nowrap">
                        <div class="text-sm font-medium text-gray-900">
                          {{ detail.dr_no }}
                        </div>
                      </td>
                      <td align="center" class="py-2 whitespace-nowrap">
                        <div class="text-sm font-medium text-gray-900" v-if="$page.auth.user.can.client.verifyPayment">
                          <text-input v-model="detail.si_no" />
                        </div>
                        <div class="text-sm font-medium text-gray-900" v-else>
                          {{ detail.si_no }}
                        </div>
                      </td>
                      <td class="py-2 whitespace-nowrap text-sm text-gray-500" align="center">
                        <div class="text-sm font-medium text-gray-900">
                         <button @click.prevent="addNewPaymentForm(year, month, transactionDetailIndex, detail.id)">
                           <icon name="plus" class="w-4 h-4 mr-2 fill-green-600"/>
                         </button>
                        </div>
                      </td>
                    </tr>
                    <!-- Payment Details -->
                    <tr v-if="detail.mindoro_payment_details.length !== 0">
                      <td colspan="11" align="center" class="p-2">
                        <table class="w-full lg:w-3/4">
                          <colgroup>
                            <col span="1" style="width: 20%;">
                            <col span="1" style="width: 20%;">
                            <col span="1" style="width: 20%;">
                            <col span="1" style="width: 20%;">
                            <col span="1" style="width: 10%;">
                            <col span="1" style="width: 10%;">
                          </colgroup>
                          <tbody>
                            <tr v-for="(payment, paymentIndex) in detail.mindoro_payment_details" :class="[payment.is_verified ? 'bg-blue-600' : 'bg-yellow-500']">
                              <td>
                                <div class="text-sm font-medium text-white p-3" v-if="payment.is_verified">
                                  {{ payment.date }}
                                </div>
                                <div class="text-sm font-medium text-gray-900" v-else>
                                  <date-picker v-model="payment.date" lang="en" value-type="format" :formatter="momentFormat"></date-picker>
                                </div>
                              </td>
                              <td>
                                <div class="text-sm font-medium text-white p-3" v-if="payment.is_verified">
                                  {{ payment.mode }}
                                </div>
                                <div class="text-sm font-medium text-gray-900" v-else>
                                  <select v-model="payment.mode" class="form-select">
                                    <option :value="null" />
                                    <option v-for="(mode, index) in modes" :key="index" :value="mode">{{ mode }}</option>
                                  </select>
                                </div>
                              </td>
                              <td class="text-sm text-gray-500">
                                <div class="text-sm font-medium text-white p-3" v-if="payment.is_verified">
                                  {{ toPHP(payment.amount) }}
                                </div>
                                <div class="text-sm font-medium text-gray-900" v-else>
                                  <text-input type="number" step="any" v-model="payment.amount" :error="errors.unit_price" />
                                </div>
                              </td>
                              <td class="text-sm text-gray-500">
                                <div class="text-sm font-medium text-white p-3" v-if="payment.is_verified">
                                  {{ payment.remarks }}
                                </div>
                                <div class="text-sm font-medium text-gray-900" v-else>
                                  <text-input v-model="payment.remarks" placeholder="remarks" />
                                </div>
                              </td>
                              <td class="text-sm text-gray-500" align="center" v-if="$page.auth.user.can.client.verifyPayment">
                                <div class="text-sm font-medium text-gray-900">
                                  <input type="checkbox" class="w-5 h-5" v-model="payment.is_verified">
                                </div>
                              </td>
                              <td class="text-sm text-gray-500" align="center">
                                <div class="text-sm font-medium text-white p-3" v-if="payment.is_verified">
                                  <span v-if="!$page.auth.user.can.client.verifyPayment">&#10004;</span> verified
                                </div>
                                <div class=" px-5 text-sm font-medium text-gray-900" v-else>
                                  <button @click.prevent="deletePaymentForm(year, month, transactionDetailIndex, paymentIndex, payment.id)" type="button" class="py-1 px-1 flex-shrink-0 text-sm leading-none" tabindex="-1">
                                    <icon name="trash" class="w-4 h-4 mr-2 fill-red-600"/>
                                  </button>
                                </div>
                              </td>
                            </tr>
                          </tbody>
                        </table>
                      </td>
                    </tr>

                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </form>
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
import { numberFormatsMixin } from '@/Mixins/numberFormatsMixin'

export default {
  mixins: [
    loadMoreMindoroTransactionMixin('clients'),
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
      modes: ['Cash', 'Cheque'],
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
    }
  },
  methods: {
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
  },
}
</script>

<style src="vue2-datepicker/index.css"></style>
