<template>
  <div>
    <div class="bg-white rounded shadow overflow-x-auto mb-8 -mt-4">
      <div class="rounded shadow overflow-x-auto m-4 lg:w-2/3" v-for="purchase in localPurchaseDetails">
        <div class="ml-5 mr-5 mt-5">

          <a  class="text-md font-bold text-blue-700 mb-2" target="_blank" :href="`/purchases/${purchase.id}/edit`" tabindex="-1" v-if="$page.auth.user.can.purchase.update">
            {{ purchase.purchase_no }}
          </a>
          <span class="text-md font-bold mb-2" v-else>
            {{ purchase.purchase_no }}
          </span>

          <p class="mt-2 text-xs font-medium text-gray-600 mb-4">{{ purchase.date }}</p>
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
            <tr v-for="detail in purchase.purchase_details" :key="detail.id" :value="detail.id">
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
            <!-- Total -->
            <tr class="bg-gray-200">
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="text-left text-xs font-medium text-gray-500 uppercase">Total:</div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="text-sm text-gray-900">{{ totalPurchasesQty(purchase.id) }}</div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="text-sm text-gray-900"></div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="text-sm text-gray-900">{{ totalPurchasesAmount(purchase.id) }}</div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
    <!-- <button @click="loadMorePurchase">Load more...</button> -->
  </div>
</template>

<script>
import axios from 'axios'
import { numberFormatsMixin } from '@/Mixins/numberFormatsMixin'

export default {
  mixins: [numberFormatsMixin],
  props: {
    purchaseDetails: Array,
    record: String,
  },
  remember: 'form',
  data() {
    return {
      localPurchaseDetails: this.purchaseDetails,
      // purchasePagination: this.links,
    }
  },
  methods: {
    totalPurchasesAmount(purchaseId) {
      for (var i = 0; i < this.localPurchaseDetails.length; i++) {
        if (this.localPurchaseDetails[i].id === purchaseId) {

          var totalAmt = this.localPurchaseDetails[i].purchase_details.reduce(function (acc, purchase) {
            acc += parseFloat(purchase.quantity) * parseFloat(purchase.unit_price);
            return acc;
          }, 0);

          return this.toPHP(totalAmt);
        }
      }
    },

    totalPurchasesQty(purchaseId) {
      for (var i = 0; i < this.localPurchaseDetails.length; i++) {
        if (this.localPurchaseDetails[i].id === purchaseId) {

          var totalQty = this.localPurchaseDetails[i].purchase_details.reduce(function (acc, purchase) {
            acc += parseFloat(purchase.quantity);
            return acc;
          }, 0);

          return this.quantityFormat(totalQty);
        }
      }
    },

    // loadMorePurchase() {
    //   var currentPage = this.purchasePagination[0].current_page;

    //   var id = window.location.href.split('/');

    //   // if (this.loadingMore) return;

    //   // this.loadingMore = true;

    //   axios.get(`/${this.record}/${id[4]}?page=${currentPage + 1}`)
    //     .then(({ data }) => {
    //       // Prepending the old messages to the list.
    //       this.localPurchaseDetails = [
    //         ...this.localPurchaseDetails,
    //         ...data.purchaseDetails.data[0].purchases,
    //       ];

    //       // Update our pagination meta information.
    //       // this.purchasePagination = data.purchaseDetails.data[0].purchases;
    //     });
    //     // .finally(() => this.loadingMore = false);
    // },

  },
}
</script>
