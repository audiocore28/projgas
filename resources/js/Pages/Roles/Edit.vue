<template>
  <div>
    <h1 class="mb-8 font-bold text-3xl">
      <inertia-link class="text-blue-600 hover:text-blue-800" :href="route('roles.index')">Roles</inertia-link>
      <span class="text-blue-600 font-medium">/</span> {{ form.name }}
    </h1>

    <trashed-message v-if="role.deleted_at" class="mb-6" @restore="restore">
      This role has been deleted.
    </trashed-message>

    <div class="bg-white rounded shadow overflow-hidden max-w-3xl">
      <form @submit.prevent="submit">
        <div class="p-8 -mr-6 -mb-8 flex flex-wrap">
          <text-input v-model="form.name" :error="errors.name" class="pr-6 pb-8 w-full lg:w-1/2" label="Name" />

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
          <button v-if="!role.deleted_at" class="text-red-600 hover:underline" tabindex="-1" type="button" @click="destroy">Delete Role</button>
          <loading-button :loading="sending" class="btn-indigo ml-auto" type="submit">Update Role</loading-button>
        </div>
      </form>
    </div>
  </div>
</template>

<script>
import Icon from '@/Shared/Icon'
import Layout from '@/Shared/Layout'
import LoadingButton from '@/Shared/LoadingButton'
import SelectInput from '@/Shared/SelectInput'
import TextInput from '@/Shared/TextInput'
import TrashedMessage from '@/Shared/TrashedMessage'
import Multiselect from 'vue-multiselect'
import {throttle} from 'lodash'

export default {
  metaInfo() {
    return { title: this.form.name }
  },
  layout: Layout,
  components: {
    Icon,
    LoadingButton,
    SelectInput,
    TextInput,
    TrashedMessage,
    Multiselect,
  },
  props: {
    errors: Object,
    role: Object,
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
        name: this.role.name,
        permissions: [],
        selectedPermissions: this.role.selected_permissions,
      },
    }
  },
  methods: {
    submit() {
      this.$inertia.put(this.route('roles.update', this.role.id), this.form, {
        onStart: () => this.sending = true,
        onFinish: () => this.sending = false,
      })
    },
    destroy() {
      if (confirm('Are you sure you want to delete this role?')) {
        this.$inertia.delete(this.route('roles.destroy', this.role.id))
      }
    },
    restore() {
      if (confirm('Are you sure you want to restore this role?')) {
        this.$inertia.put(this.route('roles.restore', this.role.id))
      }
    },

    // Multiselect
    onSearchPermissionChange: throttle(function(term) {
      this.$inertia.get(this.route('roles.edit'), {term}, {
        preserveState: true,
        preserveScroll: true,
        replace: true,
      })
    }, 300),
    onSelectedPermission(permissions) {
      this.form.permissions = permissions.map(permission => permission.id);
    },
  },
  mounted() {
    this.form.selectedPermissions.forEach(permission => {
      this.form.permissions.push(permission.id);
    });
  }
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
