<template>
  <div>
    <h1 class="mb-8 font-bold text-3xl">
      <inertia-link class="text-blue-600 hover:text-blue-800" :href="route('monthly-mindoro-transactions.index')">Mindoro Transaction</inertia-link>
      <span class="text-blue-600 font-medium">/</span> {{ `${form.date.month}, ${form.date.year}`}}
    </h1>
    <div class="bg-white rounded shadow overflow-hidden max-w-6xl">
      <form @submit.prevent="submit">

        <!-- Transaction -->
        <div class="-mr-6 -mb-12 flex flex-wrap w-full mt-8 px-8">
          <div class="w-full flex flex-wrap justify-between">
            <div class="pr-6 pb-4 lg:w-1/2">
<!--               <label class="form-label block">Purchase No.:<span class="text-red-500">*</span></label>
              <multiselect
                id="purchase_id"
                name="purchases[]"
                v-model="form.selectedPurchases"
                placeholder=""
                class="mt-3 text-xs"
                :options="purchases"
                label="purchase_no"
                track-by="id"
                @search-change="onSearchPurchaseChange"
                @input="onSelectedPurchase"
                :show-labels="false"
                :multiple="true"
              ></multiselect>
 -->
              <!-- :allow-empty="false" -->

              <!-- <month-picker @change="showDate"></month-picker> -->
              <label class="form-label block">Month:<span class="text-red-500">*</span></label>
              <month-picker-input @input="showDate" :no-default="true"></month-picker-input>

            </div>
<!--             <div class="lg:w-1/3">
              <label class="form-label block mr-5">Date:<span class="text-red-500">*</span></label>
              <div class="pr-6 pb-4 mt-3">
                <date-picker v-model="form.date" :error="errors.date" lang="en" value-type="format" :formatter="momentFormat"></date-picker>
                <div v-if="errors.date" class="form-error">{{ errors.date }}</div>
              </div>
            </div>
 -->
          </div>

<!--           <div class="w-full flex flex-wrap bg-yellow-500 pl-6 pt-4 rounded mb-2 highlight-yellow">
          </div>
 -->
        </div>
        <!-- /Transaction -->

        <!-- Details table input form -->
        <div class="bg-white rounded overflow-x-auto mb-8 mt-4 px-8 flex flex-wrap w-full">
          <div class="mb-12 overflow-x-auto lg:w-4/6">
            <table class="min-w-full mt-8 shadow divide-y divide-gray-200">
              <colgroup>
                <col span="1" style="width: 15%;">
                <col span="1" style="width: 30%;">
                <col span="1" style="width: 30%;">
                <col span="1" style="width: 25%;">
              </colgroup>
              <thead class="bg-gray-50">
                <tr>
                  <th scope="col" class="text-center px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">
                    Trip#<span class="text-red-500">*</span>
                  </th>
                  <th scope="col" class="text-center px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">
                    Driver<span class="text-red-500">*</span>
                  </th>
                  <th scope="col" class="text-center px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">
                    Helper<span class="text-red-500">*</span>
                  </th>
                  <th scope="col" class="text-center px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">
                    Tanker<span class="text-red-500">*</span>
                  </th>
                  <th>
                    <div class="text-center px-6 py-3 whitespace-nowrap text-left text-xs font-medium text-gray-700 uppercase bg-gray">
                      <button @click.prevent="addNewDetailForm()">
                        <icon name="plus" class="w-4 h-4 mr-2 fill-green-600"/>
                      </button>
                    </div>
                  </th>
                </tr>
              </thead>
              <tbody class="bg-white divide-y divide-gray-200">
                <tr v-for="(transaction, index) in form.transactions" :key="index">
                  <td>
                    <div class="text-sm font-medium text-gray-900">
                      <text-input v-model="transaction.trip_no" :error="errors.trip_no"/>
                    </div>
                  </td>
                  <td>
                    <div class="text-sm font-medium text-gray-900">
                      <select @change="onChange($event)" :id="`driver-${index}`" v-model="transaction.driver_id" class="form-select" :class="{ error: errors[`driver_id.${index}`] }">
                        <option :value="null" />
                        <option v-for="driver in drivers" :key="driver.id" :value="driver.id">{{ driver.name }}</option>
                      </select>
                    </div>
                  </td>
                  <td>
                    <div class="text-sm font-medium text-gray-900">
                      <select :id="`helper-${index}`" v-model="transaction.helper_id" class="form-select" :class="{ error: errors[`helper_id.${index}`] }">
                        <option :value="null" />
                        <option v-for="helper in helpers" :key="helper.id" :value="helper.id">{{ helper.name }}</option>
                      </select>
                    </div>
                  </td>
                  <td class="text-sm text-gray-500">
                    <div class="text-sm font-medium text-gray-900">
                      <select :id="`tanker-${index}`" v-model="transaction.tanker_id" class="form-select" :class="{ error: errors[`tanker_id.${index}`] }">
                        <option :value="null" />
                        <option v-for="tanker in tankers" :key="tanker.id" :value="tanker.id">{{ tanker.plate_no }}</option>
                      </select>
                    </div>
                  </td>
                  <td class="text-sm text-gray-500">
                    <div class=" px-5 text-sm font-medium text-gray-900">
                    <!-- <div class="px-5 py-4 whitespace-nowrap text-left text-xs font-medium text-gray-500 uppercase bg-white"> -->
                      <button @click.prevent="deleteDetailForm(index)" type="button" class="bg-white py-1 px-1 flex-shrink-0 text-sm leading-none" tabindex="-1">
                        <icon name="trash" class="w-4 h-4 mr-2 fill-red-600"/>
                      </button>
                    </div>
                  </td>
                </tr>

              </tbody>
            </table>
          </div>
          <div>
            Driver list
            <div v-for="driver in assignedDrivers" :key="driver">{{ driver }}</div>
          </div>
          <!-- /Details table input form -->
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
import { MonthPicker } from 'vue-month-picker'
import { MonthPickerInput } from 'vue-month-picker'
import moment from 'moment'
import {throttle} from 'lodash'
import { numberFormatsMixin } from '@/Mixins/numberFormatsMixin'

export default {
  mixins: [numberFormatsMixin],
  metaInfo: { title: 'Create Mindoro Transaction' },
  layout: Layout,
  components: {
    LoadingButton,
    SelectInput,
    TextInput,
    Multiselect,
    DatePicker,
    MonthPicker,
    MonthPickerInput,
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
      assignedDrivers: [],
      driverSet: new Set(),
      totalLoad: 0,
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
        date: {
          // from: null,
          // to: null,
          month: null,
          year: null,
        },
        transactions: [
          {
            // trip_no: 'M1',
            trip_no: 'M',
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

    onChange (event) {
      // this.driverSet.add(event.target.value);
      // this.assignedDrivers = [...this.driverSet];
      // const weee = this.assignedDrivers.map(driver => {
      //   return driver + 'weee';
      // });

      // console.log(weee);
      // const filteredArr = this.form.transactions.reduce((acc, current) => {
      //   const x = acc.find(item => item.id === current.id);
      //   if (!x) {
      //     return acc.concat([current]);
      //   } else {
      //     return acc;
      //   }
      // }, []);

      // console.log(filteredArr);
    },

    submit() {
      this.$inertia.post(this.route('monthly-mindoro-transactions.store'), this.form, {
        onStart: () => this.sending = true,
        onFinish: () => this.sending = false,
      });
    },

    // MindoroTransactionDetail
    addNewDetailForm() {
      this.form.transactions.push({
        // trip_no: `M${this.form.transactions.length + 1}`,
        trip_no: 'M',
        tanker_id: null,
        driver_id: null,
        helper_id: null,
      });
    },
    deleteDetailForm(index) {
      this.form.transactions.splice(index, 1);
    },

  // watch: {
  //   'form.transactions': {
  //     handler: function (val, oldVal) {
  //       //
  //     },
  //     deep: true,
  //   },
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
