<template>
  <div>
    <div class="bg-white rounded shadow overflow-hidden max-w-6xl pt-4 -mt-4">
      <form @submit.prevent="saveNewDetails">
        <!-- Details -->
        <div class="px-8 py-4 -mr-6 -mb-8 flex flex-wrap" v-for="(details, index) in createForm" :key="index">
          <div class="pr-6 pb-8 w-full lg:w-1/4">
            <label class="form-label" :for="`product-${index}`">Product:</label>
            <select :id="`product-${index}`" v-model="details.product_id" class="form-select" :class="{ error: errors[`${index}.product_id`] }">
              <option :value="null" />
              <option v-for="product in products" :key="product.id" :value="product.id">{{ product.name }}</option>
            </select>
            <div v-if="errors[`${index}.product_id`]" class="form-error">{{ errors[`${index}.product_id`] }}</div>
          </div>

          <text-input type="number" step="any" v-model="details.quantity" :error="errors.quantity" class="pr-6 pb-8 w-full lg:w-1/6" label="Quantity" />
          <text-input type="number" step="any" v-model="details.unit_price" :error="errors.unit_price" class="pr-6 pb-8 w-full lg:w-1/6" label="Unit Price" />
          <text-input v-model="details.remarks" :error="errors.remarks" class="pr-6 pb-8 w-full lg:w-1/3" label="Remarks" />

          <button @click.prevent="deleteNewDetailForm(index)" type="button" class="bg-white py-1 px-1 flex-shrink-0 text-sm leading-none">
            <icon name="trash" class="w-4 h-4 mr-2 fill-red-600"/>
          </button>
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
    purchase: Object,
    products: Array,
  },
  remember: 'form',
  data() {
    return {
      sending: false,
      createForm: [
        {
          id: null,
          purchase_id: this.purchase.id,
          product_id: null,
          quantity: null,
          unit_price: null,
          remarks: null,
        },
      ],
    }
  },
  methods: {
    saveNewDetails() {
      this.$inertia.post(this.route('purchase-details.store'), this.createForm, {
        onStart: () => this.sending = true,
        onFinish: () => this.sending = false,
      });
    },
    // create form
    createNewDetailForm() {
      this.createForm.push({
        id: null,
        purchase_id: this.purchase.id,
        product_id: null,
        quantity: null,
        unit_price: null,
        remarks: null,
      });
    },
    // remove form
    deleteNewDetailForm(index) {
      this.createForm.splice(index, 1);
    },

  }

}
</script>
