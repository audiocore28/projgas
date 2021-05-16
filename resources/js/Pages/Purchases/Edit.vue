<template>
  <div>
    <h1 class="mb-8 font-bold text-3xl">
      <inertia-link class="text-blue-600 hover:text-blue-800" :href="route('purchases.index')">Purchases</inertia-link>
      <span class="text-blue-600 font-medium">/</span> {{ form.purchase_no }}
    </h1>
    <form @submit.prevent="update">
      <div class="bg-white rounded shadow overflow-hidden max-w-6xl">
        <!-- Purchase -->
        <div class="px-8 pt-4 -mr-6 -mb-8 flex flex-wrap bg-gradient-to-r from-yellow-500 to-blue-600 highlight-yellow">
          <div>
            <label class="form-label block mr-5">Date:<span class="text-red-500">*</span></label>
            <div class="pr-6 pb-4 mt-3">
              <date-picker v-model="form.date" :error="errors.date" lang="en" value-type="format" :formatter="momentFormat"></date-picker>
              <div v-if="errors.date" class="form-error">{{ errors.date }}</div>
            </div>
          </div>

          <div class="w-full lg:w-1/4">
            <text-input v-model="form.purchase_no" :error="errors.purchase_no" class="pr-6 pb-4 w-full" label="Purchase No*" />
          </div>

          <select-input v-model="form.supplier_id" :error="errors.supplier_id" class="pr-6 pb-4 w-full lg:w-1/4" label="Supplier">
            <option :value="null" />
            <option v-for="supplier in suppliers" :key="supplier.id" :value="supplier.id">{{ supplier.name }}</option>
          </select-input>
        </div>

        <!-- PurchaseDetail table form -->
        <div class="px-8 py-4 my-6 overflow-x-auto">
          <table class="min-w-full">
            <colgroup>
              <col span="1" style="width: 15%;">
              <col span="1" style="width: 12%;">
              <col span="1" style="width: 13%;">
              <col span="1" style="width: 12%;">
              <col span="1" style="width: 38%;">
            </colgroup>
            <thead class="bg-gray-50">
              <tr>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Product<span class="text-red-500">*</span>
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
                  Remarks
                </th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  <button @click.prevent="addNewPurchaseDetailForm">
                    <icon name="plus" class="w-4 h-4 mr-2 fill-green-600"/>
                  </button>
                </th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr v-for="(details, purchaseIndex) in form.details" :key="purchaseIndex">
                <td>
                  <div class="text-sm font-medium text-gray-900">
                    <select :id="`product-${purchaseIndex}`" v-model="details.product_id" class="form-select" :class="{ error: errors[`details.${purchaseIndex}.product_id`] }">
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
                  <div class="text-sm font-medium text-gray-900 text-center">
                    <text-input v-model="details.remarks" :error="errors.remarks" />
                  </div>
                </td>
                <td class="text-sm text-gray-500">
                  <div class=" px-5 text-sm font-medium text-gray-900">
                    <button @click.prevent="deletePurchaseDetailForm(purchaseIndex, details.id)" type="button" class="bg-white py-1 px-1 flex-shrink-0 text-sm leading-none" tabindex="-1">
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
                  <div class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium text-gray-500 uppercase">
                    {{ quantityFormat(PurchaseTotalQty()) }}
                  </div>
                </td>
                <td>
                  <div class="px-6 py-4 whitespace-nowrap text-left text-sm font-medium text-gray-500 uppercase"></div>
                </td>
                <td>
                  <div class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium text-gray-500 uppercase">
                    {{ PurchaseTotalAmt() }}
                  </div>
                </td>
                <td>
                  <div class="px-6 py-4 whitespace-nowrap text-left text-sm font-medium text-gray-500 uppercase"></div>
                </td>
                <td>
                  <div class="px-6 py-4 whitespace-nowrap text-left text-xs font-medium text-gray-500 uppercase"></div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- TankerLoad Form -->
      <!-- Batangas -->
      <div class="mb-8 flex justify-between items-center">
        <div class="-mb-8 flex justify-start items-center">
          <icon name="cheveron-down" class="block w-6 h-6 fill-gray-600 mr-2" v-if="batangasSelected == 0"/>
          <icon name="cheveron-right" class="block w-6 h-6 fill-gray-600 mr-2" v-else/>
          <h1 class="my-8 font-bold text-2xl mr-4 pointer" @click="batangasSelected !== 0 ? batangasSelected = 0 : batangasSelected = null">To Batangas</h1>

          <!-- MonthlyBatangasTransaction -->
          <div class="text-sm font-medium text-gray-900 mr-4">
            <select v-model="form.monthly_batangas_transaction_id" class="form-select" @change="onMonthlyBatangasChange">
              <option :value="null" />
              <option v-for="monthlyTransaction in monthlyBatangasTransactions" :key="monthlyTransaction.id" :value="monthlyTransaction.id">{{ `${monthlyTransaction.year}, ${monthlyTransaction.month}` }}</option>
            </select>
          </div>

          <button class="btn-indigo" @click.prevent="addNewBatangasLoadForm">Add ({{ form.batangasLoads.length }})</button>

        </div>
        <div class="-mb-8 flex justify-end items-center">
          <span class="mr-2 px-2 py-2 text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
            Diesel
            <span class="p-1 rounded-full text-yellow-800 text-xs ml-2 bg-yellow-400">
              {{ totalBatangasLoadQty('Diesel') / 1000 }}
            </span>
          </span>
          <span class="mr-2 px-2 py-2 text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
            Regular
            <span class="p-1 rounded-full text-green-800 text-xs ml-2 bg-green-400">
              {{ totalBatangasLoadQty('Regular') / 1000 }}
            </span>
          </span>
          <span class="mr-2 px-2 py-2 text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
            Premium
            <span class="p-1 rounded-full text-red-800 text-xs ml-2 bg-red-400">
              {{ totalBatangasLoadQty('Premium') / 1000 }}
            </span>
          </span>
        </div>
      </div>

      <div class="grid grid-cols-1 gap-3 xl:grid-cols-3" v-show="batangasSelected == 0">
        <div class="rounded overflow-hidden max-w-6xl" v-for="(load, loadIndex) in form.batangasLoads" :key="loadIndex" :id="`load-${load.id}`">
          <div class="bg-white rounded shadow overflow-hidden max-w-6xl">
            <!-- TankerLoad -->
            <div class="p-2 -mr-6 -mb-6 flex justify-between bg-blue-600">
              <table>
<!--                 <colgroup>
                  <col span="1" style="width: 50%;">
                  <col span="1" style="width: 50%;">
                </colgroup>
 -->
                <tr>
                  <td class="text-sm text-gray-500">
                    <div class="text-sm font-medium text-gray-900 mr-2">
                      <!-- <text-input v-model="load.trip_no" :error="errors.trip_no" class="pr-6" placeholder="Trip No.*" /> -->

                      <!-- BatangasTransaction trip no. -->
                      <div class="text-sm font-medium text-gray-900">
                        <select :id="`batangas-${loadIndex}`" class="form-select" v-model="form.batangasLoads[loadIndex].batangas_transaction_id" @change="onBatangasTransactionChange($event, loadIndex)">
                          <option :value="null" />
                          <option v-for="transaction in form.batangas_transactions" :key="transaction.id" :value="transaction.id">{{ `${transaction.trip_no} - ${transaction.driver.name} (${transaction.month} ${transaction.year})` }}</option>
                        </select>
                      </div>

                    </div>
                  </td>
                  <td class="text-sm">
                    <button @click.prevent="addNewBatangasLoadDetailForm(loadIndex)">
                      <icon name="plus" class="w-4 h-4 loadIr-2 fill-white"/>
                    </button>

                    <a v-if="form.monthly_batangas_transaction_id && load.batangas_transaction_id" target="_blank" :href="`/monthly-batangas-transactions/${load.monthly_batangas_transaction.id}/edit#transaction-${load.batangas_transaction_id}`" class="ml-2 inline-block">
                      <icon name="open-link" class="w-4 h-4 loadIr-2 fill-white"/>
                    </a>

                  </td>

                </tr>
              </table>

              <div class="mr-5">
                <span class="p-1 rounded-full text-blue-400 text-xs ml-2">
                  <button @click.prevent="deleteBatangasLoadForm(loadIndex, load.id)" type="button" class="font-bold p-1 flex-shrink-0 leading-none" tabindex="-1">
                    {{ loadIndex + 1 }}
                  </button>
                </span>
              </div>
              <!-- <text-input v-model="form.remarks" :error="errors.remarks" class="pr-6 pb-8 w-full lg:w-1/2" label="Remarks" /> -->
            </div>
            <!-- /TankerLoad -->

            <!-- TankerLoadDetail table form -->
            <div class="px-6 mt-8 mb-4 overflow-x-auto">
              <table style="width: 260px">
                <colgroup>
                  <col span="1" style="width: 52%;">
                  <col span="1" style="width: 42%;">
                  <col span="1" style="width: 6%;">
                </colgroup>
                <tbody class="bg-white divide-y divide-gray-200">
                  <tr v-for="(details, detailsIndex) in load.details" :key="detailsIndex">
                    <td>
                      <div class="text-sm font-medium text-gray-900">
                        <select :id="`product-${detailsIndex}`" v-model="details.product.id" class="form-select" :class="{ error: errors[`details.${detailsIndex}.product_id`] }" @change="onBatangasChange($event, loadIndex, detailsIndex)">
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
                      <!-- <td class="text-sm text-gray-500">
                      <div class="text-sm font-medium text-gray-900">
                        <text-input type="number" step="any" v-model="details.unit_price" :error="errors.unit_price" />
                      </div>
                    </td>
                    <td class="text-sm text-gray-500">
                      <div class="text-sm font-medium text-gray-900 text-center">
                        {{ totalCurrency(details.quantity, details.unit_price) }}
                      </div>
                    </td>
                    -->
                    <td class="text-sm text-gray-500">
                      <div class=" px-5 text-sm font-medium text-gray-900">
                        <button @click.prevent="deleteBatangasLoadDetailForm(loadIndex, detailsIndex, details.id)" type="button" class="bg-white py-1 px-1 flex-shrink-0 text-sm leading-none" tabindex="-1">
                          <icon name="trash" class="w-4 h-4 mr-2 fill-red-600"/>
                        </button>
                      </div>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
            <!-- /TankerLoadDetail table form -->
          </div>
        </div>
      </div>

      <!-- Mindoro -->
      <div class="mt-8 mb-8 flex justify-between items-center border-t-2 border-gray-600">
        <div class="-mb-8 flex justify-start items-center">
          <icon name="cheveron-down" class="block w-6 h-6 fill-gray-600 mr-2" v-if="mindoroSelected == 1"/>
          <icon name="cheveron-right" class="block w-6 h-6 fill-gray-600 mr-2" v-else/>
          <h1 class="my-8 font-bold text-2xl mr-8 pointer" @click="mindoroSelected !== 1 ? mindoroSelected = 1 : mindoroSelected = null">To Mindoro</h1>

          <!-- MonthlyMindoroTransaction -->
          <div class="text-sm font-medium text-gray-900 mr-4">
            <select v-model="form.monthly_mindoro_transaction_id" class="form-select" @change="onMonthlyMindoroChange">
              <option :value="null" />
              <option v-for="monthlyTransaction in monthlyMindoroTransactions" :key="monthlyTransaction.id" :value="monthlyTransaction.id">{{ `${monthlyTransaction.year}, ${monthlyTransaction.month}` }}</option>
            </select>
          </div>

          <button class="btn-indigo" @click.prevent="addNewMindoroLoadForm">Add ({{ form.mindoroLoads.length }})</button>

        </div>
        <div class="-mb-8 flex justify-end items-center">
          <span class="mr-2 px-2 py-2 text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
            Diesel
            <span class="p-1 rounded-full text-yellow-800 text-xs ml-2 bg-yellow-400">
              {{ totalMindoroLoadQty('Diesel') / 1000 }}
            </span>
          </span>
          <span class="mr-2 px-2 py-2 text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
            Regular
            <span class="p-1 rounded-full text-green-800 text-xs ml-2 bg-green-400">
              {{ totalMindoroLoadQty('Regular') / 1000 }}
            </span>
          </span>
          <span class="mr-2 px-2 py-2 text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
            Premium
            <span class="p-1 rounded-full text-red-800 text-xs ml-2 bg-red-400">
              {{ totalMindoroLoadQty('Premium') / 1000 }}
            </span>
          </span>
        </div>
      </div>

      <div class="grid grid-cols-1 gap-3 md:grid-cols-2 xl:grid-cols-3 rounded overflow-x-auto my-8" v-show="mindoroSelected == 1">
        <div class="rounded overflow-hidden max-w-6xl" v-for="(load, loadIndex) in form.mindoroLoads" :key="loadIndex" :id="`load-${load.id}`">
          <div class="bg-white rounded shadow overflow-hidden max-w-6xl">
            <!-- TankerLoad -->
            <div class="p-2 -mr-6 -mb-8 flex justify-between bg-yellow-500">
              <table>
<!--                 <colgroup>
                  <col span="1" style="width: 50%;">
                  <col span="1" style="width: 50%;">
                </colgroup>
 -->                <tr>
                  <td class="text-sm text-gray-500">
                    <div class="text-sm font-medium text-gray-900 mr-2">
                      <!-- <text-input v-model="load.trip_no" :error="errors.trip_no" class="pr-6" placeholder="Trip No.*" /> -->

                      <!-- MindoroTransaction trip no. -->
                      <div class="text-sm font-medium text-gray-900">
                        <select :id="`mindoro-${loadIndex}`" class="form-select" v-model="form.mindoroLoads[loadIndex].mindoro_transaction_id" @change="onMindoroTransactionChange($event, loadIndex)">
                          <option :value="null" />
                          <option v-for="transaction in form.mindoro_transactions" :key="transaction.id" :value="transaction.id">{{ `${transaction.trip_no} - ${transaction.driver.name} (${transaction.month} ${transaction.year})` }}</option>
                        </select>
                      </div>

                    </div>
                  </td>
<!--                   <td class="text-sm">
                    <div class="text-sm font-semibold text-white" v-for="(transaction, index) in load.batangas_transaction" :key="index">
                      <div v-if="load.trip_no === transaction.trip_no">
                        {{ transaction.driver.name }}
                      </div>
                    </div>
                    <div class="text-sm font-semibold text-white" v-for="(transaction, index) in load.mindoro_transaction" :key="index">
                      <div v-if="load.trip_no === transaction.trip_no">
                        {{ transaction.driver.name }}
                      </div>
                    </div>
                  </td>
 -->
                  <td class="text-sm">
                     <button @click.prevent="addNewMindoroLoadDetailForm(loadIndex)">
                       <icon name="plus" class="w-4 h-4 loadIr-2 fill-white"/>
                     </button>

                     <a v-if="form.monthly_mindoro_transaction_id && load.mindoro_transaction_id" target="_blank" :href="`/monthly-mindoro-transactions/${load.monthly_mindoro_transaction.id}/edit#transaction-${load.mindoro_transaction_id}`" class="ml-2 inline-block">
                       <icon name="open-link" class="w-4 h-4 loadIr-2 fill-white"/>
                     </a>

                   </td>
                </tr>
              </table>

              <div class="mr-5">
                <span class="p-1 rounded-full text-yellow-400 text-xs ml-2">
                  <button @click.prevent="deleteMindoroLoadForm(loadIndex, load.id)" type="button" class="font-bold p-1 flex-shrink-0 leading-none" tabindex="-1">
                    {{ loadIndex + 1 }}
                  </button>
                </span>
              </div>
              <!-- <text-input v-model="load.remarks" :error="errors.remarks" class="pr-6 pb-8 w-full lg:w-1/2" label="Remarks" /> -->
            </div>

            <!-- TankerLoadDetail table form -->
            <div class="px-6 mt-10 mb-4 overflow-x-auto">
              <table style="width: 260px">
                <colgroup>
                  <col span="1" style="width: 52%;">
                  <col span="1" style="width: 42%;">
                  <col span="1" style="width: 6%;">
                </colgroup>
                <tbody class="bg-white divide-y divide-gray-200">
                  <tr v-for="(details, detailsIndex) in load.details" :key="detailsIndex">
                    <td>
                      <div class="text-sm font-medium text-gray-900">
                        <select :id="`product-${detailsIndex}`" v-model="details.product.id" class="form-select" :class="{ error: errors[`details.${detailsIndex}.product_id`] }" @change="onMindoroChange($event, loadIndex, detailsIndex)">
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
                    <!-- <td class="text-sm text-gray-500">
                      <div class="text-sm font-medium text-gray-900">
                        <text-input type="number" step="any" v-model="details.unit_price" :error="errors.unit_price" />
                      </div>
                    </td>
                    <td class="text-sm text-gray-500">
                      <div class="text-sm font-medium text-gray-900 text-center">
                        {{ totalCurrency(details.quantity, details.unit_price) }}
                      </div>
                    </td>
                    -->
                    <td class="text-sm text-gray-500">
                      <div class=" px-5 text-sm font-medium text-gray-900">
                        <button @click.prevent="deleteMindoroLoadDetailForm(loadIndex, detailsIndex, details.id)" type="button" class="bg-white py-1 px-1 flex-shrink-0 text-sm leading-none" tabindex="-1">
                          <icon name="trash" class="w-4 h-4 mr-2 fill-red-600"/>
                        </button>
                      </div>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
            <!-- /TankerLoadDetail table form -->
          </div>
        </div>
      </div>

      <!-- Total Load-->
      <div class="pb-8 my-8 flex justify-between items-center border-t-2 border-gray-600 bg-white">
        <div class="pl-10 -mb-8 flex justify-start items-center">
          <!-- <icon name="cheveron-right" class="block w-6 h-6 fill-gray-600 mr-2"/> -->
          <h1 class="my-8 font-bold text-2xl mr-4 pointer">Total Load</h1>
        </div>
        <div class="-mb-8 flex justify-end items-center">
          <span class="mr-2 px-2 py-2 text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
            Diesel
            <span class="p-1 rounded-full text-yellow-800 text-xs ml-2 bg-yellow-400">
              {{ allLoadQty('Diesel') / 1000 }}
            </span>
          </span>
          <span class="mr-2 px-2 py-2 text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
            Regular
            <span class="p-1 rounded-full text-green-800 text-xs ml-2 bg-green-400">
              {{ allLoadQty('Regular') / 1000 }}
            </span>
          </span>
          <span class="mr-2 px-2 py-2 text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
            Premium
            <span class="p-1 rounded-full text-red-800 text-xs ml-2 bg-red-400">
              {{ allLoadQty('Premium') / 1000 }}
            </span>
          </span>
        </div>
      </div>

      <div class="px-8 py-4 bg-gray-100 border-t border-gray-200 flex justify-end items-center">
        <button v-if="!purchase.deleted_at" class="text-red-600 hover:underline" tabindex="-1" type="button" @click="destroy">Delete Purchase</button>

        <loading-button :loading="sending" class="btn-indigo ml-auto" type="submit">Update Purchase</loading-button>
      </div>
    </form>
  </div>
</template>

<script>
import axios from 'axios'
import Layout from '@/Shared/Layout'
import LoadingButton from '@/Shared/LoadingButton'
import SelectInput from '@/Shared/SelectInput'
import TextInput from '@/Shared/TextInput'
import Icon from '@/Shared/Icon'
import Multiselect from 'vue-multiselect'
import DatePicker from 'vue2-datepicker'
import moment from 'moment'
import { numberFormatsMixin } from '@/Mixins/numberFormatsMixin'

export default {
  mixins: [numberFormatsMixin],
  metaInfo() {
    return { title: this.form.purchase_no }
  },
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
    purchase: Object,
    suppliers: Array,
    products: Array,
    // tanker_loads: Array,
    monthlyMindoroTransactions: Array,
    monthlyBatangasTransactions: Array,
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
        id: this.purchase.id,
        date: this.purchase.date,
        purchase_no: this.purchase.purchase_no,
        supplier_id: this.purchase.supplier_id,
        monthly_mindoro_transaction_id: this.purchase.monthly_mindoro_transaction_id,
        mindoro_transactions: [],
        monthly_batangas_transaction_id: this.purchase.monthly_batangas_transaction_id,
        batangas_transactions: [],
        details: this.purchase.details,
        removed_purchase_details: [],
        removed_loads: [],
        removed_load_details: [],
        batangasLoads: this.purchase.batangasLoads,
        mindoroLoads: this.purchase.mindoroLoads,
        // tankerLoads: this.tanker_loads,
      },
      // Accordion
      batangasSelected: 0,
      mindoroSelected: 1,
    }
  },
  methods: {
    // Purchase
    update() {
      this.$inertia.put(this.route('purchases.update', this.purchase.id), this.form, {
        onStart: () => this.sending = true,
        onFinish: () => this.sending = false,
      });
    },
    destroy() {
      if (confirm('Are you sure you want to permanently delete this purchase and its details?')) {
        this.$inertia.delete(this.route('purchases.destroy', this.purchase.id))
      }
    },

    // PurchaseDetail
    addNewPurchaseDetailForm() {
      this.form.details.push({
        id: null,
        purchase_id: this.purchase.id,
        product_id: null,
        quantity: 0,
        unit_price: 0,
        remarks: null,
      });
    },
    deletePurchaseDetailForm(purchaseIndex, purchaseDetailId) {
      if (purchaseDetailId) {
        this.form.removed_purchase_details.push(purchaseDetailId);
        this.form.details.splice(purchaseIndex, 1);
      } else {
        this.form.details.splice(purchaseIndex, 1);
      }
    },

    // PurchaseDetail Totals
    PurchaseTotalAmt() {
      var totalAmt = this.form.details.reduce((acc, detail) => {
        acc += parseFloat(detail.quantity) * parseFloat(detail.unit_price);
        return acc;
      }, 0);

      return this.toPHP(totalAmt);
    },
    PurchaseTotalQty() {
      var totalQty = this.form.details.reduce((acc, detail) => {
        acc += parseFloat(detail.quantity);
        return acc;
      }, 0);

      return totalQty;
    },

    // Batangas - TankerLoad
    addNewBatangasLoadForm() {
      this.form.batangasLoads.push({
        id: null,
        purchase_id: this.purchase.id,
        monthly_batangas_transaction: {
          id: null,
        },
        batangas_transaction_id: null,
        mindoro_transaction_id: 0,
        remarks: null,
        details: [
          {
            id: null,
            tanker_load_id: null,
            product: {
              id: null,
              name: null,
            },
            quantity: 0,
            unit_price: 0,
          }
        ],
      });
    },
    deleteBatangasLoadForm(loadIndex, loadId) {
      if (loadId) {
        this.form.removed_loads.push(loadId);
        this.form.batangasLoads.splice(loadIndex, 1);
      } else {
        this.form.batangasLoads.splice(loadIndex, 1);
      }
    },

    // Batangas - TankerLoadDetail
    addNewBatangasLoadDetailForm(loadIndex) {
      this.form.batangasLoads[loadIndex].details.push({
        id: null,
        tanker_load_id: null,
        product: {
          id: null,
          name: null,
        },
        quantity: 0,
        unit_price: 0,
      });
    },

    deleteBatangasLoadDetailForm(loadIndex, detailsIndex, loadDetailId) {
      if (loadDetailId) {
        this.form.removed_load_details.push(loadDetailId);
        this.form.batangasLoads[loadIndex].details.splice(detailsIndex, 1);
      } else{
        this.form.batangasLoads[loadIndex].details.splice(detailsIndex, 1);
      }
    },

    //
    onBatangasChange(event, loadIndex, detailsIndex) {
      const product = event.target.options[event.target.options.selectedIndex].text;
      this.form.batangasLoads[loadIndex].details[detailsIndex].product.name = product;
    },

    onBatangasTransactionChange(event, loadIndex) {
      const transactionId = event.target.value;

      axios.get(`/batangas-transactions/${transactionId}/edit`)
        .then(response => {
          this.form.batangasLoads[loadIndex].monthly_batangas_transaction.id = response.data.monthly_batangas_transaction.id;
        });
    },

    onMonthlyBatangasChange() {
      this.form.batangasLoads.forEach(load => {
        load.batangas_transaction_id = null;
      });
    },

    // Batangas - TankerLoad Totals
    totalBatangasLoadQty(product) {
      var totalQty = this.form.batangasLoads.reduce(function (acc, load) {
        load.details.forEach(detail => {
          if(detail.product.name === product) {
            acc += parseFloat(detail.quantity);
          }
        });
        return parseFloat(acc);
      }, 0);
      return totalQty;
    },


    // Mindoro - TankerLoad
    addNewMindoroLoadForm() {
      this.form.mindoroLoads.push({
        id: null,
        purchase_id: this.purchase.id,
        batangas_transaction_id: 0,
        monthly_mindoro_transaction: {
          id: null,
        },
        mindoro_transaction_id: null,
        remarks: null,
        details: [
          {
            id: null,
            tanker_load_id: null,
            product: {
              id: null,
              name: null,
            },
            quantity: 0,
            unit_price: 0,
          }
        ],
      });
    },
    deleteMindoroLoadForm(loadIndex, loadId) {
      if (loadId) {
        this.form.removed_loads.push(loadId);
        this.form.mindoroLoads.splice(loadIndex, 1);
      } else {
        this.form.mindoroLoads.splice(loadIndex, 1);
      }
    },

    // Mindoro - TankerLoadDetail
    addNewMindoroLoadDetailForm(loadIndex) {
      this.form.mindoroLoads[loadIndex].details.push({
        id: null,
        tanker_load_id: null,
        product: {
          id: null,
          name: null,
        },
        quantity: 0,
        unit_price: 0,
      });
    },
    deleteMindoroLoadDetailForm(loadIndex, detailsIndex, loadDetailId) {
      if (loadDetailId) {
        this.form.removed_load_details.push(loadDetailId);
        this.form.mindoroLoads[loadIndex].details.splice(detailsIndex, 1);
      } else{
        this.form.mindoroLoads[loadIndex].details.splice(detailsIndex, 1);
      }
    },

    onMindoroChange(event, loadIndex, detailsIndex) {
      const product = event.target.options[event.target.options.selectedIndex].text;
      this.form.mindoroLoads[loadIndex].details[detailsIndex].product.name = product;
    },

    onMindoroTransactionChange(event, loadIndex) {
      const transactionId = event.target.value;

      axios.get(`/mindoro-transactions/${transactionId}/edit`)
        .then(response => {
          this.form.mindoroLoads[loadIndex].monthly_mindoro_transaction.id = response.data.monthly_mindoro_transaction.id;
        });
    },

    onMonthlyMindoroChange() {
      this.form.mindoroLoads.forEach(load => {
        load.mindoro_transaction_id = null;
      });
    },

    // Mindoro - TankerLoad Totals
    totalMindoroLoadQty(product) {
      var totalQty = this.form.mindoroLoads.reduce(function (acc, load) {
        load.details.forEach(detail => {
          if(detail.product.name === product) {
            acc += parseFloat(detail.quantity);
          }
        });
        return parseFloat(acc);
      }, 0);
      return totalQty;
    },

    // TankerLoad Quantity Overall Totals
    allLoadQty(product) {
      var batangasLoadQty = this.totalBatangasLoadQty(product);
      var mindoroLoadQty = this.totalMindoroLoadQty(product);
      var totalLoad = batangasLoadQty + mindoroLoadQty;

      return totalLoad;
    },

    getMindoroTransactions(value) {
      axios.get(`/monthly-mindoro-transactions/${value}/edit`)
        .then(response => {
          this.form.mindoro_transactions = response.data.mindoroTransactions;
        });
    },

    getBatangasTransactions(value) {
      axios.get(`/monthly-batangas-transactions/${value}/edit`)
        .then(response => {
          this.form.batangas_transactions = response.data.batangasTransactions;
        });
    },


  },
  watch: {
    'form.monthly_batangas_transaction_id': function (value) {
      this.getBatangasTransactions(value);
    },

    'form.monthly_mindoro_transaction_id': function (value) {
      this.getMindoroTransactions(value);
    },

  },
  mounted() {
    this.getBatangasTransactions(this.form.monthly_batangas_transaction_id);
    this.getMindoroTransactions(this.form.monthly_mindoro_transaction_id);
  }

}
</script>

<style src="vue-multiselect/dist/vue-multiselect.min.css"></style>
<style src="vue2-datepicker/index.css"></style>

<style>
  .highlight-yellow label.form-label {
    @apply text-white;
  }
</style>
