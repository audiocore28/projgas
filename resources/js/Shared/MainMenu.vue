<template>
  <div>
    <div class="mb-4" v-for="link in links">
      <inertia-link class="flex items-center group py-3" :href="route(link.route)">
        <icon :name="link.icon" class="w-4 h-4 mr-2" :class="isUrl(link.isUrl) ? 'fill-white' : 'fill-blue-400 group-hover:fill-white'" />
        <div :class="isUrl(link.isUrl) ? 'text-white' : 'text-blue-300 group-hover:text-white'">{{ link.name }}</div>
      </inertia-link>
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
      links: [
        // { route: 'dashboard', icon: 'office', isUrl: 'dashboard', name: 'Dashboard'},
        { route: 'purchases.index', icon: 'shopping-cart', isUrl: 'purchases', name: 'Purchases'},
        { route: 'tanker-loads.index', icon: 'dashboard', isUrl: 'tanker-loads', name: 'Loads'},
        { route: 'hauls.index', icon: 'location', isUrl: 'hauls', name: 'Hauling'},
        { route: 'deliveries.index', icon: 'location', isUrl: 'deliveries', name: 'Deliveries'},
        { route: 'suppliers.index', icon: 'users', isUrl: 'suppliers', name: 'Suppliers'},
        { route: 'clients.index', icon: 'users', isUrl: 'clients', name: 'Clients'},
        // { route: 'statements.index', icon: 'book', isUrl: 'statements', name: 'SOA'},
        { route: 'products.index', icon: 'store-front', isUrl: 'products', name: 'Products'},
        { route: 'tankers.index', icon: 'dashboard', isUrl: 'tankers', name: 'Tankers'},
        { route: 'drivers.index', icon: 'users', isUrl: 'drivers', name: 'Drivers'},
        { route: 'helpers.index', icon: 'users', isUrl: 'helpers', name: 'Helpers'},
      ],
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