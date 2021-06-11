<template>
  <div>
    <h1 class="font-bold text-3xl">
      <inertia-link class="text-blue-600 hover:text-blue-800" :href="route('monthly-mindoro-transactions.index')">Mindoro Transaction</inertia-link>
      <span class="text-blue-600 font-medium">/</span> {{ updateForm.date }}
    </h1>

    <div class="p-1">
      <div class="w-full pt-4">
        <!-- Update Existing Transaction -->
        <div class="overflow-hidden w-full mb-8 -mt-4">
          <form @submit.prevent="updateMindoroTransaction">
            <!-- Transaction -->
            <div class="-mr-6 mb-4 flex space-between w-full mt-8 px-4">
              <div class="w-full flex flex-wrap justify-start">
                <div class="mr-12">

                  <date-picker v-model="updateForm.date" type="month" placeholder="Select month" value-type="format" :formatter="momentFormatMonth"></date-picker>
                  <button class="ml-2 btn-indigo" @click.prevent="addNewTransactionForm">Add ({{ updateForm.transactions.length }})</button>
                  <div v-if="errors.date" class="form-error">{{ errors.date }}</div>
                </div>
              </div>
              <div class="px-4">
                <!-- <button class="btn-indigo" @click.prevent="toggleAllRow">Toggle All</button> -->

                <button @click.prevent="toggleAllRow" type="button" tabindex="-1" class="text-xs font-bold">
                  <!-- <span class="mr-2">Close</span> -->
                  <icon v-if="opened.length" name="toggle-on" class="block w-10 h-10 fill-gray-600" />
                  <icon v-else name="toggle-off" class="block w-10 h-10 fill-gray-600" />
                  <!-- <span class="ml-2 ">Open</span> -->
                </button>
              </div>
            </div>

            <div class="bg-white rounded shadow overflow-x-auto">
              <table width="100%" class="w-full whitespace-no-wrap" cellpadding="5">
                <colgroup>
                  <col span="1" style="width: 10%;">
                  <col span="1" style="width: 20%;">
                  <col span="1" style="width: 20%;">
                  <col span="1" style="width: 20%;">
                  <col span="1" style="width: 20%;">
                  <col span="1" style="width: 10%;">
                </colgroup>
                <thead>
                  <tr class="text-left font-bold">
                    <th align="center" class="px-6 pt-6 pb-4">Trip No.</th>
                    <th align="center" class="px-6 pt-6 pb-4">Driver</th>
                    <th align="center" class="px-6 pt-6 pb-4">Helper</th>
                    <th align="center" class="px-6 pt-6 pb-4">Tanker</th>
                    <th align="right" colspan="2" class="text-green-600 px-6 pt-6 pb-4">Total: {{ toPHP(getNetTotal()) }}</th>
                  </tr>
                </thead>
                <tbody v-for="(transaction, transactionIndex) in updateForm.transactions" :key="transactionIndex" class="bg-white rounded shadow mb-8 hover:bg-gray-100 focus-within:bg-gray-100" :id="`transaction-${transaction.id}`">
                  <tr class="bg-gradient-to-r from-yellow-500 to-blue-600">
                    <td class="border-t">
                      <text-input v-model="transaction.trip_no" :error="errors[`transactions.${transactionIndex}.trip_no`]"/>
                    </td>
                    <td class="border-t">
                      <select :id="`driver-${transactionIndex}`" v-model="transaction.driver.id" class="form-select" :class="{ error: errors[`transactions.${transactionIndex}.driver.id`] }">
                        <option :value="null" />
                        <option v-for="driver in drivers" :key="driver.id" :value="driver.id">{{ driver.name }}</option>
                      </select>
                    </td>
                    <td class="border-t">
                      <select :id="`helper-${transactionIndex}`" v-model="transaction.helper.id" class="form-select" :class="{ error: errors[`transactions.${transactionIndex}.helper.id`] }">
                        <option :value="null" />
                        <option v-for="helper in helpers" :key="helper.id" :value="helper.id">{{ helper.name }}</option>
                      </select>
                    </td>
                    <td class="border-t">
                      <select :id="`tanker-${transactionIndex}`" v-model="transaction.tanker.id" class="form-select" :class="{ error: errors[`transactions.${transactionIndex}.tanker.id`] }">
                        <option :value="null" />
                        <option v-for="tanker in tankers" :key="tanker.id" :value="tanker.id">{{ tanker.plate_no }}</option>
                      </select>
                    </td>
                    <td @click="toggleRow(transactionIndex)" class="border-t text-blue-100 font-semibold text-sm" align="center">
                      {{ toPHP(transactionTotalAmt(transaction.id) - getLoadTotalAmt(transaction.id) - transaction.expense) }}
                    </td>
                    <td class="border-t" align="right">
                      <button @click.prevent="deleteTransactionForm(transactionIndex, transaction.id)" type="button" class="font-bold flex-shrink-0 leading-none" tabindex="-1">
                        <!-- {{ transactionIndex + 1 }} -->
                        <icon name="trash" class="w-4 h-4 mr-2 fill-red-600"/>
                      </button>
                    </td>
                  </tr>

                  <tr v-if="opened.includes(transactionIndex)">
                     <!-- Details table form -->
                    <td colspan="6">
                       <div class="mb-6 overflow-x-auto -mt-4 pb-4">
                         <table class="min-w-full mt-8 shadow divide-y divide-gray-200">
                           <colgroup>
                             <col span="1" style="width: 15%;">
                             <col span="1" style="width: 23%;">
                             <col span="1" style="width: 12%;">
                             <col span="1" style="width: 10%;">
                             <col span="1" style="width: 10%;">
                             <col span="1" style="width: 10%;">
                             <col span="1" style="width: 10%;">
                             <col span="1" style="width: 10%;">
                           </colgroup>
                           <thead class="bg-gray-50">
                             <tr>
                               <th scope="col" class="px-6 text-center py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                 Date<span class="text-red-500">*</span>
                               </th>
                               <th scope="col" class="px-6 text-center py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                 Client<span class="text-red-500">*</span>
                               </th>
                               <th scope="col" class="px-6 text-center py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                  Remarks
                               </th>
                               <th scope="col" class="px-6 text-center py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                 Product<span class="text-red-500">*</span>
                               </th>
                               <th scope="col" class="px-6 text-center py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                 Quantity
                               </th>
                               <th scope="col" class="px-6 text-center py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                 U.Price
                               </th>
                               <th scope="col" class="px-6 text-center py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                 Amount
                               </th>
                               <th scope="col" class="px-6 text-center py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                 DR#
                               </th>
                               <th scope="col" class="px-6 text-center py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                 <button @click.prevent="addNewDetailForm(transactionIndex)">
                                   <icon name="plus" class="w-4 h-4 mr-2 fill-green-600"/>
                                 </button>
                               </th>
                             </tr>
                           </thead>
                           <tbody class="bg-white divide-y divide-gray-200">
                             <tr v-for="(detail, detailIndex) in transaction.details" :key="detailIndex">
                               <td>
                                 <div class="text-sm font-medium text-gray-900">
                                   <date-picker style="width: 160px" v-model="detail.date" lang="en" value-type="format" :formatter="momentFormatDate"></date-picker>
                                 </div>
                               </td>
                               <td>
                                 <div class="text-sm font-medium text-gray-900 -mt-3">
                                   <multiselect :id="[transactionIndex, detailIndex]" v-model="detail.selected_client"
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
                               </td>
                               <td class="text-sm text-gray-500">
                                 <div class="text-sm font-medium text-gray-900">
                                    <text-input v-model="detail.remarks" />
                                 </div>
                               </td>
                               <td>
                                 <div class="text-sm font-medium text-gray-900">
                                   <select :id="`product-${detailIndex}`" v-model="detail.product_id" class="form-select" :class="{ error: errors[`transactions.${transactionIndex}.details.${detailIndex}.product_id`] }">
                                     <option :value="null" />
                                     <option v-for="product in products" :key="product.id" :value="product.id">{{ product.name }}</option>
                                   </select>
                                 </div>
                               </td>
                               <td class="text-sm text-gray-500">
                                 <div class="text-sm font-medium text-gray-900">
                                   <text-input type="number" step="any" v-model="detail.quantity" :error="errors.quantity" />
                                 </div>
                               </td>
                               <td class="text-sm text-gray-500">
                                 <div class="text-sm font-medium text-gray-900">
                                   <text-input type="number" step="any" v-model="detail.unit_price" :error="errors.unit_price" />
                                 </div>
                               </td>
                               <td class="text-sm text-gray-500">
                                 <div class="text-sm font-medium text-gray-900 text-center">
                                   {{ totalCurrency(detail.quantity, detail.unit_price) }}
                                 </div>
                               </td>
                               <td class="text-sm text-gray-500">
                                 <div class="text-sm font-medium text-gray-900">
                                   <text-input v-model="detail.dr_no"/>
                                 </div>
                               </td>
                               <td class="text-sm text-gray-500">
                                 <div class=" px-5 text-sm font-medium text-gray-900">
                                 <!-- <div class="px-5 py-4 whitespace-nowrap text-left text-xs font-medium text-gray-500 uppercase bg-white"> -->
                                   <button @click.prevent="deleteTransactionDetailForm(transactionIndex, detailIndex, detail.id)" type="button" class="bg-white py-1 px-1 flex-shrink-0 text-sm leading-none" tabindex="-1">
                                     <icon name="trash" class="w-4 h-4 mr-2 fill-red-600"/>
                                   </button>
                                 </div>
                               </td>
                             </tr>

                             <!-- Total -->
                             <tr class="bg-gray-200">
                               <td>
                                 <div class="px-6 py-4 whitespace-nowrap text-left text-sm font-medium text-gray-500 uppercase">Total:</div>
                               </td>
                               <td>
                                 <div class="px-6 py-4 whitespace-nowrap text-left text-sm font-medium text-gray-500 uppercase"></div>
                               </td>
                               <td>
                                 <div class="px-6 py-4 whitespace-nowrap text-left text-sm font-medium text-gray-500 uppercase"></div>
                               </td>
                               <td>
                                 <div class="px-6 py-4 whitespace-nowrap text-left text-sm font-medium text-gray-500 uppercase"></div>
                               </td>
                               <td>
                                 <div class="px-6 py-4 whitespace-nowrap text-left text-sm font-medium text-gray-500 uppercase">
                                   {{ transactionTotalQty(transaction.id) }}
                                 </div>
                               </td>
                               <td>
                                 <div class="px-6 py-4 whitespace-nowrap text-left text-sm font-medium text-gray-500 uppercase"></div>
                               </td>
                               <td>
                                 <div class="px-6 py-4 whitespace-nowrap text-left text-sm font-medium text-gray-500 uppercase">
                                   {{ toPHP(transactionTotalAmt(transaction.id)) }}
                                 </div>
                               </td>
                               <td>
                                 <div class="px-6 py-4 whitespace-nowrap text-left text-sm font-medium text-gray-500 uppercase"></div>
                               </td>
                               <td>
                                 <div class="px-6 py-4 whitespace-nowrap text-left text-sm font-medium text-gray-500 uppercase"></div>
                               </td>
                             </tr>
                           </tbody>
                         </table>
                       </div>
                       <!-- /Details table form -->

                      <!-- TankerLoad -->
                      <div class="flex flex-wrap px-8">
                        <div class="grid grid-cols-1 gap-1 bg-white rounded overflow-x-auto">
                          <div class="rounded overflow-x-auto mb-4" v-for="(load, loadIndex) in transaction.tanker_loads" :key="load.id" :value="load.id">
                            <a :href="`/purchases/${load.purchase.id}/edit#load-${load.id}`" target="_blank">
                              <p class="text-sm bg-blue-600 font-bold pl-4 mb-2 rounded text-center py-2 text-white">{{ load.purchase.purchase_no }}</p>
                            </a>

                            <div class="mt-2 mb-1 rounded">
                              <div>
                                <!-- TankerLoadDetail Table -->
                                <table class="min-w-full shadow divide-y divide-gray-200">
                                  <colgroup>
                                    <col span="1" style="width: 25%;">
                                    <col span="1" style="width: 25%;">
                                    <col span="1" style="width: 25%;">
                                    <col span="1" style="width: 25%;">
                                  </colgroup>
                                  <thead>
                                    <tr>
                                      <th scope="col" class="px-6 text-center py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                        Product
                                      </th>
                                      <th scope="col" class="px-6 text-center py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                        Quantity
                                      </th>
                                      <th scope="col" class="px-6 text-center py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                        Unit Price
                                      </th>
                                      <th scope="col" class="px-6 text-center py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                        Amount
                                      </th>
                                    </tr>
                                  </thead>
                                  <tbody class="bg-white divide-y divide-gray-200">
                                    <tr v-for="detail in load.tanker_load_details">
                                      <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <div class="text-sm text-gray-900">{{ detail.product.name }}</div>
                                      </td>
                                      <td class="text-center px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <div class="text-sm text-gray-900">{{ quantityFormat(detail.quantity) }}</div>
                                      </td>
                                      <td class="text-sm text-gray-500 text-center">
                                        <div class="text-sm font-medium text-gray-900">
                                          <text-input type="number" step="any" v-model="detail.unit_price" :error="errors.unit_price" />
                                        </div>
                                      </td>
                                      <td class="text-center px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <div class="text-sm text-gray-900">{{ totalCurrency(detail.quantity, detail.unit_price) }}</div>
                                      </td>
                                    </tr>
                                  </tbody>
                                </table>
                              </div>
                            </div>
                          </div>
                        </div>
                        <!-- /Tanker Load -->

                        <div class="xl:ml-12 rounded">
                          <table class="shadow divide-y divide-gray-200">
                            <tbody class="bg-white divide-y divide-gray-200">
                              <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                  Transaction:
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-yellow-600 font-semibold">
                                 {{ toPHP(transactionTotalAmt(transaction.id)) }}
                                </td>
                              </tr>
                              <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                  Load:
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-blue-600 font-semibold">
                                  {{ toPHP(getLoadTotalAmt(transaction.id)) }}
                                </td>
                              </tr>
                              <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                  Expenses:
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 font-semibold">
                                  <div class="text-sm font-medium text-gray-900">
                                    <text-input type="number" step="any" v-model="transaction.expense" :error="errors.expense" />
                                  </div>
                                </td>
                              </tr>
                              <tr class="bg-gray-200">
                                <td>
                                  <div class="px-6 py-4 whitespace-nowrap text-left text-sm font-medium text-gray-500 uppercase">
                                    Net:
                                  </div>
                                </td>
                                <td>
                                  <div class="px-6 py-4 whitespace-nowrap text-left text-sm font-semibold text-gray-500">
                                    {{ toPHP(transactionTotalAmt(transaction.id) - getLoadTotalAmt(transaction.id) - transaction.expense) }}
                                  </div>
                                </td>
                              </tr>
                            </tbody>
                          </table>
                        </div>
                      </div>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>

            <div class="px-8 py-4 bg-gray-100 border-t border-gray-200 flex justify-end items-center">
              <button v-if="!monthly_mindoro_transaction.deleted_at" class="text-red-600 hover:underline" tabindex="-1" type="button" @click="destroy">Delete Transaction</button>
              <loading-button :loading="sending" class="btn-indigo ml-auto" type="submit">Update Transaction</loading-button>
            </div>
          </form>
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
import Multiselect from 'vue-multiselect'
import DatePicker from 'vue2-datepicker'
import moment from 'moment'
import {throttle} from 'lodash'
import { numberFormatsMixin } from '@/Mixins/numberFormatsMixin'

export default {
  mixins: [numberFormatsMixin],
  metaInfo() {
    return { title: `MDO ${this.updateForm.date}` }
  },
  layout: Layout,
  components: {
    Icon,
    LoadingButton,
    SelectInput,
    TextInput,
    TrashedMessage,
    DatePicker,
    Multiselect,
  },
  props: {
    errors: Object,
    monthly_mindoro_transaction: Object,
    drivers: Array,
    helpers: Array,
    tankers: Array,
    // purchases: {
    //   type: Array,
    //   default: () => [],
    // },
    clients: {
      type: Array,
      default: () => [],
    },
    products: Array,
  },
  remember: 'form',
  data() {
    return {
      opened: [],
      sending: false,
      momentFormatMonth: {
        //[optional] Date to String
        stringify: (date) => {
          return date ? moment(date).format('MMMM, YYYY') : ''
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
      momentFormatDate: {
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
      updateForm: {
        date: `${this.monthly_mindoro_transaction.month}, ${this.monthly_mindoro_transaction.year}`,
        transactions: this.monthly_mindoro_transaction.transactions,
        removed_transactions: [],
        removed_transaction_details: [],
        // purchases: [],
        // selectedPurchases: this.monthly_mindoro_transaction.selected_purchases,
      },
    }
  },
  methods: {
    toggleRow(id) {
      const index = this.opened.indexOf(id);
      if (index > -1) {
        this.opened.splice(index, 1)
      } else {
        this.opened.push(id)
      }
    },

    toggleAllRow() {
      if (this.opened.length) {
        this.opened = [];
      } else {
        this.updateForm.transactions.forEach((transaction, index) => {
          this.opened.push(index);
        });
      }
    },

    showDate (date) {
      this.updateForm.date = date;
    },

    updateMindoroTransaction() {
      this.$inertia.put(this.route('monthly-mindoro-transactions.update', this.monthly_mindoro_transaction.id), this.updateForm, {
        onStart: () => this.sending = true,
        onFinish: () => this.sending = false,
      });
    },
    destroy() {
      if (confirm('Are you sure you want to permanently delete this Mindoro Transactions and its details?')) {
        this.$inertia.delete(this.route('monthly-mindoro-transactions.destroy', this.monthly_mindoro_transaction.id))
      }
    },

    // Multiselect
    // onSearchPurchaseChange: throttle(function(term) {
    //   this.$inertia.get(this.route('mindoro-transactions.edit'), {term}, {
    //     preserveState: true,
    //     preserveScroll: true,
    //     replace: true,
    //   })
    // }, 300),
    // onSelectedPurchase(purchases) {
    //   this.updateForm.purchases = purchases.map(purchase => purchase.id);
    // },

    onSearchClientChange: throttle(function(term) {
      this.$inertia.get(this.route('mindoro-transactions.edit'), {term}, {
        preserveState: true,
        preserveScroll: true,
        replace: true,
      })
    }, 300),
    onSelectedClient(client, id) {
      this.updateForm.transactions[id[0]].details[id[1]].client_id = client.id;
    },

    // MindoroTransaction
    addNewTransactionForm() {
      this.updateForm.transactions.push({
        id: null,
        trip_no: null,
        tanker: {
          id: null,
          plate_no: null,
        },
        driver: {
          id: null,
          name: null,
        },
        helper: {
          id: null,
          name: null,
        },
        expense: 0,
        details: [
          {
            id: null,
            date: null,
            dr_no: null,
            quantity: 0,
            unit_price: null,
            mindoro_transaction_id: null,
            product_id: null,
            selected_client: null,
            client_id: null,
            remarks: null,
          }
        ],
        tanker_loads: [
          {
            id: null,
            mindoro_transaction_id: null,
            batangas_transaction_id: 0,
            remarks: null,
            purchase: {
              purchase_no: null,
            },
            tanker_load_details: [
              {
                id: null,
                tanker_load_id: null,
                product: {
                  name: null,
                },
                quantity: null,
                unit_price: null,
              }
            ],
          }
        ],
      });
    },
    deleteTransactionForm(transactionIndex, transactionId) {
      if (transactionId) {
        this.updateForm.removed_transactions.push(transactionId);
        this.updateForm.transactions.splice(transactionIndex, 1);
      } else{
        this.updateForm.transactions.splice(transactionIndex, 1);
      }
    },

    // MindoroTransactionDetail
    addNewDetailForm(transactionIndex) {
      this.updateForm.transactions[transactionIndex].details.push({
        id: null,
        date: null,
        dr_no: null,
        quantity: 0,
        unit_price: null,
        mindoro_transaction_id: null,
        product_id: null,
        client_id: null,
        remarks: null,
      });
    },

    deleteTransactionDetailForm(transactionIndex, detailIndex, transactionDetailId) {
      if (transactionDetailId) {
        this.updateForm.removed_transaction_details.push(transactionDetailId);
        this.updateForm.transactions[transactionIndex].details.splice(detailIndex, 1);
      } else{
        this.updateForm.transactions[transactionIndex].details.splice(detailIndex, 1);
      }
    },

    // Transaction Total Amount
    transactionTotalAmt(transactionId) {
      for (var i = 0; i < this.updateForm.transactions.length; i++) {
        if (this.updateForm.transactions[i].id === transactionId) {

          var totalAmt = this.updateForm.transactions[i].details.reduce(function (acc, detail) {
            acc += parseFloat(detail.quantity) * parseFloat(detail.unit_price);
            return acc;
          }, 0);

          return totalAmt;
        }
      }
    },

    // Transaction Total Quantity
    transactionTotalQty(transactionId) {
      for (var i = 0; i < this.updateForm.transactions.length; i++) {
        if (this.updateForm.transactions[i].id === transactionId) {

          var totalQty = this.updateForm.transactions[i].details.reduce(function (acc, detail) {
            acc += parseFloat(detail.quantity);
            return acc;
          }, 0);

          return this.quantityFormat(totalQty);
        }
      }
    },

    // Load Total Amount
    getLoadTotalAmt(transactionId) {
      for (var i = 0; i < this.updateForm.transactions.length; i++) {
        if (this.updateForm.transactions[i].id === transactionId) {

          const detailsArray = this.updateForm.transactions[i].tanker_loads.map(load => load.tanker_load_details);
          const details = [].concat.apply([], detailsArray);

          var totalAmt = details.reduce(function (acc, detail) {
            acc += parseFloat(detail.quantity) * parseFloat(detail.unit_price);
            return acc;
          }, 0);

          return totalAmt;
        }
      }
    },

    // Total Net
    getNetTotal() {
      var netTotal = 0;

      for (var i = 0; i < this.updateForm.transactions.length; i++) {

        // TransactionDetail
        var transactionTotalAmt = this.updateForm.transactions[i].details.reduce((transactionDetailAcc, detail) => {
          transactionDetailAcc += parseFloat(detail.quantity) * parseFloat(detail.unit_price);
          return transactionDetailAcc;
        }, 0);

        // TankerLoadDetail
        const loadDetailsArray = this.updateForm.transactions[i].tanker_loads.map(load => load.tanker_load_details);
        const loadDetails = [].concat.apply([], loadDetailsArray);

        var loadTotalAmt = loadDetails.reduce(function (loadDetailAcc, detail) {
          loadDetailAcc += parseFloat(detail.quantity) * parseFloat(detail.unit_price);
          return loadDetailAcc;
        }, 0);

        // Net
        netTotal += transactionTotalAmt - loadTotalAmt - this.updateForm.transactions[i].expense;

      }
      return netTotal;

    },
  },
  watch: {
    // 'updateForm.selectedPurchases': {
    //   handler: function (purchases) {
    //     this.updateForm.purchases = purchases.map(purchase => purchase.id);

    //     this.getLoadTotalAmt(purchases);
    //   },
    //   deep: true,
    // },
    // 'updateForm.trip_no': function (value) {
    //   this.getLoadTotalAmt(this.updateForm.selectedPurchases);
    // },
  },
  mounted() {
    // // this.selectedPurchase = this.purchases.find(purchase => purchase.id === this.updateForm.purchase_id);
    // this.updateForm.selectedPurchases.forEach(purchase => {
    //   this.updateForm.purchases.push(purchase.id);
    // });

    this.toggleAllRow();

    this.updateForm.transactions.forEach(transaction => {
      transaction.details.forEach(detail => {
        detail.selected_client = this.clients.find(client => client.id === detail.client_id);
      });
    });

    // this.getLoadTotalAmt(this.updateForm.transactions);
  }
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
