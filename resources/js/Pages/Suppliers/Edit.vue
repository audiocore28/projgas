<template>
  <div>
    <h1 class="mb-8 font-bold text-3xl">
      <inertia-link class="text-blue-600 hover:text-blue-800" :href="route('suppliers.index')">Suppliers</inertia-link>
      <span class="text-blue-600 font-medium">/</span> {{ form.name }}
    </h1>

    <trashed-message v-if="supplier.deleted_at" class="mb-6" @restore="restore">
      This supplier has been deleted.
    </trashed-message>

    <div class="p-1">
      <ul class="flex border-b">
        <li @click="openTab = 1" :class="{ '-mb-px': openTab === 1 }" class="-mb-px mr-1">
          <a :class="openTab === 1 ? activeClasses : inactiveClasses" class="bg-white inline-block py-2 px-4 font-semibold" href="#">Info</a>
        </li>
        <li @click="openTab = 2" :class="{ '-mb-px': openTab === 2 }" class="mr-1" v-show="purchaseDetails.length">
          <a :class="openTab === 2 ? activeClasses : inactiveClasses" class="bg-white inline-block py-2 px-4 font-semibold" href="#">Purchases</a>
        </li>
      </ul>

      <!-- Tab 1 -->
      <div class="w-full pt-4">
        <div v-show="openTab === 1">
          <div class="bg-white rounded shadow overflow-hidden max-w-3xl -mt-4">
            <form @submit.prevent="submit">
              <div class="p-8 -mr-6 -mb-8 flex flex-wrap">
                <text-input v-model="form.name" :error="errors.name" class="pr-6 pb-8 w-full lg:w-1/2" label="Company Name" />
                <text-input v-model="form.office" :error="errors.office" class="pr-6 pb-8 w-full lg:w-1/2" label="Office Address" />
                <text-input v-model="form.contact_person" :error="errors.contact_person" class="pr-6 pb-8 w-full lg:w-1/2" label="Contact Person" />
                <text-input v-model="form.contact_no" :error="errors.contact_no" class="pr-6 pb-8 w-full lg:w-1/2" label="Contact No." />
                <text-input v-model="form.email_address" :error="errors.email_address" class="pr-6 pb-8 w-full lg:w-1/2" label="Email Address" />
              </div>
              <div class="px-8 py-4 bg-gray-100 border-t border-gray-200 flex justify-end items-center">
                <button v-if="!purchaseDetails.length && !supplier.deleted_at" class="text-red-600 hover:underline" tabindex="-1" type="button" @click="destroy">Delete Supplier</button>
                <loading-button :loading="sending" class="btn-indigo ml-auto" type="submit">Update Supplier</loading-button>
              </div>
            </form>
          </div>
        </div>

        <!-- Tab 2 -->
        <div v-show="openTab === 2">
          <div class="bg-white rounded shadow overflow-x-auto mb-8 -mt-4">
            <div class="rounded shadow overflow-x-auto mb-8" v-for="purchase in purchaseDetails">
              <div class="ml-5 mr-5 mt-6 flex justify-between">
                <inertia-link :href="route('purchases.edit', purchase.id)" tabindex="-1">
                  <h2 class="text-md font-bold text-blue-700 mb-2">{{ purchase.purchase_no }}</h2>
                </inertia-link>
                <p class="text-sm font-medium text-gray-900 mb-4">{{ purchase.date }}</p>
              </div>
              <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                  <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Product
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Quantity
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Unit Price
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Amount
                    </th>
                  </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                  <tr v-for="detail in purchase.purchases" :key="detail.id" :value="detail.id">
                    <td class="px-6 py-4 whitespace-nowrap">
                      <div class="text-sm font-medium text-gray-900">
                        {{ detail.product.name }}
                      </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      <div class="text-sm font-medium text-gray-900">
                        {{ quantityFormat(detail.quantity) }}
                      </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      <div class="text-sm font-medium text-gray-900">
                        {{ toPHP(detail.unit_price) }}
                      </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                      <div class="text-sm font-medium text-gray-900">
                        {{ totalCurrency(detail.quantity, detail.unit_price) }}
                      </div>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
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
    return { title: this.form.name }
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
    supplier: Object,
    purchaseDetails: Array,
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
        name: this.supplier.name,
        office: this.supplier.office,
        contact_person: this.supplier.contact_person,
        contact_no: this.supplier.contact_no,
        email_address: this.supplier.email_address,
      },
      // Tabs
      openTab: 1,
      activeClasses: 'border-l border-t border-r rounded-t text-blue-600',
      inactiveClasses: 'text-blue-500 hover:text-blue-800',
    }
  },
  methods: {
    submit() {
      this.$inertia.put(this.route('suppliers.update', this.supplier.id), this.form, {
        onStart: () => this.sending = true,
        onFinish: () => this.sending = false,
      })
    },
    destroy() {
      if (confirm('Are you sure you want to delete this Supplier?')) {
        this.$inertia.delete(this.route('suppliers.destroy', this.supplier.id))
      }
    },
    restore() {
      if (confirm('Are you sure you want to restore this Supplier?')) {
        this.$inertia.put(this.route('suppliers.restore', this.supplier.id))
      }
    },

    // Helpers
    quantityFormat(value) {
      return Number(value).toLocaleString()
    },

    toPHP(value) {
      return `₱${Number(value).toLocaleString()}`;
    },

    totalCurrency(quantity, unitPrice) {
      return this.toPHP(quantity * unitPrice);
    },
  },
}
</script>
