<template>
  <div>
    <h1 class="mb-8 font-bold text-3xl">
      <inertia-link class="text-blue-600 hover:text-blue-800" :href="route('permissions.index')">Permissions</inertia-link>
      <span class="text-blue-600 font-medium">/</span> Create
    </h1>
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

export default {
  metaInfo: { title: 'Create Permission' },
  layout: Layout,
  components: {
    LoadingButton,
    SelectInput,
    TextInput,
  },
  props: {
    errors: Object,
  },
  remember: 'form',
  data() {
    return {
      sending: false,
      groups: ['Purchases', 'Batangas Transactions', 'Mindoro Transactions', 'Suppliers', 'Clients', 'Client Payments', 'Drivers', 'Helpers', 'Tankers', 'Products', 'Users', 'Roles', 'Permissions'],
      form: {
        name: null,
        group: null,
      },
    }
  },
  methods: {
    submit() {
      this.$inertia.post(this.route('permissions.store'), this.form, {
        onStart: () => this.sending = true,
        onFinish: () => this.sending = false,
      })
    },
  },
}
</script>
