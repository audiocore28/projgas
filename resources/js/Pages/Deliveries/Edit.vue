<template>
  <div>
    <h1 class="mb-8 font-bold text-3xl">
      <inertia-link class="text-blue-600 hover:text-blue-800" :href="route('deliveries.index')">Deliveries</inertia-link>
      <span class="text-blue-600 font-medium">/</span> {{ updateForm.client_id }}
    </h1>

    <div class="p-1">
      <ul class="flex border-b">
        <li @click="openTab = 1" :class="{ '-mb-px': openTab === 1 }" class="-mb-px mr-1">
          <a :class="openTab === 1 ? activeClasses : inactiveClasses" class="bg-white inline-block py-2 px-4 font-semibold" href="#">Information</a>
        </li>
        <li @click="openTab = 2" :class="{ '-mb-px': openTab === 2 }" class="mr-1">
          <a :class="openTab === 2 ? activeClasses : inactiveClasses" class="bg-white inline-block py-2 px-4 font-semibold" href="#">From PO</a>
        </li>
        <li @click="openTab = 3" :class="{ '-mb-px': openTab === 3 }" class="mr-1">
          <a :class="openTab === 3 ? activeClasses : inactiveClasses" class="bg-white inline-block py-2 px-4 font-semibold" href="#">From Load</a>
        </li>
      </ul>

      <!-- Tab 1 -->
      <div class="w-full pt-4">
        <div v-show="openTab === 1">
          <!-- Update Existing Delivery -->
          <div class="bg-white rounded shadow overflow-hidden max-w-6xl mb-8 -mt-4">
            <form @submit.prevent="updateDelivery">
              <!-- Delivery -->
              <div class="p-8 -mr-6 -mb-8 flex flex-wrap">
                <!-- <text-input v-model="updateForm.date" :error="errors.date" class="pr-6 pb-8 w-full lg:w-1/2" label="Date" /> -->
                <label class="form-label block mr-5">Date</label>
                <div class="pr-6 pb-8 w-full">
                  <date-picker v-model="updateForm.date" lang="en" value-type="format" :formatter="momentFormat"></date-picker>
                </div>
                <select-input v-model="updateForm.client_id" :error="errors.client_id" class="pr-6 pb-8 w-full lg:w-1/2" label="Client">
                  <option :value="null" />
                  <option v-for="client in clients" :key="client.id" :value="client.id">{{ client.name }}</option>
                </select-input>
                <select-input v-model="updateForm.purchase_id" :error="errors.purchase_id" class="pr-6 pb-8 w-full lg:w-1/2" label="Purchase">
                  <option :value="null" />
                  <option v-for="purchase in purchases" :key="purchase.id" :value="purchase.id">{{ purchase.purchase_no }}</option>
                </select-input>
                <select-input v-model="updateForm.tanker_load_id" :error="errors.tanker_load_id" class="pr-6 pb-8 w-full lg:w-1/2" label="Tanker Load">
                  <option :value="null" />
                  <option v-for="tanker_load in tanker_loads" :key="tanker_load.id" :value="tanker_load.id">{{ tanker_load.tanker_id }}</option>
                </select-input>
              </div>

              <!-- Details -->
              <div class="p-8 -mr-6 -mb-8 flex flex-wrap"
                v-for="(details, index) in updateForm.details">
                <select-input v-model="details.product_id" :error="errors.product_id" class="pr-6 pb-8 w-full lg:w-1/6" label="Product">
                  <option :value="null" />
                  <option v-for="product in products" :key="product.id" :value="product.id">{{ product.name }}</option>
                </select-input>
                <text-input v-model="details.quantity" :error="errors.quantity" class="pr-6 pb-8 w-full lg:w-1/6" label="Quantity" />
                <text-input v-model="details.unit_price" :error="errors.unit_price" class="pr-6 pb-8 w-full lg:w-1/6" label="Unit Price" />
                <text-input v-model="details.amount" :error="errors.amount" class="pr-6 pb-8 w-full lg:w-1/6" label="Amount" />
                <span style="background-color: red; color: white; cursor: pointer; float: right;" @click.prevent="deleteDetailForm(index, details.id)">X</span>
              </div>

              <div class="px-8 py-4 bg-gray-100 border-t border-gray-200 flex justify-end items-center">
                <button v-if="!delivery.deleted_at" class="text-red-600 hover:underline" tabindex="-1" type="button" @click="destroy">Delete Delivery</button>
                <loading-button :loading="sending" class="btn-indigo ml-auto" type="submit">Update Delivery</loading-button>
              </div>
            </form>
          </div>


          <!-- Create Form -->
          <div class="bg-white rounded shadow overflow-hidden max-w-6xl">
            <form @submit.prevent="saveNewDetails">
              <!-- Details -->
              <div class="p-8 -mr-6 -mb-8 flex flex-wrap"
                v-for="(details, index) in createForm">
                <select-input v-model="details.product_id" :error="errors.product_id" class="pr-6 pb-8 w-full lg:w-1/6" label="Product">
                  <option :value="null" />
                  <option v-for="product in products" :key="product.id" :value="product.id">{{ product.name }}</option>
                </select-input>
                <text-input v-model="details.quantity" :error="errors.quantity" class="pr-6 pb-8 w-full lg:w-1/6" label="Quantity" />
                <text-input v-model="details.unit_price" :error="errors.unit_price" class="pr-6 pb-8 w-full lg:w-1/6" label="Unit Price" />
                <text-input v-model="details.amount" :error="errors.amount" class="pr-6 pb-8 w-full lg:w-1/6" label="Amount" />

                <span style="background-color: red; color: white; cursor: pointer; float: right;" @click.prevent="deleteNewDetailForm(index)">X</span>
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
        <!-- Tab 2 -->
        <div v-show="openTab === 2">
          From PO
        </div>
        <!-- Tab 3 -->
        <div v-show="openTab === 3">
          From Load
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
import DatePicker from 'vue2-datepicker'
import moment from 'moment'

export default {
  // metaInfo() {
  //   return { title: this.form.name }
  // },
  layout: Layout,
  components: {
    Icon,
    LoadingButton,
    SelectInput,
    TextInput,
    TrashedMessage,
    DatePicker,
  },
  props: {
    errors: Object,
    delivery: Object,
    clients: Array,
    purchases: Array,
    tanker_loads: Array,
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
        date: this.delivery.date,
        client_id: this.delivery.client_id,
        purchase_id: this.delivery.purchase_id,
        tanker_load_id: this.delivery.tanker_load_id,
        details: this.delivery.details,
      },
      createForm: [
        {
          id: null,
          delivery_id: this.delivery.id,
          product_id: null,
          quantity: null,
          unit_price: null,
          amount: null,
        },
      ],
      // Tabs
      openTab: 1,
      activeClasses: 'border-l border-t border-r rounded-t text-blue-600',
      inactiveClasses: 'text-blue-500 hover:text-blue-800',
    }
  },
  methods: {
    // update existing details
    updateDelivery() {
      this.$inertia.put(this.route('deliveries.update', this.delivery.id), this.updateForm, {
        onStart: () => this.sending = true,
        onFinish: () => this.sending = false,
      });
    },
    destroy() {
      if (confirm('Are you sure you want to delete this delivery?')) {
        this.$inertia.delete(this.route('deliveries.destroy', this.delivery.id))
      }
    },
    restore() {
      if (confirm('Are you sure you want to restore this delivery?')) {
        this.$inertia.put(this.route('deliveries.restore', this.delivery.id))
      }
    },
    deleteDetailForm(index, productDetailId) {
        if (confirm('Are you sure you want to delete this row?')) {
          this.$inertia.delete(this.route('delivery-details.destroy', productDetailId));
          this.delivery.details.splice(index, 1);
      }
    },

    // ----New Created Form

    // save
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
        product_id: null,
        quantity: null,
        unit_price: null,
        amount: null,
      });
    },
    // remove form
    deleteNewDetailForm(index) {
      this.createForm.splice(index, 1);
    },

  },
}
</script>

<style src="vue2-datepicker/index.css"></style>
