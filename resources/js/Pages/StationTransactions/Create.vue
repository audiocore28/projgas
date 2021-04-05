<template>
  <div>
    <h1 class="mb-8 font-bold text-3xl">
      <inertia-link class="text-blue-600 hover:text-blue-800" :href="route('station-transactions.index')">Transactions</inertia-link>
      <span class="text-blue-600 font-medium">/</span> Create
    </h1>
    <div class="bg-white rounded shadow overflow-hidden max-w-6xl">
      <!-- Station Transaction -->
      <form @submit.prevent="submit">
        <div class="p-8 -mr-6 -mb-8">
          <div class="pr-6 w-full">
            <select-input v-model="form.station_id" :error="errors.station_id" class="pr-6 pb-8 lg:w-1/3 w-full" label="G. Station">
              <option :value="null" />
              <option v-for="station in stations" :key="station.id" :value="station.id">{{ station.name }}</option>
            </select-input>
          </div>

          <label class="form-label block mr-5">Date:</label>
          <div class="pr-6 pb-8 mb-2 w-full">
            <date-picker v-model="form.date" lang="en" value-type="format" :formatter="momentFormat"></date-picker>
          </div>

          <text-input v-model="form.shift" :error="errors.shift" class="pr-6 pb-8 w-full lg:w-1/4" label="Shift" />

          <div class="w-full flex">

            <div class="pr-6 pb-2 lg:w-1/3">
              <select-input v-model="form.cashier_id" :error="errors.cashier_id" class="pr-6 pb-8" label="Cashier">
                <option :value="null" />
                <option v-for="cashier in cashiers" :key="cashier.id" :value="cashier.id">{{ cashier.name }}</option>
              </select-input>
            </div>

            <div class="pr-6 pb-2 lg:w-1/3">
              <select-input v-model="form.pump_attendant_id" :error="errors.pump_attendant_id" class="pr-6 pb-8" label="Pump Attendant">
                <option :value="null" />
                <option v-for="pump_attendant in pump_attendants" :key="pump_attendant.id" :value="pump_attendant.id">{{ pump_attendant.name }}</option>
              </select-input>
            </div>

            <div class="pr-6 pb-2 lg:w-1/3">
              <select-input v-model="form.office_staff_id" :error="errors.office_staff_id" class="pr-6 pb-8" label="Audited By">
                <option :value="null" />
                <option v-for="office_staff in office_staffs" :key="office_staff.id" :value="office_staff.id">{{ office_staff.name }}</option>
              </select-input>
            </div>
          </div>

        </div>

        <!-- I. Pump Reading -->
        <h4 @click="readingSelected !== 0 ? readingSelected = 0 : readingSelected = null"
        class="font-bold text-1xl cursor-pointer px-5 py-3 block hover:opacity-75 shadow hover:-mb-3 rounded-t">I. Pump Reading</h4>
        <div v-show="readingSelected == 0" class="py-4 px-2">
          <div class="px-8 py-4 -mr-6 -mb-8 flex flex-wrap" v-for="(reading, index) in form.pump_readings" :key="index">
            <text-input v-model="reading.pump" :error="errors.pump" class="pr-6 pb-8 w-full lg:w-1/6" label="Pump" />
            <text-input type="number" step="any" v-model="reading.opening" :error="errors.opening" class="pr-6 pb-8 w-full lg:w-1/6" label="Opening" />
            <text-input type="number" step="any" v-model="reading.closing" :error="errors.closing" class="pr-6 pb-8 w-full lg:w-1/6" label="Closing" />
            <text-input type="number" step="any" v-model="reading.unit_price" :error="errors.unit_price" class="pr-6 pb-8 w-full lg:w-1/6" label="Unit Price" />

            <button @click.prevent="deletePumpReadingsForm(index)" type="button" class="bg-white py-1 px-1 flex-shrink-0 text-sm leading-none">
              <icon name="trash" class="w-4 h-4 mr-2 fill-red-600"/>
            </button>
          </div>

          <div class="px-8 py-4 flex justify-end items-center">
            <button class="btn-indigo" @click.prevent="addPumpReadingsForm">Add Row</button>
          </div>
        </div>

        <!-- II. IBB Sales -->
        <h4 @click="saleSelected !== 1 ? saleSelected = 1 : saleSelected = null"
        class="font-bold text-1xl cursor-pointer px-5 py-3 block hover:opacity-75 shadow hover:-mb-3 rounded-t">II. IBB Sales</h4>
        <div v-show="saleSelected == 1" class="py-4 px-2">
          <div class="px-8 py-4 -mr-6 -mb-8 flex flex-wrap" v-for="(sale, index) in form.sales" :key="index">
            <text-input v-model="sale.pump_no" :error="errors.pump_no" class="pr-6 pb-8 w-full lg:w-1/12" label="Pump#" />
            <text-input v-model="sale.dr_no" :error="errors.dr_no" class="pr-6 pb-8 w-full lg:w-2/12" label="DR No." />

            <div class="pr-6 pb-8 w-full lg:w-2/12">
              <label class="form-label" :for="`client-${index}`">Client:</label>
              <select :id="`client-${index}`" v-model="sale.client_id" class="form-select" :class="{ error: errors[`sales.${index}.client_id`] }">
                <option :value="null" />
                <option v-for="client in clients" :key="client.id" :value="client.id">{{ client.name }}</option>
              </select>
              <div v-if="errors[`sales.${index}.client_id`]" class="form-error">{{ errors[`sales.${index}.client_id`] }}</div>
            </div>

            <div class="pr-6 pb-8 w-full lg:w-2/12">
              <label class="form-label" :for="`product-${index}`">Product:</label>
              <select :id="`product-${index}`" v-model="sale.product_id" class="form-select" :class="{ error: errors[`sales.${index}.product_id`] }">
                <option :value="null" />
                <option v-for="product in products" :key="product.id" :value="product.id">{{ product.name }}</option>
              </select>
              <div v-if="errors[`sales.${index}.product_id`]" class="form-error">{{ errors[`sales.${index}.product_id`] }}</div>
            </div>

            <text-input type="number" step="any" v-model="sale.quantity" :error="errors.quantity" class="pr-6 pb-8 w-full lg:w-2/12" label="Quantity" />

            <text-input v-model="sale.rs_no" :error="errors.rs_no" class="pr-6 pb-8 w-full lg:w-2/12" label="RS No." />

            <button @click.prevent="deleteSalesForm(index)" type="button" class="bg-white py-1 px-1 flex-shrink-0 text-sm leading-none">
              <icon name="trash" class="w-4 h-4 mr-2 fill-red-600"/>
            </button>
          </div>

          <div class="px-8 py-4 flex justify-end items-center">
            <button class="btn-indigo" @click.prevent="addSalesForm">Add Row</button>
          </div>
        </div>

        <!-- III. Company Vale -->
        <h4 @click="valeSelected !== 2 ? valeSelected = 2 : valeSelected = null"
        class="font-bold text-1xl cursor-pointer px-5 py-3 block hover:opacity-75 shadow hover:-mb-3 rounded-t">III. Company Vale</h4>
        <div v-show="valeSelected == 2" class="py-4 px-2">
          <div class="px-8 py-4 -mr-6 -mb-8 flex flex-wrap" v-for="(vale, index) in form.company_vales" :key="index">
            <text-input v-model="vale.pump_no" :error="errors.pump_no" class="pr-6 pb-8 w-full lg:w-1/12" label="Pump#" />
            <text-input v-model="vale.voucher_no" :error="errors.voucher_no" class="pr-6 pb-8 w-full lg:w-1/6" label="Voucher No." />

            <div class="pr-6 pb-8 w-full lg:w-1/6">
              <label class="form-label" :for="`company-${index}`">Company:</label>
              <select :id="`company-${index}`" v-model="vale.company_id" class="form-select" :class="{ error: errors[`company_vales.${index}.company_id`] }">
                <option :value="null" />
                <option v-for="company in companies" :key="company.id" :value="company.id">{{ company.name }}</option>
              </select>
              <div v-if="errors[`company_vales.${index}.company_id`]" class="form-error">{{ errors[`company_vales.${index}.company_id`] }}</div>
            </div>

            <div class="pr-6 pb-8 w-full lg:w-1/6">
              <label class="form-label" :for="`product-${index}`">Product:</label>
              <select :id="`product-${index}`" v-model="vale.product_id" class="form-select" :class="{ error: errors[`company_vales.${index}.product_id`] }">
                <option :value="null" />
                <option v-for="product in products" :key="product.id" :value="product.id">{{ product.name }}</option>
              </select>
              <div v-if="errors[`company_vales.${index}.product_id`]" class="form-error">{{ errors[`company_vales.${index}.product_id`] }}</div>
            </div>

            <text-input type="number" step="any" v-model="vale.quantity" :error="errors.quantity" class="pr-6 pb-8 w-full lg:w-1/6" label="Quantity" />
            <text-input v-model="vale.remarks" :error="errors.remarks" class="pr-6 pb-8 w-full lg:w-1/6" label="Remarks" />

            <button @click.prevent="deleteCompanyValesForm(index)" type="button" class="bg-white py-1 px-1 flex-shrink-0 text-sm leading-none">
              <icon name="trash" class="w-4 h-4 mr-2 fill-red-600"/>
            </button>
          </div>

          <div class="px-8 py-4 flex justify-end items-center">
            <button class="btn-indigo" @click.prevent="addCompanyValesForm">Add Row</button>
          </div>
        </div>

        <!-- IV. Calibration -->
        <h4 @click="calibrationSelected !== 3 ? calibrationSelected = 3 : calibrationSelected = null"
        class="font-bold text-1xl cursor-pointer px-5 py-3 block hover:opacity-75 shadow hover:-mb-3 rounded-t">IV. Calibration</h4>
        <div v-show="calibrationSelected == 3" class="py-4 px-2">
          <div class="px-8 py-4 -mr-6 -mb-8 flex flex-wrap" v-for="(calibration, index) in form.calibrations" :key="index">
            <text-input v-model="calibration.pump" :error="errors.pump" class="pr-6 pb-8 w-full lg:w-1/6" label="Pump" />
            <text-input type="number" step="any" v-model="calibration.quantity" :error="errors.quantity" class="pr-6 pb-8 w-full lg:w-1/6" label="Quantity" />
            <text-input v-model="calibration.pump_no" :error="errors.pump_no" class="pr-6 pb-8 w-full lg:w-1/6" label="Pump No." />
            <text-input v-model="calibration.voucher_no" :error="errors.voucher_no" class="pr-6 pb-8 w-full lg:w-1/6" label="Voucher No." />

            <button @click.prevent="deleteCalibrationsForm(index)" type="button" class="bg-white py-1 px-1 flex-shrink-0 text-sm leading-none">
              <icon name="trash" class="w-4 h-4 mr-2 fill-red-600"/>
            </button>
          </div>

          <div class="px-8 py-4 flex justify-end items-center">
            <button class="btn-indigo" @click.prevent="addCalibrationsForm">Add Row</button>
          </div>
        </div>

        <!-- V. Discounted -->
        <h4 @click="discountSelected !== 4 ? discountSelected = 4 : discountSelected = null"
        class="font-bold text-1xl cursor-pointer px-5 py-3 block hover:opacity-75 shadow hover:-mb-3 rounded-t">V. Discounted</h4>
        <div v-show="discountSelected == 4" class="py-4 px-2">
          <div class="px-8 py-4 -mr-6 -mb-8 flex flex-wrap" v-for="(discount, index) in form.discounts" :key="index">
            <text-input v-model="discount.voucher_no" :error="errors.voucher_no" class="pr-6 pb-8 w-full lg:w-1/6" label="Voucher No." />
            <text-input type="number" step="any" v-model="discount.cash" :error="errors.cash" class="pr-6 pb-8 w-full lg:w-1/6" label="Cash" />

            <div class="pr-6 pb-8 w-full lg:w-1/4">
              <label class="form-label" :for="`client-${index}`">Client:</label>
              <select :id="`client-${index}`" v-model="discount.client_id" class="form-select" :class="{ error: errors[`discounts.${index}.client_id`] }">
                <option :value="null" />
                <option v-for="client in clients" :key="client.id" :value="client.id">{{ client.name }}</option>
              </select>
              <div v-if="errors[`discounts.${index}.client_id`]" class="form-error">{{ errors[`discounts.${index}.client_id`] }}</div>
            </div>

            <text-input type="number" step="any" v-model="discount.quantity" :error="errors.quantity" class="pr-6 pb-8 w-full lg:w-1/6" label="Quantity" />

            <text-input type="number" step="any" v-model="discount.disc_amount" :error="errors.disc_amount" class="pr-6 pb-8 w-full lg:w-1/6" label="Disc Amount" />

            <button @click.prevent="deleteDiscountsForm(index)" type="button" class="bg-white py-1 px-1 flex-shrink-0 text-sm leading-none">
              <icon name="trash" class="w-4 h-4 mr-2 fill-red-600"/>
            </button>
          </div>

          <div class="px-8 py-4 flex justify-end items-center">
            <button class="btn-indigo" @click.prevent="addDiscountsForm">Add Row</button>
          </div>
        </div>

        <!--  -->

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
  metaInfo: { title: 'Create Transactions' },
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
    products: Array,
    clients: Array,
    companies: Array,
    stations: Array,
    cashiers: Array,
    pump_attendants: Array,
    office_staffs: Array,
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
        station_id: null,
        date: null,
        shift: null,
        cashier_id: null,
        pump_attendant_id: null,
        office_staff_id: null,
        pump_readings: [
          {
            pump: null,
            opening: null,
            closing: null,
            unit_price: null,
          }
        ],
        sales: [
          {
            pump_no: null,
            dr_no: null,
            client_id: null,
            product_id: null,
            quantity: null,
            rs_no: null,
          }
        ],
        company_vales: [
          {
            pump_no: null,
            voucher_no: null,
            company_id: null,
            product_id: null,
            quantity: null,
            remarks: null,
          }
        ],
        calibrations: [
          {
            pump: null,
            quantity: null,
            pump_no: null,
            voucher_no: null,
          }
        ],
        discounts: [
          {
            voucher_no: null,
            cash: null,
            client_id: null,
            quantity: null,
            disc_amount: null,
          }
        ],
      },
      // Accordion
      readingSelected: 0,
      saleSelected: 1,
      valeSelected: 2,
      calibrationSelected: 3,
      discountSelected: 4,
    }
  },
  watch: {
    'form.station_id': function (value) {
      axios.get(`/stations/${value}/edit`)
        .then(({ data }) => {
          console.log(data);

          var pumpReadings = data.pumps.map(value => {
            return {
              pump: `${value.pump}-${value.product.name} (${value.nozzle})`,
              opening: null,
              closing: null,
              unit_price: null,
            };
          });

          this.form.pump_readings = [
            ...pumpReadings,
          ];

          var pumpCalibrations = data.pumps.map(value => {
            return {
              pump: `${value.pump}-${value.product.name} (${value.nozzle})`,
              quantity: null,
              pump_no: null,
              voucher_no: null,
            };
          });

          this.form.calibrations = [
            ...pumpCalibrations,
          ];
        })
    },
  },
  methods: {
    submit() {
      this.$inertia.post(this.route('station-transactions.store'), this.form, {
        onStart: () => this.sending = true,
        onFinish: () => this.sending = false,
      });
    },

    // Add Forms/Row
    addPumpReadingsForm() {
      this.form.pump_readings.push({
        pump: null,
        opening: null,
        closing: null,
        unit_price: null,
      });
    },
    addSalesForm() {
      this.form.sales.push({
        pump_no: null,
        dr_no: null,
        client_id: null,
        product_id: null,
        quantity: null,
        rs_no: null,
      });
    },
    addCompanyValesForm() {
      this.form.company_vales.push({
        pump_no: null,
        voucher_no: null,
        company_id: null,
        product_id: null,
        quantity: null,
        remarks: null,
      });
    },
    addCalibrationsForm() {
      this.form.calibrations.push({
        pump: null,
        quantity: null,
        pump_no: null,
        voucher_no: null,
      });
    },
    addDiscountsForm() {
      this.form.discounts.push({
        voucher_no: null,
        cash: null,
        client_id: null,
        quantity: null,
        disc_amount: null,
      });
    },

    // Delete Form/Row
    deletePumpReadingsForm(index) {
      this.form.pump_readings.splice(index, 1);
    },
    deleteSalesForm(index) {
      this.form.sales.splice(index, 1);
    },
    deleteCompanyValesForm(index) {
      this.form.company_vales.splice(index, 1);
    },
    deleteCalibrationsForm(index) {
      this.form.calibrations.splice(index, 1);
    },
    deleteDiscountsForm(index) {
      this.form.discounts.splice(index, 1);
    },

  },

}
</script>

<style src="vue-multiselect/dist/vue-multiselect.min.css"></style>
<style src="vue2-datepicker/index.css"></style>
