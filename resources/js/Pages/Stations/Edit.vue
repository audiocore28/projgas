<template>
  <div>
    <h1 class="mb-8 font-bold text-3xl">
      <inertia-link class="text-blue-600 hover:text-blue-800" :href="route('stations.index')">Gasoline Stations</inertia-link>
      <span class="text-blue-600 font-medium">/</span> {{ updateForm.name }}
    </h1>

    <div class="p-1">
      <ul class="flex border-b">
        <li @click="openTab = 1" :class="{ '-mb-px': openTab === 1 }" class="-mb-px mr-1">
          <a :class="openTab === 1 ? activeClasses : inactiveClasses" class="bg-white inline-block py-2 px-4 font-semibold">Info</a>
        </li>
      </ul>

      <div class="w-full pt-4">
        <!-- Tab 1 -->
        <div v-show="openTab === 1">
          <!-- Update Existing Station -->
          <div class="bg-white rounded shadow overflow-hidden max-w-3xl mb-8 -mt-4">
            <form @submit.prevent="updateStation">
              <div class="p-8 -mr-6 -mb-8 flex flex-wrap">
                <text-input v-model="updateForm.name" :error="errors.name" class="pr-6 pb-8 w-full lg:w-1/2" label="Station Name" />
                <text-input v-model="updateForm.address" :error="errors.address" class="pr-6 pb-8 w-full lg:w-1/2" label="Address" />
                <text-input v-model="updateForm.contact_no" :error="errors.contact_no" class="pr-6 pb-8 w-full lg:w-1/2" label="Contact No." />
              </div>

              <!-- Pumps -->
              <h4 class="font-bold text-1xl px-8 py-6 block">Pumps</h4>
              <div class="px-8 py-4 -mr-6 -mb-8 flex flex-wrap" v-for="(pump, index) in updateForm.pumps" :key="index">
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

                <button @click.prevent="deletePumpForm(index, pump.id)" type="button" class="bg-white py-1 px-1 flex-shrink-0 text-sm leading-none">
                  <icon name="trash" class="w-4 h-4 mr-2 fill-red-600"/>
                </button>
              </div>

              <div class="px-8 py-4 bg-gray-100 border-t border-gray-200 flex justify-end items-center">
                <button v-if="!station.deleted_at" class="text-red-600 hover:underline" tabindex="-1" type="button" @click="destroy">Delete Station</button>
                <loading-button :loading="sending" class="btn-indigo ml-auto" type="submit">Update Station</loading-button>
              </div>
            </form>
          </div>

          <!-- Create Form -->
          <div class="bg-white rounded shadow overflow-hidden max-w-3xl pt-4">
            <form @submit.prevent="saveNewPump">
              <!-- Pumps -->
              <h4 class="font-bold text-1xl px-8 py-6 block">Add New Pumps</h4>
              <div class="px-8 py-4 -mr-6 -mb-8 flex flex-wrap" v-for="(pump, index) in createForm" :key="index">
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
    return { title: this.updateForm.name }
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
    station: Object,
    products: Array,
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
      updateForm: {
        name: this.station.name,
        address: this.station.address,
        contact_no: this.station.contact_no,
        pumps: this.station.pumps,
      },
      createForm: [
        {
          station_id: this.station.id,
          pump: null,
          nozzle: null,
          product_id: null,
        },
      ],
      // Tabs
      openTab: 1,
      activeClasses: 'border-l border-t border-r rounded-t text-blue-600',
      inactiveClasses: 'text-blue-500 hover:text-blue-800',
    }
  },
  methods: {
    updateStation() {
      this.$inertia.put(this.route('stations.update', this.station.id), this.updateForm, {
        onStart: () => this.sending = true,
        onFinish: () => this.sending = false,
      })
    },
    destroy() {
      if (confirm('Are you sure you want to permanently delete this station?')) {
        this.$inertia.delete(this.route('stations.destroy', this.station.id))
      }
    },
    restore() {
      if (confirm('Are you sure you want to restore this station?')) {
        this.$inertia.put(this.route('stations.restore', this.station.id))
      }
    },
    deletePumpForm(index, pumpId) {
        if (confirm('Are you sure you want to delete this row?')) {
          this.$inertia.delete(this.route('pumps.destroy', pumpId));
          this.station.pumps.splice(index, 1);
      }
    },

    // ----New Created Form

    // Save
    saveNewPump() {
      this.$inertia.post(this.route('pumps.store'), this.createForm, {
        onStart: () => this.sending = true,
        onFinish: () => this.sending = false,
      });
    },
    // create form
    addNewPumpForm() {
      this.createForm.push({
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
