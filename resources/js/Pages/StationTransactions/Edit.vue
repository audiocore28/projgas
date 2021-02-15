<template>
  <div>
    <h1 class="mb-8 font-bold text-3xl">
      <inertia-link class="text-blue-600 hover:text-blue-800" :href="route('station-transactions.index')">Transactions</inertia-link>
      <span class="text-blue-600 font-medium">/</span> Edit
    </h1>

    <div class="p-1">
      <ul class="flex border-b">
        <li @click="openTab = 1" :class="{ '-mb-px': openTab === 1 }" class="-mb-px mr-1">
          <a :class="openTab === 1 ? activeClasses : inactiveClasses" class="bg-white inline-block py-2 px-4 font-semibold">Info</a>
        </li>
      </ul>

      <div class="w-full pt-4">
        <!-- Tab 1 -->
        <div v-show="openTab === 1">
          <!-- Update Existing Station Transaction -->
          <div class="bg-white rounded shadow overflow-hidden max-w-6xl">
            <!-- Station Transaction -->
            <form @submit.prevent="updateTransaction">
              <div class="p-8 -mr-6 -mb-8">
                <div class="pr-6 w-full">
                  <select-input v-model="updateForm.station_id" :error="errors.station_id" class="pr-6 pb-8 lg:w-1/3 w-full" label="G. Station">
                    <option :value="null" />
                    <option v-for="station in stations" :key="station.id" :value="station.id">{{ station.name }}</option>
                  </select-input>
                </div>

                <label class="form-label block mr-5">Date</label>
                <div class="pr-6 pb-8 mb-2 w-full">
                  <date-picker v-model="updateForm.date" lang="en" value-type="format" :formatter="momentFormat"></date-picker>
                </div>

                <text-input v-model="updateForm.shift" :error="errors.shift" class="pr-6 pb-8 w-full lg:w-1/4" label="Shift" />

                <div class="w-full flex">

                  <div class="pr-6 pb-2 lg:w-1/3">
                    <select-input v-model="updateForm.cashier_id" :error="errors.cashier_id" class="pr-6 pb-8" label="Cashier">
                      <option :value="null" />
                      <option v-for="cashier in cashiers" :key="cashier.id" :value="cashier.id">{{ cashier.name }}</option>
                    </select-input>
                  </div>

                  <div class="pr-6 pb-2 lg:w-1/3">
                    <select-input v-model="updateForm.pump_attendant_id" :error="errors.pump_attendant_id" class="pr-6 pb-8" label="Pump Attendant">
                      <option :value="null" />
                      <option v-for="pump_attendant in pump_attendants" :key="pump_attendant.id" :value="pump_attendant.id">{{ pump_attendant.name }}</option>
                    </select-input>
                  </div>

                  <div class="pr-6 pb-2 lg:w-1/3">
                    <select-input v-model="updateForm.office_staff_id" :error="errors.office_staff_id" class="pr-6 pb-8" label="Audited By">
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
                <div class="px-8 py-4 -mr-6 -mb-8 flex flex-wrap" v-for="(reading, index) in updateForm.pump_readings" :key="index">
                  <text-input v-model="reading.pump" :error="errors.pump" class="pr-6 pb-8 w-full lg:w-1/6" label="Pump" />
                  <text-input type="number" step="any" v-model="reading.opening" :error="errors.opening" class="pr-6 pb-8 w-full lg:w-1/6" label="Opening" />
                  <text-input type="number" step="any" v-model="reading.closing" :error="errors.closing" class="pr-6 pb-8 w-full lg:w-1/6" label="Closing" />
                  <text-input type="number" step="any" v-model="reading.unit_price" :error="errors.unit_price" class="pr-6 pb-8 w-full lg:w-1/6" label="Unit Price" />

                  <button @click.prevent="deletePumpReadingsForm(index, reading.id)" type="button" class="bg-white py-1 px-1 flex-shrink-0 text-sm leading-none">
                    <icon name="trash" class="w-4 h-4 mr-2 fill-red-600"/>
                  </button>
                </div>

<!--                 <div class="px-8 py-4 flex justify-end items-center">
                  <button class="btn-indigo" @click.prevent="addPumpReadingsForm">Add Row</button>
                </div>
 -->              </div>

              <!-- II. IBB Sales -->
              <h4 @click="saleSelected !== 1 ? saleSelected = 1 : saleSelected = null"
              class="font-bold text-1xl cursor-pointer px-5 py-3 block hover:opacity-75 shadow hover:-mb-3 rounded-t">II. IBB Sales</h4>
              <div v-show="saleSelected == 1" class="py-4 px-2">
                <div class="px-8 py-4 -mr-6 -mb-8 flex flex-wrap" v-for="(sale, index) in updateForm.sales" :key="index">
                  <text-input v-model="sale.pump_no" :error="errors.pump_no" class="pr-6 pb-8 w-full lg:w-1/12" label="Pump#" />
                  <text-input v-model="sale.dr_no" :error="errors.dr_no" class="pr-6 pb-8 w-full lg:w-2/12" label="DR No." />

                  <div class="pr-6 pb-8 w-full lg:w-2/12">
                    <label class="form-label" :for="`client-${index}`">Client</label>
                    <select :id="`client-${index}`" v-model="sale.client_id" class="form-select" :class="{ error: errors[`sale.${index}.client_id`] }">
                      <option :value="null" />
                      <option v-for="client in clients" :key="client.id" :value="client.id">{{ client.name }}</option>
                    </select>
                    <div v-if="errors[`sale.${index}.client_id`]" class="form-error">{{ errors[`sale.${index}.client_id`] }}</div>
                  </div>

                  <div class="pr-6 pb-8 w-full lg:w-2/12">
                    <label class="form-label" :for="`product-${index}`">Product</label>
                    <select :id="`product-${index}`" v-model="sale.product_id" class="form-select" :class="{ error: errors[`sale.${index}.product_id`] }">
                      <option :value="null" />
                      <option v-for="product in products" :key="product.id" :value="product.id">{{ product.name }}</option>
                    </select>
                    <div v-if="errors[`sale.${index}.product_id`]" class="form-error">{{ errors[`sale.${index}.product_id`] }}</div>
                  </div>

                  <text-input type="number" step="any" v-model="sale.quantity" :error="errors.quantity" class="pr-6 pb-8 w-full lg:w-2/12" label="Quantity" />

                  <text-input v-model="sale.rs_no" :error="errors.rs_no" class="pr-6 pb-8 w-full lg:w-2/12" label="RS No." />

                  <button @click.prevent="deleteSalesForm(index, sale.id)" type="button" class="bg-white py-1 px-1 flex-shrink-0 text-sm leading-none">
                    <icon name="trash" class="w-4 h-4 mr-2 fill-red-600"/>
                  </button>
                </div>

<!--                 <div class="px-8 py-4 flex justify-end items-center">
                  <button class="btn-indigo" @click.prevent="addSalesForm">Add Row</button>
                </div>
 -->              </div>

              <!-- III. Company Vale -->
              <h4 @click="valeSelected !== 2 ? valeSelected = 2 : valeSelected = null"
              class="font-bold text-1xl cursor-pointer px-5 py-3 block hover:opacity-75 shadow hover:-mb-3 rounded-t">III. Company Vale</h4>
              <div v-show="valeSelected == 2" class="py-4 px-2">
                <div class="px-8 py-4 -mr-6 -mb-8 flex flex-wrap" v-for="(vale, index) in updateForm.company_vales" :key="index">
                  <text-input v-model="vale.pump_no" :error="errors.pump_no" class="pr-6 pb-8 w-full lg:w-1/6" label="Pump No." />
                  <text-input v-model="vale.voucher_no" :error="errors.voucher_no" class="pr-6 pb-8 w-full lg:w-1/6" label="Voucher No." />

                  <div class="pr-6 pb-8 w-full lg:w-1/4">
                    <label class="form-label" :for="`client-${index}`">Client</label>
                    <select :id="`client-${index}`" v-model="vale.client_id" class="form-select" :class="{ error: errors[`vale.${index}.client_id`] }">
                      <option :value="null" />
                      <option v-for="client in clients" :key="client.id" :value="client.id">{{ client.name }}</option>
                    </select>
                    <div v-if="errors[`vale.${index}.client_id`]" class="form-error">{{ errors[`vale.${index}.client_id`] }}</div>
                  </div>

                  <div class="pr-6 pb-8 w-full lg:w-1/6">
                    <label class="form-label" :for="`product-${index}`">Product</label>
                    <select :id="`product-${index}`" v-model="vale.product_id" class="form-select" :class="{ error: errors[`vale.${index}.product_id`] }">
                      <option :value="null" />
                      <option v-for="product in products" :key="product.id" :value="product.id">{{ product.name }}</option>
                    </select>
                    <div v-if="errors[`vale.${index}.product_id`]" class="form-error">{{ errors[`vale.${index}.product_id`] }}</div>
                  </div>

                  <text-input type="number" step="any" v-model="vale.quantity" :error="errors.quantity" class="pr-6 pb-8 w-full lg:w-1/6" label="Quantity" />

                  <button @click.prevent="deleteCompanyValesForm(index, vale.id)" type="button" class="bg-white py-1 px-1 flex-shrink-0 text-sm leading-none">
                    <icon name="trash" class="w-4 h-4 mr-2 fill-red-600"/>
                  </button>
                </div>

<!--                 <div class="px-8 py-4 flex justify-end items-center">
                  <button class="btn-indigo" @click.prevent="addCompanyValesForm">Add Row</button>
                </div>
 -->              </div>

              <!-- IV. Calibration -->
              <h4 @click="calibrationSelected !== 3 ? calibrationSelected = 3 : calibrationSelected = null"
              class="font-bold text-1xl cursor-pointer px-5 py-3 block hover:opacity-75 shadow hover:-mb-3 rounded-t">IV. Calibration</h4>
              <div v-show="calibrationSelected == 3" class="py-4 px-2">
                <div class="px-8 py-4 -mr-6 -mb-8 flex flex-wrap" v-for="(calibration, index) in updateForm.calibrations" :key="index">
                  <text-input v-model="calibration.pump" :error="errors.pump" class="pr-6 pb-8 w-full lg:w-1/6" label="Pump" />
                  <text-input type="number" step="any" v-model="calibration.quantity" :error="errors.quantity" class="pr-6 pb-8 w-full lg:w-1/6" label="Quantity" />
                  <text-input v-model="calibration.pump_no" :error="errors.pump_no" class="pr-6 pb-8 w-full lg:w-1/6" label="Pump No." />
                  <text-input v-model="calibration.voucher_no" :error="errors.voucher_no" class="pr-6 pb-8 w-full lg:w-1/6" label="Voucher No." />

                  <button @click.prevent="deleteCalibrationsForm(index, calibration.id)" type="button" class="bg-white py-1 px-1 flex-shrink-0 text-sm leading-none">
                    <icon name="trash" class="w-4 h-4 mr-2 fill-red-600"/>
                  </button>
                </div>

<!--                 <div class="px-8 py-4 flex justify-end items-center">
                  <button class="btn-indigo" @click.prevent="addCalibrationsForm">Add Row</button>
                </div>
 -->              </div>

              <!-- V. Discounted -->
              <h4 @click="discountSelected !== 4 ? discountSelected = 4 : discountSelected = null"
              class="font-bold text-1xl cursor-pointer px-5 py-3 block hover:opacity-75 shadow hover:-mb-3 rounded-t">V. Discounted</h4>
              <div v-show="discountSelected == 4" class="py-4 px-2">
                <div class="px-8 py-4 -mr-6 -mb-8 flex flex-wrap" v-for="(discount, index) in updateForm.discounts" :key="index">
                  <text-input v-model="discount.voucher_no" :error="errors.voucher_no" class="pr-6 pb-8 w-full lg:w-1/6" label="Voucher No." />
                  <text-input type="number" step="any" v-model="discount.cash" :error="errors.cash" class="pr-6 pb-8 w-full lg:w-1/6" label="Cash" />

                  <div class="pr-6 pb-8 w-full lg:w-1/4">
                    <label class="form-label" :for="`client-${index}`">Client</label>
                    <select :id="`client-${index}`" v-model="discount.client_id" class="form-select" :class="{ error: errors[`discount.${index}.client_id`] }">
                      <option :value="null" />
                      <option v-for="client in clients" :key="client.id" :value="client.id">{{ client.name }}</option>
                    </select>
                    <div v-if="errors[`discount.${index}.client_id`]" class="form-error">{{ errors[`discount.${index}.client_id`] }}</div>
                  </div>

                  <text-input type="number" step="any" v-model="discount.quantity" :error="errors.quantity" class="pr-6 pb-8 w-full lg:w-1/6" label="Quantity" />

                  <text-input type="number" step="any" v-model="discount.disc_amount" :error="errors.disc_amount" class="pr-6 pb-8 w-full lg:w-1/6" label="Disc Amount" />

                  <button @click.prevent="deleteDiscountsForm(index, discount.id)" type="button" class="bg-white py-1 px-1 flex-shrink-0 text-sm leading-none">
                    <icon name="trash" class="w-4 h-4 mr-2 fill-red-600"/>
                  </button>
                </div>

<!--                 <div class="px-8 py-4 flex justify-end items-center">
                  <button class="btn-indigo" @click.prevent="addDiscountsForm">Add Row</button>
                </div>
 -->              </div>

              <!--  -->

              <div class="px-8 py-4 bg-gray-100 border-t border-gray-200 flex justify-end items-center">
                <button class="text-red-600 hover:underline" tabindex="-1" type="button" @click="destroy">Delete Transaction</button>
                <loading-button :loading="sending" class="btn-indigo ml-auto" type="submit">Update Transaction</loading-button>
              </div>
            </form>
          </div>
        </div>

      </div>
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
  metaInfo: { title: 'Edit Transactions' },
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
    transaction: Object,
    products: Array,
    clients: Array,
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
      updateForm: {
        station_id: this.transaction.station_id,
        date: this.transaction.date,
        shift: this.transaction.shift,
        cashier_id: this.transaction.cashier_id,
        pump_attendant_id: this.transaction.pump_attendant_id,
        office_staff_id: this.transaction.office_staff_id,
        //
        pump_readings: this.transaction.pump_readings,
        sales: this.transaction.sales,
        company_vales: this.transaction.company_vales,
        calibrations: this.transaction.calibrations,
        discounts: this.transaction.discounts,
      },
      createForm: {
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
            client_id: null,
            product_id: null,
            quantity: null,
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
      // Tabs
      openTab: 1,
      activeClasses: 'border-l border-t border-r rounded-t text-blue-600',
      inactiveClasses: 'text-blue-500 hover:text-blue-800',
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
      axios.get(`/station-transactions/${value}/edit`)
        .then(response => {
          console.log(response.data);
          // this.createForm.pump_readings = response.data.data
        })
    },
  },
  methods: {
    updateTransaction() {
      this.$inertia.put(this.route('station-transactions.update', this.transaction.id), this.updateForm, {
        onStart: () => this.sending = true,
        onFinish: () => this.sending = false,
      });
    },
    destroy() {
      if (confirm('Are you sure you want to permanently delete this transaction?')) {
        this.$inertia.delete(this.route('station-transactions.destroy', this.transaction.id))
      }
    },
    //
    deletePumpReadingsForm(index, pumpReadingId) {
      if (confirm('Are you sure you want to delete this row?')) {
        this.$inertia.delete(this.route('pump-readings.destroy', pumpReadingId));
        this.transaction.pump_readings.splice(index, 1);
      }
    },
    deleteSalesForm(index, saleId) {
      if (confirm('Are you sure you want to delete this row?')) {
        this.$inertia.delete(this.route('sales.destroy', saleId));
        this.transaction.sales.splice(index, 1);
      }
    },
    deleteCompanyValesForm(index, companyValeId) {
      if (confirm('Are you sure you want to delete this row?')) {
        this.$inertia.delete(this.route('company-vales.destroy', companyValeId));
        this.transaction.company_vales.splice(index, 1);
      }
    },
    deleteCalibrationsForm(index, calibrationId) {
      if (confirm('Are you sure you want to delete this row?')) {
        this.$inertia.delete(this.route('calibrations.destroy', calibrationId));
        this.transaction.calibrations.splice(index, 1);
      }
    },
    deleteDiscountsForm(index, discountId) {
      if (confirm('Are you sure you want to delete this row?')) {
        this.$inertia.delete(this.route('discounts.destroy', discountId));
        this.transaction.discounts.splice(index, 1);
      }
    },

    // ----New Created Form

    // Save
    saveNewPumpReading() {
      this.$inertia.post(this.route('pump-readings.store'), this.createForm.pump_readings, {
        onStart: () => this.sending = true,
        onFinish: () => this.sending = false,
      });
    },
    saveNewSale() {
      this.$inertia.post(this.route('sales.store'), this.createForm.sales, {
        onStart: () => this.sending = true,
        onFinish: () => this.sending = false,
      });
    },
    saveNewCompanyVale() {
      this.$inertia.post(this.route('company-vales.store'), this.createForm.company_vales, {
        onStart: () => this.sending = true,
        onFinish: () => this.sending = false,
      });
    },
    saveNewCalibration() {
      this.$inertia.post(this.route('calibrations.store'), this.createForm.calibrations, {
        onStart: () => this.sending = true,
        onFinish: () => this.sending = false,
      });
    },
    saveNewDiscount() {
      this.$inertia.post(this.route('discounts.store'), this.createForm.discounts, {
        onStart: () => this.sending = true,
        onFinish: () => this.sending = false,
      });
    },


    // create form
    addPumpReadingsForm() {
      this.createForm.pump_readings.push({
        id: null,
        station_transaction_id: this.transaction.id,
        pump: null,
        opening: null,
        closing: null,
        unit_price: null,
      });
    },
    addSalesForm() {
      this.createForm.sales.push({
        id: null,
        station_transaction_id: this.transaction.id,
        pump_no: null,
        dr_no: null,
        client_id: null,
        product_id: null,
        quantity: null,
        rs_no: null,
      });
    },
    addCompanyValesForm() {
      this.createForm.company_vales.push({
        id: null,
        station_transaction_id: this.transaction.id,
        pump_no: null,
        voucher_no: null,
        client_id: null,
        product_id: null,
        quantity: null,
      });
    },
    addCalibrationsForm() {
      this.createForm.calibrations.push({
        id: null,
        station_transaction_id: this.transaction.id,
        pump: null,
        quantity: null,
        pump_no: null,
        voucher_no: null,
      });
    },
    addDiscountsForm() {
      this.createForm.discounts.push({
        id: null,
        station_transaction_id: this.transaction.id,
        voucher_no: null,
        cash: null,
        client_id: null,
        quantity: null,
        disc_amount: null,
      });
    },

    // Delete Form/Row
    deletePumpReadingsForm(index) {
      this.createForm.pump_readings.splice(index, 1);
    },
    deleteSalesForm(index) {
      this.createForm.sales.splice(index, 1);
    },
    deleteCompanyValesForm(index) {
      this.createForm.company_vales.splice(index, 1);
    },
    deleteCalibrationsForm(index) {
      this.createForm.calibrations.splice(index, 1);
    },
    deleteDiscountsForm(index) {
      this.createForm.discounts.splice(index, 1);
    },

  },

}
</script>

<style src="vue-multiselect/dist/vue-multiselect.min.css"></style>
<style src="vue2-datepicker/index.css"></style>
