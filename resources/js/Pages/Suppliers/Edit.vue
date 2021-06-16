<template>
  <div>
    <h1 class="mb-8 font-bold text-3xl">
      <inertia-link class="text-blue-600 hover:text-blue-800" :href="route('suppliers.index')">Suppliers</inertia-link>
      <span class="text-blue-600 font-medium">/</span> {{ form.name }}
    </h1>

    <trashed-message v-if="supplier.deleted_at" class="mb-6" @restore="restore" :canRestore="$page.auth.user.can.supplier.restore">
      This supplier has been deleted.
    </trashed-message>

    <div class="p-1">
      <ul class="flex border-b">
        <li @click="openTab = 1" :class="{ '-mb-px': openTab === 1 }" class="-mb-px mr-1">
          <a :class="openTab === 1 ? activeClasses : inactiveClasses" class="bg-white inline-block py-2 px-4 font-semibold">Info</a>
        </li>
      </ul>

      <div class="w-full pt-4">
        <!-- Tab 1 -->
        <div v-show="openTab === 1">
          <div class="bg-white rounded shadow overflow-hidden max-w-3xl -mt-4">
            <form @submit.prevent="submit">
              <div class="p-8 -mr-6 -mb-8 flex flex-wrap">
                <text-input v-model="form.name" :error="errors.name" class="pr-6 pb-8 w-full lg:w-1/2" label="Company Name" />
                <text-input v-model="form.office" :error="errors.office" class="pr-6 pb-8 w-full lg:w-1/2" label="Office Address" />
                <text-input v-model="form.contact_person" :error="errors.contact_person" class="pr-6 pb-8 w-full lg:w-1/2" label="Contact Person" />
                <text-input v-model="form.contact_no" :error="errors.contact_no" class="pr-6 pb-8 w-full lg:w-1/2" label="Contact No." />
                <text-input v-model="form.email_address" :error="errors.email_address" class="pr-6 pb-8 w-full lg:w-1/2" label="Email Address" />
              </div>
              <div class="px-8 py-4 bg-gray-100 border-t border-gray-200 flex justify-end items-center">
                <button v-if="!supplier.deleted_at && $page.auth.user.can.supplier.delete" class="text-red-600 hover:underline" tabindex="-1" type="button" @click="destroy">Delete Supplier</button>
                <loading-button :loading="sending" class="btn-indigo ml-auto" type="submit">Update Supplier</loading-button>
              </div>
            </form>
          </div>
        </div>

      </div>
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
    supplier: Object,
  },
  remember: 'form',
  data() {
    return {
      sending: false,
      form: {
        id: this.supplier.id,
        name: this.supplier.name,
        office: this.supplier.office,
        contact_person: this.supplier.contact_person,
        contact_no: this.supplier.contact_no,
        email_address: this.supplier.email_address,
      },
      // Tabs
      openTab: 1,
      activeClasses: 'border-l border-t border-r rounded-t text-blue-600',
      inactiveClasses: 'text-blue-500 hover:text-blue-800',
    }
  },
  methods: {
    submit() {
      this.$inertia.put(this.route('suppliers.update', this.supplier.id), this.form, {
        onStart: () => this.sending = true,
        onFinish: () => this.sending = false,
      })
    },
    destroy() {
      if (confirm('Are you sure you want to delete this Supplier?')) {
        this.$inertia.delete(this.route('suppliers.destroy', this.supplier.id))
      }
    },
    restore() {
      if (confirm('Are you sure you want to restore this Supplier?')) {
        this.$inertia.put(this.route('suppliers.restore', this.supplier.id))
      }
    },
  },
}
</script>
