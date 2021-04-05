<template>
  <div>
    <div class="bg-white rounded shadow overflow-x-auto mb-8 -mt-4">
      <div class="rounded shadow overflow-x-auto m-4" v-for="transaction in localTransactionDetails">
        <div class="ml-5 mt-5 mb-1">
          <inertia-link :href="route(`mindoro-transactions.edit`, transaction.id)" tabindex="-1">
            <p class="text-sm font-bold text-blue-700 mb-2">{{ transaction.trip_no }}. {{ transaction.driver.name }} & {{ transaction.helper.name }} ({{ transaction.tanker.plate_no }})</p>

            <div class="text-xs font-medium text-gray-600">
              <span class="mb-4">{{ transaction.date }}</span> -
              <span class="-ml-2 px-2 rounded-full" v-if="transaction.purchases" v-for="purchase in transaction.purchases">
                {{ purchase.purchase_no }}
              </span>
            </div>
          </inertia-link>
        </div>
        <table class="min-w-full divide-y divide-gray-200 mt-4">
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
            <tr v-for="detail in transaction.details" :key="detail.id" :value="detail.id">
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
                <div class="text-sm text-gray-900">{{ totalTransactionsQty(transaction.id) }}</div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="text-sm text-gray-900"></div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="text-sm text-gray-900">{{ totalTransactionsAmount(transaction.id) }}</div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
    <!-- <button @click="loadMore" :disabled="loadingMore">Load more...</button> -->
    <button @click="loadMoreTransactions">Load more...</button>
  </div>
</template>

<script>
import axios from 'axios'
import { numberFormatsMixin } from '@/Mixins/numberFormatsMixin'

export default {
  mixins: [numberFormatsMixin],
  props: {
    transactionDetails: Object,
    record: String,
  },
  data() {
    return {
      localTransactionDetails: this.transactionDetails.data,
      transactionPagination: this.transactionDetails,
    }
  },
  methods: {
    totalTransactionsAmount(transactionId) {
      for (var i = 0; i < this.localTransactionDetails.length; i++) {
        if (this.localTransactionDetails[i].id === transactionId) {

          var totalAmt = this.localTransactionDetails[i].details.reduce(function (acc, detail) {
            acc += parseFloat(detail.quantity) * parseFloat(detail.unit_price);
            return acc;
          }, 0);

          return this.toPHP(totalAmt);
        }
      }
    },

    totalTransactionsQty(transactionId) {
      for (var i = 0; i < this.localTransactionDetails.length; i++) {
        if (this.localTransactionDetails[i].id === transactionId) {

          var totalQty = this.localTransactionDetails[i].details.reduce(function (acc, detail) {
            acc += parseFloat(detail.quantity);
            return acc;
          }, 0);

          return this.quantityFormat(totalQty);
        }
      }
    },

    loadMoreTransactions() {
        var currentPage = this.transactionPagination.links[0].current_page;
        var id = window.location.href.split('/');

        // if (this.loadingMore) return;

        // this.loadingMore = true;

        axios.get(`/${this.record}/${id[4]}/edit?page=${currentPage + 1}`)
          .then(({ data }) => {
            // Prepending the old messages to the list.
            this.localTransactionDetails = [
            ...this.localTransactionDetails,
            ...data.mindoroDetails.data,
            ];

            // Update our pagination meta information.
            this.transactionPagination = data.mindoroDetails;
          });
          // .finally(() => this.loadingMore = false);
    },
  },
}
</script>