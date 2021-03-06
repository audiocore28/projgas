<template>
  <div>
    <h1 class="mb-8 font-bold text-3xl">Suppliers</h1>
    <div class="mb-6 flex justify-between items-center">
      <search-filter v-model="form.search" class="w-full max-w-md mr-4" @reset="reset" :isVisible="$page.auth.user.can.supplier.restore">
        <label class="block text-gray-700">Trashed:</label>
        <select v-model="form.trashed" class="mt-1 w-full form-select">
          <option :value="null" />
          <option value="with">With Trashed</option>
          <option value="only">Only Trashed</option>
        </select>
      </search-filter>
      <inertia-link class="btn-indigo" :href="route('suppliers.create')" v-if="$page.auth.user.can.supplier.create">
        <span>Add</span>
        <span class="hidden md:inline">Supplier</span>
      </inertia-link>
    </div>
    <div class="bg-white rounded shadow overflow-x-auto">
      <table class="w-full whitespace-no-wrap">
        <tr class="text-left font-bold">
          <th class="px-6 pt-6 pb-4">Company Name</th>
          <th class="px-6 pt-6 pb-4">Office</th>
          <th class="px-6 pt-6 pb-4">Contact No.</th>
          <th class="px-6 pt-6 pb-4"></th>
          <th class="px-6 pt-6 pb-4"></th>
        </tr>
         <tr v-for="supplier in suppliers.data" :key="supplier.id" class="hover:bg-gray-100 focus-within:bg-gray-100">
          <td class="border-t">
            <div class="px-6 py-4 flex items-center focus:text-indigo-500">
              {{ supplier.name }}
              <icon v-if="supplier.deleted_at" name="trash" class="flex-shrink-0 w-3 h-3 fill-gray-400 ml-2" />
            </div>
          </td>
          <td class="border-t">
            <div class="px-6 py-4 flex items-center focus:text-indigo-500">
              {{ supplier.office }}
            </div>
          </td>
          <td class="border-t">
            <div class="px-6 py-4 flex items-center focus:text-indigo-500">
              {{ supplier.contact_no }}
            </div>
          </td>
          <td class="border-t w-px">
            <inertia-link class="px-4 flex items-center focus:text-indigo-500" :href="route('suppliers.show', supplier.id)" tabindex="-1" v-if="$page.auth.user.can.supplier.view">
              <icon name="view" class="block w-6 h-6 fill-gray-400" />
            </inertia-link>
          </td>
          <td class="border-t w-px">
            <inertia-link class="px-4 flex items-center focus:text-indigo-500" :href="route('suppliers.edit', supplier.id)" tabindex="-1" v-if="$page.auth.user.can.supplier.update">
              <icon name="edit" class="block w-6 h-6 fill-gray-400" />
            </inertia-link>
          </td>
        </tr>
         <tr v-if="suppliers.data.length === 0">
          <td class="border-t px-6 py-4" colspan="4">No suppliers found.</td>
        </tr>
    </table>
    </div>
    <pagination :links="suppliers.links" />
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
  metaInfo: { title: 'Suppliers' },
  layout: Layout,
  components: {
    Icon,
    Pagination,
    SearchFilter,
  },
  props: {
    suppliers: Object,
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
        this.$inertia.replace(this.route('suppliers.index', Object.keys(query).length ? query : { remember: 'forget' }))
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
