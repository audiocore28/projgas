<template>
  <div>
    <h1 class="mb-8 font-bold text-3xl">
      <inertia-link class="text-blue-600 hover:text-blue-800" :href="route('purchases.index')">Purchases</inertia-link>
      <span class="text-blue-600 font-medium">/</span> {{ updateForm.purchase_no }}
    </h1>

    <!-- Overview of Distribution -->
    <div class="rounded shadow overflow-x-auto mb-8 -mt-4">
      <table class="w-full whitespace-no-wrap">
        <thead class="bg-gray-50">
          <tr>
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
              Products
            </th>
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
              Purchases
            </th>
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
              Loads
            </th>
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
              Hauling
            </th>
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
              Deliveries
            </th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          <tr class="hover:bg-gray-100 focus-within:bg-gray-100">
            <td class="px-6 py-4 text-sm text-gray-500">
              <span class="px-2 py-2 text-sm leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                {{ 'Diesel' }}
              </span>
            </td>
            <td class="px-6 py-4">
              <div class="text-md text-gray-900" v-for="purchase in figures.purchases" v-if="purchase.product.name === 'Diesel'">
                <span class="font-semibold">
                  {{ toFigure(purchase.quantity) }}
                </span>
                <span class="font-semibold text-sm ml-2 px-2 inline-flex leading-5 rounded-full bg-yellow-100 text-yellow-800">
                  {{ toPHP(purchase.unit_price) }}
                </span>
              </div>
            </td>
<!--             <td>
              <div class="flex items-center">
                <span class="font-semibold text-md font-medium text-gray-900">
                  {{ 'Diesel' }}
                </span>
                <div class="px-6 py-4" v-for="load in figures.loads">
                  <div class="relative w-12 h-12 ml-1" v-for="detail in load.loads" v-if="detail.product_id === 3">
                    <img class="rounded-full border border-gray-100 shadow-sm" src="https://randomuser.me/api/portraits/women/81.jpg" alt="user image" />
                    <div class="text-xs font-medium font-semibold absolute top-0 right-0 h-6 w-6 -my-2 -mx-2 border-2 border-white rounded-full bg-yellow-200 z-2 leading-5 text-yellow-800">
                      {{ toFigure(detail.quantity) }}
                    </div>
                  </div>
                </div>
              </div>
            </td>
 -->
            <td class="px-6 py-4">
              <span class="font-semibold text-md font-medium text-gray-900 mr-1">{{ 'test' }}</span>
              <span v-for="load in figures.loads">
                <span class="px-2 py-2 text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800" v-for="detail in load.loads.load" v-if="detail.product.name === 'Diesel'">
                  {{ load.driver.name }}
                  <span class="p-1 rounded-full text-yellow-800 text-xs ml-2 bg-yellow-400">
                      {{ toFigure(detail.quantity) }}
                  </span>
                </span>
              </span>
            </td>
            <td class="px-6 py-4">
              <span class="font-semibold text-md font-medium text-gray-900 mr-1">{{ 'H100' }}</span>
              <span v-for="haul in figures.hauls">
                <span class="px-2 py-2 text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800" v-for="detail in haul.hauls.haul" v-if="detail.product.name === 'Diesel'">
                  {{ detail.client.name }}
                  <span class="p-1 rounded-full text-yellow-800 text-xs ml-2 bg-yellow-400">
                      {{ toFigure(detail.quantity) }}
                  </span>
                </span>
              </span>
            </td>
            <td class="px-6 py-4">
              <span class="font-semibold text-md font-medium text-gray-900 mr-1">{{ 'D120' }}</span>
              <span v-for="delivery in figures.deliveries">
                <span class="px-2 py-2 text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800" v-for="detail in delivery.deliveries.delivery" v-if="detail.product.name === 'Diesel'">
                  {{ detail.client.name }}
                  <span class="p-1 rounded-full text-yellow-800 text-xs ml-2 bg-yellow-400">
                      {{ toFigure(detail.quantity) }}
                  </span>
                </span>
              </span>
            </td>
          </tr>
          <tr class="hover:bg-gray-100 focus-within:bg-gray-100">
            <td class="px-6 py-4 text-sm text-gray-500">
              <span class="px-2 py-2 text-sm leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                {{ 'Regular' }}
              </span>
            </td>
            <td class="px-6 py-4">
              <div class="text-md text-gray-900" v-for="purchase in figures.purchases" v-if="purchase.product.name === 'Regular'">
                <span class="font-semibold">
                  {{ toFigure(purchase.quantity) }}
                </span>
                <span class="font-semibold text-sm ml-2 px-2 inline-flex leading-5 rounded-full bg-green-100 text-green-800">
                  {{ toPHP(purchase.unit_price) }}
                </span>
              </div>
            </td>
<!--             <td>
              <div class="flex items-center">
                <span class="font-semibold text-md font-medium text-gray-900">
                  {{ 'Regular' }}
                </span>
                <div class="px-6 py-4" v-for="load in figures.loads">
                  <div class="relative w-12 h-12 ml-1" v-for="detail in load.loads" v-if="detail.product_id === 2">
                    <img class="rounded-full border border-gray-100 shadow-sm" src="https://randomuser.me/api/portraits/women/81.jpg" alt="user image" />
                    <div class="text-xs font-medium font-semibold absolute top-0 right-0 h-6 w-6 -my-2 -mx-2 border-2 border-white rounded-full bg-green-200 z-2 leading-5 text-green-800">
                      {{ toFigure(detail.quantity) }}
                    </div>
                  </div>
                </div>
              </div>
            </td>
 -->
            <td class="px-6 py-4">
              <span class="font-semibold text-md font-medium text-gray-900 mr-1">{{ 'R140' }}</span>
              <span v-for="load in figures.loads">
                <span class="px-2 py-2 text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800" v-for="detail in load.loads.load" v-if="detail.product.name === 'Regular'">
                  {{ load.driver.name }}
                  <span class="p-1 rounded-full text-green-800 text-xs ml-2 bg-green-400">
                      {{ toFigure(detail.quantity) }}
                  </span>
                </span>
              </span>
            </td>
            <td class="px-6 py-4">
              <span class="font-semibold text-md font-medium text-gray-900 mr-1">{{ 'H100' }}</span>
              <span v-for="haul in figures.hauls">
                <span class="px-2 py-2 text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800" v-for="detail in haul.hauls.haul" v-if="detail.product.name === 'Regular'">
                  {{ detail.client.name }}
                  <span class="p-1 rounded-full text-green-800 text-xs ml-2 bg-green-400">
                      {{ toFigure(detail.quantity) }}
                  </span>
                </span>
              </span>
            </td>
            <td class="px-6 py-4">
              <span class="font-semibold text-md font-medium text-gray-900 mr-1">{{ 'R140' }}</span>
              <span v-for="delivery in figures.deliveries">
                <span class="px-2 py-2 text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800" v-for="detail in delivery.deliveries.delivery" v-if="detail.product.name === 'Regular'">
                  {{ detail.client.name }}
                  <span class="p-1 rounded-full text-green-800 text-xs ml-2 bg-green-400">
                      {{ toFigure(detail.quantity) }}
                  </span>
                </span>
              </span>
            </td>
          </tr>
          <tr class="hover:bg-gray-100 focus-within:bg-gray-100">
            <td class="px-6 py-4 text-sm text-gray-500">
              <span class="px-2 py-2 text-sm leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                {{ 'Premium' }}
              </span>
            </td>
            <td class="px-6 py-4">
              <div class="text-md text-gray-900" v-for="purchase in figures.purchases" v-if="purchase.product.name === 'Premium'">
                <span class="font-semibold">
                  {{ toFigure(purchase.quantity) }}
                </span>
                <span class="font-semibold text-sm ml-2 px-2 inline-flex leading-5 rounded-full bg-red-100 text-red-800">
                  {{ toPHP(purchase.unit_price) }}
                </span>
              </div>
            </td>
<!--             <td>
              <div class="flex items-center">
                <span class="font-semibold text-md font-medium text-gray-900">
                  {{ 'Premium' }}
                </span>
                <div class="px-6 py-4" v-for="load in figures.loads">
                  <div class="relative w-12 h-12 ml-1" v-for="detail in load.loads" v-if="detail.product_id === 1">
                    <img class="rounded-full border border-gray-100 shadow-sm" src="https://randomuser.me/api/portraits/women/81.jpg" alt="user image" />
                    <div class="text-xs font-medium font-semibold absolute top-0 right-0 h-6 w-6 -my-2 -mx-2 border-2 border-white rounded-full bg-red-200 z-2 leading-5 text-red-800">
                      {{ toFigure(detail.quantity) }}
                    </div>
                  </div>
                </div>
              </div>
            </td>
 -->
            <td class="px-6 py-4">
              <span class="font-semibold text-md font-medium text-gray-900 mr-1">{{ 'P110' }}</span>
              <span v-for="load in figures.loads">
                <span class="px-2 py-2 text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800" v-for="detail in load.loads.load" v-if="detail.product.name === 'Premium'">
                  {{ load.driver.name }}
                  <span class="p-1 rounded-full text-red-800 text-xs ml-2 bg-red-400">
                      {{ toFigure(detail.quantity) }}
                  </span>
                </span>
              </span>
            </td>
            <td class="px-6 py-4">
              <span class="font-semibold text-md font-medium text-gray-900 mr-1">{{ 'H100' }}</span>
              <span v-for="haul in figures.hauls">
                <span class="px-2 py-2 text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800" v-for="detail in haul.hauls.haul" v-if="detail.product.name === 'Premium'">
                  {{ detail.client.name }}
                  <span class="p-1 rounded-full text-red-800 text-xs ml-2 bg-red-400">
                      {{ toFigure(detail.quantity) }}
                  </span>
                </span>
              </span>
            </td>
            <td class="px-6 py-4">
              <span class="font-semibold text-md font-medium text-gray-900 mr-1">{{ 'P110' }}</span>
              <span v-for="delivery in figures.deliveries">
                <span class="px-2 py-2 text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800" v-for="detail in delivery.deliveries.delivery" v-if="detail.product.name === 'Premium'">
                  {{ detail.client.name }}
                  <span class="p-1 rounded-full text-red-800 text-xs ml-2 bg-red-400">
                      {{ toFigure(detail.quantity) }}
                  </span>
                </span>
              </span>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
    <!-- /Overview of Distribution -->


    <!-- Tabs -->
    <div class="p-1">
      <ul class="flex border-b">
        <li @click="openTab = 1" :class="{ '-mb-px': openTab === 1 }" class="mr-1">
          <a :class="openTab === 1 ? activeClasses : inactiveClasses" class="bg-white inline-block py-2 px-4 font-semibold" href="#">Details</a>
        </li>
        <li @click="openTab = 2" :class="{ '-mb-px': openTab === 2 }" class="-mb-px mr-1">
          <a :class="openTab === 2 ? activeClasses : inactiveClasses" class="bg-white inline-block py-2 px-4 font-semibold" href="#">Edit Purchase</a>
        </li>
      </ul>

      <div class="w-full pt-4">
        <!-- Tab 1 -->
        <div v-show="openTab === 1">
<!--           <div class="bg-white rounded shadow overflow-hidden max-w-6xl mb-8 -mt-4">
          </div>
 -->
          <div class="bg-white rounded shadow overflow-x-auto mb-8 -mt-4">
            <ul class="block mt-8 w-full">
              <!-- Purchase -->
              <li class="flex align-center flex-col">
                <h4 @click="purchaseSelected !== 0 ? purchaseSelected = 0 : purchaseSelected = null"
                class="cursor-pointer px-5 py-3 bg-blue-600 text-white inline-block hover:opacity-75 hover:shadow hover:-mb-3 rounded-t">Purchases</h4>
                <div v-show="purchaseSelected == 0" class="border py-4 px-2">
                  <div class="mb-8 ml-5">
                    <h2 class="text-md font-bold text-gray-900 mb-2">{{ purchase.purchase_no }}</h2>
                    <!-- <p class="text-sm font-medium text-gray-900 mb-2">{{ purchase.supplier.name }}</p> -->
                    <p class="text-sm font-medium text-gray-900 mb-2">{{ purchase.date }}</p>
                  </div>
                  <div class="rounded shadow overflow-x-auto mb-8 -mt-4">
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
                        <tr v-for="purchase in figures.purchases" :key="purchase.id" :value="purchase.id">
                          <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ purchase.product.name }}</div>
                          </td>
                          <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ quantityFormat(purchase.quantity) }}</div>
                          </td>
                          <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ toPHP(purchase.unit_price) }}</div>
                          </td>
                          <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ totalCurrency(purchase.quantity, purchase.unit_price) }}</div>
                          </td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                </div>
              </li>
              <!-- Loads -->
              <li class="flex align-center flex-col">
                <h4 @click="loadSelected !== 1 ? loadSelected = 1 : loadSelected = null"
                class="cursor-pointer px-5 py-3 bg-blue-600 text-white inline-block hover:opacity-75 hover:shadow hover:-mb-3">Loads</h4>
                <div v-show="loadSelected == 1" class="border py-4 px-2">
                  <div class="rounded shadow overflow-x-auto mb-8 -mt-4" v-for="load in figures.loads" :key="load.id" :value="load.id">
                    <div class="ml-5 mt-5 mb-1">
                      <inertia-link :href="route('tanker-loads.edit', load.id)" tabindex="-1">
                        <p class="text-sm font-bold text-blue-700 mb-2">{{ load.driver.name }} & {{ load.helper.name }} ({{ load.tanker.plate_no }})</p>
                      </inertia-link>
                    </div>
                    <table class="min-w-full divide-y divide-gray-200">
<!--                       <thead class="bg-gray-50">
                        <tr>
                          <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Driver
                          </th>
                          <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Product
                          </th>
                          <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Quantity
                          </th>
                        </tr>
                      </thead>
 -->                      <tbody class="bg-white divide-y divide-gray-200">
                        <tr v-for="detail in load.loads.load">
<!--                           <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                              <div class="ml-4">
                                <div class="text-sm font-medium text-gray-900">
                                  Rodel - Driver
                                </div>
                              </div>
                            </div>
                          </td>
 -->
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
              </li>
             <!-- Hauling -->
              <li class="flex align-center flex-col">
                <h4 @click="haulSelected !== 2 ? haulSelected = 2 : haulSelected = null"
                class="cursor-pointer px-5 py-3 bg-blue-600 text-white inline-block hover:opacity-75 hover:shadow hover:-mb-3">Hauling</h4>
                <div v-show="haulSelected == 2" class="border py-4 px-2">
                  <div class="rounded shadow overflow-x-auto mb-8 -mt-4" v-for="haul in figures.hauls" :key="haul.id" :value="haul.id">
                    <div class="ml-5 mt-5 mb-1">
                      <inertia-link :href="route('hauls.edit', haul.id)" tabindex="-1">
                        <p class="text-sm font-bold text-blue-700 mb-2">{{ haul.driver.name }} & {{ haul.helper.name }} ({{ haul.tanker.plate_no }})</p>
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
                        <tr v-for="detail in haul.hauls.haul">
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
<!--                           <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">GTEX - Fortune</div>
                          </td>
 -->
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

                      </tbody>
                    </table>
                  </div>
                </div>
              </li>
             <!-- Deliveries -->
              <li class="flex align-center flex-col">
                <h4 @click="deliverySelected !== 3 ? deliverySelected = 3 : deliverySelected = null"
                :class="{'cursor-pointer px-5 py-3 bg-blue-600 text-white inline-block hover:opacity-75 hover:shadow hover:-mb-3': true, 'rounded-b': deliverySelected != 3}">Deliveries</h4>
                <div v-show="deliverySelected == 3" :class="{'border py-4 px-2': true, 'rounded-b': deliverySelected == 3}">
                  <div class="rounded shadow overflow-x-auto mb-8 -mt-4" v-for="delivery in figures.deliveries" :key="delivery.id" :value="delivery.id">
                    <div class="ml-5 mt-5 mb-1">
                      <inertia-link :href="route('deliveries.edit', delivery.id)" tabindex="-1">
                        <p class="text-sm font-bold text-blue-700 mb-2">{{ delivery.driver.name }} & {{ delivery.helper.name }} ({{ delivery.tanker.plate_no }})</p>
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
                        <tr v-for="detail in delivery.deliveries.delivery">
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
<!--                           <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">GTEX - Fortune</div>
                          </td>
 -->
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

                      </tbody>
                    </table>
                  </div>
                </div>
              </li>
            </ul>
          </div>
        </div>

        <!-- Tab 2 -->
        <div v-show="openTab === 2">
          <!-- Update Existing Purchase -->
          <div class="bg-white rounded shadow overflow-hidden max-w-6xl mb-8 -mt-4">
            <form @submit.prevent="updatePurchase">
              <!-- Purchase -->
              <div class="p-8 -mr-6 -mb-8 flex flex-wrap">
                <!-- <text-input v-model="updateForm.date" :error="errors.date" class="pr-6 pb-8 w-full lg:w-1/2" label="Date" /> -->
                <label class="form-label block mr-5">Date</label>
                <div class="pr-6 pb-8 w-full">
                  <date-picker v-model="updateForm.date" lang="en" value-type="format" :formatter="momentFormat"></date-picker>
                </div>
                <text-input v-model="updateForm.purchase_no" :error="errors.purchase_no" class="pr-6 pb-8 w-full lg:w-1/2" label="Purchase No" />
                <select-input v-model="updateForm.supplier_id" :error="errors.supplier_id" class="pr-6 pb-8 w-full lg:w-1/2" label="Supplier">
                  <option :value="null" />
                  <option v-for="supplier in suppliers" :key="supplier.id" :value="supplier.id">{{ supplier.name }}</option>
                </select-input>
              </div>

              <!-- Details -->
              <div class="p-8 -mr-6 -mb-8 flex flex-wrap"
              v-for="(details, index) in updateForm.details">
              <select-input v-model="details.product.id" :error="errors.product_id" class="pr-6 pb-8 w-full lg:w-1/6" label="Product">
                <option :value="null" />
                <option v-for="product in products" :key="product.id" :value="product.id">{{ product.name }}</option>
              </select-input>
              <text-input type="number" step="any" v-model="details.quantity" :error="errors.quantity" class="pr-6 pb-8 w-full lg:w-1/6" label="Quantity" />
              <text-input type="number" step="any" v-model="details.unit_price" :error="errors.unit_price" class="pr-6 pb-8 w-full lg:w-1/6" label="Unit Price" />
              <text-input v-model="details.remarks" :error="errors.remarks" class="pr-6 pb-8 w-full lg:w-1/6" label="Remarks" />
              <span style="background-color: red; color: white; cursor: pointer; float: right;" @click.prevent="deleteDetailForm(index, details.id)">X</span>
            </div>

        <!--         <div class="px-8 py-4 bg-gray-100 border-t border-gray-200 flex justify-end items-center">
                  <button class="btn-indigo" @click.prevent="addNewDetailForm">Add Row</button>
                </div>
              -->
              <div class="px-8 py-4 bg-gray-100 border-t border-gray-200 flex justify-end items-center">
                <button v-if="!purchase.deleted_at" class="text-red-600 hover:underline" tabindex="-1" type="button" @click="destroy">Delete Purchase</button>
                <loading-button :loading="sending" class="btn-indigo ml-auto" type="submit">Update Purchase</loading-button>
              </div>
            </form>
          </div>


          <!-- Create Form -->
          <div class="bg-white rounded shadow overflow-hidden max-w-6xl">
            <form @submit.prevent="saveNewDetails">
              <!-- Details -->
              <div class="p-8 -mr-6 -mb-8 flex flex-wrap"
              v-for="(details, index) in createForm">
              <select-input v-model="details.product_id" :error="errors.product_id" class="pr-6 pb-8 w-full lg:w-1/6" label="Product">
                <option :value="null" />
                <option v-for="product in products" :key="product.id" :value="product.id">{{ product.name }}</option>
              </select-input>
              <text-input type="number" step="any" v-model="details.quantity" :error="errors.quantity" class="pr-6 pb-8 w-full lg:w-1/6" label="Quantity" />
              <text-input type="number" step="any" v-model="details.unit_price" :error="errors.unit_price" class="pr-6 pb-8 w-full lg:w-1/6" label="Unit Price" />
              <text-input v-model="details.remarks" :error="errors.remarks" class="pr-6 pb-8 w-full lg:w-1/6" label="Remarks" />

              <span style="background-color: red; color: white; cursor: pointer; float: right;" @click.prevent="deleteNewDetailForm(index)">X</span>
            </div>

            <div class="px-8 py-4 flex justify-end items-center">
              <button class="btn-indigo" @click.prevent="createNewDetailForm">Add Row</button>
            </div>

            <div class="px-8 py-4 bg-gray-100 border-t border-gray-200 flex justify-end items-center">
              <loading-button :loading="sending" class="btn-indigo ml-auto" type="submit">Save Details</loading-button>
            </div>
          </form>
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
  import DatePicker from 'vue2-datepicker'
  import moment from 'moment'

  export default {
  // metaInfo() {
  //   return { title: this.form.name }
  // },
  layout: Layout,
  components: {
    Icon,
    LoadingButton,
    SelectInput,
    TextInput,
    TrashedMessage,
    DatePicker,
  },
  props: {
    errors: Object,
    purchase: Object,
    suppliers: Array,
    products: Array,
    figures: Object,
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
      updateForm: {
        date: this.purchase.date,
        purchase_no: this.purchase.purchase_no,
        supplier_id: this.purchase.supplier_id,
        details: this.purchase.details,
      },
      createForm: [
        {
          id: null,
          purchase_id: this.purchase.id,
          product_id: null,
          quantity: null,
          unit_price: null,
          remarks: null,
        },
      ],
      // Tabs
      openTab: 1,
      activeClasses: 'border-l border-t border-r rounded-t text-blue-600',
      inactiveClasses: 'text-blue-500 hover:text-blue-800',
      // Accordion
      purchaseSelected: 0,
      loadSelected: 1,
      haulSelected: 2,
      deliverySelected: 3,
    }
  },
  methods: {
    // update existing details
    updatePurchase() {
      this.$inertia.put(this.route('purchases.update', this.purchase.id), this.updateForm, {
        onStart: () => this.sending = true,
        onFinish: () => this.sending = false,
      });
    },
    destroy() {
      if (confirm('Are you sure you want to delete this purchase?')) {
        this.$inertia.delete(this.route('purchases.destroy', this.purchase.id))
      }
    },
    restore() {
      if (confirm('Are you sure you want to restore this purchase?')) {
        this.$inertia.put(this.route('purchases.restore', this.purchase.id))
      }
    },
    deleteDetailForm(index, productDetailId) {
      if (confirm('Are you sure you want to delete this row?')) {
        this.$inertia.delete(this.route('purchase-details.destroy', productDetailId));
        this.purchase.details.splice(index, 1);
      }
    },

    // ----New Created Form

    // save
    saveNewDetails() {
      this.$inertia.post(this.route('purchase-details.store'), this.createForm, {
        onStart: () => this.sending = true,
        onFinish: () => this.sending = false,
      });
    },
    // create form
    createNewDetailForm() {
      this.createForm.push({
        id: null,
        purchase_id: this.purchase.id,
        product_id: null,
        quantity: null,
        unit_price: null,
        remarks: null,
      });
    },
    // remove form
    deleteNewDetailForm(index) {
      this.createForm.splice(index, 1);
    },

    // Helpers
    toFigure(value) {
      return value / 1000;
    },

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

<style src="vue2-datepicker/index.css"></style>
