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
          <select-input v-model="form.purchase_id" :error="errors.purchase_id" class="pr-6 pb-8 w-full lg:w-1/2" label="Purchase">
            <option :value="null" />
            <option v-for="purchase in purchases" :key="purchase.id" :value="purchase.id">{{ purchase.purchase_no }}</option>
          </select-input>

          <text-input v-model="form.trip_no" :error="errors.trip_no" class="pr-6 pb-8 w-full lg:w-1/2" label="Trip No." />

          <select-input v-model="form.tanker_id" :error="errors.tanker_id" class="pr-6 pb-8 w-full lg:w-1/2" label="Tanker">
            <option :value="null" />
            <option v-for="tanker in tankers" :key="tanker.id" :value="tanker.id">{{ tanker.plate_no }}</option>
          </select-input>
          <select-input v-model="form.driver_id" :error="errors.driver_id" class="pr-6 pb-8 w-full lg:w-1/2" label="Driver">
            <option :value="null" />
            <option v-for="driver in drivers" :key="driver.id" :value="driver.id">{{ driver.name }}</option>
          </select-input>
          <select-input v-model="form.helper_id" :error="errors.helper_id" class="pr-6 pb-8 w-full lg:w-1/2" label="Helper">
            <option :value="null" />
            <option v-for="helper in helpers" :key="helper.id" :value="helper.id">{{ helper.name }}</option>
          </select-input>
        </div>

        <!-- Details -->
        <div class="p-8 -mr-6 -mb-8 flex flex-wrap"
          v-for="(details, index) in form.details">

          <label class="form-label block ml-1">Date</label>
          <span class="pr-6 pb-8 mt-8 -ml-10">
            <date-picker v-model="details.date" lang="en" value-type="format" :formatter="momentFormat"></date-picker>
          </span>

          <text-input v-model="details.dr_no" :error="errors.dr_no" class="pr-6 pb-8 w-full lg:w-1/6" label="DR No." />
          <select-input v-model="details.client_id" :error="errors.client_id" class="pr-6 pb-8 w-full lg:w-1/6" label="Client">
            <option :value="null" />
            <option v-for="client in clients" :key="client.id" :value="client.id">{{ client.name }}</option>
          </select-input>
          <select-input v-model="details.product_id" :error="errors.product_id" class="pr-6 pb-8 w-full lg:w-1/6" label="Product">
            <option :value="null" />
            <option v-for="product in products" :key="product.id" :value="product.id">{{ product.name }}</option>
          </select-input>
          <text-input type="number" step="any" v-model="details.quantity" :error="errors.quantity" class="pr-6 pb-8 w-full lg:w-1/6" label="Quantity" />
          <text-input type="number" step="any" v-model="details.unit_price" :error="errors.unit_price" class="pr-6 pb-8 w-full lg:w-1/6" label="Unit Price" />

          <span style="background-color: red; color: white; cursor: pointer; float: right;" @click.prevent="deleteDetailForm(index)">X</span>
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
import Multiselect from 'vue-multiselect'
import DatePicker from 'vue2-datepicker'
import moment from 'moment'

export default {
  metaInfo: { title: 'Create Delivery' },
  layout: Layout,
  components: {
    LoadingButton,
    SelectInput,
    TextInput,
    Multiselect,
    DatePicker,
  },
  props: {
    errors: Object,
    clients: Array,
    purchases: Array,
    products: Array,
    tankers: Array,
    drivers: Array,
    helpers: Array,
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
  		  date: null,
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
        product_id: null,
        quantity: null,
        unit_price: null,
      });
    },
    deleteDetailForm(index) {
      this.form.details.splice(index, 1);
    }
  },

}
</script>

<style src="vue-multiselect/dist/vue-multiselect.min.css"></style>
<style src="vue2-datepicker/index.css"></style>
