<template>
  <div>
    <h1 class="mb-8 font-bold text-3xl">
      <inertia-link class="text-blue-600 hover:text-blue-800" :href="route('mindoro-transactions.index')">Mindoro Transaction</inertia-link>
      <span class="text-blue-600 font-medium">/</span> {{ updateForm.client_id }}
    </h1>

    <div class="p-1">
      <div class="w-full pt-4">
        <!-- Update Existing Transaction -->
        <div class="bg-white rounded shadow overflow-hidden max-w-6xl mb-8 -mt-4">
          <form @submit.prevent="updateMindoroTransaction">

            <!-- Transaction -->
            <div class="-mr-6 -mb-12 flex flex-wrap w-full mt-8 px-8">
              <div class="w-full flex flex-wrap justify-between">
                <div class="pr-6 pb-4 lg:w-1/2">
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
                  <!-- :allow-empty="false" -->
                </div>
                <div class="lg:w-1/3">
                  <label class="form-label block mr-5">Date:<span class="text-red-500">*</span></label>
                  <div class="pr-6 pb-4 mt-3">
                    <date-picker v-model="updateForm.date" :error="errors.date" lang="en" value-type="format" :formatter="momentFormat"></date-picker>
                    <div v-if="errors.date" class="form-error">{{ errors.date }}</div>
                  </div>
                </div>
              </div>

              <div class="w-full flex flex-wrap mb-2 bg-yellow-500 rounded pl-6 pt-4 highlight-yellow">
                <text-input v-model="updateForm.trip_no" :error="errors.trip_no" class="pr-6 pb-4 w-full lg:w-1/6" label="Trip No.*" />
                <select-input v-model="updateForm.driver_id" :error="errors.driver_id" class="pr-6 pb-4 w-full lg:w-1/4" label="Driver">
                  <option :value="null" />
                  <option v-for="driver in drivers" :key="driver.id" :value="driver.id">{{ driver.name }}</option>
                </select-input>
                <select-input v-model="updateForm.helper_id" :error="errors.helper_id" class="pr-6 pb-4 w-full lg:w-1/4" label="Helper">
                  <option :value="null" />
                  <option v-for="helper in helpers" :key="helper.id" :value="helper.id">{{ helper.name }}</option>
                </select-input>
                <select-input v-model="updateForm.tanker_id" :error="errors.tanker_id" class="pr-6 pb-4 w-full lg:w-1/4" label="Tanker">
                  <option :value="null" />
                  <option v-for="tanker in tankers" :key="tanker.id" :value="tanker.id">{{ tanker.plate_no }}</option>
                </select-input>
              </div>
            </div>

            <!-- Details table form -->
            <div class="px-8 py-4 mb-6 overflow-x-auto">
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
                      <button @click.prevent="addNewDetailForm">
                        <icon name="plus" class="w-4 h-4 mr-2 fill-green-600"/>
                      </button>
                    </th>
                  </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                  <tr v-for="(details, index) in updateForm.details" :key="index">
                    <td>
                      <div class="text-sm font-medium text-gray-900">
                        <date-picker v-model="details.date" lang="en" value-type="format" :formatter="momentFormat"></date-picker>
                      </div>
                    </td>
                    <td>
                      <div class="text-sm font-medium text-gray-900 -mt-3">
                        <multiselect :id="index" v-model="details.selected_client"
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
                        <select :id="`product-${index}`" v-model="details.product_id" class="form-select" :class="{ error: errors[`details.${index}.product_id`] }">
                          <option :value="null" />
                          <option v-for="product in products" :key="product.id" :value="product.id">{{ product.name }}</option>
                        </select>
                      </div>
                    </td>
                    <td class="text-sm text-gray-500">
                      <div class="text-sm font-medium text-gray-900">
                        <text-input type="number" step="any" v-model="details.quantity" :error="errors.quantity" />
                      </div>
                    </td>
                    <td class="text-sm text-gray-500">
                      <div class="text-sm font-medium text-gray-900">
                        <text-input type="number" step="any" v-model="details.unit_price" :error="errors.unit_price" />
                      </div>
                    </td>
                    <td class="text-sm text-gray-500">
                      <div class="text-sm font-medium text-gray-900 text-center">
                        {{ totalCurrency(details.quantity, details.unit_price) }}
                      </div>
                    </td>
                    <td class="text-sm text-gray-500">
                      <div class="text-sm font-medium text-gray-900">
                        <text-input v-model="details.dr_no"/>
                      </div>
                    </td>
                    <td class="text-sm text-gray-500">
                      <div class=" px-5 text-sm font-medium text-gray-900">
                      <!-- <div class="px-5 py-4 whitespace-nowrap text-left text-xs font-medium text-gray-500 uppercase bg-white"> -->
                      <button @click.prevent="deleteDetailForm(index, details.id)" type="button" class="bg-white py-1 px-1 flex-shrink-0 text-sm leading-none" tabindex="-1">
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
                        {{ transactionTotalQty() }}
                      </div>
                    </td>
                    <td>
                      <div class="px-6 py-4 whitespace-nowrap text-left text-sm font-medium text-gray-500 uppercase"></div>
                    </td>
                    <td>
                      <div class="px-6 py-4 whitespace-nowrap text-left text-sm font-medium text-gray-500 uppercase">
                        {{ this.toPHP(transactionTotalAmt()) }}
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

            <!-- <div class="grid grid-cols-1 gap-3 xl:grid-cols-2 mb-8"> -->
            <div class="flex flex-wrap px-8">
              <!-- TankerLoad -->
              <div class="grid grid-cols-1 gap-1 bg-white rounded overflow-x-auto">
                <div class="rounded overflow-x-auto mb-4" v-for="purchase in updateForm.selectedPurchases" :key="purchase.id" :value="purchase.id">
                  <p class="text-sm bg-blue-600 font-bold pl-4 mb-2 rounded text-center py-2 text-white">{{ purchase.purchase_no }}</p>

                  <div class="mt-2 mb-1 rounded" v-for="load in purchase.tanker_loads">
                    <div v-if="load.trip_no === updateForm.trip_no">
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
                <!-- <p class="w-full text-sm bg-yellow-500 font-bold pl-4 mb-2 rounded text-center py-2 text-white">Computation</p> -->
                <table class="shadow divide-y divide-gray-200">
                  <tbody class="bg-white divide-y divide-gray-200">
                    <tr>
                      <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        Transaction:
                      </td>
                      <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-yellow-600 font-semibold">
                        {{ toPHP(transactionTotalAmt()) }}
                      </td>
                    </tr>
                    <tr>
                      <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        Load:
                      </td>
                      <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-blue-600 font-semibold">
                        {{ toPHP(totalLoad) }}
                      </td>
                    </tr>
                    <tr>
                      <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        <!-- Constant: -->
                      </td>
                      <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 font-semibold">
                        {{ toPHP(35000) }}
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
                          {{ toPHP(transactionTotalAmt() - totalLoad - 35000) }}
                        </div>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>

            <div class="px-8 py-4 bg-gray-100 border-t border-gray-200 flex justify-end items-center">
              <button v-if="!mindoro_transaction.deleted_at" class="text-red-600 hover:underline" tabindex="-1" type="button" @click="destroy">Delete Transaction</button>
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
  metaInfo: { title: 'Edit Mindoro Transaction' },
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
    mindoro_transaction: Object,
    drivers: Array,
    helpers: Array,
    tankers: Array,
    purchases: {
      type: Array,
      default: () => [],
    },
    clients: {
      type: Array,
      default: () => [],
    },
    products: Array,
  },
  remember: 'form',
  data() {
    return {
      totalLoad: 0,
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
        date: this.mindoro_transaction.date,
        trip_no: this.mindoro_transaction.trip_no,
        driver_id: this.mindoro_transaction.driver_id,
        helper_id: this.mindoro_transaction.helper_id,
        tanker_id: this.mindoro_transaction.tanker_id,
        purchases: [],
        details: this.mindoro_transaction.details,
        selectedPurchases: this.mindoro_transaction.selected_purchases,
      },
    }
  },
  methods: {
    updateMindoroTransaction() {
      this.$inertia.put(this.route('mindoro-transactions.update', this.mindoro_transaction.id), this.updateForm, {
        onStart: () => this.sending = true,
        onFinish: () => this.sending = false,
      });
    },
    destroy() {
      if (confirm('Are you sure you want to permanently delete this Mindoro Transaction and its details?')) {
        this.$inertia.delete(this.route('mindoro-transactions.destroy', this.mindoro_transaction.id))
      }
    },
    deleteDetailForm(index, productDetailId) {
      if (productDetailId) {
        if (confirm('Are you sure you want to delete this row?')) {
          this.$inertia.delete(this.route('mindoro-transaction-details.destroy', productDetailId));
          this.updateForm.details.splice(index, 1);
        }
      } else {
        this.updateForm.details.splice(index, 1);
      }
    },

    // Multiselect
    onSearchPurchaseChange: throttle(function(term) {
      this.$inertia.get(this.route('mindoro-transactions.edit'), {term}, {
        preserveState: true,
        preserveScroll: true,
        replace: true,
      })
    }, 300),
    onSelectedPurchase(purchases) {
      this.updateForm.purchases = purchases.map(purchase => purchase.id);
    },

    onSearchClientChange: throttle(function(term) {
      this.$inertia.get(this.route('mindoro-transactions.edit'), {term}, {
        preserveState: true,
        preserveScroll: true,
        replace: true,
      })
    }, 300),
    onSelectedClient(client, id) {
      this.updateForm.details[id].client_id = client.id;
    },

    // MindoroTransactionDetail
    addNewDetailForm() {
      this.updateForm.details.push({
        id: null,
        mindoro_transaction_id: this.mindoro_transaction.id,
        date: null,
        dr_no: null,
        client_id: null,
        selectedClient: null,
        product_id: null,
        quantity: 0,
        unit_price: 0,
      });
    },

    // Transaction Total Amount
    transactionTotalAmt() {
      var totalAmt = this.updateForm.details.reduce((acc, detail) => {
        acc += parseFloat(detail.quantity) * parseFloat(detail.unit_price);
        return acc;
      }, 0);

      return totalAmt;
    },

    // Transaction Total Quantity
    transactionTotalQty() {
      var totalQty = this.updateForm.details.reduce((acc, detail) => {
        acc += parseFloat(detail.quantity);
        return acc;
      }, 0);

      return this.quantityFormat(totalQty);
    },

    getLoadTotalAmt(purchasesArray) {
      const loadsArray = purchasesArray.map(purchase => purchase.tanker_loads);
      const loads = [].concat.apply([], loadsArray);
      const filteredLoads = loads
                              .filter(load => load.trip_no === this.updateForm.trip_no)
                              .map(load => load.tanker_load_details);

      const details = [].concat.apply([], filteredLoads);
      const totalAmt = details.reduce((acc, detail) => {
        acc += parseFloat(detail.quantity) * parseFloat(detail.unit_price);
        return acc;
      }, 0);

      this.totalLoad = totalAmt;
    }
  },
  watch: {
    'updateForm.selectedPurchases': {
      handler: function (purchases) {
        this.updateForm.purchases = purchases.map(purchase => purchase.id);

        this.getLoadTotalAmt(purchases);
      },
      deep: true,
    },
    'updateForm.trip_no': function (value) {
      this.getLoadTotalAmt(this.updateForm.selectedPurchases);
    },
  },
  mounted() {
    // this.selectedPurchase = this.purchases.find(purchase => purchase.id === this.updateForm.purchase_id);
    this.updateForm.selectedPurchases.forEach(purchase => {
      this.updateForm.purchases.push(purchase.id);
    });

    this.updateForm.details.forEach(detail => {
      detail.selected_client = this.clients.find(client => client.id === detail.client_id);
    });

    this.getLoadTotalAmt(this.updateForm.selectedPurchases);
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
</style>
