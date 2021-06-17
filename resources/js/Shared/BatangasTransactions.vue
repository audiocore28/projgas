<template>
  <div>
    <div class="bg-white rounded shadow overflow-x-auto mb-8 -mt-4">
      <div class="rounded shadow overflow-x-auto mx-4 my-8" v-for="(months, year) in localTransactionDetails">
        <h1 class="font-semibold text-center">{{ year }}</h1>
        <div class="mt-6 mb-1" v-for="(transactions, month) in months">
          <div class="text-sm font-bold bg-gradient-to-r from-yellow-500 to-blue-600 text-white px-4 py-1 flex justify-between">
            <h2>{{ month }}</h2>
            <span class="text-xs px-3 bg-white text-blue-600 rounded-full">{{ transactions.length }}</span>
          </div>
          <div class="ml-4 mt-6 mb-1" v-for="(transaction, transactionIndex) in transactions">
            <a target="_blank" :href="`/monthly-batangas-transactions/${transaction.monthly_batangas_transaction_id}/edit#transaction-${transaction.id}`" tabindex="-1" v-if="$page.auth.user.can.batangasTransaction.update">
              <p class="text-sm font-bold text-blue-700 mb-2">{{ transaction.trip_no }}. {{ transaction.driver.name }} & {{ transaction.helper.name }} ({{ transaction.tanker.plate_no }})</p>

<!--                 <div class="text-xs font-medium text-gray-600">
                <span class="mb-4">{{ transaction.date }}</span> -
                <span class="-ml-2 px-2 rounded-full" v-if="transaction.purchases" v-for="purchase in transaction.purchases">
                  {{ purchase.purchase_no }}
                </span>
              </div>
-->
            </a>
            <span v-else>
              <p class="text-sm font-bold mb-2">{{ transaction.trip_no }}. {{ transaction.driver.name }} & {{ transaction.helper.name }} ({{ transaction.tanker.plate_no }})</p>
            </span>

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
                    Remarks
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
                      {{ detail.remarks }}
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
                    <div class="text-sm text-gray-900">{{ totalTransactionsQty(year, month, transaction.id) }}</div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm text-gray-900"></div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm text-gray-900">{{ totalTransactionsAmount(year, month, transaction.id) }}</div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
    <!-- <button @click="loadMore" :disabled="loadingMore">Load more...</button> -->
    <!-- <button @click="loadMoreTransactions">Load more...</button> -->
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
      localTransactionDetails: this.transactionDetails,
      // transactionPagination: this.transactionDetails,
    }
  },
  methods: {
    totalTransactionsAmount(yr, mo, transactionId) {
      var totalAmt = Object.entries(this.localTransactionDetails).reduce((acc, [year, months]) => {
        if(year === yr) {
          for (let [month, transactions] of Object.entries(months)) {
            if(month === mo) {
              transactions.map(transaction => {
                if(transaction.id === transactionId) {
                  transaction.details.forEach(detail => {
                    acc += parseFloat(detail.quantity) * parseFloat(detail.unit_price);
                  });
                }
              });
            }
          }
        }
        return parseFloat(acc);
      }, 0);

      return this.toPHP(totalAmt);
    },

    totalTransactionsQty(yr, mo, transactionId) {
      var totalQty = Object.entries(this.localTransactionDetails).reduce((acc, [year, months]) => {
        if(year === yr) {
          for (let [month, transactions] of Object.entries(months)) {
            if(month === mo) {
              transactions.map(transaction => {
                if(transaction.id === transactionId) {
                  transaction.details.forEach(detail => {
                    acc += parseFloat(detail.quantity);
                  });
                }
              });
            }
          }
        }
        return parseFloat(acc);
      }, 0);

      return this.quantityFormat(totalQty);
    },

    // loadMoreTransactions() {
    //     var currentPage = this.transactionPagination.links[0].current_page;
    //     var id = window.location.href.split('/');

    //     // if (this.loadingMore) return;

    //     // this.loadingMore = true;

    //     axios.get(`/${this.record}/${id[4]}?page=${currentPage + 1}`)
    //       .then(({ data }) => {
    //         // Prepending the old messages to the list.
    //         this.localTransactionDetails = [
    //         ...this.localTransactionDetails,
    //         ...data.batangasDetails.data,
    //         ];

    //         // Update our pagination meta information.
    //         this.transactionPagination = data.batangasDetails;
    //       });
    //       // .finally(() => this.loadingMore = false);
    // },
  },
}
</script>