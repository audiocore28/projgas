<template>
  <div>
    <h1 class="mb-8 font-bold text-3xl">Roles</h1>
    <div class="mb-6 flex justify-between items-center">
<!--       <search-filter v-model="form.search" class="w-full max-w-md mr-4" @reset="reset">
        <label class="block text-gray-700">Trashed:</label>
        <select v-model="form.trashed" class="mt-1 w-full form-select">
          <option :value="null" />
          <option value="with">With Trashed</option>
          <option value="only">Only Trashed</option>
        </select>
      </search-filter>
 -->
      <inertia-link class="btn-indigo" :href="route('roles.create')" v-if="$page.auth.user.can.role.create">
        <span>Add</span>
        <span class="hidden md:inline">Role</span>
      </inertia-link>
    </div>
    <div class="bg-white rounded shadow overflow-x-auto">
      <table class="w-full whitespace-no-wrap">
        <tr class="text-left font-bold">
          <th class="px-6 pt-6 pb-4">Name</th>
          <th class="px-6 pt-6 pb-4">Permissions</th>
        </tr>
         <tr v-for="role in roles.data" :key="role.id" class="hover:bg-gray-100 focus-within:bg-gray-100">
          <td class="border-t">
            <div class="px-6 py-4 flex items-center focus:text-indigo-500">
              {{ role.name }}
              <icon v-if="role.deleted_at" name="trash" class="flex-shrink-0 w-3 h-3 fill-gray-400 ml-2" />
            </div>
          </td>
          <td class="border-t">
            <div class="px-6 py-4 flex items-center focus:text-indigo-500">
              <div v-if="role.permissions" v-for="permission in role.permissions">
                <span class="px-2 py-2 text-sm leading-5 font-semibold rounded-full bg-grey-100 text-grey-800">
                  {{ permission.name }}
                </span>
              </div>
            </div>
          </td>
          <td class="border-t w-px" v-if="$page.auth.user.can.role.update">
            <inertia-link class="px-4 flex items-center" :href="route('roles.edit', role.id)" tabindex="-1">
              <icon name="edit" class="block w-6 h-6 fill-gray-400" />
            </inertia-link>
          </td>
        </tr>
         <tr v-if="roles.data.length === 0">
          <td class="border-t px-6 py-4" colspan="4">No roles found.</td>
        </tr>
    </table>
    </div>
    <pagination :links="roles.links" />
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
// import { sortingMixin } from '@/Mixins/sortingMixin'

export default {
  // mixins: [sortingMixin],
  metaInfo: { title: 'Roles' },
  layout: Layout,
  components: {
    Icon,
    Pagination,
    SearchFilter,
  },
  props: {
    roles: Object,
    // filters: Object,
  },
  // data() {
  //   return {
  //     form: {
  //       search: this.filters.search,
  //       trashed: this.filters.trashed,
  //     },
  //   }
  // },
  // watch: {
  //   form: {
  //     handler: throttle(function() {
  //       let query = pickBy(this.form)
  //       this.$inertia.replace(this.route('roles.index', Object.keys(query).length ? query : { remember: 'forget' }))
  //     }, 150),
  //     deep: true,
  //   },
  // },
  // methods: {
  //   reset() {
  //     this.form = mapValues(this.form, () => null)
  //   },
  // },
}
</script>
