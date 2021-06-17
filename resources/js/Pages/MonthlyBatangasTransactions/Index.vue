<template>
  <div>
    <h1 class="mb-8 font-bold text-3xl">Monthly Batangas Transactions</h1>
    <div class="mb-6 flex justify-between items-center">
      <search-filter v-model="form.search" class="w-full max-w-md mr-4" @reset="reset">
<!--         <label class="block text-gray-700">Trashed:</label>
        <select v-model="form.trashed" class="mt-1 w-full form-select">
          <option :value="null" />
          <option value="with">With Trashed</option>
          <option value="only">Only Trashed</option>
        </select>
 -->      </search-filter>
      <inertia-link class="btn-indigo" :href="route('monthly-batangas-transactions.create')" v-if="$page.auth.user.can.batangasTransaction.create">
        <span>Add</span>
        <span class="hidden md:inline">Monthly Transaction</span>
      </inertia-link>
    </div>
    <div class="bg-white rounded shadow overflow-x-auto">
      <table class="w-full whitespace-no-wrap">
        <tr class="text-left font-bold">
          <th class="px-6 pt-6 pb-4">Year</th>
          <th class="px-6 pt-6 pb-4">Month</th>
        </tr>
        <tr v-for="batangasTransaction in monthly_batangas_transactions.data" :key="batangasTransaction.id" class="hover:bg-gray-100 focus-within:bg-gray-100">
          <td class="border-t">
            <div class="px-6 py-4 flex items-center">
              {{ batangasTransaction.year }}
            </div>
          </td>
          <td class="border-t">
            <div class="px-6 py-4 flex items-center">
              {{ batangasTransaction.month }}
            </div>
          </td>
          <td class="border-t w-px">
            <inertia-link class="px-4 flex items-center" :href="route('monthly-batangas-transactions.edit', batangasTransaction.id)" tabindex="-1" v-if="$page.auth.user.can.batangasTransaction.update">
              <icon name="edit" class="block w-6 h-6 fill-gray-400" />
            </inertia-link>
          </td>
          <td class="border-t w-px">
            <a class="px-4 flex items-center" target="_blank" :href="route('monthly-batangas-transactions.print', batangasTransaction.id)" tabindex="-1" v-if="$page.auth.user.can.batangasTransaction.print">
              <icon name="printer" class="block w-6 h-6 fill-gray-400" />
            </a>
          </td>
        </tr>
        <tr v-if="monthly_batangas_transactions.data.length === 0">
          <td class="border-t px-6 py-4" colspan="4">No transaction found.</td>
        </tr>
    </table>
    </div>
    <pagination :links="monthly_batangas_transactions.links" />
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
  metaInfo: { title: 'Batangas Transaction' },
  layout: Layout,
  components: {
    Icon,
    Pagination,
    SearchFilter,
  },
  props: {
    monthly_batangas_transactions: Object,
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
        this.$inertia.replace(this.route('monthly-batangas-transactions.index', Object.keys(query).length ? query : { remember: 'forget' }))
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
