<template>
  <div>
    <h1 class="mb-8 font-bold text-3xl">
      <inertia-link class="text-blue-600 hover:text-blue-800" :href="route('suppliers.index')">Suppliers</inertia-link>
      <span class="text-blue-600 font-medium">/</span> Create
    </h1>
    <div class="bg-white rounded shadow overflow-hidden max-w-3xl">
      <form @submit.prevent="submit">
        <div class="p-8 -mr-6 -mb-8 flex flex-wrap">
          <text-input v-model="form.name" :error="errors.name" class="pr-6 pb-8 w-full lg:w-1/2" label="Name" />
          <text-input v-model="form.office" :error="errors.office" class="pr-6 pb-8 w-full lg:w-1/2" label="Office" />
          <text-input v-model="form.contact_person" :error="errors.contact_person" class="pr-6 pb-8 w-full lg:w-1/2" label="Contact Person" />
          <text-input v-model="form.contact_no" :error="errors.contact_no" class="pr-6 pb-8 w-full lg:w-1/2" label="Contact No." />
          <text-input v-model="form.email_address" :error="errors.email_address" class="pr-6 pb-8 w-full lg:w-1/2" label="Email Address" />
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
import TextInput from '@/Shared/TextInput'

export default {
  metaInfo: { title: 'Create Supplier' },
  layout: Layout,
  components: {
    LoadingButton,
    TextInput,
  },
  props: {
    errors: Object,
  },
  remember: 'form',
  data() {
    return {
      sending: false,
      form: {
        name: null,
        office: null,
        contact_person: null,
        contact_no: null,
        email_address: null,
      },
    }
  },
  methods: {
    submit() {
      this.$inertia.post(this.route('suppliers.store'), this.form, {
        onStart: () => this.sending = true,
        onFinish: () => this.sending = false,
      })
    },
  },
}
</script>