<template>
  <div>
    <!-- Create Form -->
    <div class="bg-white rounded shadow overflow-hidden max-w-6xl pt-4 -mt-4">
      <form @submit.prevent="saveNewDetails">
        <!-- Details -->
        <div class="px-8 py-4 -mr-6 -mb-8 flex flex-wrap" v-for="(details, index) in createForm" :key="index">

          <label class="form-label block mr-5">Date:</label>
          <div class="pr-6 pb-8 w-full">
            <date-picker v-model="details.date" lang="en" value-type="format" :formatter="momentFormat"></date-picker>
          </div>

<!--           <div class="pr-6 pb-8 w-full lg:w-1/4">
            <label class="form-label" :for="`client-${index}`">Client:</label>
            <select :id="`client-${index}`" v-model="details.client_id" class="form-select" :class="{ error: errors[`${index}.client_id`] }">
              <option :value="null" />
              <option v-for="client in clients" :key="client.id" :value="client.id">{{ client.name }}</option>
            </select>
            <div v-if="errors[`${index}.client_id`]" class="form-error">{{ errors[`${index}.client_id`] }}</div>
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
            <div v-if="errors[`${index}.client_id`]" class="form-error">{{ errors[`${index}.client_id`] }}</div>
          </div>

          <text-input v-model="details.dr_no" :error="errors.dr_no" class="pr-6 pb-8 w-full lg:w-1/6" label="DR No." />

          <div class="pr-6 pb-8 w-full lg:w-1/6">
            <label class="form-label" :for="`product-${index}`">Product:</label>
            <select :id="`product-${index}`" v-model="details.product_id" class="form-select" :class="{ error: errors[`${index}.product_id`] }">
              <option :value="null" />
              <option v-for="product in products" :key="product.id" :value="product.id">{{ product.name }}</option>
            </select>
            <div v-if="errors[`${index}.product_id`]" class="form-error">{{ errors[`${index}.product_id`] }}</div>
          </div>

          <text-input type="number" step="any" v-model="details.quantity" :error="errors.quantity" class="pr-6 pb-8 w-full lg:w-1/6" label="Quantity" />
          <text-input type="number" step="any" v-model="details.unit_price" :error="errors.unit_price" class="pr-6 pb-8 w-full lg:w-1/6" label="Unit Price" />

          <button @click.prevent="deleteNewDetailForm(index)" type="button" class="bg-white py-1 px-1 flex-shrink-0 text-sm leading-none">
            <icon name="trash" class="w-4 h-4 mr-2 fill-red-600"/>
          </button>
        </div>

        <div class="px-8 py-4 flex justify-end items-center">
          <button class="btn-indigo" @click.prevent="createNewDetailForm">Add Row</button>
        </div>

        <div class="px-8 py-4 bg-gray-100 border-t border-gray-200 flex justify-end items-center">
          <loading-button :loading="sending" class="btn-indigo ml-auto" type="submit">Save Details</loading-button>
        </div>
      </form>
    </div>
  </div>
</template>

<script>
import Icon from '@/Shared/Icon'
import LoadingButton from '@/Shared/LoadingButton'
import SelectInput from '@/Shared/SelectInput'
import TextInput from '@/Shared/TextInput'
import Multiselect from 'vue-multiselect'
import DatePicker from 'vue2-datepicker'
import moment from 'moment'
import {throttle} from 'lodash'

export default {
  components: {
    Icon,
    LoadingButton,
    SelectInput,
    TextInput,
    DatePicker,
    Multiselect,
  },
  props: {
    errors: Object,
    delivery: Object,
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
      createForm: [
        {
          id: null,
          delivery_id: this.delivery.id,
          date: null,
          dr_no: null,
          client_id: null,
          selectedClient: null,
          product_id: null,
          quantity: null,
          unit_price: null,
        },
      ],
    }
  },
  methods: {
    saveNewDetails() {
      this.$inertia.post(this.route('delivery-details.store'), this.createForm, {
        onStart: () => this.sending = true,
        onFinish: () => this.sending = false,
      });
    },
    // create form
    createNewDetailForm() {
      this.createForm.push({
        id: null,
        delivery_id: this.delivery.id,
        date: null,
        dr_no: null,
        client_id: null,
        selectedClient: null,
        product_id: null,
        quantity: null,
        unit_price: null,
      });
    },
    // remove form
    deleteNewDetailForm(index) {
      this.createForm.splice(index, 1);
    },

    onSearchClientChange: throttle(function(term) {
      this.$inertia.get(this.route('deliveries.edit'), {term}, {
        preserveState: true,
        preserveScroll: true,
        replace: true,
      })
    }, 300),
    onSelectedClient(client, id) {
      this.createForm[id].client_id = client.id;
    },
  },

}
</script>

<style src="vue2-datepicker/index.css"></style>
