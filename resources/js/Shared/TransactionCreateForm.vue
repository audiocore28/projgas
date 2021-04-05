<template>
  <div>
    <div class="bg-white rounded shadow overflow-hidden max-w-6xl pt-6 mb-8 -mt-4">
      <form @submit.prevent="saveNewDetails">
        <!-- I. Pump Reading -->
        <h4 @click="readingSelected !== 0 ? readingSelected = 0 : readingSelected = null"
        class="font-bold text-1xl cursor-pointer px-5 py-3 block hover:opacity-75 shadow hover:-mb-3 rounded-t">I. Pump Reading</h4>
        <div v-show="readingSelected == 0" class="py-4 px-2">
          <div class="px-8 py-4 -mr-6 -mb-8 flex flex-wrap" v-for="(reading, index) in createForm.pump_readings" :key="index">
            <text-input v-model="reading.pump" :error="errors.pump" class="pr-6 pb-8 w-full lg:w-1/6" label="Pump" />
            <text-input type="number" step="any" v-model="reading.opening" :error="errors.opening" class="pr-6 pb-8 w-full lg:w-1/6" label="Opening" />
            <text-input type="number" step="any" v-model="reading.closing" :error="errors.closing" class="pr-6 pb-8 w-full lg:w-1/6" label="Closing" />
            <text-input type="number" step="any" v-model="reading.unit_price" :error="errors.unit_price" class="pr-6 pb-8 w-full lg:w-1/6" label="Unit Price" />

            <button @click.prevent="deleteNewPumpReadingsForm(index)" type="button" class="bg-white py-1 px-1 flex-shrink-0 text-sm leading-none">
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
          <div class="px-8 py-4 -mr-6 -mb-8 flex flex-wrap" v-for="(sale, index) in createForm.sales" :key="index">
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

            <button @click.prevent="deleteNewSalesForm(index)" type="button" class="bg-white py-1 px-1 flex-shrink-0 text-sm leading-none">
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
          <div class="px-8 py-4 -mr-6 -mb-8 flex flex-wrap" v-for="(vale, index) in createForm.company_vales" :key="index">
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

            <button @click.prevent="deleteNewCompanyValesForm(index)" type="button" class="bg-white py-1 px-1 flex-shrink-0 text-sm leading-none">
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
          <div class="px-8 py-4 -mr-6 -mb-8 flex flex-wrap" v-for="(calibration, index) in createForm.calibrations" :key="index">
            <text-input v-model="calibration.pump" :error="errors.pump" class="pr-6 pb-8 w-full lg:w-1/6" label="Pump" />
            <text-input type="number" step="any" v-model="calibration.quantity" :error="errors.quantity" class="pr-6 pb-8 w-full lg:w-1/6" label="Quantity" />
            <text-input v-model="calibration.pump_no" :error="errors.pump_no" class="pr-6 pb-8 w-full lg:w-1/6" label="Pump No." />
            <text-input v-model="calibration.voucher_no" :error="errors.voucher_no" class="pr-6 pb-8 w-full lg:w-1/6" label="Voucher No." />

            <button @click.prevent="deleteNewCalibrationsForm(index)" type="button" class="bg-white py-1 px-1 flex-shrink-0 text-sm leading-none">
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
          <div class="px-8 py-4 -mr-6 -mb-8 flex flex-wrap" v-for="(discount, index) in createForm.discounts" :key="index">
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

            <button @click.prevent="deleteNewDiscountsForm(index)" type="button" class="bg-white py-1 px-1 flex-shrink-0 text-sm leading-none">
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
import axios from 'axios'
import LoadingButton from '@/Shared/LoadingButton'
import SelectInput from '@/Shared/SelectInput'
import TextInput from '@/Shared/TextInput'
import Icon from '@/Shared/Icon'
import Multiselect from 'vue-multiselect'
import DatePicker from 'vue2-datepicker'

export default {
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
    companies: Array,
  },
  remember: 'form',
  data() {
    return {
      sending: false,
      createForm: {
        pump_readings: [
          {
            id: null,
            station_transaction_id: this.transaction.id,
            pump: null,
            opening: null,
            closing: null,
            unit_price: null,
          }
        ],
        sales: [
          {
            id: null,
            station_transaction_id: this.transaction.id,
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
            id: null,
            station_transaction_id: this.transaction.id,
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
            id: null,
            station_transaction_id: this.transaction.id,
            pump: null,
            quantity: null,
            pump_no: null,
            voucher_no: null,
          }
        ],
        discounts: [
          {
            id: null,
            station_transaction_id: this.transaction.id,
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
  methods: {
    saveNewDetails() {
      this.$inertia.post(this.route('station-transactions.addNewRow'), this.createForm, {
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
        company_id: null,
        product_id: null,
        quantity: null,
        remarks: null,
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
    deleteNewPumpReadingsForm(index) {
      this.createForm.pump_readings.splice(index, 1);
    },
    deleteNewSalesForm(index) {
      this.createForm.sales.splice(index, 1);
    },
    deleteNewCompanyValesForm(index) {
      this.createForm.company_vales.splice(index, 1);
    },
    deleteNewCalibrationsForm(index) {
      this.createForm.calibrations.splice(index, 1);
    },
    deleteNewDiscountsForm(index) {
      this.createForm.discounts.splice(index, 1);
    },

  },
}
</script>