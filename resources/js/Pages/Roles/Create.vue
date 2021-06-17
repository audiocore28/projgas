<template>
  <div>
    <h1 class="mb-8 font-bold text-3xl">
      <inertia-link class="text-blue-600 hover:text-blue-800" :href="route('roles.index')">Roles</inertia-link>
      <span class="text-blue-600 font-medium">/</span> Create
    </h1>
    <div class="bg-white rounded shadow overflow-hidden max-w-3xl">
      <form @submit.prevent="submit">
        <div class="p-8 -mr-6 -mb-8">
          <text-input v-model="form.name" :error="errors.name" class="pr-6 pb-8 w-full lg:w-1/2" label="Name" />

          <label class="font-bold text-md inline-flex items-center mt-3 mb-6">
            <input type="checkbox" class="form-checkbox h-4 w-4 text-gray-600" v-model="selectAll" @click="selectAllPermissions()"><span class="ml-2 text-gray-700">Permissions</span>
          </label>

          <ul class="text-sm font-medium text-gray-900 w-full grid grid-cols-1 gap-3 xl:grid-cols-3">

            <li v-for="(lists, group) in permissions" class="mb-6">
              <div class="text-sm font-bold mb-2">
                <label class="inline-flex items-center mt-3">
                  <input type="checkbox" class="form-checkbox h-4 w-4 text-gray-600" :value="group" v-model="selectedGroups" @click="selectGroup(lists, group)"><span class="ml-2 text-gray-700">{{ group }}</span>
                </label>
              </div>
              <div class="ml-4">
                <ul v-for="list in lists" :key="list.id">
                  <li>
                    <label class="inline-flex items-center mt-3 text-xs">
                      <input type="checkbox" class="form-checkbox h-4 w-4 text-gray-600" :value="list.id" v-model="form.selectedPermissions"><span class="ml-2 text-gray-700">{{ list.name }}</span>
                    </label>
                  </li>
                </ul>
              </div>
            </li>

          </ul>
        </div>

        <div class="px-8 py-4 bg-gray-100 border-t border-gray-200 flex justify-end items-center">
          <loading-button :loading="sending" class="btn-indigo" type="submit">Save</loading-button>
        </div>
      </form>
    </div>
  </div>
</template>

<script>
import Layout from '@/Shared/Layout'
import LoadingButton from '@/Shared/LoadingButton'
import SelectInput from '@/Shared/SelectInput'
import TextInput from '@/Shared/TextInput'
import Multiselect from 'vue-multiselect'
import {throttle} from 'lodash'

export default {
  metaInfo: { title: 'Create Role' },
  layout: Layout,
  components: {
    LoadingButton,
    SelectInput,
    TextInput,
    Multiselect,
  },
  props: {
    errors: Object,
    permissions: {
      type: Object,
      default: () => [],
    },
  },
  remember: 'form',
  data() {
    return {
      sending: false,
      selectAll: false,
      selectedGroups: [],
      form: {
        name: null,
        selectedPermissions: [],
      },
    }
  },
  methods: {
    submit() {
      this.$inertia.post(this.route('roles.store'), this.form, {
        onStart: () => this.sending = true,
        onFinish: () => this.sending = false,
      })
    },

    selectGroup(lists, group) {
      var selectedPermissionIds = lists.map(list => list.id);

      selectedPermissionIds.forEach(id => {
        if (!this.selectedGroups.includes(group)) {
          if (!this.form.selectedPermissions.includes(id)) {
            this.form.selectedPermissions.push(id);
          }
        } else {
          const index = this.form.selectedPermissions.indexOf(id);
          if (index > -1) {
            this.form.selectedPermissions.splice(index, 1);
          }
        }
      });
    },

    selectAllPermissions() {
      this.form.selectedPermissions = [];

      var permissionsArray = Object.entries(this.permissions).map(permission => permission[1]);
      const permissions = [].concat.apply([], permissionsArray);

      if (!this.selectAll) {
        permissions.forEach(permission => {
          this.form.selectedPermissions.push(permission.id);
        });
      }
    },
  },
}
</script>

<style>
  .multiselect__single, .multiselect__option {
    font-size: 0.875rem;
  }

  .multiselect__tag {
    @apply bg-blue-500;
  }

  .multiselect__tag-icon:after {
    @apply text-white;
  }

  .multiselect__tag-icon:hover {
    @apply bg-blue-600;
  }

  .multiselect__option--highlight {
    @apply bg-blue-500;
  }

  /*.multiselect__element span {}*/

  .multiselect--active {
    position: relative;
    z-index: 100;
  }
</style>
