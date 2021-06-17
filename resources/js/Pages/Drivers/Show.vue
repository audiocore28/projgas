<template>
  <div>
    <h1 class="mb-8 font-bold text-3xl">
      <inertia-link class="text-blue-600 hover:text-blue-800" :href="route('drivers.index')">Drivers</inertia-link>
      <span class="text-blue-600 font-medium">/</span> {{ driver.name }}
    </h1>

    <trashed-message v-if="driver.deleted_at" class="mb-6" @restore="restore" :canRestore="$page.auth.user.can.driver.restore">
      This driver has been deleted.
    </trashed-message>

    <div class="p-1">
      <ul class="flex border-b">
        <li @click="openTab = 2" :class="{ '-mb-px': openTab === 2 }" class="mr-1" v-show="batangasTrips">
          <a :class="openTab === 2 ? activeClasses : inactiveClasses" class="bg-white inline-block py-2 px-4 font-semibold">Batangas</a>
        </li>
        <li @click="openTab = 3" :class="{ '-mb-px': openTab === 3 }" class="mr-1" v-show="mindoroTrips">
          <a :class="openTab === 3 ? activeClasses : inactiveClasses" class="bg-white inline-block py-2 px-4 font-semibold">Mindoro</a>
        </li>
      </ul>

      <div class="w-full pt-4">
        <!-- Tab 2 -->
        <div v-show="openTab === 2">
          <batangas-transactions
            :transactionDetails="batangasTrips"
            record="drivers">
          </batangas-transactions>
        </div>

        <!-- Tab 3 -->
        <div v-show="openTab === 3">
          <mindoro-transactions
            :transactionDetails="mindoroTrips"
            record="drivers">
          </mindoro-transactions>
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
import BatangasTransactions from '@/Shared/BatangasTransactions'
import MindoroTransactions from '@/Shared/MindoroTransactions'
import moment from 'moment'
import { numberFormatsMixin } from '@/Mixins/numberFormatsMixin'

export default {
  mixins: [numberFormatsMixin],
  metaInfo() {
    return { title: this.driver.name }
  },
  layout: Layout,
  components: {
    Icon,
    LoadingButton,
    SelectInput,
    TextInput,
    TrashedMessage,
    BatangasTransactions,
    MindoroTransactions,
  },
  props: {
    errors: Object,
    driver: Object,
    batangasTrips: Object,
    mindoroTrips: Object,
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
      if (confirm('Are you sure you want to restore this driver?')) {
        this.$inertia.put(this.route('drivers.restore', this.driver.id))
      }
    },
  },
}
</script>