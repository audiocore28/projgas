<template>
  <div>
    <h1 class="mb-8 font-bold text-3xl">
      <inertia-link class="text-blue-600 hover:text-blue-800" :href="route('suppliers.index')">Suppliers</inertia-link>
      <span class="text-blue-600 font-medium">/</span> {{ supplier.name }}
    </h1>

    <trashed-message v-if="supplier.deleted_at" class="mb-6" @restore="restore">
      This supplier has been deleted.
    </trashed-message>

    <div class="p-1">
      <ul class="flex border-b">
        <li @click="openTab = 2" :class="{ '-mb-px': openTab === 2 }" class="mr-1" v-show="purchaseDetails.data.length">
          <a :class="openTab === 2 ? activeClasses : inactiveClasses" class="bg-white inline-block py-2 px-4 font-semibold">Purchases</a>
        </li>
      </ul>

      <div class="w-full pt-4">
        <div v-show="openTab === 2">
          <purchases :purchaseDetails="purchaseDetails" record="suppliers"></purchases>
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
import Purchases from '@/Shared/Purchases'

export default {
  metaInfo() {
    return { title: this.supplier.name }
  },
  layout: Layout,
  components: {
    Icon,
    LoadingButton,
    SelectInput,
    TextInput,
    TrashedMessage,
    Purchases,
  },
  props: {
    errors: Object,
    supplier: Object,
    purchaseDetails: Object,
  },
  remember: 'form',
  data() {
    return {
      sending: false,
      momentFormat: {
        //[optional] Date to String
        stringify: (date) => {
          return date ? moment(date).format('ll') : ''
        },
        //[optional]  String to Date
        parse: (value) => {
          return value ? moment(value, 'll').toDate() : null
        },
        // [optional] getWeekNumber
        getWeek: (date) => {
          return // a number
        }
      },
      // Tabs
      openTab: 2,
      activeClasses: 'border-l border-t border-r rounded-t text-blue-600',
      inactiveClasses: 'text-blue-500 hover:text-blue-800',
    }
  },
  methods: {
    restore() {
      if (confirm('Are you sure you want to restore this Supplier?')) {
        this.$inertia.put(this.route('suppliers.restore', this.supplier.id))
      }
    },
  },
}
</script>
