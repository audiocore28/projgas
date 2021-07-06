<template>
  <div>
    <h1 class="mb-8 font-bold text-3xl">
      <inertia-link class="text-blue-600 hover:text-blue-800" :href="route('tankers.index')">Tankers</inertia-link>
      <span class="text-blue-600 font-medium">/</span> {{ form.plate_no }}
    </h1>

    <trashed-message v-if="tanker.deleted_at" class="mb-6" @restore="restore" :canRestore="$page.auth.user.can.tanker.restore">
      This tanker has been deleted.
    </trashed-message>

    <div class="p-1">
<!--       <ul class="flex border-b">
        <li @click="openTab = 1" :class="{ '-mb-px': openTab === 1 }" class="-mb-px mr-1">
          <a :class="openTab === 1 ? activeClasses : inactiveClasses" class="bg-white inline-block py-2 px-4 font-semibold" href="#">Info</a>
        </li>
      </ul>
 -->
      <div class="w-full pt-4">
        <!-- Tab 1 -->
        <div v-show="openTab === 1">
          <div class="bg-white rounded shadow overflow-hidden max-w-3xl -mt-4">
            <form @submit.prevent="submit">
              <div class="p-8 -mr-6 -mb-8 flex flex-wrap">
                <text-input v-model="form.plate_no" :error="errors.plate_no" class="pr-6 pb-8 w-full lg:w-1/2" label="Plate No" />
                <text-input v-model="form.compartment" :error="errors.compartment" class="pr-6 pb-8 w-full lg:w-1/2" label="Compartment" />
              </div>
              <div class="px-8 py-4 bg-gray-100 border-t border-gray-200 flex justify-end items-center">
                <button v-if="!tanker.deleted_at && $page.auth.user.can.tanker.delete" class="text-red-600 hover:underline" tabindex="-1" type="button" @click="destroy">Delete Tanker</button>
                <loading-button :loading="sending" class="btn-indigo ml-auto" type="submit">Update Tanker</loading-button>
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
    return { title: this.form.plate_no }
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
    tanker: Object,
    drivers: Array,
    helpers: Array,
  },
  remember: 'form',
  data() {
    return {
      sending: false,
      form: {
        id: this.tanker.id,
        plate_no: this.tanker.plate_no,
        compartment: this.tanker.compartment,
      },
      // Tabs
      openTab: 1,
      activeClasses: 'border-l border-t border-r rounded-t text-blue-600',
      inactiveClasses: 'text-blue-500 hover:text-blue-800',
    }
  },
  methods: {
    submit() {
      this.$inertia.put(this.route('tankers.update', this.tanker.id), this.form, {
        onStart: () => this.sending = true,
        onFinish: () => this.sending = false,
      })
    },
    destroy() {
      if (confirm('Are you sure you want to delete this tanker?')) {
        this.$inertia.delete(this.route('tankers.destroy', this.tanker.id))
      }
    },
    restore() {
      if (confirm('Are you sure you want to restore this tanker?')) {
        this.$inertia.put(this.route('tankers.restore', this.tanker.id))
      }
    },
  },
}
</script>
