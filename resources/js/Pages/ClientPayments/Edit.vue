<template>
  <div>
    <h1 class="mb-8 font-bold text-3xl">
      <inertia-link class="text-blue-600 hover:text-blue-800" :href="route('client-payments.index')">Client Payments</inertia-link>
      <span class="text-blue-600 font-medium">/</span> {{ form.client.name }}
    </h1>

    <trashed-message v-if="client_payment.deleted_at" class="mb-6" @restore="restore" :canRestore="$page.auth.user.can.clientPayment.restore">
      This payment has been deleted.
    </trashed-message>

    <div class="bg-white rounded shadow overflow-hidden max-w-3xl">
      <form @submit.prevent="submit">
        <div class="p-8 -mr-6 -mb-8 flex flex-wrap">
          <div class="w-full mb-2">
            <label class="form-label block mr-5">Date:<span class="text-red-500">*</span></label>
            <div class="pr-6 pb-4 my-3">
              <date-picker v-model="form.date" :error="errors.date" lang="en" value-type="format" :formatter="momentFormat"></date-picker>
              <div v-if="errors.date" class="form-error">{{ errors.date }}</div>
            </div>
          </div>

          <div class="pr-6 pb-8 w-full -mt-1 lg:w-1/3">
            <label class="form-label block">Client:</label>
             <multiselect
               id="client_id"
               v-model="form.selected_client"
               placeholder=""
               class="mt-3 text-xs z-50"
               :options="clients"
               label="name"
               track-by="id"
               @search-change="onSearchClientChange"
               @input="onSelectedClient"
               :show-labels="false"
               :allow-empty="false"
             ></multiselect>
           </div>

          <select-input v-model="form.mode" :error="errors.mode" class="pr-6 pb-4 w-full lg:w-1/3" label="Mode of Payment">
            <option :value="null"/>
            <option v-for="(mode, index) in modes" :key="index" :value="mode">{{ mode }}</option>
          </select-input>

          <text-input type="number" step="any" v-model="form.amount" :error="errors.amount" class="pr-6 pb-8 w-full lg:w-1/3" label="Amount"/>
          <text-input v-model="form.remarks" :error="errors.remarks" class="pr-6 pb-8 w-full" label="Remarks"/>
        </div>
        <div class="px-8 py-4 bg-gray-100 border-t border-gray-200 flex justify-end items-center">
          <button v-if="!client_payment.deleted_at && $page.auth.user.can.clientPayment.delete" class="text-red-600 hover:underline" tabindex="-1" type="button" @click="destroy">Delete Payment</button>
          <loading-button :loading="sending" class="btn-indigo ml-auto" type="submit">Save</loading-button>
        </div>
      </form>
    </div>
  </div>
</template>

<script>
import Layout from '@/Shared/Layout'
import LoadingButton from '@/Shared/LoadingButton'
import TextInput from '@/Shared/TextInput'
import TrashedMessage from '@/Shared/TrashedMessage'
import Multiselect from 'vue-multiselect'
import DatePicker from 'vue2-datepicker'
import SelectInput from '@/Shared/SelectInput'
import moment from 'moment'
import {throttle} from 'lodash'

export default {
  metaInfo() {
    return {
      title: `Edit Payment`,
    }
  },
  layout: Layout,
  components: {
    LoadingButton,
    TextInput,
    Multiselect,
    DatePicker,
    SelectInput,
    TrashedMessage,
  },
  props: {
    errors: Object,
    client_payment: Object,
    clients: {
      type: Array,
      default: () => [],
    },
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
      modes: ['Cash', 'Cheque'],
      form: {
        date: this.client_payment.date,
        client: {
          id: this.client_payment.client.id,
          name: this.client_payment.client.name,
        },
        selected_client: this.client_payment.selected_client,
        mode: this.client_payment.mode,
        amount: this.client_payment.amount,
        remarks: this.client_payment.remarks,
      },
    }
  },
  methods: {
    submit() {
      this.$inertia.put(this.route('client-payments.update', this.client_payment.id), this.form, {
        onStart: () => this.sending = true,
        onFinish: () => this.sending = false,
      })
    },
    destroy() {
      if (confirm('Are you sure you want to delete this Payment?')) {
        this.$inertia.delete(this.route('client-payments.destroy', this.client_payment.id))
      }
    },
    restore() {
      if (confirm('Are you sure you want to restore this Payment?')) {
        this.$inertia.put(this.route('client-payments.restore', this.client_payment.id))
      }
    },

    // Multiselect
    onSearchClientChange: throttle(function(term) {
      this.$inertia.get(this.route('client-payments.edit'), {term}, {
        preserveState: true,
        preserveScroll: true,
        replace: true,
      })
    }, 300),
    onSelectedClient(client) {
      this.form.client.id = client.id
    },
  },
  mounted() {
    this.form.selected_client = this.clients.find(client => client.id === this.form.client.id);
  }
}
</script>

<style src="vue2-datepicker/index.css"></style>

<style>
  .highlight-yellow label.form-label {
    @apply text-white;
  }

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

  .month-picker__container, .month-picker__container  {
    position: relative;
    z-index: 100;
    background-color: #fff;
  }
</style>
