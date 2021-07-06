<template>
  <div>
    <div v-for="error in errors" class="form-error mb-4">{{ error }}</div>
    <h1 class="mb-8 font-bold text-3xl">
      <inertia-link class="text-blue-600 hover:text-blue-800" :href="route('monthly-batangas-transactions.index')">Batangas Transaction</inertia-link>
      <span class="text-blue-600 font-medium">/</span> {{ form.date }}
    </h1>
    <form @submit.prevent="submit">
      <div class="w-full mt-8 px-4">
        <div class="w-full flex flex-wrap justify-between">
          <div class="pr-6 pb-4 lg:w-1/2">

            <date-picker v-model="form.date" type="month" placeholder="Select month" value-type="format" :formatter="momentFormatMonth"></date-picker>
            <div v-if="errors.date" class="form-error">{{ errors.date }}</div>

          </div>
        </div>
      </div>

      <!-- Transactions table input form -->
      <div class="bg-white rounded shadow overflow-x-auto">
        <table width="100%" class="w-full whitespace-nowrap shadow divide-y divide-gray-200" cellpadding="5">
          <colgroup>
            <col span="1" style="width: 10%;">
            <col span="1" style="width: 20%;">
            <col span="1" style="width: 20%;">
            <col span="1" style="width: 20%;">
            <col span="1" style="width: 15%;">
            <col span="1" style="width: 15%;">
          </colgroup>
          <thead>
            <tr class="text-left font-bold">
              <th align="center" class="px-6 pt-6 pb-4">Trip #</th>
              <th align="center" class="px-6 pt-6 pb-4">Driver</th>
              <th align="center" class="px-6 pt-6 pb-4">Helper</th>
              <th align="center" class="px-6 pt-6 pb-4">Tanker</th>
              <th></th>
              <th align="center">
                <div class="text-center whitespace-nowrap text-left text-xs font-medium text-gray-700 uppercase">
                  <form @submit.prevent="addNewDetailForm" class="flex mr-6">
                    <text-input type="number" step="any" v-model="num" min="1" max="100" class="mr-5"/>
                    <button type="submit">
                      <icon name="plus" class="w-4 h-4 mr-2 fill-green-600"/>
                    </button>
                  </form>
                </div>
              </th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <tr v-for="(transaction, index) in form.transactions" :key="index" class="bg-gradient-to-r from-yellow-500 to-blue-600">
              <td>
                <div class="text-sm font-medium text-gray-900">
                  <text-input v-model="transaction.trip_no" :error="errors[`transactions.${index}.trip_no`]"/>
                </div>
              </td>
              <td>
                <div class="text-sm font-medium text-gray-900">
                  <select :id="`driver-${index}`" v-model="transaction.driver_id" class="form-select" :class="{ error: errors[`transactions.${index}.driver_id`] }">
                    <option :value="null" />
                    <option v-for="driver in drivers" :key="driver.id" :value="driver.id">{{ driver.name }}</option>
                  </select>
                </div>
              </td>
              <td>
                <div class="text-sm font-medium text-gray-900">
                  <select :id="`helper-${index}`" v-model="transaction.helper_id" class="form-select" :class="{ error: errors[`transactions.${index}.helper_id`] }">
                    <option :value="null" />
                    <option v-for="helper in helpers" :key="helper.id" :value="helper.id">{{ helper.name }}</option>
                  </select>
                </div>
              </td>
              <td class="text-sm text-gray-500">
                <div class="text-sm font-medium text-gray-900">
                  <select :id="`tanker-${index}`" v-model="transaction.tanker_id" class="form-select" :class="{ error: errors[`transactions.${index}.tanker_id`] }">
                    <option :value="null" />
                    <option v-for="tanker in tankers" :key="tanker.id" :value="tanker.id">{{ tanker.plate_no }}</option>
                  </select>
                </div>
              </td>
              <td></td>
              <td class="text-sm text-gray-500" align="right">
                <div class=" px-5 text-sm font-medium text-gray-900">
                <!-- <div class="px-5 py-4 whitespace-nowrap text-left text-xs font-medium text-gray-500 uppercase bg-white"> -->
                  <button @click.prevent="deleteDetailForm(index)" type="button" class="py-1 px-1 flex-shrink-0 text-sm leading-none" tabindex="-1">
                    <icon name="trash" class="w-4 h-4 mr-2 fill-red-600"/>
                  </button>
                </div>
              </td>
            </tr>

          </tbody>
        </table>
      </div>
      <!-- /Transactions table input form -->

      <div class="px-8 py-4 bg-gray-100 border-t border-gray-200 flex justify-end items-center">
        <loading-button :loading="sending" class="btn-indigo" type="submit">Save</loading-button>
      </div>
    </form>
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
import { numberFormatsMixin } from '@/Mixins/numberFormatsMixin'

export default {
  mixins: [numberFormatsMixin],
  metaInfo: { title: 'Create Batangas Transaction' },
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
    tankers: Array,
    drivers: Array,
    helpers: Array,
  },
  remember: 'form',
  data() {
    return {
      num: "",
      sending: false,
      momentFormatMonth: {
        //[optional] Date to String
        stringify: (date) => {
          return date ? moment(date).format('MMMM, YYYY') : ''
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
        transactions: [
          {
            trip_no: null,
            tanker_id: null,
            driver_id: null,
            helper_id: null,
          }
        ],
      },
    }
  },
  methods: {
    showDate (date) {
      this.form.date = date;
    },

    submit() {
      this.$inertia.post(this.route('monthly-batangas-transactions.store'), this.form, {
        onStart: () => this.sending = true,
        onFinish: () => this.sending = false,
      });
    },

    // BatangasTransactionDetail
    addNewDetailForm() {
      if (this.num === "") {
        this.form.transactions.push({
          // trip_no: `M${this.form.transactions.length + 1}`,
          trip_no: null,
          tanker_id: null,
          driver_id: null,
          helper_id: null,
        });
      }

      for (let i = 0; i < this.num; i++) {
        this.form.transactions.push({
          // trip_no: `M${this.form.transactions.length + 1}`,
          trip_no: null,
          tanker_id: null,
          driver_id: null,
          helper_id: null,
        });
      }

      this.num = "";
    },
    deleteDetailForm(index) {
      this.form.transactions.splice(index, 1);
    },

  },
}
</script>

<style src="vue2-datepicker/index.css"></style>

<style>
  .highlight-yellow label.form-label {
    @apply text-white;
  }

  .multiselect__single, .multiselect__option {
    font-size: 0.875rem;
  }

  .multiselect__tag {
    @apply bg-blue-500;
  }

  .multiselect__tag-icon:after {
    @apply text-white;
  }

  .multiselect__tag-icon:hover {
    @apply bg-blue-600;
  }

  .multiselect__option--highlight {
    @apply bg-blue-500;
  }

  /*.multiselect__element span {}*/

  .multiselect--active {
    position: relative;
    z-index: 100;
  }

  .month-picker__container, .month-picker__container  {
    position: relative;
    z-index: 100;
    background-color: #fff;
  }
</style>
