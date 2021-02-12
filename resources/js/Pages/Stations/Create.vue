<template>
  <div>
    <h1 class="mb-8 font-bold text-3xl">
      <inertia-link class="text-blue-600 hover:text-blue-800" :href="route('stations.index')">Gasoline Stations</inertia-link>
      <span class="text-blue-600 font-medium">/</span> Create
    </h1>
    <div class="bg-white rounded shadow overflow-hidden max-w-3xl">
      <form @submit.prevent="submit">
        <div class="p-8 -mr-6 -mb-8 flex flex-wrap">
          <text-input v-model="form.name" :error="errors.name" class="pr-6 pb-8 w-full lg:w-1/2" label="Name" />
          <text-input v-model="form.address" :error="errors.address" class="pr-6 pb-8 w-full lg:w-1/2" label="Address" />
          <text-input v-model="form.contact_no" :error="errors.contact_no" class="pr-6 pb-8 w-full lg:w-1/2" label="Contact No." />
        </div>

        <!-- Pumps -->
        <h4 class="font-bold text-1xl px-8 py-6 block">Pumps</h4>
        <div class="px-8 py-4 -mr-6 -mb-8 flex flex-wrap" v-for="(pump, index) in form.pumps" :key="index">
          <text-input v-model="pump.pump" :error="errors.pump" class="pr-6 pb-8 w-full lg:w-1/4" label="Pump" />
          <text-input v-model="pump.nozzle" :error="errors.nozzle" class="pr-6 pb-8 w-full lg:w-1/4" label="Nozzle" />

          <div class="pr-6 pb-8 w-full lg:w-1/4">
            <label class="form-label" :for="`product-${index}`">Product:</label>
            <select :id="`product-${index}`" v-model="pump.product_id" class="form-select" :class="{ error: errors[`pump.${index}.product_id`] }">
              <option :value="null" />
              <option v-for="product in products" :key="product.id" :value="product.id">{{ product.name }}</option>
            </select>
            <div v-if="errors[`pump.${index}.product_id`]" class="form-error">{{ errors[`pump.${index}.product_id`] }}</div>
          </div>

          <button @click.prevent="deletePumpForm(index)" type="button" class="bg-white py-1 px-1 flex-shrink-0 text-sm leading-none">
            <icon name="trash" class="w-4 h-4 mr-2 fill-red-600"/>
          </button>
        </div>

        <div class="px-8 py-4 flex justify-end items-center">
          <button class="btn-indigo" @click.prevent="addPumpForm">Add Row</button>
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
import TextInput from '@/Shared/TextInput'
import Icon from '@/Shared/Icon'

export default {
  metaInfo: { title: 'Create G. Station' },
  layout: Layout,
  components: {
    LoadingButton,
    TextInput,
    Icon,
  },
  props: {
    errors: Object,
    products: Array,
  },
  remember: 'form',
  data() {
    return {
      sending: false,
      form: {
        name: null,
        address: null,
        contact_no: null,
        pumps: [
          {
            pump: null,
            product_id: null,
            nozzle: null,
            station_id: null,
          },
        ],
      },
    }
  },
  methods: {
    submit() {
      this.$inertia.post(this.route('stations.store'), this.form, {
        onStart: () => this.sending = true,
        onFinish: () => this.sending = false,
      })
    },

    addPumpForm() {
      this.form.pumps.push({
        pump: null,
        product_id: null,
        nozzle: null,
        station_id: null,
      });
    },
    deletePumpForm(index) {
      this.form.pumps.splice(index, 1);
    }
  },
}
</script>