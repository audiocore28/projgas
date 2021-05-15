<template>
  <div>
    <div class="mb-6 flex justify-between items-center">
      <h1 class="font-bold text-3xl">Purchases</h1>
      <div class="flex">
        <date-picker
          v-model="form.range"
          type="date"
          range
          value-type="format"
          :formatter="momentFormat"
          placeholder="Select date range">
        </date-picker>
        <a v-if="form.range !== null" class="px-4 flex items-center" target="_blank" :href="route('purchases.prints', {
            start: form.range[0],
            end: form.range[1],
            search: form.search,
          })" tabindex="-1">
          <icon name="printer" class="block w-6 h-6 fill-gray-400"/>
        </a>
      </div>
    </div>

    <div class="mb-6 flex justify-between items-center">
      <search-filter v-model="form.search" class="w-full max-w-md mr-4" @reset="reset">
<!--         <label class="block text-gray-700">Trashed:</label>
        <select v-model="form.trashed" class="mt-1 w-full form-select">
          <option :value="null" />
          <option value="with">With Trashed</option>
          <option value="only">Only Trashed</option>
        </select>
 -->      </search-filter>
      <inertia-link class="btn-indigo" :href="route('purchases.create')">
        <span>Add</span>
        <span class="hidden md:inline">Purchase</span>
      </inertia-link>
    </div>
    <div class="bg-white rounded shadow overflow-x-auto">
      <table class="w-full whitespace-no-wrap">
        <tr class="text-left font-bold">
          <th class="px-6 pt-6 pb-4">Date</th>
          <th class="px-6 pt-6 pb-4">Supplier</th>
          <th class="px-6 pt-6 pb-4">Purchase No.</th>
          <th class="px-6 pt-6 pb-4">Purchases</th>
        </tr>
        <tr v-for="purchase in purchases.data" :key="purchase.id" class="hover:bg-gray-100 focus-within:bg-gray-100">
          <td class="border-t">
            <inertia-link class="px-6 py-4 flex items-center focus:text-indigo-500" :href="route('purchases.edit', purchase.id)">
              {{ purchase.date }}
            </inertia-link>
          </td>
          <td class="border-t">
            <inertia-link class="px-6 py-4 flex items-center" :href="route('purchases.edit', purchase.id)" tabindex="-1">
              <div v-if="purchase.supplier">
                {{ purchase.supplier.name }}
              </div>
            </inertia-link>
          </td>
          <td class="border-t">
            <inertia-link class="px-6 py-4 flex items-center" :href="route('purchases.edit', purchase.id)" tabindex="-1">
              {{ purchase.purchase_no }}
            </inertia-link>
          </td>
          <td class="border-t">
            <inertia-link class="px-6 py-4 flex items-center" :href="route('purchases.edit', purchase.id)" tabindex="-1">
              <div v-if="purchase.purchase_details" v-for="detail in purchase.purchase_details">
                <span class="px-2 py-2 text-sm leading-5 font-semibold rounded-full bg-grey-100 text-grey-800">
                  {{ detail.product.name }}
                  <span class="p-1 rounded-full text-grey-800 text-sm bg-grey-400">
                      {{ toFigure(detail.quantity) }}
                  </span>
                </span>
              </div>
            </inertia-link>
          </td>
          <td class="border-t w-px">
            <inertia-link class="px-4 flex items-center" :href="route('purchases.edit', purchase.id)" tabindex="-1">
              <icon name="edit" class="block w-6 h-6 fill-gray-400" />
            </inertia-link>
          </td>
          <td class="border-t w-px">
            <a class="px-4 flex items-center" target="_blank" :href="route('purchases.print', purchase.id)" tabindex="-1">
              <icon name="printer" class="block w-6 h-6 fill-gray-400" />
            </a>
          </td>
        </tr>
        <tr v-if="purchases.data.length === 0">
          <td class="border-t px-6 py-4" colspan="4">No purchases found.</td>
        </tr>
    </table>
    </div>
    <pagination :links="purchases.links" />
  </div>
</template>

<script>
import Icon from '@/Shared/Icon'
import Layout from '@/Shared/Layout'
import mapValues from 'lodash/mapValues'
import Pagination from '@/Shared/Pagination'
import SearchFilter from '@/Shared/SearchFilter'
import DatePicker from 'vue2-datepicker'
import pickBy from 'lodash/pickBy'
import throttle from 'lodash/throttle'
import moment from 'moment'

export default {
  metaInfo: { title: 'Purchases' },
  layout: Layout,
  components: {
    Icon,
    Pagination,
    SearchFilter,
  },
  props: {
    purchases: Object,
    filters: Object,
  },
  data() {
    return {
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
        search: this.filters.search,
        range: this.filters.range,
        // trashed: this.filters.trashed,
      },
    }
  },
  watch: {
    form: {
      handler: throttle(function() {
        let query = pickBy(this.form)
        this.$inertia.replace(this.route('purchases.index', Object.keys(query).length ? query : { remember: 'forget' }))
      }, 150),
      deep: true,
    },
  },
  methods: {
    reset() {
      this.form = mapValues(this.form, () => null)
    },

    // Helpers
    toFigure(value) {
      return value / 1000;
    },
  },
}
</script>

<style src="vue2-datepicker/index.css"></style>
