<template>
  <div>
    <h1 class="mb-8 font-bold text-3xl">Deliveries</h1>
    <div class="mb-6 flex justify-between items-center">
      <search-filter v-model="form.search" class="w-full max-w-md mr-4" @reset="reset">
<!--         <label class="block text-gray-700">Trashed:</label>
        <select v-model="form.trashed" class="mt-1 w-full form-select">
          <option :value="null" />
          <option value="with">With Trashed</option>
          <option value="only">Only Trashed</option>
        </select>
 -->      </search-filter>
      <inertia-link class="btn-indigo" :href="route('deliveries.create')">
        <span>Add</span>
        <span class="hidden md:inline">Delivery</span>
      </inertia-link>
    </div>
    <div class="bg-white rounded shadow overflow-x-auto">
      <table class="w-full whitespace-no-wrap">
        <tr class="text-left font-bold">
          <th class="px-6 pt-6 pb-4">ID</th>
          <th class="px-6 pt-6 pb-4">Purchase No.</th>
          <th class="px-6 pt-6 pb-4">Trip No.</th>
          <th class="px-6 pt-6 pb-4">Driver</th>
          <th class="px-6 pt-6 pb-4">Helper</th>
          <th class="px-6 pt-6 pb-4">Tanker</th>
<!--           <th class="px-6 pt-6 pb-4">DR Nos.</th>
          <th class="px-6 pt-6 pb-4">Client</th>
 -->        </tr>
         <tr v-for="delivery in deliveries.data" :key="delivery.id" class="hover:bg-gray-100 focus-within:bg-gray-100">
          <!-- table columns -->
          <td class="border-t">
            <inertia-link class="px-6 py-4 flex items-center focus:text-indigo-500" :href="route('deliveries.edit', delivery.id)">
              {{ delivery.id }}
            </inertia-link>
          </td>
          <td class="border-t">
            <inertia-link class="px-6 py-4 flex items-center" :href="route('deliveries.edit', delivery.id)" tabindex="-1">
              <div v-if="delivery.purchase">
                {{ delivery.purchase.purchase_no }}
              </div>
            </inertia-link>
          </td>
          <td class="border-t">
            <inertia-link class="px-6 py-4 flex items-center" :href="route('deliveries.edit', delivery.id)" tabindex="-1">
              {{ delivery.trip_no }}
            </inertia-link>
          </td>
          <td class="border-t">
            <inertia-link class="px-6 py-4 flex items-center" :href="route('deliveries.edit', delivery.id)" tabindex="-1">
              <div v-if="delivery.driver">
                {{ delivery.driver.name }}
              </div>
            </inertia-link>
          </td>
          <td class="border-t">
            <inertia-link class="px-6 py-4 flex items-center" :href="route('deliveries.edit', delivery.id)" tabindex="-1">
              <div v-if="delivery.helper">
                {{ delivery.helper.name }}
              </div>
            </inertia-link>
          </td>
          <td class="border-t">
            <inertia-link class="px-6 py-4 flex items-center" :href="route('deliveries.edit', delivery.id)" tabindex="-1">
              <div v-if="delivery.tanker">
                {{ delivery.tanker.plate_no }}
              </div>
            </inertia-link>
          </td>
<!--           <td class="border-t">
            <inertia-link class="px-6 py-4 flex items-center" :href="route('deliveries.edit', delivery.id)" tabindex="-1">
              <div v-if="delivery.clients" v-for="client in delivery.clients">
                <span class="px-2 py-2 text-sm leading-5 font-semibold rounded-full bg-grey-100 text-grey-800">
                  {{ client.dr_no }}
                </span>
              </div>
            </inertia-link>
          </td>
          <td class="border-t">
            <inertia-link class="px-6 py-4 flex items-center" :href="route('deliveries.edit', delivery.id)" tabindex="-1">
              <div v-if="delivery.clients" v-for="client in delivery.clients">
                <span class="px-2 py-2 text-sm leading-5 font-semibold rounded-full bg-grey-100 text-grey-800">
                  {{ client.client.name }}
                </span>
              </div>
            </inertia-link>
          </td>
 -->          <td class="border-t w-px">
            <inertia-link class="px-4 flex items-center" :href="route('deliveries.edit', delivery.id)" tabindex="-1">
              <icon name="cheveron-right" class="block w-6 h-6 fill-gray-400" />
            </inertia-link>
          </td>
        </tr>
         <tr v-if="deliveries.data.length === 0">
          <td class="border-t px-6 py-4" colspan="4">No deliveries found.</td>
        </tr>
    </table>
    </div>
    <pagination :links="deliveries.links" />
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

export default {
  metaInfo: { title: 'Deliveries' },
  layout: Layout,
  components: {
    Icon,
    Pagination,
    SearchFilter,
  },
  props: {
    deliveries: Object,
    filters: Object,
  },
  data() {
    return {
      form: {
        search: this.filters.search,
        // trashed: this.filters.trashed,
      },
    }
  },
  watch: {
    form: {
      handler: throttle(function() {
        let query = pickBy(this.form)
        this.$inertia.replace(this.route('deliveries.index', Object.keys(query).length ? query : { remember: 'forget' }))
      }, 150),
      deep: true,
    },
  },
  methods: {
    reset() {
      this.form = mapValues(this.form, () => null)
    },
  },
}
</script>
