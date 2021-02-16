<template>
  <div>
    <h4 @click="truckingSelected !== 0 ? truckingSelected = 0 : truckingSelected = null"
    class="text-blue-300 text-xs cursor-pointer py-3 hover:text-white opacity-75 rounded-t">Trucking</h4>
    <div v-show="truckingSelected == 0" class="py-4 text-sm">
      <div class="mb-4" v-for="link in truckingLinks">
        <inertia-link class="flex items-center group py-3" :href="route(link.route)">
          <icon :name="link.icon" class="w-4 h-4 mr-2" :class="isUrl(link.isUrl) ? 'fill-white' : 'fill-blue-400 group-hover:fill-white'" />
          <div :class="isUrl(link.isUrl) ? 'text-white' : 'text-blue-300 group-hover:text-white'">{{ link.name }}</div>
        </inertia-link>
      </div>
    </div>

    <h4 @click="recordSelected !== 1 ? recordSelected = 1 : recordSelected = null"
    class="text-blue-300 text-xs cursor-pointer py-3 hover:text-white opacity-75 rounded-t">Records</h4>
    <div v-show="recordSelected == 1" class="py-4 text-sm">
      <div class="mb-4" v-for="link in recordLinks">
        <inertia-link class="flex items-center group py-3" :href="route(link.route)">
          <icon :name="link.icon" class="w-4 h-4 mr-2" :class="isUrl(link.isUrl) ? 'fill-white' : 'fill-blue-400 group-hover:fill-white'" />
          <div :class="isUrl(link.isUrl) ? 'text-white' : 'text-blue-300 group-hover:text-white'">{{ link.name }}</div>
        </inertia-link>
      </div>
    </div>

<!--     <h4 @click="reportSelected !== 2 ? reportSelected = 2 : reportSelected = null"
    class="text-blue-300 text-xs cursor-pointer py-3 hover:text-white opacity-75 rounded-t">Reports</h4>
    <div v-show="reportSelected == 2" class="py-4 text-sm">
      <div class="mb-4" v-for="link in reportLinks">
        <inertia-link class="flex items-center group py-3" :href="route(link.route)">
          <icon :name="link.icon" class="w-4 h-4 mr-2" :class="isUrl(link.isUrl) ? 'fill-white' : 'fill-blue-400 group-hover:fill-white'" />
          <div :class="isUrl(link.isUrl) ? 'text-white' : 'text-blue-300 group-hover:text-white'">{{ link.name }}</div>
        </inertia-link>
      </div>
    </div>
 -->
    <h4 @click="employeeSelected !== 3 ? employeeSelected = 3 : employeeSelected = null"
    class="text-blue-300 text-xs cursor-pointer py-3 hover:text-white opacity-75 rounded-t">Employees</h4>
    <div v-show="employeeSelected == 3" class="py-4 text-sm">
      <div class="mb-4" v-for="link in employeeLinks">
        <inertia-link class="flex items-center group py-3" :href="route(link.route)">
          <icon :name="link.icon" class="w-4 h-4 mr-2" :class="isUrl(link.isUrl) ? 'fill-white' : 'fill-blue-400 group-hover:fill-white'" />
          <div :class="isUrl(link.isUrl) ? 'text-white' : 'text-blue-300 group-hover:text-white'">{{ link.name }}</div>
        </inertia-link>
      </div>
    </div>

    <h4 @click="assetSelected !== 4 ? assetSelected = 4 : assetSelected = null"
    class="text-blue-300 text-xs cursor-pointer py-3 hover:text-white opacity-75 rounded-t">Assets</h4>
    <div v-show="assetSelected == 4" class="py-4 text-sm">
      <div class="mb-4" v-for="link in assetLinks">
        <inertia-link class="flex items-center group py-3" :href="route(link.route)">
          <icon :name="link.icon" class="w-4 h-4 mr-2" :class="isUrl(link.isUrl) ? 'fill-white' : 'fill-blue-400 group-hover:fill-white'" />
          <div :class="isUrl(link.isUrl) ? 'text-white' : 'text-blue-300 group-hover:text-white'">{{ link.name }}</div>
        </inertia-link>
      </div>
    </div>
  </div>
</template>

<script>
import Icon from '@/Shared/Icon'

export default {
  components: {
    Icon,
  },
  props: {
    url: String,
  },
  data() {
    return {
      truckingLinks: [
        // { route: 'dashboard', icon: 'office', isUrl: 'dashboard', name: 'Dashboard'},
        { route: 'purchases.index', icon: 'shopping-cart', isUrl: 'purchases', name: 'Purchases'},
        { route: 'tanker-loads.index', icon: 'dashboard', isUrl: 'tanker-loads', name: 'Loads'},
        { route: 'hauls.index', icon: 'location', isUrl: 'hauls', name: 'Hauling'},
        { route: 'deliveries.index', icon: 'location', isUrl: 'deliveries', name: 'Deliveries'},
      ],
      recordLinks: [
        { route: 'suppliers.index', icon: 'office', isUrl: 'suppliers', name: 'Suppliers'},
        { route: 'clients.index', icon: 'office', isUrl: 'clients', name: 'Clients'},
        // { route: 'statements.index', icon: 'book', isUrl: 'statements', name: 'SOA'},
      ],
      // reportLinks: [
        // { route: 'station-transactions.index', icon: 'store-front', isUrl: 'station-transactions', name: 'DTR'},
      // ],
      employeeLinks: [
        { route: 'drivers.index', icon: 'users', isUrl: 'drivers', name: 'Drivers'},
        { route: 'helpers.index', icon: 'users', isUrl: 'helpers', name: 'Helpers'},
        // { route: 'cashiers.index', icon: 'users', isUrl: 'cashiers', name: 'Cashiers'},
        // { route: 'pump-attendants.index', icon: 'users', isUrl: 'pump-attendants', name: 'Pump Attnd'},
        // { route: 'office-staffs.index', icon: 'users', isUrl: 'office-staffs', name: 'Office Staffs'},
      ],
      assetLinks: [
        { route: 'tankers.index', icon: 'dashboard', isUrl: 'tankers', name: 'Tankers'},
        // { route: 'stations.index', icon: 'store-front', isUrl: 'stations', name: 'G. Stations'},
        // { route: 'companies.index', icon: 'office', isUrl: 'companies', name: 'B. Company'},
        { route: 'products.index', icon: 'store-front', isUrl: 'products', name: 'Products'},
      ],
      truckingSelected: 0,
      recordSelected: 1,
      // reportSelected: 2,
      employeeSelected: 3,
      assetSelected: 4,
    }
  },
  methods: {
    isUrl(...urls) {
      if (urls[0] === '') {
        return this.url === ''
      }

      return urls.filter(url => this.url.startsWith(url)).length
    },
  },
}
</script>