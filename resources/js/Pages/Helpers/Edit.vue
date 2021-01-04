<template>
  <div>
    <h1 class="mb-8 font-bold text-3xl">
      <inertia-link class="text-blue-600 hover:text-blue-800" :href="route('helpers.index')">Helpers</inertia-link>
      <span class="text-blue-600 font-medium">/</span> {{ form.name }}
    </h1>
    <div class="bg-white rounded shadow overflow-hidden max-w-3xl">
      <form @submit.prevent="submit">
        <div class="p-8 -mr-6 -mb-8 flex flex-wrap">
          <text-input v-model="form.name" :error="errors.name" class="pr-6 pb-8 w-full lg:w-1/2" label="Full Name" />
          <text-input v-model="form.nickname" :error="errors.nickname" class="pr-6 pb-8 w-full lg:w-1/2" label="Nickname" />
          <text-input v-model="form.address" :error="errors.address" class="pr-6 pb-8 w-full lg:w-1/2" label="Address" />
          <text-input v-model="form.dob" :error="errors.dob" class="pr-6 pb-8 w-full lg:w-1/2" label="Date of Birth" />
          <text-input v-model="form.date_hired" :error="errors.date_hired" class="pr-6 pb-8 w-full lg:w-1/2" label="Date Hired" />
          <select-input v-model="form.status" :error="errors.status" class="pr-6 pb-8 w-full lg:w-1/2" label="Status">
            <option :value="null" />
            <option value="1">Active</option>
            <option value="0">Inactive</option>
          </select-input>
          <text-input v-model="form.contact_no" :error="errors.contact_no" class="pr-6 pb-8 w-full lg:w-1/2" label="Contact No" />
        </div>
        <div class="px-8 py-4 bg-gray-100 border-t border-gray-200 flex justify-end items-center">
          <button v-if="!helper.deleted_at" class="text-red-600 hover:underline" tabindex="-1" type="button" @click="destroy">Delete Helper</button>
          <loading-button :loading="sending" class="btn-indigo ml-auto" type="submit">Update Helper</loading-button>
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
  // metaInfo() {
  //   return { title: this.form.name }
  // },
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
    helper: Object,
  },
  remember: 'form',
  data() {
    return {
      sending: false,
      form: {
        name: this.helper.name,
        nickname: this.helper.nickname,
        address: this.helper.address,
        dob: this.helper.dob,
        date_hired: this.helper.date_hired,
        status: this.helper.status,
        contact_no: this.helper.contact_no,
      },
    }
  },
  methods: {
    submit() {
      this.$inertia.put(this.route('helpers.update', this.helper.id), this.form, {
        onStart: () => this.sending = true,
        onFinish: () => this.sending = false,
      })
    },
    destroy() {
      if (confirm('Are you sure you want to delete this helper?')) {
        this.$inertia.delete(this.route('helpers.destroy', this.helper.id))
      }
    },
    restore() {
      if (confirm('Are you sure you want to restore this helper?')) {
        this.$inertia.put(this.route('helpers.restore', this.helper.id))
      }
    },
  },
}
</script>