<template>
  <div>
    <h1 class="mb-8 font-bold text-3xl">Clients</h1>
    <div class="mb-6 flex justify-between items-center">
      <search-filter v-model="form.search" class="w-full max-w-md mr-4" @reset="reset" :isVisible="$page.auth.user.can.client.restore">
        <label class="block text-gray-700">Trashed:</label>
        <select v-model="form.trashed" class="mt-1 w-full form-select">
          <option :value="null" />
          <option value="with">With Trashed</option>
          <option value="only">Only Trashed</option>
        </select>
      </search-filter>
      <inertia-link class="btn-indigo" :href="route('clients.create')" v-if="$page.auth.user.can.client.create">
        <span>Add</span>
        <span class="hidden md:inline">Client</span>
      </inertia-link>
    </div>
    <div class="bg-white rounded shadow overflow-x-auto">
      <table class="w-full whitespace-no-wrap">
        <tr class="text-left font-bold">
          <th class="px-6 pt-6 pb-4 cursor-pointer">
            <span class="inline-flex w-full justify-between" @click="sort('id')">ID
              <icon v-if="form.field === 'id' && form.direction === 'asc'" name="sort-up" class="block w-6 h-6 fill-gray-400" />
              <icon v-if="form.field === 'id' && form.direction === 'desc'" name="sort-down" class="block w-6 h-6 fill-gray-400" />
            </span>
          </th>
          <th class="px-6 pt-6 pb-4 cursor-pointer">
            <span class="inline-flex w-full justify-between" @click="sort('name')">Name
              <icon v-if="form.field === 'name' && form.direction === 'asc'" name="sort-up" class="block w-6 h-6 fill-gray-400" />
              <icon v-if="form.field === 'name' && form.direction === 'desc'" name="sort-down" class="block w-6 h-6 fill-gray-400" />
            </span>
          </th>
          <th class="px-6 pt-6 pb-4">Contact No</th>
          <th class="px-6 pt-6 pb-4" colspan="2" align="center" v-if="$page.auth.user.can.client.viewPayment">SOA</th>
          <th class="px-6 pt-6 pb-4" v-if="$page.auth.user.can.client.update">Info</th>
        </tr>
         <tr v-for="client in clients.data" :key="client.id" class="hover:bg-gray-100 focus-within:bg-gray-100">
          <td class="border-t">
            <div class="px-6 py-4 flex items-center focus:text-indigo-500">
              {{ client.id }}
            </div>
          </td>
          <td class="border-t">
            <div class="px-6 py-4 flex items-center focus:text-indigo-500">
              {{ client.name }}
              <icon v-if="client.deleted_at" name="trash" class="flex-shrink-0 w-3 h-3 fill-gray-400 ml-2" />
            </div>
          </td>
          <td class="border-t">
            <div class="px-6 py-4 flex items-center focus:text-indigo-500">
              {{ client.contact_no }}
            </div>
          </td>
          <td class="border-t w-px" v-if="$page.auth.user.can.client.viewPayment">
            <inertia-link class="px-4 flex items-center focus:text-indigo-500" :href="route('batangas-payment-details.edit', client.id)" tabindex="-1" v-if="client.batangas_transaction_details_count">
              <!-- <icon name="view" class="block w-6 h-6 fill-gray-400" /> -->
              <span class="relative inline-block">
                <span class="text-gray-400 font-extrabold">BAT</span>
                <span class="absolute top-0 right-0 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-red-100 transform translate-x-1/2 -translate-y-1/2 bg-yellow-500 rounded-full" v-if="client.unverified_batangas_payment_count">{{ client.unverified_batangas_payment_count }}</span>
              </span>
            </inertia-link>
          </td>
          <td class="border-t w-px" v-if="$page.auth.user.can.client.viewPayment">
            <inertia-link class="px-4 flex items-center focus:text-indigo-500" :href="route('mindoro-payment-details.edit', client.id)" tabindex="-1" v-if="client.mindoro_transaction_details_count">
              <!-- <icon name="view" class="block w-6 h-6 fill-gray-400" /> -->
              <span class="relative inline-block">
                <span class="text-gray-400 font-extrabold">MDO</span>
                <span class="absolute top-0 right-0 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-red-100 transform translate-x-1/2 -translate-y-1/2 bg-yellow-500 rounded-full" v-if="client.unverified_mindoro_payment_count">{{ client.unverified_mindoro_payment_count }}</span>
              </span>
            </inertia-link>
          </td>
 <!--           <td class="border-t w-px">
            <inertia-link class="px-4 flex items-center focus:text-indigo-500" :href="route('clients.show', client.id)" tabindex="-1" v-if="$page.auth.user.can.client.view">
              <icon name="view" class="block w-6 h-6 fill-gray-400" />
            </inertia-link>
          </td>
 -->
          <td class="border-t w-px" v-if="$page.auth.user.can.client.update">
            <inertia-link class="px-4 flex items-center focus:text-indigo-500" :href="route('clients.edit', client.id)" tabindex="-1">
              <icon name="edit" class="block w-6 h-6 fill-gray-400" />
            </inertia-link>
          </td>
        </tr>
         <tr v-if="clients.data.length === 0">
          <td class="border-t px-6 py-4" colspan="4">No clients found.</td>
        </tr>
    </table>
    </div>
    <pagination :links="clients.links" />
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
  metaInfo: { title: 'Clients' },
  layout: Layout,
  components: {
    Icon,
    Pagination,
    SearchFilter,
  },
  props: {
    clients: Object,
    filters: Object,
  },
  data() {
    return {
      form: {
        search: this.filters.search,
        field: this.filters.field,
        direction: this.filters.direction,
        trashed: this.filters.trashed,
      },
    }
  },
  watch: {
    form: {
      handler: throttle(function() {
        let query = pickBy(this.form)
        this.$inertia.replace(this.route('clients.index', Object.keys(query).length ? query : { remember: 'forget' }))
      }, 150),
      deep: true,
    },
  },
  methods: {
    sort(field) {
      this.form.field = field;
      this.form.direction = this.form.direction === 'desc' ? 'asc' : 'desc';
    },
    reset() {
      this.form = mapValues(this.form, () => null)
    },
  },
}
</script>
