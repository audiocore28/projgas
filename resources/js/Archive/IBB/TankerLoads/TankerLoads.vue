<template>
  <div>
    <div class="bg-white rounded shadow overflow-x-auto mb-8 -mt-4 px-8">
      <div class="rounded shadow overflow-x-auto mb-8 mt-2" v-for="load in localLoadDetails" :key="load.id" :value="load.id">
        <div class="ml-5 mt-5 mb-1">
          <inertia-link :href="route('tanker-loads.edit', load.id)" tabindex="-1">
            <p class="text-sm font-bold text-blue-700 mb-2">{{ load.trip_no }}. {{ load.driver.name }} & {{ load.helper.name }} ({{ load.tanker.plate_no }})</p>
          </inertia-link>
          <p class="text-sm font-medium text-gray-600 mb-4">{{ load.date }} / {{ load.purchase.purchase_no }}</p>
        </div>
        <table class="min-w-full divide-y divide-gray-200">
          <tbody class="bg-white divide-y divide-gray-200">
            <tr v-for="detail in load.loads">
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                <div class="text-sm text-gray-900">{{ detail.product.name }}</div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                <div class="text-sm text-gray-900">{{ quantityFormat(detail.quantity) }}</div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
    <button @click="loadMoreLoad">Load more...</button>
  </div>
</template>

<script>
import axios from 'axios'
import { numberFormatsMixin } from '@/Mixins/numberFormatsMixin'

export default {
  mixins: [numberFormatsMixin],
  props: {
    loadDetails: Object,
    record: String,
  },
  remember: 'form',
  data() {
    return {
      localLoadDetails: this.loadDetails.data,
      loadPagination: this.loadDetails,
    }
  },
  methods: {
    loadMoreLoad() {
      var currentPage = this.loadPagination.links[0].current_page;
      var id = window.location.href.split('/');

      // if (this.loadingMore) return;

      // this.loadingMore = true;

      axios.get(`/${this.record}/${id[4]}/edit?page=${currentPage + 1}`)
        .then(({ data }) => {
          // Prepending the old messages to the list.
          this.localLoadDetails = [
            ...this.localLoadDetails,
            ...data.loadDetails.data,
          ];

          // Update our pagination meta information.
          this.loadPagination = data.loadDetails;
       });
       // .finally(() => this.loadingMore = false);
    },

  },
}
</script>