<template>
  <div>
    <h1 class="mb-8 font-bold text-3xl">
      <inertia-link class="text-blue-600 hover:text-blue-800" :href="route('monthly-mindoro-transactions.index')">Mindoro Transaction</inertia-link>
      <span class="text-blue-600 font-medium">/</span> {{ `${updateForm.date.year}, ${updateForm.date.month}`}}
    </h1>

    <div class="p-1">
      <div class="w-full pt-4">
        <!-- Update Existing Transaction -->
        <div class="overflow-hidden max-w-6xl mb-8 -mt-4">
          <form @submit.prevent="updateMindoroTransaction">
            <!-- Transaction -->
            <div class="-mr-6 mb-4 flex space-between w-full mt-8 px-4">
              <div class="w-full flex flex-wrap justify-start">
                <div class="-mt-6 mr-12">
                  <label class="form-label block">Month:<span class="text-red-500">*</span></label>
                  <month-picker-input @input="showDate" :no-default="true"></month-picker-input>
                </div>
                <div class="h-10">
                  <button class="btn-indigo" @click.prevent="addNewTransactionForm">Add ({{ updateForm.transactions.length }})</button>
                </div>

<!--                 <div class="pr-6 pb-4 lg:w-1/2">
                  <label class="form-label block">Purchase No.<span class="text-red-500">*</span></label>
                  <multiselect
                    id="purchase_id"
                    name="purchases[]"
                    v-model="updateForm.selectedPurchases"
                    placeholder=""
                    class="mt-3 text-xs z-20"
                    :options="purchases"
                    label="purchase_no"
                    track-by="id"
                    @search-change="onSearchPurchaseChange"
                    @input="onSelectedPurchase"
                    :show-labels="false"
                    :multiple="true"
                  ></multiselect>
                </div>

                <div class="lg:w-1/3">
                  <label class="form-label block mr-5">Date:<span class="text-red-500">*</span></label>
                  <div class="pr-6 pb-4 mt-3">
                    <date-picker v-model="updateForm.date" :error="errors.date" lang="en" value-type="format" :formatter="momentFormat"></date-picker>
                    <div v-if="errors.date" class="form-error">{{ errors.date }}</div>
                  </div>
                </div>
 -->
              </div>
              <div>
                <button class="btn-indigo" @click.prevent="toggleAllRow">Toggle All</button>
              </div>
            </div>

            <div v-for="(transaction, transactionIndex) in updateForm.transactions" :key="transactionIndex" class="bg-white rounded shadow mb-8">
              <div @click="toggleRow(transactionIndex)" class="flex justify-between bg-gradient-to-r from-yellow-500 to-blue-600 rounded pl-6 pt-4 highlight-yellow">
                <div class="flex flex-wrap">
                  <text-input v-model="transaction.trip_no" :error="errors.trip_no" class="pr-6 pb-4 w-full lg:w-1/6" label="Trip No.*" />

                  <div class="pr-6 pb-4 w-full lg:w-1/4">
                    <label class="form-label" :for="`driver-${transactionIndex}`">Driver:<span class="text-red-500">*</span></label>
                    <select :id="`driver-${transactionIndex}`" v-model="transaction.driver.id" class="form-select">
                      <option :value="null" />
                      <option v-for="driver in drivers" :key="driver.id" :value="driver.id">{{ driver.name }}</option>
                    </select>
                  </div>

                  <div class="pr-6 pb-4 w-full lg:w-1/4">
                    <label class="form-label" :for="`helper-${transactionIndex}`">Helper:<span class="text-red-500">*</span></label>
                    <select :id="`helper-${transactionIndex}`" v-model="transaction.helper.id" class="form-select">
                      <option :value="null" />
                      <option v-for="helper in helpers" :key="helper.id" :value="helper.id">{{ helper.name }}</option>
                    </select>
                  </div>

                  <div class="pr-6 pb-4 w-full lg:w-1/4">
                    <label class="form-label" :for="`tanker-${transactionIndex}`">Tanker:<span class="text-red-500">*</span></label>
                    <select :id="`tanker-${transactionIndex}`" v-model="transaction.tanker.id" class="form-select">
                      <option :value="null" />
                      <option v-for="tanker in tankers" :key="tanker.id" :value="tanker.id">{{ tanker.plate_no }}</option>
                    </select>
                  </div>
                </div>
                <div class="mr-5">
                  <span class="p-1 rounded-full text-yellow-300 text-xs ml-2">
                    <button @click.prevent="deleteTransactionForm(transactionIndex, transaction.id)" type="button" class="font-bold p-1 flex-shrink-0 leading-none" tabindex="-1">
                      {{ transactionIndex + 1 }}
                    </button>
                  </span>
                </div>
              </div>

               <!-- Details table form -->
               <div v-if="opened.includes(transactionIndex)" class="mb-6 overflow-x-auto -mt-4 px-4 pb-4">
                 <table class="min-w-full mt-8 shadow divide-y divide-gray-200">
                   <colgroup>
                     <col span="1" style="width: 10%;">
                     <col span="1" style="width: 38%;">
                     <col span="1" style="width: 12%;">
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
                           <date-picker v-model="detail.date" lang="en" value-type="format" :formatter="momentFormat"></date-picker>
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
                       <td>
                         <div class="text-sm font-medium text-gray-900">
                           <select :id="`product-${detailIndex}`" v-model="detail.product_id" class="form-select" :class="{ error: errors[`detail.${detailIndex}.product_id`] }">
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
                <div v-if="opened.includes(transactionIndex)" class="flex flex-wrap px-8">
                  <div class="grid grid-cols-1 gap-1 bg-white rounded overflow-x-auto">
                    <div class="rounded overflow-x-auto mb-4" v-for="(load, loadIndex) in transaction.tanker_loads" :key="load.id" :value="load.id">
                      <p class="text-sm bg-blue-600 font-bold pl-4 mb-2 rounded text-center py-2 text-white">{{ load.purchase.purchase_no }}</p>

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
                              <!-- Total: -->
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
import { MonthPicker } from 'vue-month-picker'
import { MonthPickerInput } from 'vue-month-picker'
import moment from 'moment'
import {throttle} from 'lodash'
import { numberFormatsMixin } from '@/Mixins/numberFormatsMixin'

export default {
  mixins: [numberFormatsMixin],
  metaInfo() {
    return { title: `MDO ${this.updateForm.date.year}, ${this.updateForm.date.month}` }
  },
  layout: Layout,
  components: {
    Icon,
    LoadingButton,
    SelectInput,
    TextInput,
    TrashedMessage,
    DatePicker,
    MonthPicker,
    MonthPickerInput,
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
      updateForm: {
        date: {
          year: this.monthly_mindoro_transaction.year,
          month: this.monthly_mindoro_transaction.month,
        },
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
    }

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
