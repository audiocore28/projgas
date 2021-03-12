<template>
  <div>
    <h1 class="mb-8 font-bold text-3xl">
      <inertia-link class="text-blue-600 hover:text-blue-800" :href="route('mindoro-transactions.index')">Mindoro Transaction</inertia-link>
      <span class="text-blue-600 font-medium">/</span> Create
    </h1>
    <div class="bg-white rounded shadow overflow-hidden max-w-6xl">
      <form @submit.prevent="submit">

        <!-- Transaction -->
        <div class="-mr-6 -mb-12 flex flex-wrap w-full mt-8 px-8">
          <div class="w-full">
            <div class="pr-6 pb-8 lg:w-1/6">
              <text-input v-model="form.trip_no" :error="errors.trip_no" label="Trip No." />
            </div>
          </div>

          <div class="pr-6 pb-8 lg:w-1/2">
            <label class="form-label block">Purchase No.</label>
            <multiselect
              id="purchase_id"
              name="purchases[]"
              v-model="selectedPurchases"
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
                  DR #
                </th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr v-for="(details, index) in form.details" :key="index">
                <td>
                  <div class="text-sm font-medium text-gray-900">
                    <date-picker v-model="details.date" lang="en" value-type="format" :formatter="momentFormat"></date-picker>
                  </div>
                </td>
                <td>
                  <div class="text-sm font-medium text-gray-900 -mt-3">
                    <multiselect :id="index" v-model="details.selectedClient"
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
                    <text-input v-model="details.dr_no" :error="errors.unit_price" />
                  </div>
                </td>
                <td class="text-sm text-gray-500">
                  <div class=" px-5 text-sm font-medium text-gray-900">
                  <!-- <div class="px-5 py-4 whitespace-nowrap text-left text-xs font-medium text-gray-500 uppercase bg-white"> -->
                    <button @click.prevent="deleteDetailForm(index)" type="button" class="bg-white py-1 px-1 flex-shrink-0 text-sm leading-none">
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
                  <div class="px-6 py-4 whitespace-nowrap text-left text-xs font-medium text-gray-500 uppercase bg-gray">
                    <button @click.prevent="addNewDetailForm">
                      <icon name="plus" class="w-4 h-4 mr-2 fill-green-600"/>
                    </button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
        <!-- /table form -->

        <!-- Tanker Load -->
        <div class="bg-white rounded overflow-x-auto mb-8 -mt-4 px-8">
          <div class="rounded shadow overflow-x-auto mb-8" v-for="purchase in selectedPurchases" :key="purchase.id" :value="purchase.id">
            <div class="ml-5 mt-5 mb-1" v-for="load in purchase.tanker_loads">
              <div v-if="load.trip_no === form.trip_no">
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
          <loading-button :loading="sending" class="btn-indigo" type="submit">Save</loading-button>
        </div>
      </form>
    </div>
  </div>
</template>

<script>
import Layout from '@/Shared/Layout'
import LoadingButton from '@/Shared/LoadingButton'
import SelectInput from '@/Shared/SelectInput'
import TextInput from '@/Shared/TextInput'
import Icon from '@/Shared/Icon'
import Multiselect from 'vue-multiselect'
import DatePicker from 'vue2-datepicker'
import moment from 'moment'
import {throttle} from 'lodash'
import { numberFormatsMixin } from '@/Mixins/numberFormatsMixin'

export default {
  mixins: [numberFormatsMixin],
  metaInfo: { title: 'Create Mindoro Transaction' },
  layout: Layout,
  components: {
    LoadingButton,
    SelectInput,
    TextInput,
    Multiselect,
    DatePicker,
    Icon,
  },
  props: {
    errors: Object,
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
      form: {
        trip_no: null,
        purchases: [],
        details: [
          {
            date: null,
            dr_no: null,
            client_id: null,
            selectedClient: null,
            product_id: null,
            quantity: null,
            unit_price: null,
          }
        ],
      },
      selectedPurchases: [],
    }
  },
  methods: {
    submit() {
      this.$inertia.post(this.route('mindoro-transactions.store'), this.form, {
        onStart: () => this.sending = true,
        onFinish: () => this.sending = false,
      });
    },

    addNewDetailForm() {
      this.form.details.push({
        // mindoro_transaction_id: null,
        date: null,
        dr_no: null,
        client_id: null,
        selectedClient: null,
        product_id: null,
        quantity: null,
        unit_price: null,
      });
    },
    deleteDetailForm(index) {
      this.form.details.splice(index, 1);
    },

    // Multiselect
    onSearchPurchaseChange: throttle(function(term) {
      this.$inertia.get(this.route('mindoro-transactions.create'), {term}, {
        preserveState: true,
        preserveScroll: true,
        replace: true,
      })
    }, 300),
    onSelectedPurchase(purchases) {
      // this.form.purchase_id = purchase.id;
      this.form.purchases = purchases.map(purchase => purchase.id);
    },

    onSearchClientChange: throttle(function(term) {
      this.$inertia.get(this.route('mindoro-transactions.create'), {term}, {
        preserveState: true,
        preserveScroll: true,
        replace: true,
      })
    }, 300),
    onSelectedClient(client, id) {
      this.form.details[id].client_id = client.id;
    },

    // Transaction Totals
    transactionTotalAmt() {
      var totalAmt = this.form.details.reduce((acc, detail) => {
        acc += parseFloat(detail.quantity) * parseFloat(detail.unit_price);
        return acc;
      }, 0);

      return this.toPHP(totalAmt);
    },
    transactionTotalQty() {
      var totalQty = this.form.details.reduce((acc, detail) => {
        acc += parseFloat(detail.quantity);
        return acc;
      }, 0);

      return this.quantityFormat(totalQty);
    },
  },
  watch: {
    selectedPurchases (purchases) {
      this.form.purchases = purchases.map(purchase => purchase.id);
    },
  },
}
</script>

<style src="vue2-datepicker/index.css"></style>

<style>
  .multiselect__single, .multiselect__option {
    font-size: 0.875rem;
  }

  .multiselect__element span {}
</style>
