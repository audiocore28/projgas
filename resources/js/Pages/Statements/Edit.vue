<template>
  <div>
    <h1 class="mb-8 font-bold text-3xl">
      <inertia-link class="text-blue-600 hover:text-blue-800" :href="route('statements.index')">Statement of Accounts</inertia-link>
      <span class="text-blue-600 font-medium">/</span> {{ form.soa_no }}
    </h1>

    <trashed-message v-if="statement.deleted_at" class="mb-6" @restore="restore">
      This SOA has been deleted.
    </trashed-message>

    <div class="p-1">
      <div class="w-full pt-4">
        <!-- Tab 1 -->
        <div class="bg-white rounded shadow overflow-hidden max-w-3xl -mt-4">
          <form @submit.prevent="submit">
            <div class="p-8 -mr-6 -mb-8 flex flex-wrap">
              <label class="form-label block mr-5">Date</label>
              <div class="pr-6 pb-8 w-full">
                <date-picker v-model="form.date" lang="en" value-type="format" :formatter="momentFormat"></date-picker>
              </div>

              <text-input v-model="form.soa_no" :error="errors.soa_no" class="pr-6 pb-8 w-full lg:w-1/2" label="SOA No." />
              <select-input v-model="form.client_id" :error="errors.client_id" class="pr-6 pb-8 w-full lg:w-1/2" label="Client">
                <option :value="null" />
                <option v-for="client in clients" :key="client.id" :value="client.id">{{ client.name }}</option>
              </select-input>

              <text-input type="number" step="any" v-model="form.payment" :error="errors.payment" class="pr-6 pb-8 w-full lg:w-1/2" label="Payment" />
              <text-input v-model="form.check_no" :error="errors.check_no" class="pr-6 pb-8 w-full lg:w-1/2" label="Check No." />
            </div>
            <div class="px-8 py-4 bg-gray-100 border-t border-gray-200 flex justify-end items-center">
              <button v-if="!statement.deleted_at" class="text-red-600 hover:underline" tabindex="-1" type="button" @click="destroy">Delete SOA</button>
              <loading-button :loading="sending" class="btn-indigo ml-auto" type="submit">Update SOA</loading-button>
            </div>
          </form>
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
import Multiselect from 'vue-multiselect'
import DatePicker from 'vue2-datepicker'
import moment from 'moment'

export default {
  metaInfo() {
    return { title: this.form.soa_no }
  },
  layout: Layout,
  components: {
    Icon,
    LoadingButton,
    SelectInput,
    TextInput,
    TrashedMessage,
    Multiselect,
    DatePicker,
  },
  props: {
    errors: Object,
    statement: Object,
    clients: Array,
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
      form: {
        date: this.statement.date,
        client_id: this.statement.client_id,
        payment: this.statement.payment,
        check_no: this.statement.check_no,
        soa_no: this.statement.soa_no,
      },
    }
  },
  methods: {
    submit() {
      this.$inertia.put(this.route('statements.update', this.statement.id), this.form, {
        onStart: () => this.sending = true,
        onFinish: () => this.sending = false,
      })
    },
    destroy() {
      if (confirm('Are you sure you want to delete this statement?')) {
        this.$inertia.delete(this.route('statements.destroy', this.statement.id))
      }
    },
    restore() {
      if (confirm('Are you sure you want to restore this statement?')) {
        this.$inertia.put(this.route('statements.restore', this.statement.id))
      }
    },
  },
}
</script>

<style src="vue-multiselect/dist/vue-multiselect.min.css"></style>
<style src="vue2-datepicker/index.css"></style>
