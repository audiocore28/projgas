<template>
  <div>
    <div class="bg-white rounded shadow overflow-hidden max-w-3xl pt-4 -mt-4">
      <form @submit.prevent="saveNewPump">
        <!-- Pumps -->
        <div class="px-8 py-4 -mr-6 -mb-8 flex flex-wrap" v-for="(pump, index) in createForm" :key="index">
          <text-input v-model="pump.pump" :error="errors.pump" class="pr-6 pb-8 w-full lg:w-1/4" label="Pump" />
          <text-input v-model="pump.nozzle" :error="errors.nozzle" class="pr-6 pb-8 w-full lg:w-1/4" label="Nozzle" />

          <div class="pr-6 pb-8 w-full lg:w-1/4">
            <label class="form-label" :for="`product-${index}`">Product:</label>
            <select :id="`product-${index}`" v-model="pump.product_id" class="form-select" :class="{ error: errors[`${index}.product_id`] }">
              <option :value="null" />
              <option v-for="product in products" :key="product.id" :value="product.id">{{ product.name }}</option>
            </select>
            <div v-if="errors[`${index}.product_id`]" class="form-error">{{ errors[`${index}.product_id`] }}</div>
          </div>

          <button @click.prevent="deleteNewPumpForm(index)" type="button" class="bg-white py-1 px-1 flex-shrink-0 text-sm leading-none">
            <icon name="trash" class="w-4 h-4 mr-2 fill-red-600"/>
          </button>
        </div>

        <div class="px-8 py-4 flex justify-end items-center">
          <button class="btn-indigo" @click.prevent="addNewPumpForm">Add Row</button>
        </div>

        <div class="px-8 py-4 bg-gray-100 border-t border-gray-200 flex justify-end items-center">
          <loading-button :loading="sending" class="btn-indigo ml-auto" type="submit">Save Pumps</loading-button>
        </div>
      </form>
    </div>
  </div>
</template>

<script>
import Icon from '@/Shared/Icon'
import LoadingButton from '@/Shared/LoadingButton'
import SelectInput from '@/Shared/SelectInput'
import TextInput from '@/Shared/TextInput'

export default {
  components: {
    Icon,
    LoadingButton,
    SelectInput,
    TextInput,
  },
  props: {
    errors: Object,
    station: Object,
    products: Array,
  },
  remember: 'form',
  data() {
    return {
      sending: false,
      createForm: [
        {
          station_id: this.station.id,
          pump: null,
          nozzle: null,
          product_id: null,
        },
      ],
    }
  },
  methods: {
    saveNewPump() {
      this.$inertia.post(this.route('pumps.store'), this.createForm, {
        onStart: () => this.sending = true,
        onFinish: () => this.sending = false,
      });
    },
    // create form
    addNewPumpForm() {
      this.createForm.push({
        id: null,
        station_id: this.station.id,
        pump: null,
        nozzle: null,
        product_id: null,
      });
    },
    // remove form
    deleteNewPumpForm(index) {
      this.createForm.splice(index, 1);
    },
  },
}
</script>
