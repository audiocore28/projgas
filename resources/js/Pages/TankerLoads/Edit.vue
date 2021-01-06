<template>
  <div>
    <h1 class="mb-8 font-bold text-3xl">
      <inertia-link class="text-blue-600 hover:text-blue-800" :href="route('tanker-loads.index')">Loads</inertia-link>
      <span class="text-blue-600 font-medium">/</span> {{ updateForm.purchase_id }}
    </h1>
    <!-- Update Existing Load -->
    <div class="bg-white rounded shadow overflow-hidden max-w-6xl mb-8">
      <form @submit.prevent="updateLoad">
        <!-- Load -->
        <div class="p-8 -mr-6 -mb-8 flex flex-wrap">
          <text-input v-model="updateForm.date" :error="errors.date" class="pr-6 pb-8 w-full lg:w-1/2" label="Date" />
          <!-- Select inputs -->
          <select-input v-model="updateForm.purchase_id" :error="errors.purchase_id" class="pr-6 pb-8 w-full lg:w-1/2" label="Purchase No.">
            <option :value="null" />
            <option v-for="purchase in purchases" :key="purchase.id" :value="purchase.id">{{ purchase.purchase_no }}</option>
          </select-input>
          <select-input v-model="updateForm.tanker_id" :error="errors.tanker_id" class="pr-6 pb-8 w-full lg:w-1/2" label="Tanker">
            <option :value="null" />
            <option v-for="tanker in tankers" :key="tanker.id" :value="tanker.id">{{ tanker.plate_no }}</option>
          </select-input>
          <select-input v-model="updateForm.driver_id" :error="errors.driver_id" class="pr-6 pb-8 w-full lg:w-1/2" label="Driver">
            <option :value="null" />
            <option v-for="driver in drivers" :key="driver.id" :value="driver.id">{{ driver.name }}</option>
          </select-input>
          <select-input v-model="updateForm.helper_id" :error="errors.helper_id" class="pr-6 pb-8 w-full lg:w-1/2" label="Helper">
            <option :value="null" />
            <option v-for="helper in helpers" :key="helper.id" :value="helper.id">{{ helper.name }}</option>
          </select-input>
          <select-input v-model="updateForm.status" :error="errors.status" class="pr-6 pb-8 w-full lg:w-1/2" label="Status">
            <option :value="null" />
            <option value="loaded">Loaded</option>
            <option value="delivered">Delivered</option>
          </select-input>
          <!-- Text inputs -->
          <text-input v-model="updateForm.remarks" :error="errors.remarks" class="pr-6 pb-8 w-full lg:w-1/2" label="Remarks" />
        </div>

        <!-- Details -->
        <div class="p-8 -mr-6 -mb-8 flex flex-wrap"
          v-for="(details, index) in updateForm.details">
          <select-input v-model="details.product_id" :error="errors.product_id" class="pr-6 pb-8 w-full lg:w-1/4" label="Product">
            <option :value="null" />
            <option v-for="product in products" :key="product.id" :value="product.id">{{ product.name }}</option>
          </select-input>
          <text-input v-model="details.quantity" :error="errors.quantity" class="pr-6 pb-8 w-full lg:w-1/4" label="Quantity" />
          <select-input v-model="details.status" :error="errors.status" class="pr-6 pb-8 w-full lg:w-1/2" label="Status">
            <option :value="null" />
            <option value="loaded">Loaded</option>
            <option value="delivered">Delivered</option>
          </select-input>

          <!-- Fix Me -->
          <span style="background-color: red; color: white; cursor: pointer; float: right;" @click.prevent="deleteDetailForm(index, details.id)">X</span>
        </div>
        <div class="px-8 py-4 bg-gray-100 border-t border-gray-200 flex justify-end items-center">
          <button v-if="!tanker_load.deleted_at" class="text-red-600 hover:underline" tabindex="-1" type="button" @click="destroy">Delete Load</button>
          <loading-button :loading="sending" class="btn-indigo ml-auto" type="submit">Update Load</loading-button>
        </div>
      </form>
    </div>


    <!-- Create Form -->
    <div class="bg-white rounded shadow overflow-hidden max-w-6xl">
      <form @submit.prevent="saveNewDetails">
        <!-- Details -->
        <div class="p-8 -mr-6 -mb-8 flex flex-wrap"
          v-for="(details, index) in createForm">
          <select-input v-model="details.product_id" :error="errors.product_id" class="pr-6 pb-8 w-full lg:w-1/4" label="Product">
            <option :value="null" />
            <option v-for="product in products" :key="product.id" :value="product.id">{{ product.name }}</option>
          </select-input>
          <text-input v-model="details.quantity" :error="errors.quantity" class="pr-6 pb-8 w-full lg:w-1/4" label="Quantity" />
          <select-input v-model="details.status" :error="errors.status" class="pr-6 pb-8 w-full lg:w-1/2" label="Status">
            <option :value="null" />
            <option value="loaded">Loaded</option>
            <option value="delivered">Delivered</option>
          </select-input>

          <span style="background-color: red; color: white; cursor: pointer; float: right;" @click.prevent="deleteNewDetailForm(index)">X</span>
        </div>

        <div class="px-8 py-4 flex justify-end items-center">
          <button class="btn-indigo" @click.prevent="createNewDetailForm">Add Row</button>
        </div>

        <div class="px-8 py-4 bg-gray-100 border-t border-gray-200 flex justify-end items-center">
          <loading-button :loading="sending" class="btn-indigo ml-auto" type="submit">Save Details</loading-button>
        </div>
      </form>
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
  // metaInfo() {
  //   return { title: this.form.name }
  // },
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
    tanker_load: Object,
    purchases: Array,
    tankers: Array,
    drivers: Array,
    helpers: Array,
    products: Array,
  },
  remember: 'form',
  data() {
    return {
      sending: false,
      updateForm: {
        date: this.tanker_load.date,
        purchase_id: this.tanker_load.purchase_id,
        tanker_id: this.tanker_load.tanker_id,
        driver_id: this.tanker_load.driver_id,
        helper_id: this.tanker_load.helper_id,
        status: this.tanker_load.status,
        remarks: this.tanker_load.remarks,
        details: this.tanker_load.details,
      },
      createForm: [
        {
          id: null,
          tanker_load_id: this.tanker_load.id,
          product_id: null,
          quantity: null,
          status: null,
        },
      ],
    }
  },
  methods: {
    // update existing details
    updateLoad() {
      this.$inertia.put(this.route('tanker-loads.update', this.tanker_load.id), this.updateForm, {
        onStart: () => this.sending = true,
        onFinish: () => this.sending = false,
      });
    },
    destroy() {
      if (confirm('Are you sure you want to delete this load?')) {
        this.$inertia.delete(this.route('tanker-loads.destroy', this.tanker_load.id))
      }
    },
    restore() {
      if (confirm('Are you sure you want to restore this load?')) {
        this.$inertia.put(this.route('tanker-loads.restore', this.tanker_load.id))
      }
    },
    deleteDetailForm(index, detailId) {
        if (confirm('Are you sure you want to delete this row?')) {
          this.$inertia.delete(this.route('tanker-load-details.destroy', detailId));
          this.tanker_load.details.splice(index, 1);
      }
    },

    // ----New Created Form

    // save
    saveNewDetails() {
      this.$inertia.post(this.route('tanker-load-details.store'), this.createForm, {
        onStart: () => this.sending = true,
        onFinish: () => this.sending = false,
      });
    },
    // create form
    createNewDetailForm() {
      this.createForm.push({
        id: null,
        tanker_load_id: this.tanker_load.id,
        product_id: null,
        quantity: null,
        status: null,
      });
    },
    // remove form
    deleteNewDetailForm(index) {
      this.createForm.splice(index, 1);
    },

  },
}
</script>
