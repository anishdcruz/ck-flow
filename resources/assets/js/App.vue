<template>
  <div class="container">
    <div class="navbar">
      <div class="navbar-inner">
        <div class="navbar-toggle">
          <span class="icon icon-android-menu" @click="toggleMenu"></span>
        </div>
        <div class="navbar-brand">
            <img src="../img/flow.png" class="navbar-logo">
        </div>
        <div class="navbar-dropdown">
          <x-dropdown size="sm" dir="right" icon="more">
            <strong class="navbar-dropdown-link"
              slot="button" slot-scope="{ toggle }"
              @click="toggle()">
                <i class="icon icon-person"></i>
                <span>{{user.name}}</span>
                <i class="icon icon-arrow-down-b"></i>
              </strong>
            <x-dropdown-menu slot="menu">
              <x-dropdown-link :to="`/personal_settings`">
                {{$t('personal_settings')}}
              </x-dropdown-link>
              <x-dropdown-item divide>
                <a href="/logout">Logout</a>
              </x-dropdown-item>
            </x-dropdown-menu>
          </x-dropdown>
        </div>
      </div>
    </div>
    <div class="container-main">
      <div class="sidebar" :style="{width: sm ? '4%' : '17%'}">
        <div class="sidebar-inner">
          <template v-for="link in links">
            <router-link :to="link.to" class="sidebar-link">
              <div class="sidebar-icon">
                <span :class="`icon icon-${link.icon}`"></span>
              </div>
              <div v-if="!sm" class="sidebar-text">{{$t(link.title)}}</div>
              <div v-if="!sm" class="sidebar-arrow">
                <span class="icon icon-arrow-right-b"></span>
              </div>
            </router-link>
            <div class="sidebar-break" v-if="link.break"></div>
          </template>
        </div>
      </div>
      <div class="content" :style="{width: sm ? '96%' : '83%'}">
        <transition name="fade" mode="out-in">
          <router-view :key="$route.path"></router-view>
        </transition>
      </div>
    </div>
  </div>
</template>
<script>
  import {includes} from 'lodash'
  export default {
    computed: {
      links() {
        const list = [
          {icon: 'home', title: 'overview', to: '/', break: true},
          {icon: 'ios-people', title: 'contacts', to: '/contacts'},
          {icon: 'ios-briefcase', title: 'organizations', to: '/organizations'},
          {icon: 'images', title: 'media_library', to: '/media_library'},
          {icon: 'cube', title: 'items', to: '/items', break: true},
          {icon: 'funnel', title: 'leads', to: '/leads'},
          {icon: 'flash', title: 'opportunities', to: '/opportunities'},
          {icon: 'document-text', title: 'proposals', to: '/proposals'},
          {icon: 'android-hand', title: 'contracts', to: '/contracts'},
          {icon: 'hammer', title: 'projects', to: '/projects', break: true},
          {icon: 'document-text', title: 'invoices', to: '/invoices'},
          {icon: 'card', title: 'payments', to: '/payments'},
          {icon: 'android-send', title: 'payment_requests', to: '/payment_requests'},
          {icon: 'cash', title: 'expenses', to: '/expenses', break: true},
          {icon: 'ios-people', title: 'vendors', to: '/vendors'},
          // {icon: 'android-refresh', title: this.$t('recurring_invoices'), to: '/recurring-invoices'},
          {icon: 'android-refresh', title: 'recurring_exports', to: '/recurring_exports'},
          {icon: 'folder', title: 'templates', to: '/templates'},
          {icon: 'settings', title: 'settings', to: '/settings'},
        ]

        const available = window.FLOW.links || []

        // enable auth
        return list.filter((item) => {
          return includes(available, item.title)
        })
      }
    },
    data() {
      return {
        user: window.FLOW.user,
        sm: 0
      }
    },
    created() {
      let found = localStorage.getItem('flow.sm')
      this.sm = Number(found)
    },
    methods: {
      toggleMenu() {

        if(this.sm) {
          this.sm = 0
        } else {
          this.sm = 1
        }

        localStorage.setItem('flow.sm', (this.sm))
      }
    }
  }
</script>