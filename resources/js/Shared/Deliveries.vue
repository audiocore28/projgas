<template>
  <div>
    <div class="bg-white rounded shadow overflow-x-auto mb-8 -mt-4">
      <div class="rounded shadow overflow-x-auto mb-8" v-for="delivery in localDeliveryDetails">
        <div class="ml-5 mt-5 mb-1">
          <inertia-link :href="route('deliveries.edit', delivery.id)" tabindex="-1">
            <p class="text-sm font-bold text-blue-700 mb-2">{{ delivery.trip_no }}. {{ delivery.driver.name }} & {{ delivery.helper.name }} ({{ delivery.tanker.plate_no }})</p>
            <p class="text-sm font-medium text-gray-600 mb-4">{{ delivery.purchase.purchase_no }}</p>
          </inertia-link>
        </div>
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Date
              </th>
              <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                DR No.
              </th>
              <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Client
              </th>
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
            <tr v-for="detail in delivery.deliveries" :key="detail.id" :value="detail.id">
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="text-sm font-medium text-gray-900">
                  {{ detail.date }}
                </div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="text-sm font-medium text-gray-900">
                  {{ detail.dr_no }}
                </div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="text-sm font-medium text-gray-900">
                  {{ detail.client.name  }}
                </div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="text-sm font-medium text-gray-900">
                  {{ detail.product.name }}
                </div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                <div class="text-sm font-medium text-gray-900">
                  {{ quantityFormat(detail.quantity) }}
                </div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
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
            <!-- Total -->
            <tr class="bg-gray-200">
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="text-left text-xs font-medium text-gray-500 uppercase">Total:</div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="text-sm text-gray-900"></div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="text-sm text-gray-900"></div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="text-sm text-gray-900"></div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="text-sm text-gray-900">{{ totalDeliveriesQty(delivery.id) }}</div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="text-sm text-gray-900"></div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="text-sm text-gray-900">{{ totalDeliveriesAmount(delivery.id) }}</div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
    <button @click="loadMoreDelivery">Load more...</button>
  </div>
</template>

<script>
import axios from 'axios'
import { numberFormatsMixin } from '@/Mixins/numberFormatsMixin'

export default {
  mixins: [numberFormatsMixin],
  props: {
    deliveryDetails: Object,
    record: String,
  },
  data() {
    return {
      localDeliveryDetails: this.deliveryDetails.data,
      deliveryPagination: this.deliveryDetails,
    }
  },
  methods: {
    totalDeliveriesAmount(deliveryId) {
      for (var i = 0; i < this.localDeliveryDetails.length; i++) {
        if (this.localDeliveryDetails[i].id === deliveryId) {

          var totalAmt = this.localDeliveryDetails[i].deliveries.reduce(function (acc, delivery) {
            acc += parseFloat(delivery.quantity) * parseFloat(delivery.unit_price);
            return acc;
          }, 0);

          return this.toPHP(totalAmt);
        }
      }
    },

    totalDeliveriesQty(deliveryId) {
      for (var i = 0; i < this.localDeliveryDetails.length; i++) {
        if (this.localDeliveryDetails[i].id === deliveryId) {

          var totalQty = this.localDeliveryDetails[i].deliveries.reduce(function (acc, delivery) {
            acc += parseFloat(delivery.quantity);
            return acc;
          }, 0);

          return this.quantityFormat(totalQty);
        }
      }
    },

    loadMoreDelivery() {
      var currentPage = this.deliveryPagination.links[0].current_page;
      var id = window.location.href.split('/');

      // if (this.loadingMore) return;

      // this.loadingMore = true;

      axios.get(`/${this.record}/${id[4]}/edit?page=${currentPage + 1}`)
        .then(({ data }) => {
            // Prepending the old messages to the list.
            this.localDeliveryDetails = [
            ...this.localDeliveryDetails,
            ...data.deliveryDetails.data,
            ];

            // Update our pagination meta information.
            this.deliveryPagination = data.deliveryDetails;
         });
          // .finally(() => this.loadingMore = false);
    },
  },
}
</script>