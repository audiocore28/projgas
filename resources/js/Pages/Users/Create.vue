<template>
  <div>
    <h1 class="mb-8 font-bold text-3xl">
      <inertia-link class="text-blue-600 hover:text-blue-800" :href="route('users.index')">Users</inertia-link>
      <span class="text-blue-600 font-medium">/</span> Create
    </h1>
    <div class="bg-white rounded shadow overflow-hidden max-w-3xl">
      <form @submit.prevent="submit">
        <div class="p-8 -mr-6 -mb-8 flex flex-wrap">
          <text-input v-model="form.first_name" :error="errors.first_name" class="pr-6 pb-8 w-full lg:w-1/2" label="First Name" />
          <text-input v-model="form.last_name" :error="errors.last_name" class="pr-6 pb-8 w-full lg:w-1/2" label="Last name" />
          <text-input v-model="form.email" :error="errors.email" class="pr-6 pb-8 w-full lg:w-1/2" label="Email" />
          <text-input v-model="form.password" :error="errors.password" class="pr-6 pb-8 w-full lg:w-1/2" type="password" autocomplete="new-password" label="Password" />
<!--           <select-input v-model="form.owner" :error="errors.owner" class="pr-6 pb-8 w-full lg:w-1/2" label="Owner">
            <option :value="true">Yes</option>
            <option :value="false">No</option>
          </select-input>
          <file-input v-model="form.photo" :error="errors.photo" class="pr-6 pb-8 w-full lg:w-1/2" type="file" accept="image/*" label="Photo" />
 -->

          <div class="pr-6 pb-8 w-full">
            <label class="form-label block">Roles:</label>
            <multiselect
              id="role_id"
              name="roles[]"
              v-model="form.selectedRoles"
              placeholder=""
              class="mt-3 text-xs"
              :options="roles"
              label="name"
              track-by="id"
              @search-change="onSearchRoleChange"
              @input="onSelectedRole"
              :show-labels="false"
              :multiple="true"
            ></multiselect>
            <!-- :allow-empty="false" -->
          </div>

          <div class="pr-6 pb-8 w-full">
            <label class="form-label block">Permissions:</label>
            <multiselect
              id="permission_id"
              name="permissions[]"
              v-model="form.selectedPermissions"
              placeholder=""
              class="mt-3 text-xs"
              :options="permissions"
              label="name"
              track-by="id"
              @search-change="onSearchPermissionChange"
              @input="onSelectedPermission"
              :show-labels="false"
              :multiple="true"
            ></multiselect>
            <!-- :allow-empty="false" -->
          </div>
        </div>


        <div class="px-8 py-4 bg-gray-100 border-t border-gray-200 flex justify-end items-center">
          <loading-button :loading="sending" class="btn-indigo" type="submit">Create User</loading-button>
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
import FileInput from '@/Shared/FileInput'
import Multiselect from 'vue-multiselect'
import {throttle} from 'lodash'

export default {
  metaInfo: { title: 'Create User' },
  layout: Layout,
  components: {
    LoadingButton,
    SelectInput,
    TextInput,
    FileInput,
    Multiselect,
  },
  props: {
    errors: Object,
    roles: {
      type: Array,
      default: () => [],
    },
    permissions: {
      type: Array,
      default: () => [],
    },
  },
  remember: 'form',
  data() {
    return {
      sending: false,
      form: {
        first_name: null,
        last_name: null,
        email: null,
        password: null,
        // owner: false,
        // photo: null,
        roles: [],
        selectedRoles: [],
        permissions: [],
        selectedPermissions: [],
      },
    }
  },
  methods: {
    submit() {
      this.$inertia.post(this.route('users.store'), this.form, {
        onStart: () => this.sending = true,
        onFinish: () => this.sending = false,
      })
    },

    // Multiselect
    onSearchRoleChange: throttle(function(term) {
      this.$inertia.get(this.route('users.create'), {term}, {
        preserveState: true,
        preserveScroll: true,
        replace: true,
        only: ['roles'],
      })
    }, 300),
    onSelectedRole(roles) {
      this.form.roles = roles.map(role => role.id);
    },
    onSearchPermissionChange: throttle(function(term) {
      this.$inertia.get(this.route('users.create'), {term}, {
        preserveState: true,
        preserveScroll: true,
        replace: true,
        only: ['permissions'],
      })
    }, 300),
    onSelectedPermission(permissions) {
      this.form.permissions = permissions.map(permission => permission.id);
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
