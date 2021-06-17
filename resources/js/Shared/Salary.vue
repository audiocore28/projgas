<template>
  <div>
    <div class="ml-5 pt-6">
      <text-input v-model="rate" class="pr-6 pb-8 w-full lg:w-1/6" label="Salary Rate" />
    </div>
    <div class="bg-white rounded overflow-x-auto mb-8 -mt-4 mx-4 lg:w-3/4" v-for="(months, year) in trips">
      <h3>{{ year }}</h3>
      <div class="rounded shadow overflow-x-auto mb-8">
        <table class="min-w-full divide-y divide-gray-200" width="100%">
          <colgroup>
            <col span="1" style="width: 20%;">
            <col span="1" style="width: 40%;">
            <col span="1" style="width: 20%;">
            <col span="1" style="width: 20%;">
          </colgroup>
          <thead class="bg-gray-50">
            <tr>
              <th scope="col" class="text-center px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Month
              </th>
              <th scope="col" class="text-center px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Trips
              </th>
              <th scope="col" class="text-center px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Total
              </th>
              <th scope="col" class="text-center px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Salary
              </th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <tr v-for="(transactions, month) in months">
              <td class="px-6 py-4 whitespace-nowrap text-center">
                <div class="text-sm font-medium text-gray-900">
                  {{ month }}
                </div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-center">
                <div class="text-sm font-medium text-gray-900">
                  <span v-for="transaction in transactions" class="mr-2 px-2 py-2 text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                    {{ transaction.trip_no }}
                  </span>
                </div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-center">
                <div class="text-sm font-medium text-gray-900">
                  {{ transactions.length }}
                </div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-center">
                <div class="text-sm font-medium text-gray-900">
                  {{ toPHP(transactions.length * rate) }}
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
    trips: Object,
    rate: Number,
  },
  remember: 'form',
  data() {
    return {}
  },
}
</script>