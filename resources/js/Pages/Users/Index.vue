<template>
  <div>
    <h1 class="mb-8 font-bold text-3xl">Users</h1>
    <div class="mb-6 flex justify-between items-center">
      <search-filter v-model="form.search" class="w-full max-w-md mr-4" @reset="reset" :isVisible="$page.auth.user.can.user.restore">
<!--         <label class="block text-gray-700">Role:</label>
        <select v-model="form.role" class="mt-1 w-full form-select">
          <option :value="null" />
          <option value="user">User</option>
          <option value="owner">Owner</option>
        </select>
 -->
        <label class="block text-gray-700">Trashed:</label>
        <select v-model="form.trashed" class="mt-1 w-full form-select">
          <option :value="null" />
          <option value="with">With Trashed</option>
          <option value="only">Only Trashed</option>
        </select>
      </search-filter>
      <inertia-link class="btn-indigo" :href="route('users.create')" v-if="$page.auth.user.can.user.create">
        <span>Add</span>
        <span class="hidden md:inline">User</span>
      </inertia-link>
    </div>
    <div class="bg-white rounded shadow overflow-x-auto">
      <table class="w-full whitespace-no-wrap">
        <tr class="text-left font-bold">
          <th class="px-6 pt-6 pb-4">First Name</th>
          <th class="px-6 pt-6 pb-4">Last Name</th>
          <th class="px-6 pt-6 pb-4">Email</th>
          <th class="px-6 pt-6 pb-4">Role</th>
          <th class="px-6 pt-6 pb-4"></th>
        </tr>
        <tr v-for="user in users.data" :key="user.id" class="hover:bg-gray-100 focus-within:bg-gray-100">
          <td class="border-t">
            <div class="px-6 py-4 flex items-center focus:text-indigo-500">
              <!-- <img v-if="user.photo" class="block w-5 h-5 rounded-full mr-2 -my-2" :src="user.photo"> -->
              {{ user.first_name }}
              <icon v-if="user.deleted_at" name="trash" class="flex-shrink-0 w-3 h-3 fill-gray-400 ml-2" />
            </div>
          </td>
          <td class="border-t">
            <div class="px-6 py-4 flex items-center focus:text-indigo-500">
              {{ user.last_name }}
            </div>
          </td>
          <td class="border-t">
            <div class="px-6 py-4 flex items-center focus:text-indigo-500">
              {{ user.email }}
            </div>
          </td>
          <td class="border-t">
            <div class="px-6 py-4 flex items-center focus:text-indigo-500">
              <div v-if="user.roles" v-for="role in user.roles">
                <span class="px-2 py-2 text-sm leading-5 font-semibold rounded-full bg-grey-100 text-grey-800">
                  {{ role.name }}
                </span>
              </div>
            </div>
          </td>
          <td class="border-t w-px" v-if="$page.auth.user.can.user.update">
            <inertia-link class="px-4 flex items-center focus:text-indigo-500" :href="route('users.edit', user.id)" tabindex="-1">
              <icon name="edit" class="block w-6 h-6 fill-gray-400" />
            </inertia-link>
          </td>
        </tr>
        <tr v-if="users.length === 0">
          <td class="border-t px-6 py-4" colspan="4">No users found.</td>
        </tr>
      </table>
    </div>
  </div>
</template>

<script>
import Icon from '@/Shared/Icon'
import Layout from '@/Shared/Layout'
import mapValues from 'lodash/mapValues'
import pickBy from 'lodash/pickBy'
import SearchFilter from '@/Shared/SearchFilter'
import throttle from 'lodash/throttle'

export default {
  metaInfo: { title: 'Users' },
  layout: Layout,
  components: {
    Icon,
    SearchFilter,
  },
  props: {
    users: Object,
    filters: Object,
  },
  data() {
    return {
      form: {
        search: this.filters.search,
        // role: this.filters.role,
        trashed: this.filters.trashed,
      },
    }
  },
  watch: {
    form: {
      handler: throttle(function() {
        let query = pickBy(this.form)
        this.$inertia.replace(this.route('users.index', Object.keys(query).length ? query : { remember: 'forget' }))
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