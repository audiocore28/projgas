<template>
  <div>
    <h1 class="mb-8 font-bold text-3xl">
      <inertia-link class="text-blue-600 hover:text-blue-800" :href="route('deliveries.index')">Deliveries</inertia-link>
      <span class="text-blue-600 font-medium">/</span> Create
    </h1>
    <div class="bg-white rounded shadow overflow-hidden max-w-6xl">
      <form @submit.prevent="submit">
        <!-- Delivery -->
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

          <text-input v-model="form.trip_no" :error="errors.trip_no" class="pr-6 pb-8 w-full lg:w-1/6" label="Trip No." />
          <select-input v-model="form.driver_id" :error="errors.driver_id" class="pr-6 pb-8 w-full lg:w-1/4" label="Driver">
            <option :value="null" />
            <option v-for="driver in drivers" :key="driver.id" :value="driver.id">{{ driver.name }}</option>
          </select-input>
          <select-input v-model="form.helper_id" :error="errors.helper_id" class="pr-6 pb-8 w-full lg:w-1/4" label="Helper">
            <option :value="null" />
            <option v-for="helper in helpers" :key="helper.id" :value="helper.id">{{ helper.name }}</option>
          </select-input>
          <select-input v-model="form.tanker_id" :error="errors.tanker_id" class="pr-6 pb-8 w-full lg:w-1/4" label="Tanker">
            <option :value="null" />
            <option v-for="tanker in tankers" :key="tanker.id" :value="tanker.id">{{ tanker.plate_no }}</option>
          </select-input>
        </div>

        <!-- Details -->
        <div class="px-8 py-4 -mr-6 -mb-8 flex flex-wrap" v-for="(details, index) in form.details" :key="index">

          <label class="form-label block ml-1">Date:</label>
          <div class="pr-6 pb-8 w-full">
            <date-picker v-model="details.date" lang="en" value-type="format" :formatter="momentFormat"></date-picker>
          </div>

<!--           <div class="pr-6 pb-8 w-full lg:w-1/4">
            <label class="form-label" :for="`client-${index}`">Client:</label>
            <select :id="`client-${index}`" v-model="details.client_id" class="form-select" :class="{ error: errors[`details.${index}.client_id`] }">
              <option :value="null" />
              <option v-for="client in clients" :key="client.id" :value="client.id">{{ client.name }}</option>
            </select>
            <div v-if="errors[`details.${index}.client_id`]" class="form-error">{{ errors[`details.${index}.client_id`] }}</div>
          </div>
 -->
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

          <text-input v-model="details.dr_no" :error="errors.dr_no" class="pr-6 pb-8 w-full lg:w-1/6" label="DR No." />

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

          <button @click.prevent="deleteDetailForm(index)" type="button" class="bg-white py-1 px-1 flex-shrink-0 text-sm leading-none">
            <icon name="trash" class="w-4 h-4 mr-2 fill-red-600"/>
          </button>
        </div>

        <div class="px-8 py-4 flex justify-end items-center">
          <!-- <loading-button :loading="sending" class="btn-indigo" @click="addNewDetailForm">Add</loading-button> -->
          <button class="btn-indigo" @click.prevent="addNewDetailForm">Add Row</button>
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
import SelectInput from '@/Shared/SelectInput'
import TextInput from '@/Shared/TextInput'
import Icon from '@/Shared/Icon'
import Multiselect from 'vue-multiselect'
import DatePicker from 'vue2-datepicker'
import moment from 'moment'
import {throttle} from 'lodash'

export default {
  metaInfo: { title: 'Create Delivery' },
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
    tankers: Array,
    drivers: Array,
    helpers: Array,
  },
  remember: 'form',
  data() {
    return {
      selectedPurchase: undefined,
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
        tanker_id: null,
        driver_id: null,
        helper_id: null,
        purchase_id: null,
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
    }
  },
  methods: {
    submit() {
      this.$inertia.post(this.route('deliveries.store'), this.form, {
        onStart: () => this.sending = true,
        onFinish: () => this.sending = false,
      });
    },

    addNewDetailForm() {
      this.form.details.push({
        // delivery_id: null,
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
      this.$inertia.get(this.route('deliveries.create'), {term}, {
        preserveState: true,
        preserveScroll: true,
        replace: true,
      })
    }, 300),
    onSelectedPurchase(purchase) {
      this.form.purchase_id = purchase.id;
    },
    onSearchClientChange: throttle(function(term) {
      this.$inertia.get(this.route('deliveries.create'), {term}, {
        preserveState: true,
        preserveScroll: true,
        replace: true,
      })
    }, 300),
    onSelectedClient(client, id) {
      this.form.details[id].client_id = client.id;
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
