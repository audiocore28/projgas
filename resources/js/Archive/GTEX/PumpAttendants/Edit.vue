<template>
  <div>
    <h1 class="mb-8 font-bold text-3xl">
      <inertia-link class="text-blue-600 hover:text-blue-800" :href="route('pump-attendants.index')">Pump Attendants</inertia-link>
      <span class="text-blue-600 font-medium">/</span> {{ form.name }}
    </h1>

    <trashed-message v-if="pump_attendant.deleted_at" class="mb-6" @restore="restore">
      This Pump Attendant has been deleted.
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
                <text-input v-model="form.name" :error="errors.name" class="pr-6 pb-8 w-full lg:w-1/2" label="Full Name" />
                <text-input v-model="form.nickname" :error="errors.nickname" class="pr-6 pb-8 w-full lg:w-1/2" label="Nickname" />
                <text-input v-model="form.address" :error="errors.address" class="pr-6 pb-8 w-full lg:w-1/2" label="Address" />
                <text-input v-model="form.contact_no" :error="errors.contact_no" class="pr-6 pb-8 w-full lg:w-1/2" label="Contact No" />
              </div>
              <div class="px-8 py-4 bg-gray-100 border-t border-gray-200 flex justify-end items-center">
                <button v-if="!pump_attendant.deleted_at" class="text-red-600 hover:underline" tabindex="-1" type="button" @click="destroy">Delete Pump Attendant</button>
                <loading-button :loading="sending" class="btn-indigo ml-auto" type="submit">Update Pump Attendant</loading-button>
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
    pump_attendant: Object,
  },
  remember: 'form',
  data() {
    return {
      sending: false,
      form: {
        name: this.pump_attendant.name,
        nickname: this.pump_attendant.nickname,
        address: this.pump_attendant.address,
        contact_no: this.pump_attendant.contact_no,
      },
      // Tabs
      openTab: 1,
      activeClasses: 'border-l border-t border-r rounded-t text-blue-600',
      inactiveClasses: 'text-blue-500 hover:text-blue-800',
    }
  },
  methods: {
    submit() {
      this.$inertia.put(this.route('pump-attendants.update', this.pump_attendant.id), this.form, {
        onStart: () => this.sending = true,
        onFinish: () => this.sending = false,
      })
    },
    destroy() {
      if (confirm('Are you sure you want to delete this Pump Attendant?')) {
        this.$inertia.delete(this.route('pump-attendants.destroy', this.pump_attendant.id))
      }
    },
    restore() {
      if (confirm('Are you sure you want to restore this Pump Attendant?')) {
        this.$inertia.put(this.route('pump-attendants.restore', this.pump_attendant.id))
      }
    },

  },
}
</script>