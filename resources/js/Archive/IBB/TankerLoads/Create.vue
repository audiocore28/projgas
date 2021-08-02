<template>
  <div class="overflow-hidden">
    <h1 class="mb-4 font-bold text-3xl">
      <inertia-link class="text-blue-600 hover:text-blue-800" :href="route('tanker-loads.index')">Loads</inertia-link>
      <span class="text-blue-600 font-medium">/</span> Create
    </h1>
    <!-- <div class="bg-white rounded shadow overflow-hidden max-w-6xl"> -->
      <form @submit.prevent="submit">
        <!-- Load -->
        <div class="py-8 px-2 -mr-6 -mb-8 flex flex-wrap">
          <!-- <text-input v-model="form.trip_no" @input="setDestination($event)" :error="errors.trip_no" class="pr-6 pb-8 w-full lg:w-1/2" label="Trip No." /> -->

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

          <!-- <text-input v-model="form.remarks" :error="errors.remarks" class="pr-6 pb-8 w-full lg:w-1/2" label="Remarks" /> -->
        </div>

        <!-- Details -->
<!--         <div class="px-8 py-4 bg-gray-100 border-t border-gray-200 flex justify-end items-center">
          <loading-button :loading="sending" class="btn-indigo" type="submit">Save</loading-button>
        </div>
 -->
      </form>

      <div class="bg-white rounded shadow overflow-x-auto mb-8 -mt-4 lg:w-1/2" v-if="selectedPurchase">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Product
              </th>
              <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Quantity
              </th>
              <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Unit Price
              </th>
              <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Amount
              </th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-200">
            <tr v-for="detail in selectedPurchase.purchase_details" :key="detail.id" :value="detail.id">
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="text-sm text-gray-900">{{ detail.product.name }}</div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="text-sm text-gray-900">{{ quantityFormat(detail.quantity) }}</div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="text-sm text-gray-900">{{ toPHP(detail.unit_price) }}</div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="text-sm text-gray-900">{{ totalCurrency(detail.quantity, detail.unit_price) }}</div>
              </td>
            </tr>
            <!-- Total -->
            <tr class="bg-gray-200">
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="text-left text-xs font-medium text-gray-500 uppercase">Total:</div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="text-sm text-gray-900">{{ totalPurchasesQty }}</div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="text-sm text-gray-900"></div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="text-sm text-gray-900">{{ totalPurchasesAmount }}</div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    <!-- </div> -->

    <tanker-load-multiple-create-form
      :errors="errors"
      :products="products"
      :purchase_id="purchase_id"
    ></tanker-load-multiple-create-form>

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
import TankerLoadMultipleCreateForm from '@/Shared/TankerLoadMultipleCreateForm'

export default {
  mixins: [numberFormatsMixin],
  metaInfo: { title: 'Create Loads' },
  layout: Layout,
  components: {
    LoadingButton,
    SelectInput,
    TextInput,
    Multiselect,
    DatePicker,
    Icon,
    TankerLoadMultipleCreateForm,
  },
  props: {
    errors: Object,
    purchases: {
      type: Array,
      default: () => [],
    },
    products: Array,
  },
  remember: 'form',
  data() {
    return {
      selectedPurchase: undefined,
      purchase_id: null,
      sending: false,
    }
  },
  methods: {
    // Multiselect
    onSearchPurchaseChange: throttle(function(term) {
      this.$inertia.get(this.route('tanker-loads.create'), {term}, {
        preserveState: true,
        preserveScroll: true,
        replace: true,
      })
    }, 300),
    onSelectedPurchase(purchase) {
      this.purchase_id = purchase.id;
    },
  },
  computed: {
    // Purchases
    totalPurchasesAmount() {
      var totalAmt = this.selectedPurchase.purchase_details.reduce(function (acc, purchase) {
        acc += parseFloat(purchase.quantity) * parseFloat(purchase.unit_price);
        return acc;
      }, 0);

      return this.toPHP(totalAmt);
    },

    totalPurchasesQty() {
      var totalQty = this.selectedPurchase.purchase_details.reduce(function (acc, purchase) {
        acc += parseFloat(purchase.quantity);
        return acc;
      }, 0);
      return this.quantityFormat(totalQty);
    },
  }

}
</script>

<style>
  .multiselect__single, .multiselect__option {
    font-size: 0.875rem;
  }

  .multiselect__element span {}
</style>
