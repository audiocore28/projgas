<template>
  <div>
    <h1 class="mb-8 font-bold text-3xl">
      <inertia-link class="text-blue-600 hover:text-blue-800" :href="route('purchases.index')">Purchases</inertia-link>
      <span class="text-blue-600 font-medium">/</span> Create
    </h1>
    <form @submit.prevent="submit">
      <div class="bg-white rounded shadow overflow-hidden max-w-6xl mb-6">
        <!-- Purchase -->
        <div class="px-8 pt-4 -mr-6 -mb-8 flex flex-wrap bg-gradient-to-r from-yellow-500 to-blue-600 highlight-yellow">
          <div>
            <label class="form-label block mr-5">Date:<span class="text-red-500">*</span></label>
            <div class="pr-6 pb-4 mt-3">
              <date-picker v-model="form.date" style="width: 150px" :error="errors.date" lang="en" value-type="format" :formatter="momentFormat"></date-picker>
              <div v-if="errors.date" class="form-error">{{ errors.date }}</div>
            </div>
          </div>

          <select-input v-model="form.supplier_id" :error="errors.supplier_id" class="pr-6 pb-4 w-full lg:w-1/6" label="Supplier">
            <option :value="null" />
            <option v-for="supplier in suppliers" :key="supplier.id" :value="supplier.id">{{ supplier.name }}</option>
          </select-input>
          <select-input v-model="form.depot_id" :error="errors.depot_id" class="pr-6 pb-4 w-full lg:w-1/6" label="Depot">
            <option :value="null" />
            <option v-for="depot in depots" :key="depot.id" :value="depot.id">{{ depot.name }}</option>
          </select-input>
          <select-input v-model="form.account_id" :error="errors.account_id" class="pr-6 pb-4 w-full lg:w-1/6" label="Account">
            <option :value="null" />
            <option v-for="account in accounts" :key="account.id" :value="account.id">{{ account.name }}</option>
          </select-input>

          <div class="w-full lg:w-2/6">
            <text-input v-model="form.purchase_no" :error="errors.purchase_no" class="pr-6 pb-4 w-full" label="Purchase No*" />
          </div>
        </div>

        <!-- PurchaseDetail table form -->
        <div class="px-8 py-4 my-6 overflow-x-auto">
          <table class="min-w-full">
            <colgroup>
              <col span="1" style="width: 15%;">
              <col span="1" style="width: 12%;">
              <col span="1" style="width: 13%;">
              <col span="1" style="width: 12%;">
              <col span="1" style="width: 38%;">
            </colgroup>
            <thead class="bg-gray-50">
              <tr>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Product<span class="text-red-500">*</span>
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
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Remarks
                </th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  <button @click.prevent="addNewPurchaseDetailForm">
                    <icon name="plus" class="w-4 h-4 mr-2 loadIr-2 fill-green-600"/>
                  </button>
                </th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr v-for="(details, purchaseIndex) in form.details" :key="purchaseIndex">
                <td>
                  <div class="text-sm font-medium text-gray-900">
                    <select @change="onPurchaseChange($event, purchaseIndex)" :id="`product-${purchaseIndex}`" v-model="details.product.id" class="form-select" :class="{ error: errors[`details.${purchaseIndex}.product.id`] }">
                      <option :value="null" />
                      <option v-for="product in products" :key="product.id" :value="product.id">{{ product.name }}</option>
                    </select>
                  </div>
                </td>
                <td class="text-sm text-gray-500">
                  <div class="text-sm font-medium text-gray-900">
                    <text-input type="number" step="any" v-model="details.quantity" :error="errors.quantity" />
                  </div>
                </td>
                <td class="text-sm text-gray-500">
                  <div class="text-sm font-medium text-gray-900">
                    <text-input type="number" step="any" v-model="details.unit_price" :error="errors.unit_price" />
                  </div>
                </td>
                <td class="text-sm text-gray-500">
                  <div class="text-sm font-medium text-gray-900 text-center">
                    {{ totalCurrency(details.quantity, details.unit_price) }}
                  </div>
                </td>
                <td class="text-sm text-gray-500">
                  <div class="text-sm font-medium text-gray-900 text-center">
                    <text-input v-model="details.remarks" :error="errors.remarks" />
                  </div>
                </td>
                <td class="text-sm text-gray-500">
                  <div class=" px-5 text-sm font-medium text-gray-900">
                    <button @click.prevent="deletePurchaseDetailForm(purchaseIndex)" type="button" class="bg-white py-1 px-1 flex-shrink-0 text-sm leading-none" tabindex="-1">
                      <icon name="trash" class="w-4 h-4 mr-2 fill-red-600" />
                    </button>
                  </div>
                </td>
              </tr>

              <!-- Total -->
              <tr class="bg-gray-200">
                <td>
                  <div class="px-6 py-4 whitespace-nowrap text-left text-sm font-medium text-gray-500 uppercase">Total:</div>
                </td>
                <td>
                  <div class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium text-gray-500 uppercase">
                    {{ quantityFormat(PurchaseTotalQty()) }}
                  </div>
                </td>
                <td>
                  <div class="px-6 py-4 whitespace-nowrap text-left text-sm font-medium text-gray-500 uppercase"></div>
                </td>
                <td>
                  <div class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium text-gray-500 uppercase">
                    {{ PurchaseTotalAmt() }}
                  </div>
                </td>
                <td>
                  <div class="px-6 py-4 whitespace-nowrap text-left text-sm font-medium text-gray-500 uppercase"></div>
                </td>
                <td>
                  <div class="px-6 py-4 whitespace-nowrap text-left text-xs font-medium text-gray-500 uppercase"></div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
        <!-- /PurchaseDetail table form -->

      </div>

      <!-- TankerLoad Form -->

      <!-- Batangas -->
      <div class="mb-8 flex justify-between items-center">
        <div class="-mb-8 flex justify-start items-center">
          <icon name="cheveron-down" class="block w-6 h-6 fill-blue-600 mr-2" v-if="batangasSelected == 0"/>
          <icon name="cheveron-right" class="block w-6 h-6 fill-blue-600 mr-2" v-else/>
          <h1 class="text-blue-600 my-8 font-bold text-xl mr-4 pointer" @click="batangasSelected !== 0 ? batangasSelected = 0 : batangasSelected = null">To Batangas</h1>

          <!-- MonthlyBatangasTransaction -->
          <div class="text-sm font-medium text-gray-900 mr-4">
            <select v-model="form.monthly_batangas_transaction_id" class="form-select" :class="{ error: errors[`monthly_batangas_transaction_id`] }">
              <option :value="null" />
              <option v-for="monthlyTransaction in monthlyBatangasTransactions" :key="monthlyTransaction.id" :value="monthlyTransaction.id">{{ `${monthlyTransaction.year}, ${monthlyTransaction.month}` }}</option>
            </select>
          </div>

          <button class="btn-indigo" @click.prevent="addNewBatangasLoadForm">Add ({{ form.batangasLoads.length }})</button>

        </div>
        <div class="-mb-8 flex justify-end items-center">
          <span class="mr-2 px-2 py-2 text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
            Diesel
            <span class="p-1 rounded-full text-yellow-800 text-xs ml-2 bg-yellow-400">
              {{ totalBatangasLoadQty('Diesel') / 1000 }}
            </span>
          </span>
          <span class="mr-2 px-2 py-2 text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
            Regular
            <span class="p-1 rounded-full text-green-800 text-xs ml-2 bg-green-400">
              {{ totalBatangasLoadQty('Regular') / 1000 }}
            </span>
          </span>
          <span class="mr-2 px-2 py-2 text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
            Premium
            <span class="p-1 rounded-full text-red-800 text-xs ml-2 bg-red-400">
              {{ totalBatangasLoadQty('Premium') / 1000 }}
            </span>
          </span>
        </div>
      </div>

      <div class="grid grid-cols-1 gap-3 xl:grid-cols-3" v-show="batangasSelected == 0">
        <div class="rounded overflow-hidden max-w-6xl" v-for="(load, loadIndex) in form.batangasLoads" :key="loadIndex">
          <div class="bg-white rounded shadow overflow-hidden max-w-6xl">
            <!-- TankerLoad -->
            <div class="p-2 -mr-6 -mb-6 flex justify-between bg-blue-600">
              <table>
<!--                 <colgroup>
                  <col span="1" style="width: 50%;">
                  <col span="1" style="width: 50%;">
                </colgroup>
 -->                <tr>
                  <td class="text-sm text-gray-500">
                    <div class="text-sm font-medium text-gray-900 mr-2">
                      <!-- <text-input v-model="load.trip_no" :error="errors.trip_no" class="pr-6" placeholder="Trip No.*" /> -->

                      <!-- BatangasTransaction trip no. -->
                      <div class="text-sm font-medium text-gray-900">
                        <select :id="`batangas-${loadIndex}`" class="form-select" v-model="form.batangasLoads[loadIndex].batangas_transaction_id" @change="onBatangasTransactionChange($event, loadIndex)" :class="{ error: errors[`batangasLoads.${loadIndex}.batangas_transaction_id`] }">
                          <option :value="null" />
                          <option v-for="transaction in form.batangas_transactions" :key="transaction.id" :value="transaction.id">{{ `${transaction.trip_no} - ${transaction.driver.name} (${transaction.monthly_batangas_transaction.month} ${transaction.monthly_batangas_transaction.year})` }}</option>
                        </select>
                      </div>

                    </div>
                  </td>
                  <td class="text-sm">
                    <button @click.prevent="addNewBatangasLoadDetailForm(loadIndex)">
                      <icon name="plus" class="w-4 h-4 loadIr-2 fill-white"/>
                    </button>

                    <a v-if="form.monthly_batangas_transaction_id && load.batangas_transaction_id" target="_blank" :href="`/monthly-batangas-transactions/${load.monthly_batangas_transaction.id}/edit#transaction-${load.batangas_transaction_id}`" class="ml-2 inline-block">
                      <icon name="open-link" class="w-4 h-4 loadIr-2 fill-white"/>
                    </a>

                  </td>

                </tr>
              </table>

              <div class="mr-5">
                <span class="p-1 rounded-full text-blue-400 text-xs ml-2">
                  <button @click.prevent="deleteBatangasLoadForm(loadIndex)" type="button" class="font-bold p-1 flex-shrink-0 leading-none" tabindex="-1">
                    {{ loadIndex + 1 }}
                  </button>
                </span>
              </div>
              <!-- <text-input v-model="form.remarks" :error="errors.remarks" class="pr-6 pb-8 w-full lg:w-1/2" label="Remarks" /> -->
            </div>
            <!-- /TankerLoad -->

            <!-- TankerLoadDetail table form -->
            <div class="px-6 mt-8 mb-4 overflow-x-auto">
              <table style="width: 260px">
                <colgroup>
                  <col span="1" style="width: 52%;">
                  <col span="1" style="width: 42%;">
                  <col span="1" style="width: 6%;">
                </colgroup>
                <tbody class="bg-white divide-y divide-gray-200">
                  <tr v-for="(details, detailsIndex) in load.details" :key="detailsIndex">
                    <td>
                      <div class="text-sm font-medium text-gray-900">
                        <select :id="`product-${detailsIndex}`" v-model="details.product.id" class="form-select" @change="onBatangasChange($event, loadIndex, detailsIndex)" :class="{ error: errors[`batangasLoads.${loadIndex}.details.${detailsIndex}.product.id`] }">
                          <option :value="null" />
                          <option v-for="product in products" :key="product.id" :value="product.id">{{ product.name }}</option>
                        </select>
                      </div>
                    </td>
                    <td class="text-sm text-gray-500">
                      <div class="text-sm font-medium text-gray-900">
                        <text-input type="number" step="any" v-model="details.quantity" :error="errors.quantity" />
                      </div>
                    </td>
                      <!-- <td class="text-sm text-gray-500">
                      <div class="text-sm font-medium text-gray-900">
                        <text-input type="number" step="any" v-model="details.unit_price" :error="errors.unit_price" />
                      </div>
                    </td>
                    <td class="text-sm text-gray-500">
                      <div class="text-sm font-medium text-gray-900 text-center">
                        {{ totalCurrency(details.quantity, details.unit_price) }}
                      </div>
                    </td>
                    -->
                    <td class="text-sm text-gray-500">
                      <div class=" px-5 text-sm font-medium text-gray-900">
                        <button @click.prevent="deleteBatangasLoadDetailForm(loadIndex, detailsIndex)" type="button" class="bg-white py-1 px-1 flex-shrink-0 text-sm leading-none" tabindex="-1">
                          <icon name="trash" class="w-4 h-4 mr-2 fill-red-600"/>
                        </button>
                      </div>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
            <!-- /TankerLoadDetail table form -->
          </div>
        </div>
      </div>


      <!-- Mindoro -->
      <div class="mt-6 mb-8 flex justify-between items-center border-t-2 border-gray-600">
        <div class="-mb-8 flex justify-start items-center">
          <icon name="cheveron-down" class="block w-6 h-6 fill-yellow-600 mr-2" v-if="mindoroSelected == 1"/>
          <icon name="cheveron-right" class="block w-6 h-6 fill-yellow-600 mr-2" v-else/>
          <h1 class="text-yellow-600 my-8 font-bold text-xl mr-4 pointer" @click="mindoroSelected !== 1 ? mindoroSelected = 1 : mindoroSelected = null">To Mindoro</h1>

          <!-- MonthlyMindoroTransaction -->
          <div class="text-sm font-medium text-gray-900 mr-4">
            <select v-model="form.monthly_mindoro_transaction_id" class="form-select" :class="{ error: errors[`monthly_mindoro_transaction_id`] }">
              <option :value="null" />
              <option v-for="monthlyTransaction in monthlyMindoroTransactions" :key="monthlyTransaction.id" :value="monthlyTransaction.id">{{ `${monthlyTransaction.year}, ${monthlyTransaction.month}` }}</option>
            </select>
          </div>

          <button class="btn-indigo" @click.prevent="addNewMindoroLoadForm">Add ({{ form.mindoroLoads.length }})</button>

        </div>
        <div class="-mb-8 flex justify-end items-center">
          <span class="mr-2 px-2 py-2 text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
            Diesel
            <span class="p-1 rounded-full text-yellow-800 text-xs ml-2 bg-yellow-400">
              {{ totalMindoroLoadQty('Diesel') / 1000 }}
            </span>
          </span>
          <span class="mr-2 px-2 py-2 text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
            Regular
            <span class="p-1 rounded-full text-green-800 text-xs ml-2 bg-green-400">
              {{ totalMindoroLoadQty('Regular') / 1000 }}
            </span>
          </span>
          <span class="mr-2 px-2 py-2 text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
            Premium
            <span class="p-1 rounded-full text-red-800 text-xs ml-2 bg-red-400">
              {{ totalMindoroLoadQty('Premium') / 1000 }}
            </span>
          </span>
        </div>
      </div>

      <div class="grid grid-cols-1 gap-3 xl:grid-cols-3" v-show="mindoroSelected == 1">
        <div class="rounded overflow-hidden max-w-6xl" v-for="(load, loadIndex) in form.mindoroLoads" :key="loadIndex">
          <div class="bg-white rounded shadow overflow-hidden max-w-6xl">
            <!-- TankerLoad -->
            <div class="p-2 -mr-6 -mb-6 flex justify-between bg-yellow-500">
              <table>
<!--                 <colgroup>
                  <col span="1" style="width: 50%;">
                  <col span="1" style="width: 50%;">
                </colgroup>
 -->                <tr>
                  <td class="text-sm text-gray-500">
                    <div class="text-sm font-medium text-gray-900 mr-2">
                      <!-- <text-input v-model="load.trip_no" :error="errors.trip_no" class="pr-6" placeholder="Trip No.*" /> -->

                      <!-- MindoroTransaction trip no. -->
                      <div class="text-sm font-medium text-gray-900">
                        <select :id="`mindoro-${loadIndex}`" class="form-select" v-model="form.mindoroLoads[loadIndex].mindoro_transaction_id" @change="onMindoroTransactionChange($event, loadIndex)" :class="{ error: errors[`mindoroLoads.${loadIndex}.mindoro_transaction_id`] }">
                          <option :value="null" />
                          <option v-for="transaction in form.mindoro_transactions" :key="transaction.id" :value="transaction.id">{{ `${transaction.trip_no} - ${transaction.driver.name} (${transaction.monthly_mindoro_transaction.month} ${transaction.monthly_mindoro_transaction.year})` }}</option>
                        </select>
                      </div>

                    </div>
                  </td>
                  <td class="text-sm">
                    <button @click.prevent="addNewMindoroLoadDetailForm(loadIndex)">
                      <icon name="plus" class="w-4 h-4 loadIr-2 fill-white"/>
                    </button>

                    <a v-if="form.monthly_mindoro_transaction_id && load.mindoro_transaction_id" target="_blank" :href="`/monthly-mindoro-transactions/${load.monthly_mindoro_transaction.id}/edit#transaction-${load.mindoro_transaction_id}`" class="ml-2 inline-block">
                      <icon name="open-link" class="w-4 h-4 loadIr-2 fill-white"/>
                    </a>

                  </td>

                </tr>
              </table>

              <div class="mr-5">
                <span class="p-1 rounded-full text-yellow-400 text-xs ml-2">
                  <button @click.prevent="deleteMindoroLoadForm(loadIndex)" type="button" class="font-bold p-1 flex-shrink-0 leading-none" tabindex="-1">
                    {{ loadIndex + 1 }}
                  </button>
                </span>
              </div>
              <!-- <text-input v-model="form.remarks" :error="errors.remarks" class="pr-6 pb-8 w-full lg:w-1/2" label="Remarks" /> -->
            </div>
            <!-- /TankerLoad -->

            <!-- TankerLoadDetail table form -->
            <div class="px-6 mt-8 mb-4 overflow-x-auto">
              <table style="width: 260px;">
                <colgroup>
                  <col span="1" style="width: 52%;">
                  <col span="1" style="width: 42%;">
                  <col span="1" style="width: 6%;">
                </colgroup>
                <tbody class="bg-white divide-y divide-gray-200">
                  <tr v-for="(details, detailsIndex) in load.details" :key="detailsIndex">
                    <td>
                      <div class="text-sm font-medium text-gray-900">
                        <select :id="`product-${detailsIndex}`" v-model="details.product.id" class="form-select" @change="onMindoroChange($event, loadIndex, detailsIndex)" :class="{ error: errors[`mindoroLoads.${loadIndex}.details.${detailsIndex}.product.id`] }">
                          <option :value="null" />
                          <option v-for="product in products" :key="product.id" :value="product.id">{{ product.name }}</option>
                        </select>
                      </div>
                    </td>
                    <td class="text-sm text-gray-500">
                      <div class="text-sm font-medium text-gray-900">
                        <text-input type="number" step="any" v-model="details.quantity" :error="errors.quantity" />
                      </div>
                    </td>
                      <!-- <td class="text-sm text-gray-500">
                      <div class="text-sm font-medium text-gray-900">
                        <text-input type="number" step="any" v-model="details.unit_price" :error="errors.unit_price" />
                      </div>
                    </td>
                    <td class="text-sm text-gray-500">
                      <div class="text-sm font-medium text-gray-900 text-center">
                        {{ totalCurrency(details.quantity, details.unit_price) }}
                      </div>
                    </td>
                    -->
                    <td class="text-sm text-gray-500">
                      <div class=" px-5 text-sm font-medium text-gray-900">
                        <button @click.prevent="deleteMindoroLoadDetailForm(loadIndex, detailsIndex)" type="button" class="bg-white py-1 px-1 flex-shrink-0 text-sm leading-none" tabindex="-1">
                          <icon name="trash" class="w-4 h-4 mr-2 fill-red-600"/>
                        </button>
                      </div>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
            <!-- /TankerLoadDetail table form -->
          </div>
        </div>
      </div>


      <!-- Total Load-->
      <div class="mt-6 pb-8 flex justify-between items-center border-t-2 border-gray-600 bg-gray-600">
        <div class="pl-10 -mb-8 flex justify-start items-center">
          <!-- <icon name="cheveron-right" class="block w-6 h-6 fill-gray-600 mr-2"/> -->
          <h1 class="text-white my-8 font-bold text-xl mr-4 pointer">Total Load</h1>
        </div>
        <div class="-mb-8 flex justify-end items-center">
          <span class="mr-2 px-2 py-2 text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
            Diesel
            <span class="p-1 rounded-full text-yellow-800 text-xs ml-2 bg-yellow-400">
              {{ allLoadQty('Diesel') / 1000 }}
            </span>
          </span>
          <span class="mr-2 px-2 py-2 text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
            Regular
            <span class="p-1 rounded-full text-green-800 text-xs ml-2 bg-green-400">
              {{ allLoadQty('Regular') / 1000 }}
            </span>
          </span>
          <span class="mr-2 px-2 py-2 text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
            Premium
            <span class="p-1 rounded-full text-red-800 text-xs ml-2 bg-red-400">
              {{ allLoadQty('Premium') / 1000 }}
            </span>
          </span>
        </div>
      </div>

      <!-- Unlifted -->
      <div class="pb-8 flex justify-between items-center border-t-2 border-gray-600 bg-white">
        <div class="pl-10 -mb-8 flex justify-start items-center">
          <!-- <icon name="cheveron-right" class="block w-6 h-6 fill-gray-600 mr-2"/> -->
          <h1 class="text-red-600 my-8 font-bold text-xl mr-4 pointer">Unlifted</h1>
        </div>
        <div class="-mb-8 flex justify-end items-center">
          <span class="mr-2 px-2 py-2 text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
            Diesel
            <span class="p-1 rounded-full text-yellow-800 text-xs ml-2 bg-yellow-400">
              {{ unliftedQty('Diesel') / 1000 }}
            </span>
          </span>
          <span class="mr-2 px-2 py-2 text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
            Regular
            <span class="p-1 rounded-full text-green-800 text-xs ml-2 bg-green-400">
              {{ unliftedQty('Regular') / 1000 }}
            </span>
          </span>
          <span class="mr-2 px-2 py-2 text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
            Premium
            <span class="p-1 rounded-full text-red-800 text-xs ml-2 bg-red-400">
              {{ unliftedQty('Premium') / 1000 }}
            </span>
          </span>
        </div>
      </div>

      <div class="px-8 py-4 border-t border-gray-200 flex justify-end items-center">
        <loading-button :loading="sending" class="btn-indigo" type="submit">Save</loading-button>
      </div>
    </form>
  </div>
</template>

<script>
import axios from 'axios'
import Layout from '@/Shared/Layout'
import LoadingButton from '@/Shared/LoadingButton'
import SelectInput from '@/Shared/SelectInput'
import TextInput from '@/Shared/TextInput'
import Icon from '@/Shared/Icon'
import Multiselect from 'vue-multiselect'
import DatePicker from 'vue2-datepicker'
import moment from 'moment'
import { numberFormatsMixin } from '@/Mixins/numberFormatsMixin'

export default {
  mixins: [numberFormatsMixin],
  metaInfo: { title: 'Create Purchase' },
  layout: Layout,
  components: {
    LoadingButton,
    SelectInput,
    TextInput,
    Multiselect,
    DatePicker,
    Icon,
  },
  props: {
    errors: Object,
    suppliers: Array,
    depots: Array,
    accounts: Array,
    products: Array,
    monthlyMindoroTransactions: Array,
    monthlyBatangasTransactions: Array,
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
  		  date: null,
        purchase_no: null,
        supplier_id: null,
        depot_id: null,
        account_id: null,
        monthly_mindoro_transaction_id: null,
        mindoro_transactions: [],
        monthly_batangas_transaction_id: null,
        batangas_transactions: [],
        details: [
          {
            product: {
              id: null,
              name: null,
            },
            quantity: 0,
            unit_price: 0,
            remarks: null,
          }
        ],
        batangasLoads: [
          // {
          //   monthly_batangas_transaction: {
          //     id: null,
          //   },
          //   batangas_transaction_id: null,
          //   remarks: null,
          //   details: [
          //     {
          //       product: {
          //         id: null,
          //         name: null,
          //       },
          //       quantity: 0,
          //       unit_price: 0,
          //     }
          //   ],
          // },
        ],
        mindoroLoads: [
          // {
          //   monthly_mindoro_transaction: {
          //     id: null,
          //   },
          //   mindoro_transaction_id: null,
          //   remarks: null,
          //   details: [
          //     {
          //       product: {
          //         id: null,
          //         name: null,
          //       },
          //       quantity: 0,
          //       unit_price: 0,
          //     }
          //   ],
          // },
        ],
      },
      // Accordion
      batangasSelected: 0,
      mindoroSelected: 1,
    }
  },
  methods: {
    // Purchase
    submit() {
      this.$inertia.post(this.route('purchases.store'), this.form, {
        onStart: () => this.sending = true,
        onFinish: () => this.sending = false,
      });
    },

    // PurchaseDetail
    addNewPurchaseDetailForm() {
      this.form.details.push({
        // purchase_id: null,
        product: {
          id: null,
          name: null,
        },
        quantity: 0,
        unit_price: 0,
        remarks: null,
      });
    },
    deletePurchaseDetailForm(purchaseIndex) {
      this.form.details.splice(purchaseIndex, 1);
    },

    // PurchaseDetail Totals
    PurchaseTotalAmt() {
      var totalAmt = this.form.details.reduce((acc, detail) => {
        acc += parseFloat(detail.quantity) * parseFloat(detail.unit_price);
        return acc;
      }, 0);

      return this.toPHP(totalAmt);
    },
    PurchaseTotalQty() {
      var totalQty = this.form.details.reduce((acc, detail) => {
        acc += parseFloat(detail.quantity);
        return acc;
      }, 0);

      return totalQty;
    },


    // Batangas - TankerLoad
    addNewBatangasLoadForm() {
      this.form.batangasLoads.push({
        monthly_batangas_transaction: {
          id: null,
        },
        batangas_transaction_id: null,
        remarks: null,
        details: [
          {
            product: {
              id: null,
              name: null,
            },
            quantity: 0,
            unit_price: 0,
          }
        ],
      });
    },
    deleteBatangasLoadForm(loadIndex) {
      this.form.batangasLoads.splice(loadIndex, 1);
    },

    // Batangas - TankerLoadDetail
    addNewBatangasLoadDetailForm(loadIndex) {
      this.form.batangasLoads[loadIndex].details.push({
        product: {
          id: null,
          name: null,
        },
        quantity: 0,
        unit_price: 0,
      });
    },
    deleteBatangasLoadDetailForm(loadIndex, detailsIndex) {
      this.form.batangasLoads[loadIndex].details.splice(detailsIndex, 1);
    },

    //
    onBatangasChange(event, loadIndex, detailsIndex) {
      const product = event.target.options[event.target.options.selectedIndex].text;
      this.form.batangasLoads[loadIndex].details[detailsIndex].product.name = product;
    },

    onBatangasTransactionChange(event, loadIndex) {
      const transactionId = event.target.value;

      axios.get(`/batangas-transactions/${transactionId}/edit`)
        .then(response => {
          this.form.batangasLoads[loadIndex].monthly_batangas_transaction.id = response.data.monthly_batangas_transaction_id;
        });
    },

    // Batangas - TankerLoad Totals
    totalBatangasLoadQty(product) {
      var totalQty = this.form.batangasLoads.reduce(function (acc, load) {
        load.details.forEach(detail => {
          if(detail.product.name === product) {
            acc += parseFloat(detail.quantity);
          }
        });
        return parseFloat(acc);
      }, 0);
      return totalQty;
    },


    // Mindoro - TankerLoad
    addNewMindoroLoadForm() {
      this.form.mindoroLoads.push({
        monthly_mindoro_transaction: {
          id: null,
        },
        mindoro_transaction_id: null,
        remarks: null,
        details: [
          {
            product: {
              id: null,
              name: null,
            },
            quantity: 0,
            unit_price: 0,
          }
        ],
      });
    },
    deleteMindoroLoadForm(loadIndex) {
      this.form.mindoroLoads.splice(loadIndex, 1);
    },

    // Mindoro - TankerLoadDetail
    addNewMindoroLoadDetailForm(loadIndex) {
      this.form.mindoroLoads[loadIndex].details.push({
        product: {
          id: null,
          name: null,
        },
        quantity: 0,
        unit_price: 0,
      });
    },
    deleteMindoroLoadDetailForm(loadIndex, detailsIndex) {
      this.form.mindoroLoads[loadIndex].details.splice(detailsIndex, 1);
    },

    //
    onMindoroChange(event, loadIndex, detailsIndex) {
      const product = event.target.options[event.target.options.selectedIndex].text;
      this.form.mindoroLoads[loadIndex].details[detailsIndex].product.name = product;
    },

    onMindoroTransactionChange(event, loadIndex) {
      const transactionId = event.target.value;

      axios.get(`/mindoro-transactions/${transactionId}/edit`)
        .then(response => {
          this.form.mindoroLoads[loadIndex].monthly_mindoro_transaction.id = response.data.monthly_mindoro_transaction_id;
        });
    },

    // Mindoro - TankerLoad Totals
    totalMindoroLoadQty(product) {
      var totalQty = this.form.mindoroLoads.reduce(function (acc, load) {
        load.details.forEach(detail => {
          if(detail.product.name === product) {
            acc += parseFloat(detail.quantity);
          }
        });
        return parseFloat(acc);
      }, 0);
      return totalQty;
    },

    // TankerLoad Quantity Overall Totals
    allLoadQty(product) {
      var batangasLoadQty = this.totalBatangasLoadQty(product);
      var mindoroLoadQty = this.totalMindoroLoadQty(product);
      var totalLoad = batangasLoadQty + mindoroLoadQty;

      return totalLoad;
    },

    getMindoroTransactions(value) {
      if(value !== null) {
        axios.get(`/monthly-mindoro-transactions/${value}/edit`)
          .then(response => this.form.mindoro_transactions = response.data.mindoroTransactions)
          .catch(err => this.form.mindoro_transactions = []);
      }

      this.form.mindoro_transactions = [];
    },

    getBatangasTransactions(value) {
      if(value !== null) {
        axios.get(`/monthly-batangas-transactions/${value}/edit`)
          .then(response => this.form.batangas_transactions = response.data.batangasTransactions)
          .catch(err => this.form.batangas_transactions = []);
      }

      this.form.batangas_transactions = [];
    },

    onPurchaseChange(event, purchaseIndex) {
      const product = event.target.options[event.target.options.selectedIndex].text;
      this.form.details[purchaseIndex].product.name = product;
    },

    unliftedQty(product) {
      // Purchase Qty
      var totalPurchaseQty = this.form.details.reduce((acc, detail) => {
        if (detail.product.name === product) {
          acc += parseFloat(detail.quantity);
        }
        return parseFloat(acc);
      }, 0);

      // Load Qty
      var totalLoadQty = this.allLoadQty(product);

      // Unlifted Qty
      var unliftedQty = totalPurchaseQty - totalLoadQty;
      return unliftedQty;

    },

  },
  watch: {
    'form.monthly_mindoro_transaction_id': function (value) {
      this.getMindoroTransactions(value);

      this.form.mindoroLoads.forEach(load => {
        load.mindoro_transaction_id = null;
        load.monthly_mindoro_transaction.id = null;
      });
    },

    'form.monthly_batangas_transaction_id': function (value) {
      this.getBatangasTransactions(value);

      this.form.batangasLoads.forEach(load => {
        load.batangas_transaction_id = null;
        load.monthly_batangas_transaction.id = null;
      });
    }

  },

}
</script>

<style src="vue-multiselect/dist/vue-multiselect.min.css"></style>
<style src="vue2-datepicker/index.css"></style>

<style>
  .highlight-yellow label.form-label {
    @apply text-white;
  }
</style>