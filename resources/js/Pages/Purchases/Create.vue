<template>
  <div>
    <h1 class="mb-8 font-bold text-3xl">
      <inertia-link class="text-blue-600 hover:text-blue-800" :href="route('purchases.index')">Purchases</inertia-link>
      <span class="text-blue-600 font-medium">/</span> Create
    </h1>
    <div class="bg-white rounded shadow overflow-hidden max-w-6xl">
      <form @submit.prevent="submit">
        <!-- Purchase -->
        <div class="p-8 -mr-6 -mb-8 flex flex-wrap">
          <!-- <text-input v-model="form.date" :error="errors.date" class="pr-6 pb-8 w-full lg:w-1/2" label="Date" /> -->
          <label class="form-label block mr-5">Date</label>
          <div class="pr-6 pb-8 w-full">
            <date-picker v-model="form.date" lang="en" value-type="format" :formatter="momentFormat"></date-picker>
          </div>

          <div class="pr-6 w-full">
            <text-input v-model="form.purchase_no" :error="errors.purchase_no" class="pr-6 pb-8 w-full lg:w-1/4" label="Purchase No" />
          </div>
          <select-input v-model="form.supplier_id" :error="errors.supplier_id" class="pr-6 pb-8 w-full lg:w-1/4" label="Supplier">
            <option :value="null" />
            <option v-for="supplier in suppliers" :key="supplier.id" :value="supplier.id">{{ supplier.name }}</option>
          </select-input>
        </div>

        <!-- Details -->
        <div class="px-8 py-4 -mr-6 -mb-8 flex flex-wrap" v-for="(details, index) in form.details" :key="index">
          <div class="pr-6 pb-8 w-full lg:w-1/4">
            <label class="form-label" :for="`product-${index}`">Product</label>
            <select :id="`product-${index}`" v-model="details.product_id" class="form-select" :class="{ error: errors[`details.${index}.product_id`] }">
              <option :value="null" />
              <option v-for="product in products" :key="product.id" :value="product.id">{{ product.name }}</option>
            </select>
            <div v-if="errors[`details.${index}.product_id`]" class="form-error">{{ errors[`details.${index}.product_id`] }}</div>
          </div>

          <text-input type="number" step="any" v-model="details.quantity" :error="errors.quantity" class="pr-6 pb-8 w-full lg:w-1/6" label="Quantity" />
          <text-input type="number" step="any" v-model="details.unit_price" :error="errors.unit_price" class="pr-6 pb-8 w-full lg:w-1/6" label="Unit Price" />
          <text-input v-model="details.remarks" :error="errors.remarks" class="pr-6 pb-8 w-full lg:w-1/3" label="Remarks" />

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

export default {
  metaInfo: { title: 'Create Purchase' },
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
    // purchases: Array,
    suppliers: Array,
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
  		  date: null,
        purchase_no: null,
        supplier_id: null,
        details: [
          {
            product_id: null,
            quantity: null,
            unit_price: null,
            remarks: null,
          }
        ],
      },
    }
  },
  methods: {
    submit() {
      this.$inertia.post(this.route('purchases.store'), this.form, {
        onStart: () => this.sending = true,
        onFinish: () => this.sending = false,
      });
    },

    addNewDetailForm() {
      this.form.details.push({
        // purchase_id: null,
        product_id: null,
        quantity: null,
        unit_price: null,
        remarks: null,
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