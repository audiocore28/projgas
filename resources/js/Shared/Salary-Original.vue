<template>
  <div>
    <div class="ml-5 pt-6">
      <text-input v-model="rate" class="pr-6 pb-8 w-full lg:w-1/6" label="Salary Rate" />
    </div>
    <div class="bg-white rounded overflow-x-auto mb-8 -mt-4 mx-4 lg:w-1/2">
      <div class="rounded shadow overflow-x-auto mb-8">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th scope="col" class="text-center px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Year
              </th>
              <th scope="col" class="text-center px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Month
              </th>
              <th scope="col" class="text-center px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Mindoro
              </th>
              <th scope="col" class="text-center px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Amount
              </th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <tr v-for="trip in trips" :key="trip.id" :value="trip.id">
              <td class="px-6 py-4 whitespace-nowrap text-center">
                <div class="text-sm font-medium text-gray-900">
                  {{ trip.year }}
                </div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-center">
                <div class="text-sm font-medium text-gray-900">
                  {{ trip.month }}
                </div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-center">
                <div class="text-sm font-medium text-gray-900">
                  {{ trip.loaded }}
                </div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-center">
                <div class="text-sm font-medium text-gray-900">
                  {{ toPHP(trip.loaded * rate) }}
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>

<script>
import TextInput from '@/Shared/TextInput'
import moment from 'moment'
import { numberFormatsMixin } from '@/Mixins/numberFormatsMixin'

export default {
  mixins: [numberFormatsMixin],
  components: {
    TextInput,
  },
  props: {
    trips: Array,
    rate: Number,
  },
  remember: 'form',
  data() {
    return {}
  },
}
</script>