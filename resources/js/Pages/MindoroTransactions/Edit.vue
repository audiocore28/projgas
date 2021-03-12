<template>
  <div>
    <h1 class="mb-8 font-bold text-3xl">
      <inertia-link class="text-blue-600 hover:text-blue-800" :href="route('mindoro-transactions.index')">Mindoro Transaction</inertia-link>
      <span class="text-blue-600 font-medium">/</span> {{ updateForm.client_id }}
    </h1>

    <div class="p-1">
      <ul class="flex border-b">
        <li @click="openTab = 1" :class="{ '-mb-px': openTab === 1 }" class="-mb-px mr-1">
          <a :class="openTab === 1 ? activeClasses : inactiveClasses" class="bg-white inline-block py-2 px-4 font-semibold" href="#">Edit Transaction</a>
        </li>
        <li @click="openTab = 2" :class="{ '-mb-px': openTab === 2 }" class="-mb-px mr-1">
          <a :class="openTab === 2 ? activeClasses : inactiveClasses" class="bg-white inline-block py-2 px-4 font-semibold" href="#">Add Row</a>
        </li>
      </ul>

      <!-- Tab 1 -->
      <div class="w-full pt-4">
        <div v-show="openTab === 1">
          <!-- Update Existing Transaction -->
          <div class="bg-white rounded shadow overflow-hidden max-w-6xl mb-8 -mt-4">
            <form @submit.prevent="updateMindoroTransaction">

              <!-- Transaction -->
              <div class="-mr-6 -mb-12 flex flex-wrap w-full mt-8 px-8">
                <div class="w-full">
                  <div class="pr-6 pb-8 lg:w-1/6">
                    <text-input v-model="updateForm.trip_no" :error="errors.trip_no" label="Trip No." />
                  </div>
                </div>

                <div class="pr-6 pb-8 lg:w-1/2">
                  <label class="form-label block">Purchase No.</label>
                  <multiselect
                    id="purchase_id"
                    name="purchases[]"
                    v-model="updateForm.selectedPurchases"
                    placeholder=""
                    class="mt-3 text-xs"
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
              </div>

              <!-- Details table form -->
              <div class="px-8 py-4 mb-6 overflow-x-auto">
                <table class="min-w-full mt-8">
                  <colgroup>
                    <col span="1" style="width: 10%;">
                    <col span="1" style="width: 25%;">
                    <col span="1" style="width: 25%;">
                    <col span="1" style="width: 10%;">
                    <col span="1" style="width: 10%;">
                    <col span="1" style="width: 10%;">
                    <col span="1" style="width: 10%;">
                  </colgroup>
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
                      <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        DR#
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
                            class="mt-3 text-xs"
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
                        <button @click.prevent="deleteDetailForm(index, details.id)" type="button" class="bg-white py-1 px-1 flex-shrink-0 text-sm leading-none">
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
                          {{ transactionTotalAmt() }}
                        </div>
                      </td>
                      <td>
                        <div class="px-6 py-4 whitespace-nowrap text-left text-sm font-medium text-gray-500 uppercase"></div>
                      </td>
                      <td>
                        <div class="px-6 py-4 whitespace-nowrap text-left text-sm font-medium text-gray-500 uppercase"></div>
                      </td>
<!--                       <td>
                        <div class="px-6 py-4 whitespace-nowrap text-left text-xs font-medium text-gray-500 uppercase bg-gray">
                          <button @click.prevent="addNewDetailForm">
                            <icon name="plus" class="w-4 h-4 mr-2 fill-green-600"/>
                          </button>
                        </div>
                      </td>
 -->                    </tr>
                  </tbody>
                </table>
              </div>
              <!-- /Details table form -->

              <!-- Tanker Load -->
              <div class="bg-white rounded overflow-x-auto mb-8 -mt-4 px-8">
                <div class="rounded shadow overflow-x-auto mb-8" v-for="purchase in updateForm.selectedPurchases" :key="purchase.id" :value="purchase.id">
                  <div class="ml-5 mt-5 mb-1" v-for="load in purchase.tanker_loads">
                    <div v-if="load.trip_no === updateForm.trip_no">
                      <p class="text-sm font-bold mb-2">{{ purchase.purchase_no }} - {{ load.driver.name }} & {{ load.helper.name }} ({{ load.tanker.plate_no }})</p>
                      <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                          <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                              Products
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                              Loads
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                              Unit Price
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                              Amount
                            </th>
                          </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                          <tr v-for="detail in load.tanker_load_details">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                              <div class="text-sm text-gray-900">{{ detail.product.name }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                              <div class="text-sm text-gray-900">{{ quantityFormat(detail.quantity) }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                              <div class="text-sm text-gray-900">{{ detail.unit_price }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
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

              <div class="px-8 py-4 bg-gray-100 border-t border-gray-200 flex justify-end items-center">
                <button v-if="!mindoro_transaction.deleted_at" class="text-red-600 hover:underline" tabindex="-1" type="button" @click="destroy">Delete Transaction</button>
                <loading-button :loading="sending" class="btn-indigo ml-auto" type="submit">Update Transaction</loading-button>
              </div>
            </form>
          </div>
        </div>

        <!-- Tab 2 -->
        <div v-show="openTab === 2">
          <mindoro-transaction-create-form
            :errors="errors"
            :mindoro_transaction="mindoro_transaction"
            :clients="clients"
            :products="products">
          </mindoro-transaction-create-form>
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
import MindoroTransactionCreateForm from '@/Shared/MindoroTransactionCreateForm'
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
    MindoroTransactionCreateForm,
    Multiselect,
  },
  props: {
    errors: Object,
    mindoro_transaction: Object,
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
        trip_no: this.mindoro_transaction.trip_no,
        purchases: [],
        details: this.mindoro_transaction.details,
        selectedPurchases: this.mindoro_transaction.selected_purchases,
      },
      // Tabs
      openTab: 1,
      activeClasses: 'border-l border-t border-r rounded-t text-blue-600',
      inactiveClasses: 'text-blue-500 hover:text-blue-800',
    }
  },
  methods: {
    // update existing details
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
    restore() {
      if (confirm('Are you sure you want to restore this Mindoro Transaction?')) {
        this.$inertia.put(this.route('mindoro-transactions.restore', this.mindoro_transaction.id))
      }
    },
    deleteDetailForm(index, productDetailId) {
      if (confirm('Are you sure you want to delete this row?')) {
        this.$inertia.delete(this.route('mindoro-transaction-details.destroy', productDetailId));
        this.mindoro_transaction.details.splice(index, 1);
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

    // Transaction Total Amount
    transactionTotalAmt() {
      var totalAmt = this.updateForm.details.reduce((acc, detail) => {
        acc += parseFloat(detail.quantity) * parseFloat(detail.unit_price);
        return acc;
      }, 0);

      return this.toPHP(totalAmt);
    },

    // Transaction Total Quantity
    transactionTotalQty() {
      var totalQty = this.updateForm.details.reduce((acc, detail) => {
        acc += parseFloat(detail.quantity);
        return acc;
      }, 0);

      return this.quantityFormat(totalQty);
    },
  },
  watch: {
    'updateForm.selectedPurchases': function (purchases) {
      this.updateForm.purchases = purchases.map(purchase => purchase.id);
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
  }
}
</script>

<style src="vue2-datepicker/index.css"></style>

<style>
  .multiselect__single, .multiselect__option {
    font-size: 0.875rem;
  }

  .multiselect__element span {}
</style>
