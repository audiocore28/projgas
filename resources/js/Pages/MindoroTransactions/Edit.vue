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
              <div class="p-8 -mr-6 -mb-8 flex flex-wrap">

                <div class="pr-6 pb-8 w-full">
                  <div class="lg:w-1/4">
                    <label class="form-label block">Purchase No.</label>
                    <multiselect id="purchase_id" v-model="selectedPurchase"
                      placeholder=""
                      class="mt-3 text-xs"
                      :options="purchases"
                      label="purchase_no"
                      track-by="id"
                      @search-change="onSearchPurchaseChange"
                      @input="onSelectedPurchase"
                      :show-labels="false"
                      :allow-empty="false"
                    ></multiselect>
                  </div>
                </div>

                <text-input v-model="updateForm.trip_no" :error="errors.trip_no" class="pr-6 pb-8 w-full lg:w-1/6" label="Trip No." />
                <select-input v-model="updateForm.driver_id" :error="errors.driver_id" class="pr-6 pb-8 w-full lg:w-1/4" label="Driver">
                  <option :value="null" />
                  <option v-for="driver in drivers" :key="driver.id" :value="driver.id">{{ driver.name }}</option>
                </select-input>
                <select-input v-model="updateForm.helper_id" :error="errors.helper_id" class="pr-6 pb-8 w-full lg:w-1/4" label="Helper">
                  <option :value="null" />
                  <option v-for="helper in helpers" :key="helper.id" :value="helper.id">{{ helper.name }}</option>
                </select-input>
                <select-input v-model="updateForm.tanker_id" :error="errors.tanker_id" class="pr-6 pb-8 w-full lg:w-1/4" label="Tanker">
                  <option :value="null" />
                  <option v-for="tanker in tankers" :key="tanker.id" :value="tanker.id">{{ tanker.plate_no }}</option>
                </select-input>
              </div>

              <!-- Details -->
              <div class="px-8 py-4 -mr-6 -mb-8 flex flex-wrap" v-for="(details, index) in updateForm.details" :key="index">
                <label class="form-label block ml-1">Date:</label>
                <span class="pr-6 pb-8 w-full">
                  <date-picker v-model="details.date" lang="en" value-type="format" :formatter="momentFormat"></date-picker>
                </span>

                <div class="-mt-1 pr-6 pb-8 w-full lg:w-1/4">
                  <label class="form-label block">Client:</label>
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
                  <div v-if="errors[`details.${index}.client_id`]" class="form-error">{{ errors[`details.${index}.client_id`] }}</div>
                </div>

                <div class="pr-6 pb-8 w-full lg:w-1/6">
                  <label class="form-label" :for="`product-${index}`">Product:</label>
                  <select :id="`product-${index}`" v-model="details.product_id" class="form-select" :class="{ error: errors[`details.${index}.product_id`] }">
                    <option :value="null" />
                    <option v-for="product in products" :key="product.id" :value="product.id">{{ product.name }}</option>
                  </select>
                  <div v-if="errors[`details.${index}.product_id`]" class="form-error">{{ errors[`details.${index}.product_id`] }}</div>
                </div>

                <text-input type="number" step="any" v-model="details.quantity" :error="errors.quantity" class="pr-6 pb-8 w-full lg:w-1/6" label="Quantity" />
                <text-input type="number" step="any" v-model="details.unit_price" :error="errors.unit_price" class="pr-6 pb-8 w-full lg:w-1/6" label="Unit Price" />

                <button @click.prevent="deleteDetailForm(index, details.id)" type="button" class="bg-white py-1 px-1 flex-shrink-0 text-sm leading-none">
                  <icon name="trash" class="w-4 h-4 mr-2 fill-red-600"/>
                </button>
              </div>

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

export default {
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
    tankers: Array,
    drivers: Array,
    helpers: Array,
  },
  remember: 'form',
  data() {
    return {
      selectedPurchase: undefined,
      selectedClient: undefined,
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
        tanker_id: this.mindoro_transaction.tanker_id,
        driver_id: this.mindoro_transaction.driver_id,
        helper_id: this.mindoro_transaction.helper_id,
        purchase_id: this.mindoro_transaction.purchase_id,
        details: this.mindoro_transaction.details,
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
    onSelectedPurchase(purchase) {
      this.updateForm.purchase_id = purchase.id;
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
  },
  mounted() {
    this.selectedPurchase = this.purchases.find(purchase => purchase.id === this.updateForm.purchase_id);

    this.updateForm.details.forEach(detail => {
      detail.selectedClient = this.clients.find(client => client.id === detail.client_id);
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
