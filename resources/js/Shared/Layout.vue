<template>
  <div>
    <portal-target name="dropdown" slim />
    <div class="md:flex md:flex-col">
      <div class="md:h-screen md:flex md:flex-col" @click="hideDropdownMenus">
        <div class="md:flex md:flex-shrink-0">
          <div class="bg-yellow-500 md:flex-shrink-0 md:w-56 px-6 py-4 flex items-center justify-between md:justify-center">
            <inertia-link class="mt-1" :href="route('purchases.index')">
              <logo class="fill-white" width="120" height="28" />
            </inertia-link>
            <dropdown class="md:hidden" placement="bottom-end">
              <svg class="fill-white w-6 h-6" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M0 3h20v2H0V3zm0 6h20v2H0V9zm0 6h20v2H0v-2z" /></svg>
              <div slot="dropdown" class="mt-2 px-8 py-4 shadow-lg bg-blue-800 rounded">
                <main-menu :url="url()" />
              </div>
            </dropdown>
          </div>
          <div class="bg-white border-b w-full p-4 md:py-0 text-sm md:text-md flex justify-between items-center">
            <div class="mt-1 mr-4 cursor-pointer" @click="menuOpened !== 0 ? menuOpened = 0 : menuOpened = null">
              <svg class="invisible md:visible fill-gray-600 w-6 h-6" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M0 3h20v2H0V3zm0 6h20v2H0V9zm0 6h20v2H0v-2z" /></svg>
              <!-- {{ $page.auth.user.id }} -->
            </div>
            <dropdown class="mt-1" placement="bottom-end">
              <div class="flex items-center cursor-pointer select-none group">
                <div class="text-gray-700 group-hover:text-blue-600 focus:text-blue-600 mr-1 whitespace-no-wrap">
                  <span>{{ $page.auth.user.name }}</span>
                  <!-- <span class="hidden md:inline">{{ $page.auth.user.email }}</span> -->
                </div>
                <icon class="w-5 h-5 group-hover:fill-blue-600 fill-gray-700 focus:fill-blue-600" name="cheveron-down" />
              </div>
              <div slot="dropdown" class="mt-2 py-2 shadow-xl bg-white rounded text-sm">
                <!-- <inertia-link class="block px-6 py-2 hover:bg-blue-600 hover:text-white" :href="route('users.edit', $page.auth.user.id)">My Profile</inertia-link> -->
                <inertia-link class="block px-6 py-2 hover:bg-blue-600 hover:text-white" :href="route('profile.show')">Profile</inertia-link>
                <inertia-link class="block px-6 py-2 hover:bg-blue-600 hover:text-white" :href="route('users.index')">Manage Users</inertia-link>
                <inertia-link class="block px-6 py-2 hover:bg-blue-600 hover:text-white" :href="route('logout')" method="post">Logout</inertia-link>
              </div>
            </dropdown>
          </div>
        </div>
        <div class="md:flex md:flex-grow md:overflow-hidden">
          <main-menu :url="url()" class="hidden md:block bg-blue-800 flex-shrink-0 w-56 p-12 overflow-y-auto" v-show="menuOpened == 0"/>
          <div class="md:flex-1 px-4 py-8 md:px-2 py-12 md:overflow-y-auto" scroll-region>
            <flash-messages />
            <slot />
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import Dropdown from '@/Shared/Dropdown'
import FlashMessages from '@/Shared/FlashMessages'
import Icon from '@/Shared/Icon'
import Logo from '@/Shared/Logo'
import MainMenu from '@/Shared/MainMenu'

export default {
  components: {
    Dropdown,
    FlashMessages,
    Icon,
    Logo,
    MainMenu,
  },
  data() {
    return {
      showUserMenu: false,
      // sidebar toggle
      menuOpened: 0,
      // accounts: null,
    }
  },
  methods: {
    url() {
      return location.pathname.substr(1)
    },
    hideDropdownMenus() {
      this.showUserMenu = false
    },
  },
}
</script>