<template>
  <div>
    <h1 class="mb-8 font-bold text-3xl">Drivers</h1>
    <div class="mb-6 flex justify-between items-center">
      <search-filter v-model="form.search" class="w-full max-w-md mr-4" @reset="reset">
        <label class="block text-gray-700">Trashed:</label>
        <select v-model="form.trashed" class="mt-1 w-full form-select">
          <option :value="null" />
          <option value="with">With Trashed</option>
          <option value="only">Only Trashed</option>
        </select>
      </search-filter>
      <inertia-link class="btn-indigo" :href="route('drivers.create')">
        <span>Add</span>
        <span class="hidden md:inline">Driver</span>
      </inertia-link>
    </div>
    <div class="bg-white rounded shadow overflow-x-auto">
      <table class="w-full whitespace-no-wrap">
        <tr class="text-left font-bold">
          <th class="px-6 pt-6 pb-4">ID</th>
          <th class="px-6 pt-6 pb-4">Full Name</th>
          <th class="px-6 pt-6 pb-4">Nickname</th>
          <th class="px-6 pt-6 pb-4">License No</th>
          <th class="px-6 pt-6 pb-4">Contact No</th>
          <!-- <th class="px-6 pt-6 pb-4" colspan="2">Driver No.</th> -->
        </tr>
         <tr v-for="driver in drivers.data" :key="driver.id" class="hover:bg-gray-100 focus-within:bg-gray-100">
          <!-- table columns -->
          <td class="border-t">
            <inertia-link class="px-6 py-4 flex items-center focus:text-indigo-500" :href="route('drivers.edit', driver.id)">
              {{ driver.id }}
            </inertia-link>
          </td>
          <td class="border-t">
            <inertia-link class="px-6 py-4 flex items-center focus:text-indigo-500" :href="route('drivers.edit', driver.id)">
              {{ driver.name }}
              <icon v-if="driver.deleted_at" name="trash" class="flex-shrink-0 w-3 h-3 fill-gray-400 ml-2" />
            </inertia-link>
          </td>
          <td class="border-t">
            <inertia-link class="px-6 py-4 flex items-center focus:text-indigo-500" :href="route('drivers.edit', driver.id)">
              {{ driver.nickname }}
            </inertia-link>
          </td>
<!--           <td class="border-t">
            <inertia-link class="px-6 py-4 flex items-center" :href="route('drivers.edit', driver.id)" tabindex="-1">
              <div v-if="contact.organization">
                {{ contact.organization.name }}
              </div>
            </inertia-link>
          </td>
          <td class="border-t">
            <inertia-link class="px-6 py-4 flex items-center" :href="route('drivers.edit', driver.id)" tabindex="-1">
              {{ driver.contact_no }}
            </inertia-link>
          </td>
 -->          <td class="border-t">
            <inertia-link class="px-6 py-4 flex items-center" :href="route('drivers.edit', driver.id)" tabindex="-1">
              {{ driver.license_no }}
            </inertia-link>
          </td>
          <td class="border-t">
            <inertia-link class="px-6 py-4 flex items-center" :href="route('drivers.edit', driver.id)" tabindex="-1">
              {{ driver.contact_no }}
            </inertia-link>
          </td>
          <td class="border-t w-px">
            <inertia-link class="px-4 flex items-center" :href="route('drivers.edit', driver.id)" tabindex="-1">
              <icon name="cheveron-right" class="block w-6 h-6 fill-gray-400" />
            </inertia-link>
          </td>
        </tr>
         <tr v-if="drivers.data.length === 0">
          <td class="border-t px-6 py-4" colspan="4">No drivers found.</td>
        </tr>
    </table>
    </div>
    <pagination :links="drivers.links" />
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
  metaInfo: { title: 'Drivers' },
  layout: Layout,
  components: {
    Icon,
    Pagination,
    SearchFilter,
  },
  props: {
    drivers: Object,
    filters: Object,
  },
  data() {
    return {
      form: {
        search: this.filters.search,
        trashed: this.filters.trashed,
      },
    }
  },
  watch: {
    form: {
      handler: throttle(function() {
        let query = pickBy(this.form)
        this.$inertia.replace(this.route('drivers.index', Object.keys(query).length ? query : { remember: 'forget' }))
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