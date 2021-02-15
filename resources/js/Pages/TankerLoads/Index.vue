<template>
  <div>
    <h1 class="mb-8 font-bold text-3xl">Loads</h1>
    <div class="mb-6 flex justify-between items-center">
      <search-filter v-model="form.search" class="w-full max-w-md mr-4" @reset="reset">
<!--         <label class="block text-gray-700">Trashed:</label>
        <select v-model="form.trashed" class="mt-1 w-full form-select">
          <option :value="null" />
          <option value="with">With Trashed</option>
          <option value="only">Only Trashed</option>
        </select>
 -->      </search-filter>

      <label class="form-label block mr-5">From</label>
      <div class="pr-6 pb-8 w-full">
        <date-picker v-model="form.range.from" lang="en" value-type="format"></date-picker>
      </div>
      <label class="form-label block mr-5">To</label>
      <div class="pr-6 pb-8 w-full">
        <date-picker v-model="form.range.to" lang="en" value-type="format"></date-picker>
      </div>

      <inertia-link class="btn-indigo" :href="route('tanker-loads.create')">
        <span>Add</span>
        <span class="hidden md:inline">Load</span>
      </inertia-link>
    </div>
    <div class="bg-white rounded shadow overflow-x-auto">
      <table class="w-full whitespace-no-wrap">
        <tr class="text-left font-bold">
          <th class="px-6 pt-6 pb-4">ID</th>
          <th class="px-6 pt-6 pb-4">Date</th>
          <th class="px-6 pt-6 pb-4">Purchase No.</th>
          <th class="px-6 pt-6 pb-4">Trip No.</th>
          <th class="px-6 pt-6 pb-4">Driver</th>
          <th class="px-6 pt-6 pb-4">Helper</th>
          <th class="px-6 pt-6 pb-4">Tanker</th>
          <th class="px-6 pt-6 pb-4">Loads</th>
        </tr>
         <tr v-for="load in tanker_loads.data" :key="load.id" class="hover:bg-gray-100 focus-within:bg-gray-100">
          <!-- table columns -->
          <td class="border-t">
            <inertia-link class="px-6 py-4 flex items-center focus:text-indigo-500" :href="route('tanker-loads.edit', load.id)">
              {{ load.id }}
            </inertia-link>
          </td>
          <td class="border-t">
            <inertia-link class="px-6 py-4 flex items-center focus:text-indigo-500" :href="route('tanker-loads.edit', load.id)">
              {{ load.date }}
            </inertia-link>
          </td>
          <td class="border-t">
            <inertia-link class="px-6 py-4 flex items-center" :href="route('tanker-loads.edit', load.id)" tabindex="-1">
              <div v-if="load.purchase">
                {{ load.purchase.purchase_no }}
              </div>
            </inertia-link>
          </td>
          <td class="border-t">
            <inertia-link class="px-6 py-4 flex items-center" :href="route('tanker-loads.edit', load.id)" tabindex="-1">
              {{ load.trip_no }}
            </inertia-link>
          </td>
          <td class="border-t">
            <inertia-link class="px-6 py-4 flex items-center" :href="route('tanker-loads.edit', load.id)" tabindex="-1">
              <div v-if="load.driver">
                {{ load.driver.name }}
              </div>
            </inertia-link>
          </td>
          <td class="border-t">
            <inertia-link class="px-6 py-4 flex items-center" :href="route('tanker-loads.edit', load.id)" tabindex="-1">
              <div v-if="load.helper">
                {{ load.helper.name }}
              </div>
            </inertia-link>
          </td>
          <td class="border-t">
            <inertia-link class="px-6 py-4 flex items-center" :href="route('tanker-loads.edit', load.id)" tabindex="-1">
              <div v-if="load.tanker">
                {{ load.tanker.plate_no }}
              </div>
            </inertia-link>
          </td>
          <td class="border-t">
            <inertia-link class="px-6 py-4 flex items-center" :href="route('tanker-loads.edit', load.id)" tabindex="-1">
              <div v-if="load.products" v-for="product in load.products">
                <span class="px-2 py-2 text-sm leading-5 font-semibold rounded-full bg-grey-100 text-grey-800">
                  {{ product.product.name }}
                  <span class="p-1 rounded-full text-grey-800 text-sm bg-grey-400">
                      {{ toFigure(product.quantity) }}
                  </span>
                </span>
              </div>
            </inertia-link>
          </td>
          <td class="border-t w-px">
            <inertia-link class="px-4 flex items-center" :href="route('tanker-loads.edit', load.id)" tabindex="-1">
              <icon name="cheveron-right" class="block w-6 h-6 fill-gray-400" />
            </inertia-link>
          </td>
        </tr>
         <tr v-if="tanker_loads.data.length === 0">
          <td class="border-t px-6 py-4" colspan="4">No loads found.</td>
        </tr>
    </table>
    </div>
    <pagination :links="tanker_loads.links" />
  </div>
</template>

<script>
import Icon from '@/Shared/Icon'
import Layout from '@/Shared/Layout'
import mapValues from 'lodash/mapValues'
import Pagination from '@/Shared/Pagination'
import pickBy from 'lodash/pickBy'
import SearchFilter from '@/Shared/SearchFilter'
import throttle from 'lodash/throttle'
import DatePicker from 'vue2-datepicker'

export default {
  metaInfo: { title: 'Loads' },
  layout: Layout,
  components: {
    Icon,
    Pagination,
    SearchFilter,
    DatePicker,
  },
  props: {
    tanker_loads: Object,
    filters: Object,
  },
  data() {
    return {
      form: {
        search: this.filters.search,
        // trashed: this.filters.trashed,
        range: {
          from: null,
          to: null,
        }
      },
    }
  },
  watch: {
    form: {
      handler: throttle(function() {
        let query = pickBy(this.form)

        if (query.range.from !== null && query.range.to === null) {
          return;
        }

        if (query.range.from === null && query.range.to !== null) {
          return;
        }

        this.$inertia.replace(this.route('tanker-loads.index', Object.keys(query).length ? query : { remember: 'forget' }))
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
