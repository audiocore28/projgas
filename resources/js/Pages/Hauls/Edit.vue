<template>
  <div>
    <h1 class="mb-8 font-bold text-3xl">
      <inertia-link class="text-blue-600 hover:text-blue-800" :href="route('hauls.index')">Hauling</inertia-link>
      <span class="text-blue-600 font-medium">/</span> {{ updateForm.client_id }}
    </h1>

    <div class="p-1">
      <ul class="flex border-b">
        <li @click="openTab = 1" :class="{ '-mb-px': openTab === 1 }" class="-mb-px mr-1">
          <a :class="openTab === 1 ? activeClasses : inactiveClasses" class="bg-white inline-block py-2 px-4 font-semibold" href="#">Edit Info</a>
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
          <!-- Update Existing Hauling -->
          <div class="bg-white rounded shadow overflow-hidden max-w-6xl mb-8 -mt-4">
            <form @submit.prevent="updateHaul">
              <!-- haul -->
              <div class="p-8 -mr-6 -mb-8 flex flex-wrap">
                <!-- <text-input v-model="updateForm.date" :error="errors.date" class="pr-6 pb-8 w-full lg:w-1/2" label="Date" /> -->
                <select-input v-model="updateForm.tanker_id" :error="errors.tanker_id" class="pr-6 pb-8 w-full lg:w-1/2" label="Tanker">
                  <option :value="null" />
                  <option v-for="tanker in tankers" :key="tanker.id" :value="tanker.id">{{ tanker.plate_no }}</option>
                </select-input>
                <select-input v-model="updateForm.driver_id" :error="errors.driver_id" class="pr-6 pb-8 w-full lg:w-1/2" label="Driver">
                  <option :value="null" />
                  <option v-for="driver in drivers" :key="driver.id" :value="driver.id">{{ driver.name }}</option>
                </select-input>
                <select-input v-model="updateForm.helper_id" :error="errors.helper_id" class="pr-6 pb-8 w-full lg:w-1/2" label="Helper">
                  <option :value="null" />
                  <option v-for="helper in helpers" :key="helper.id" :value="helper.id">{{ helper.name }}</option>
                </select-input>
                <select-input v-model="updateForm.purchase_id" :error="errors.purchase_id" class="pr-6 pb-8 w-full lg:w-1/2" label="Purchase">
                  <option :value="null" />
                  <option v-for="purchase in purchases" :key="purchase.id" :value="purchase.id">{{ purchase.purchase_no }}</option>
                </select-input>
              </div>

              <!-- Details -->
              <div class="p-8 -mr-6 -mb-8 flex flex-wrap"
                v-for="(details, index) in updateForm.details">

                <label class="form-label block ml-1">Date</label>
                <span class="pr-6 pb-8 mt-8 -ml-10">
                  <date-picker v-model="details.date" lang="en" value-type="format" :formatter="momentFormat"></date-picker>
                </span>

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

                <span style="background-color: red; color: white; cursor: pointer; float: right;" @click.prevent="deleteDetailForm(index, details.id)">X</span>
              </div>

              <div class="px-8 py-4 bg-gray-100 border-t border-gray-200 flex justify-end items-center">
                <button v-if="!haul.deleted_at" class="text-red-600 hover:underline" tabindex="-1" type="button" @click="destroy">Delete Hauling</button>
                <loading-button :loading="sending" class="btn-indigo ml-auto" type="submit">Update Hauling</loading-button>
              </div>
            </form>
          </div>


          <!-- Create Form -->
          <div class="bg-white rounded shadow overflow-hidden max-w-6xl">
            <form @submit.prevent="saveNewDetails">
              <!-- Details -->
              <div class="p-8 -mr-6 -mb-8 flex flex-wrap"
                v-for="(details, index) in createForm">

                <label class="form-label block ml-1">Date</label>
                <span class="pr-6 pb-8 mt-8 -ml-10">
                  <date-picker v-model="details.date" lang="en" value-type="format" :formatter="momentFormat"></date-picker>
                </span>

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
    haul: Object,
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
      updateForm: {
        tanker_id: this.haul.tanker_id,
        driver_id: this.haul.driver_id,
        helper_id: this.haul.helper_id,
        purchase_id: this.haul.purchase_id,
        details: this.haul.details,
      },
      createForm: [
        {
          id: null,
          haul_id: this.haul.id,
          date: null,
          client_id: null,
          product_id: null,
          quantity: null,
          unit_price: null,
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
    updateHaul() {
      this.$inertia.put(this.route('hauls.update', this.haul.id), this.updateForm, {
        onStart: () => this.sending = true,
        onFinish: () => this.sending = false,
      });
    },
    destroy() {
      if (confirm('Are you sure you want to delete this Hauling?')) {
        this.$inertia.delete(this.route('hauls.destroy', this.haul.id))
      }
    },
    restore() {
      if (confirm('Are you sure you want to restore this Hauling?')) {
        this.$inertia.put(this.route('hauls.restore', this.haul.id))
      }
    },
    deleteDetailForm(index, productDetailId) {
        if (confirm('Are you sure you want to delete this row?')) {
          this.$inertia.delete(this.route('haul-details.destroy', productDetailId));
          this.haul.details.splice(index, 1);
      }
    },

    // ----New Created Form

    // save
    saveNewDetails() {
      this.$inertia.post(this.route('haul-details.store'), this.createForm, {
        onStart: () => this.sending = true,
        onFinish: () => this.sending = false,
      });
    },
    // create form
    createNewDetailForm() {
      this.createForm.push({
        id: null,
        haul_id: this.haul.id,
        date: null,
        client_id: null,
        product_id: null,
        quantity: null,
        unit_price: null,
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
