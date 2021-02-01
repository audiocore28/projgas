<template>
  <div>
    <div class="bg-white rounded shadow overflow-x-auto mb-8 -mt-4">
      <div class="rounded shadow overflow-x-auto mb-8" v-for="haul in localHaulDetails">
        <div class="ml-5 mt-5 mb-1">
          <inertia-link :href="route('hauls.edit', haul.id)" tabindex="-1">
            <p class="text-sm font-bold text-blue-700 mb-2">{{ haul.trip_no }}. {{ haul.driver.name }} & {{ haul.helper.name }} ({{ haul.tanker.plate_no }})</p>
            <p class="text-sm font-medium text-gray-600 mb-4">{{ haul.purchase.purchase_no }}</p>
          </inertia-link>
        </div>
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Date
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
            <tr v-for="detail in haul.hauls" :key="detail.id" :value="detail.id">
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="text-sm font-medium text-gray-900">
                  {{ detail.date }}
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
                <div class="text-sm text-gray-900">{{ totalHaulsQty(haul.id) }}</div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="text-sm text-gray-900"></div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="text-sm text-gray-900">{{ totalHaulsAmount(haul.id) }}</div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
    <!-- <button @click="loadMore" :disabled="loadingMore">Load more...</button> -->
    <button @click="loadMoreHaul">Load more...</button>
  </div>
</template>

<script>
import axios from 'axios'
import { numberFormatsMixin } from '@/Mixins/numberFormatsMixin'

export default {
  mixins: [numberFormatsMixin],
  props: {
    haulDetails: Object,
    record: String,
  },
  data() {
    return {
      localHaulDetails: this.haulDetails.data,
      haulPagination: this.haulDetails,
    }
  },
  methods: {
    totalHaulsAmount(haulId) {
      for (var i = 0; i < this.localHaulDetails.length; i++) {
        if (this.localHaulDetails[i].id === haulId) {

          var totalAmt = this.localHaulDetails[i].hauls.reduce(function (acc, haul) {
            acc += parseFloat(haul.quantity) * parseFloat(haul.unit_price);
            return acc;
          }, 0);

          return this.toPHP(totalAmt);
        }
      }
    },

    totalHaulsQty(haulId) {
      for (var i = 0; i < this.localHaulDetails.length; i++) {
        if (this.localHaulDetails[i].id === haulId) {

          var totalQty = this.localHaulDetails[i].hauls.reduce(function (acc, haul) {
            acc += parseFloat(haul.quantity);
            return acc;
          }, 0);

          return this.quantityFormat(totalQty);
        }
      }
    },

    loadMoreHaul() {
        var currentPage = this.haulPagination.links[0].current_page;
        var id = window.location.href.split('/');

        // if (this.loadingMore) return;

        // this.loadingMore = true;

        axios.get(`/${this.record}/${id[4]}/edit?page=${currentPage + 1}`)
          .then(({ data }) => {
            // Prepending the old messages to the list.
            this.localHaulDetails = [
            ...this.localHaulDetails,
            ...data.haulDetails.data,
            ];

            // Update our pagination meta information.
            this.haulPagination = data.haulDetails;
          });
          // .finally(() => this.loadingMore = false);
    },
  },
}
</script>