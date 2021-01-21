<template>
  <div>
    <h1 class="mb-8 font-bold text-3xl">
      <inertia-link class="text-blue-600 hover:text-blue-800" :href="route('statements.index')">Statement of Accounts</inertia-link>
      <span class="text-blue-600 font-medium">/</span> Create
    </h1>
    <div class="bg-white rounded shadow overflow-hidden max-w-3xl">
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

          <div>
            <label class="form-label block">Driver(s)</label>
            <multiselect
              class="mt-3"
              v-model="form.driver_id"
              :options="drivers"
              :multiple="true"
              :searchable="true">
            </multiselect>
          </div>

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
import Multiselect from 'vue-multiselect'
import DatePicker from 'vue2-datepicker'
import moment from 'moment'

export default {
  metaInfo: { title: 'Create SOA' },
  layout: Layout,
  components: {
    LoadingButton,
    SelectInput,
    TextInput,
    Multiselect,
    DatePicker,
  },
  props: {
    errors: Object,
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
  		  date: null,
        client_id: null,
        payment: null,
        check_no: null,
        soa_no: null,
      },
    }
  },
  methods: {
    submit() {
      this.$inertia.post(this.route('statements.store'), this.form, {
        onStart: () => this.sending = true,
        onFinish: () => this.sending = false,
      })
    },
  },
}
</script>

<style src="vue-multiselect/dist/vue-multiselect.min.css"></style>
<style src="vue2-datepicker/index.css"></style>
