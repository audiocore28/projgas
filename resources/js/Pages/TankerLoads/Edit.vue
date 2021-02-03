<template>
  <div>
    <h1 class="mb-8 font-bold text-3xl">
      <inertia-link class="text-blue-600 hover:text-blue-800" :href="route('tanker-loads.index')">Loads</inertia-link>
      <span class="text-blue-600 font-medium">/</span> {{ updateForm.purchase_id }}
    </h1>

    <div class="p-1">
<!--       <ul class="flex border-b">
        <li @click="openTab = 1" :class="{ '-mb-px': openTab === 1 }" class="-mb-px mr-1">
          <a :class="openTab === 1 ? activeClasses : inactiveClasses" class="bg-white inline-block py-2 px-4 font-semibold" href="#">Info</a>
        </li>
      </ul>
 -->
      <div class="w-full pt-4">
        <!-- Tab 1 -->
        <div v-show="openTab === 1">
          <!-- Update Existing Load -->
          <div class="bg-white rounded shadow overflow-hidden max-w-6xl mb-8 -mt-4">
            <form @submit.prevent="updateLoad">
              <!-- Load -->
              <div class="p-8 -mr-6 -mb-8 flex flex-wrap">
                <label class="form-label block mr-5">Date</label>
                <div class="pr-6 pb-8 w-full">
                  <date-picker v-model="updateForm.date" lang="en" value-type="format" :formatter="momentFormat"></date-picker>
                </div>

                <div class="pr-6 pb-2 w-full">
                  <select-input v-model="updateForm.purchase_id" :error="errors.purchase_id" class="pr-6 pb-8 w-full lg:w-1/4" label="Purchase No.">
                    <option :value="null" />
                    <option v-for="purchase in purchases" :key="purchase.id" :value="purchase.id">{{ purchase.purchase_no }}</option>
                  </select-input>
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
                <text-input v-model="updateForm.remarks" :error="errors.remarks" class="pr-6 pb-8 w-full lg:w-1/2" label="Remarks" />
              </div>

              <!-- Details -->
              <div class="p-8 -mr-6 -mb-8 flex flex-wrap"
                v-for="(details, index) in updateForm.details">
                <select-input v-model="details.product_id" :error="errors.product_id" class="pr-6 pb-8 w-full lg:w-1/4" label="Product">
                  <option :value="null" />
                  <option v-for="product in products" :key="product.id" :value="product.id">{{ product.name }}</option>
                </select-input>
                <text-input type="number" step="any" v-model="details.quantity" :error="errors.quantity" class="pr-6 pb-8 w-full lg:w-1/6" label="Quantity" />

                <button @click.prevent="deleteDetailForm(index, details.id)" type="button" class="bg-white py-1 px-1 flex-shrink-0 text-sm leading-none">
                  <icon name="trash" class="w-4 h-4 mr-2 fill-red-600"/>
                </button>
              </div>
              <div class="px-8 py-4 bg-gray-100 border-t border-gray-200 flex justify-end items-center">
                <button v-if="!tanker_load.deleted_at" class="text-red-600 hover:underline" tabindex="-1" type="button" @click="destroy">Delete Load</button>
                <loading-button :loading="sending" class="btn-indigo ml-auto" type="submit">Update Load</loading-button>
              </div>
            </form>
          </div>


          <!-- Create Form -->
          <div class="bg-white rounded shadow overflow-hidden max-w-6xl pt-4">
            <form @submit.prevent="saveNewDetails">
              <!-- Details -->
              <div class="px-8 py-4 -mr-6 -mb-8 flex flex-wrap"
                v-for="(details, index) in createForm">
                <select-input v-model="details.product_id" :error="errors.product_id" class="pr-6 pb-8 w-full lg:w-1/4" label="Product">
                  <option :value="null" />
                  <option v-for="product in products" :key="product.id" :value="product.id">{{ product.name }}</option>
                </select-input>
                <text-input type="number" step="any" v-model="details.quantity" :error="errors.quantity" class="pr-6 pb-8 w-full lg:w-1/6" label="Quantity" />

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
  metaInfo: { title: 'Edit Load' },
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
    tanker_load: Object,
    purchases: Array,
    tankers: Array,
    drivers: Array,
    helpers: Array,
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
        date: this.tanker_load.date,
        trip_no: this.tanker_load.trip_no,
        purchase_id: this.tanker_load.purchase_id,
        tanker_id: this.tanker_load.tanker_id,
        driver_id: this.tanker_load.driver_id,
        helper_id: this.tanker_load.helper_id,
        remarks: this.tanker_load.remarks,
        details: this.tanker_load.details,
      },
      createForm: [
        {
          id: null,
          tanker_load_id: this.tanker_load.id,
          product_id: null,
          quantity: null,
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
    updateLoad() {
      this.$inertia.put(this.route('tanker-loads.update', this.tanker_load.id), this.updateForm, {
        onStart: () => this.sending = true,
        onFinish: () => this.sending = false,
      });
    },
    destroy() {
      if (confirm('Are you sure you want to permanently delete this load and its details?')) {
        this.$inertia.delete(this.route('tanker-loads.destroy', this.tanker_load.id))
      }
    },
    restore() {
      if (confirm('Are you sure you want to restore this load?')) {
        this.$inertia.put(this.route('tanker-loads.restore', this.tanker_load.id))
      }
    },
    deleteDetailForm(index, detailId) {
        if (confirm('Are you sure you want to delete this row?')) {
          this.$inertia.delete(this.route('tanker-load-details.destroy', detailId));
          this.tanker_load.details.splice(index, 1);
      }
    },

    // ----New Created Form

    // save
    saveNewDetails() {
      this.$inertia.post(this.route('tanker-load-details.store'), this.createForm, {
        onStart: () => this.sending = true,
        onFinish: () => this.sending = false,
      });
    },
    // create form
    createNewDetailForm() {
      this.createForm.push({
        id: null,
        tanker_load_id: this.tanker_load.id,
        product_id: null,
        quantity: null,
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
