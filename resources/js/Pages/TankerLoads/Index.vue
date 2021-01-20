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
          <th class="px-6 pt-6 pb-4">Tanker</th>
          <th class="px-6 pt-6 pb-4">Driver</th>
          <th class="px-6 pt-6 pb-4">Helper</th>
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
              <div v-if="load.tanker">
                {{ load.tanker.plate_no }}
              </div>
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
          <td class="border-t w-px">
            <inertia-link class="px-4 flex items-center" :href="route('tanker-loads.edit', load.id)" tabindex="-1">
              <icon name="cheveron-right" class="block w-6 h-6 fill-gray-400" />
            </inertia-link>
          </td>
        </tr>
         <tr v-if="tanker_loads.length === 0">
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

export default {
  // metaInfo: { title: 'tanker-loads' },
  layout: Layout,
  components: {
    Icon,
    Pagination,
    SearchFilter,
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
      },
    }
  },
  watch: {
    form: {
      handler: throttle(function() {
        let query = pickBy(this.form)
        this.$inertia.replace(this.route('tanker-loads.index', Object.keys(query).length ? query : { remember: 'forget' }))
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
