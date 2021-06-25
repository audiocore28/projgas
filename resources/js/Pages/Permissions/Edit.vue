<template>
  <div>
    <h1 class="mb-8 font-bold text-3xl">
      <inertia-link class="text-blue-600 hover:text-blue-800" :href="route('permissions.index')">Permissions</inertia-link>
      <span class="text-blue-600 font-medium">/</span> {{ form.name }}
    </h1>

    <trashed-message v-if="permission.deleted_at" class="mb-6" @restore="restore">
      This permission has been deleted.
    </trashed-message>

    <div class="bg-white rounded shadow overflow-hidden max-w-3xl">
      <form @submit.prevent="submit">
        <div class="p-8 -mr-6 -mb-8 flex flex-wrap">
          <text-input v-model="form.name" :error="errors.name" class="pr-6 pb-8 w-full lg:w-1/2" label="Name" />
          <select-input v-model="form.group" :error="errors.group" class="pr-6 pb-4 w-full lg:w-1/2" label="Group">
            <option :value="null" />
            <option v-for="(group, index) in groups" :key="index" :value="group">{{ group }}</option>
          </select-input>
        </div>
        <div class="px-8 py-4 bg-gray-100 border-t border-gray-200 flex justify-end items-center">
          <button v-if="!permission.deleted_at" class="text-red-600 hover:underline" tabindex="-1" type="button" @click="destroy">Delete Permission</button>
          <loading-button :loading="sending" class="btn-indigo ml-auto" type="submit">Update Permission</loading-button>
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
  },
  props: {
    errors: Object,
    permission: Object,
  },
  remember: 'form',
  data() {
    return {
      sending: false,
      groups: ['Purchases', 'Batangas Transactions', 'Mindoro Transactions', 'Suppliers', 'Clients', 'Client Payments', 'Drivers', 'Helpers', 'Tankers', 'Products', 'Users', 'Roles', 'Permissions'],
      form: {
        name: this.permission.name,
        group: this.permission.group,
      },
    }
  },
  methods: {
    submit() {
      this.$inertia.put(this.route('permissions.update', this.permission.id), this.form, {
        onStart: () => this.sending = true,
        onFinish: () => this.sending = false,
      })
    },
    destroy() {
      if (confirm('Are you sure you want to delete this permission?')) {
        this.$inertia.delete(this.route('permissions.destroy', this.permission.id))
      }
    },
    restore() {
      if (confirm('Are you sure you want to restore this permission?')) {
        this.$inertia.put(this.route('permissions.restore', this.permission.id))
      }
    },
  },
}
</script>
