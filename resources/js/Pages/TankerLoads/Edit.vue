<template>
  <div>
    <h1 class="mb-8 font-bold text-3xl">
      <inertia-link class="text-blue-600 hover:text-blue-800" :href="route('tanker-loads.index')">Loads</inertia-link>
      <!-- <span class="text-blue-600 font-medium">/</span> {{ value }} -->
    </h1>

    <div class="p-1">
      <ul class="flex border-b">
        <li @click="openTab = 1" :class="{ '-mb-px': openTab === 1 }" class="-mb-px mr-1">
          <a :class="openTab === 1 ? activeClasses : inactiveClasses" class="bg-white inline-block py-2 px-4 font-semibold" href="#">Edit Load</a>
        </li>
        <li @click="openTab = 2" :class="{ '-mb-px': openTab === 2 }" class="-mb-px mr-1">
          <a :class="openTab === 2 ? activeClasses : inactiveClasses" class="bg-white inline-block py-2 px-4 font-semibold" href="#">Add Row</a>
        </li>
      </ul>

      <div class="w-full pt-4">
        <!-- Tab 1 -->
        <div v-show="openTab === 1">
          <!-- Update Existing Load -->
          <div class="bg-white rounded shadow overflow-hidden max-w-6xl mb-8 -mt-4">
            <form @submit.prevent="updateLoad">
              <!-- Load -->
              <div class="p-8 -mr-6 -mb-8 flex flex-wrap">
                <label class="form-label block mr-5">Date:</label>
                <div class="pr-6 pb-8 w-full">
                  <date-picker v-model="updateForm.date" lang="en" value-type="format" :formatter="momentFormat"></date-picker>
                </div>

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
                <text-input v-model="updateForm.remarks" :error="errors.remarks" class="pr-6 pb-8 w-full lg:w-1/2" label="Remarks" />
              </div>

              <!-- Details -->
              <div class="p-8 -mr-6 -mb-8 flex flex-wrap" v-for="(details, index) in updateForm.details" :key="index">
                <div class="pr-6 pb-8 w-full lg:w-1/4">
                  <label class="form-label" :for="`product-${index}`">Product:</label>
                  <select :id="`product-${index}`" v-model="details.product_id" class="form-select" :class="{ error: errors[`details.${index}.product_id`] }">
                    <option :value="null" />
                    <option v-for="product in products" :key="product.id" :value="product.id">{{ product.name }}</option>
                  </select>
                  <div v-if="errors[`details.${index}.product_id`]" class="form-error">{{ errors[`details.${index}.product_id`] }}</div>
                </div>

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
        </div>

        <!-- Tab 2 -->
        <div v-show="openTab === 2">
          <tanker-load-create-form
            :errors="errors"
            :tanker_load="tanker_load"
            :products="products">
          </tanker-load-create-form>
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
import TankerLoadCreateForm from '@/Shared/TankerLoadCreateForm'
import moment from 'moment'
import {throttle} from 'lodash'
import Multiselect from 'vue-multiselect'

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
    TankerLoadCreateForm,
    Multiselect,
  },
  props: {
    errors: Object,
    tanker_load: Object,
    purchases: {
      type: Array,
      default: () => [],
    },
    tankers: Array,
    drivers: Array,
    helpers: Array,
    products: Array,
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
      // value: null,
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

    // Multiselect
    onSearchPurchaseChange: throttle(function(term) {
      this.$inertia.get(this.route('tanker-loads.edit'), {term}, {
        preserveState: true,
        preserveScroll: true,
        replace: true,
      })
    }, 300),
    onSelectedPurchase(purchase) {
      this.updateForm.purchase_id = purchase.id;
    },

  },
  mounted() {
    this.selectedPurchase = this.purchases.find(purchase => purchase.id === this.updateForm.purchase_id);
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
