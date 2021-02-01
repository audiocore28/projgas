<template>
  <div>
    <h1 class="mb-8 font-bold text-3xl">
      <inertia-link class="text-blue-600 hover:text-blue-800" :href="route('tankers.index')">Tankers</inertia-link>
      <span class="text-blue-600 font-medium">/</span> Create
    </h1>
    <div class="bg-white rounded shadow overflow-hidden max-w-3xl">
      <form @submit.prevent="submit">
        <div class="p-8 -mr-6 -mb-8 flex flex-wrap">
          <text-input v-model="form.plate_no" :error="errors.plate_no" class="pr-6 pb-8 w-full lg:w-1/2" label="Plate No" />
          <text-input v-model="form.compartment" :error="errors.compartment" class="pr-6 pb-8 w-full lg:w-1/2" label="Compartment" />
<!--           <select-input v-model="form.driver_id" :error="errors.driver_id" class="pr-6 pb-8 w-full lg:w-1/2" label="Driver">
            <option :value="null" />
            <option v-for="driver in drivers" :key="driver.id" :value="driver.id">{{ driver.fname }}</option>
          </select-input>
 -->
          <!-- <div>
            <label class="form-label block">Driver(s)</label>
            <multiselect
              class="mt-3"
              v-model="form.driver_id"
              :options="drivers"
              :multiple="true"
              :searchable="true">
            </multiselect>
          </div> -->

          <!-- <div class="ml-5">
            <label class="form-label block">Helper(s)</label>
            <multiselect
              class="mt-3"
              v-model="form.helper_id"
              :options="helpers"
              :multiple="true"
              :searchable="true">
            </multiselect>
          </div> -->

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
// import Multiselect from 'vue-multiselect'

export default {
  metaInfo: { title: 'Create Tanker' },
  layout: Layout,
  components: {
    LoadingButton,
    SelectInput,
    TextInput,
    // Multiselect,
  },
  props: {
    errors: Object,
    drivers: Array,
    helpers: Array,
  },
  remember: 'form',
  data() {
    return {
      sending: false,
      form: {
        plate_no: null,
  		  compartment: null,
        driver_id: null,
      },
    }
  },
  methods: {
    submit() {
      this.$inertia.post(this.route('tankers.store'), this.form, {
        onStart: () => this.sending = true,
        onFinish: () => this.sending = false,
      })
    },
  },
}
</script>

<!-- <style src="vue-multiselect/dist/vue-multiselect.min.css"></style> -->