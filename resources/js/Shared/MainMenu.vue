<template>
  <div>
    <div class="-ml-6 flex justify-start items-center cursor-pointer" @click="truckingSelected !== 0 ? truckingSelected = 0 : truckingSelected = null">
      <icon name="cheveron-down" class="block w-4 h-4 fill-blue-400 mr-2" v-if="truckingSelected == 0"/>
      <icon name="cheveron-right" class="block w-4 h-4 fill-blue-400 mr-2" v-else/>
      <h4 class="text-blue-300 text-xs py-3 hover:text-white opacity-75 rounded-t">Trucking</h4>
    </div>
    <div v-show="truckingSelected == 0" class="py-4 text-sm">
      <div class="mb-4" v-for="link in truckingLinks">
        <inertia-link class="flex items-center group py-3" :href="route(link.route)" v-if="link.visible">
          <icon :name="link.icon" class="w-4 h-4 mr-2" :class="isUrl(link.isUrl) ? 'fill-white' : 'fill-blue-400 group-hover:fill-white'" />
          <div :class="isUrl(link.isUrl) ? 'text-white' : 'text-blue-300 group-hover:text-white'">{{ link.name }}</div>
        </inertia-link>
      </div>
    </div>

    <div class="-ml-6 flex justify-start items-center cursor-pointer" @click="recordSelected !== 1 ? recordSelected = 1 : recordSelected = null">
      <icon name="cheveron-down" class="block w-4 h-4 fill-blue-400 mr-2" v-if="recordSelected == 1"/>
      <icon name="cheveron-right" class="block w-4 h-4 fill-blue-400 mr-2" v-else/>
      <h4 class="text-blue-300 text-xs py-3 hover:text-white opacity-75 rounded-t">Records</h4>
    </div>
    <div v-show="recordSelected == 1" class="py-4 text-sm">
      <div class="mb-4" v-for="link in recordLinks">
        <inertia-link class="flex items-center group py-3" :href="route(link.route)" v-if="link.visible">
          <icon :name="link.icon" class="w-4 h-4 mr-2" :class="isUrl(link.isUrl) ? 'fill-white' : 'fill-blue-400 group-hover:fill-white'" />
          <div :class="isUrl(link.isUrl) ? 'text-white' : 'text-blue-300 group-hover:text-white'">{{ link.name }}</div>
        </inertia-link>
      </div>
    </div>

<!--     <div class="-ml-6 flex justify-start items-center cursor-pointer" @click="reportSelected !== 2 ? reportSelected = 2 : reportSelected = null">
      <icon name="cheveron-down" class="block w-4 h-4 fill-blue-400 mr-2" v-if="reportSelected == 2"/>
      <icon name="cheveron-right" class="block w-4 h-4 fill-blue-400 mr-2" v-else/>
      <h4 class="text-blue-300 text-xs py-3 hover:text-white opacity-75 rounded-t">Reports</h4>
    </div>
    <div v-show="reportSelected == 2" class="py-4 text-sm">
      <div class="mb-4" v-for="link in reportLinks">
        <inertia-link class="flex items-center group py-3" :href="route(link.route)">
          <icon :name="link.icon" class="w-4 h-4 mr-2" :class="isUrl(link.isUrl) ? 'fill-white' : 'fill-blue-400 group-hover:fill-white'" />
          <div :class="isUrl(link.isUrl) ? 'text-white' : 'text-blue-300 group-hover:text-white'">{{ link.name }}</div>
        </inertia-link>
      </div>
    </div>
 -->
    <div class="-ml-6 flex justify-start items-center cursor-pointer" @click="employeeSelected !== 3 ? employeeSelected = 3 : employeeSelected = null">
      <icon name="cheveron-down" class="block w-4 h-4 fill-blue-400 mr-2" v-if="employeeSelected == 3"/>
      <icon name="cheveron-right" class="block w-4 h-4 fill-blue-400 mr-2" v-else/>
      <h4 class="text-blue-300 text-xs py-3 hover:text-white opacity-75 rounded-t">Employees</h4>
    </div>
    <div v-show="employeeSelected == 3" class="py-4 text-sm">
      <div class="mb-4" v-for="link in employeeLinks">
        <inertia-link class="flex items-center group py-3" :href="route(link.route)" v-if="link.visible">
          <icon :name="link.icon" class="w-4 h-4 mr-2" :class="isUrl(link.isUrl) ? 'fill-white' : 'fill-blue-400 group-hover:fill-white'" />
          <div :class="isUrl(link.isUrl) ? 'text-white' : 'text-blue-300 group-hover:text-white'">{{ link.name }}</div>
        </inertia-link>
      </div>
    </div>

    <div class="-ml-6 flex justify-start items-center cursor-pointer" @click="assetSelected !== 4 ? assetSelected = 4 : assetSelected = null">
      <icon name="cheveron-down" class="block w-4 h-4 fill-blue-400 mr-2" v-if="assetSelected == 4"/>
      <icon name="cheveron-right" class="block w-4 h-4 fill-blue-400 mr-2" v-else/>
      <h4 class="text-blue-300 text-xs py-3 hover:text-white opacity-75 rounded-t">Assets</h4>
    </div>
    <div v-show="assetSelected == 4" class="py-4 text-sm">
      <div class="mb-4" v-for="link in assetLinks">
        <inertia-link class="flex items-center group py-3" :href="route(link.route)" v-if="link.visible">
          <icon :name="link.icon" class="w-4 h-4 mr-2" :class="isUrl(link.isUrl) ? 'fill-white' : 'fill-blue-400 group-hover:fill-white'" />
          <div :class="isUrl(link.isUrl) ? 'text-white' : 'text-blue-300 group-hover:text-white'">{{ link.name }}</div>
        </inertia-link>
      </div>
    </div>

    <div class="-ml-6 flex justify-start items-center cursor-pointer" @click="userSelected !== 5 ? userSelected = 5 : userSelected = null" v-if="$page.auth.user.can.user.viewAny || $page.auth.user.can.role.viewAny || $page.auth.user.can.permission.viewAny">
      <icon name="cheveron-down" class="block w-4 h-4 fill-blue-400 mr-2" v-if="userSelected == 5"/>
      <icon name="cheveron-right" class="block w-4 h-4 fill-blue-400 mr-2" v-else/>
      <h4 class="text-blue-300 text-xs py-3 hover:text-white opacity-75 rounded-t">Manage Users</h4>
    </div>
    <div v-show="userSelected == 5" class="py-4 text-sm">
      <div class="mb-4" v-for="link in userLinks">
        <inertia-link class="flex items-center group py-3" :href="route(link.route)" v-if="link.visible">
          <icon :name="link.icon" class="w-4 h-4 mr-2" :class="isUrl(link.isUrl) ? 'fill-white' : 'fill-blue-400 group-hover:fill-white'" />
          <div :class="isUrl(link.isUrl) ? 'text-white' : 'text-blue-300 group-hover:text-white'">{{ link.name }}</div>
        </inertia-link>
      </div>
    </div>

    <div class="-ml-6 flex justify-start items-center cursor-pointer" @click="utilitySelected !== 6 ? utilitySelected = 6 : utilitySelected = null">
      <icon name="cheveron-down" class="block w-4 h-4 fill-blue-400 mr-2" v-if="utilitySelected == 6"/>
      <icon name="cheveron-right" class="block w-4 h-4 fill-blue-400 mr-2" v-else/>
      <h4 class="text-blue-300 text-xs py-3 hover:text-white opacity-75 rounded-t">Utilities</h4>
    </div>
    <div v-show="utilitySelected == 6" class="py-4 text-sm">
      <div class="mb-4" v-for="link in utilityLinks">
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
        {
          route: 'purchases.index',
          icon: 'shopping-cart',
          isUrl: 'purchases',
          name: 'Purchases',
          visible: this.$page.auth.user.can.purchase.viewAny,
        },
        {
          route: 'monthly-batangas-transactions.index',
          icon: 'location',
          isUrl: 'monthly-batangas-transactions',
          name: 'Batangas',
          visible: this.$page.auth.user.can.batangasTransaction.viewAny,
        },
        {
          route: 'monthly-mindoro-transactions.index',
          icon: 'location',
          isUrl: 'monthly-mindoro-transactions',
          name: 'Mindoro',
          visible: this.$page.auth.user.can.mindoroTransaction.viewAny,
        },
      ],
      recordLinks: [
        {
          route: 'suppliers.index',
          icon: 'office',
          isUrl: 'suppliers',
          name: 'Suppliers',
          visible: this.$page.auth.user.can.supplier.viewAny,
        },
        {
          route: 'depots.index',
          icon: 'office',
          isUrl: 'depots',
          name: 'Depots',
          visible: this.$page.auth.user.can.depot.viewAny,
        },
        {
          route: 'accounts.index',
          icon: 'office',
          isUrl: 'accounts',
          name: 'Accounts',
          visible: this.$page.auth.user.can.account.viewAny,
        },
        {
          route: 'clients.index',
          icon: 'office',
          isUrl: 'clients',
          name: 'Clients',
          visible: this.$page.auth.user.can.client.viewAny,
        },
        {
          route: 'client-payments.index',
          icon: 'office',
          isUrl: 'client-payments',
          name: 'Payments',
          visible: this.$page.auth.user.can.clientPayment.viewAny,
        },
        // { route: 'statements.index', icon: 'book', isUrl: 'statements', name: 'SOA'},
      ],
      // reportLinks: [
      //   { route: 'station-transactions.index', icon: 'book', isUrl: 'station-transactions', name: 'DTR'},
      // ],
      employeeLinks: [
        {
          route: 'drivers.index',
          icon: 'users',
          isUrl: 'drivers',
          name: 'Drivers',
          visible: this.$page.auth.user.can.driver.viewAny,
        },
        {
          route: 'helpers.index',
          icon: 'users',
          isUrl: 'helpers',
          name: 'Helpers',
          visible: this.$page.auth.user.can.helper.viewAny,
        },
        // { route: 'cashiers.index', icon: 'users', isUrl: 'cashiers', name: 'Cashiers'},
        // { route: 'pump-attendants.index', icon: 'users', isUrl: 'pump-attendants', name: 'Pump Attnd'},
        // { route: 'office-staffs.index', icon: 'users', isUrl: 'office-staffs', name: 'Office Staffs'},
      ],
      assetLinks: [
        {
          route: 'tankers.index',
          icon: 'dashboard',
          isUrl: 'tankers',
          name: 'Tankers',
          visible: this.$page.auth.user.can.tanker.viewAny,
        },
        // { route: 'stations.index', icon: 'store-front', isUrl: 'stations', name: 'G. Stations'},
        // { route: 'companies.index', icon: 'office', isUrl: 'companies', name: 'B. Company'},
        {
          route: 'products.index',
          icon: 'store-front',
          isUrl: 'products',
          name: 'Products',
          visible: this.$page.auth.user.can.product.viewAny,
        },
      ],
      userLinks: [
        {
          route: 'users.index',
          icon: 'users',
          isUrl: 'users',
          name: 'Users',
          visible: this.$page.auth.user.can.user.viewAny,
        },
        {
          route: 'roles.index',
          icon: 'users',
          isUrl: 'roles',
          name: 'Roles',
          visible: this.$page.auth.user.can.role.viewAny,
        },
        {
          route: 'permissions.index',
          icon: 'users',
          isUrl: 'permissions',
          name: 'Permissions',
          visible: this.$page.auth.user.can.permission.viewAny,
        },
      ],
      utilityLinks: [
        {
          route: 'backup',
          icon: 'book',
          isUrl: 'backup',
          name: 'Backup DB',
        },
      ],
      truckingSelected: 0,
      recordSelected: 1,
      // reportSelected: 2,
      employeeSelected: 3,
      assetSelected: 4,
      userSelected: 5,
      utilitySelected: 6,
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