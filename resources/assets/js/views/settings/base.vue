<template>
	<div class="settings">
    <div class="settings-sidebar">
      <div class="settings-sidebar-inner">
        <strong class="settings-title">{{$t('settings')}}</strong>
        <ul class="settings-list">
          <template v-for="item in items">
            <li class="sidebar-break" v-if="item.break"></li>
            <li>
              <router-link class="settings-link" :to="item.to">
                <span class="settings-text">{{$t(item.title)}}</span>
                <i class="settings-icon2 icon icon-arrow-right-b"></i>
              </router-link>
            </li>
          </template>
        </ul>
      </div>
    </div>
    <div class="settings-inner">
      <transition name="fade" mode="out-in">
        <router-view></router-view>
      </transition>
    </div>
	</div>
</template>
<script>
    export default {
        data () {
            return {
                items: [
                    {to: '/settings', title: 'general'},
                    {to: '/settings/email', title: 'email'},
                    {to: '/settings/globals', title: 'globals'},
                    {to: '/settings/contacts', title: 'contacts', break: true},
                    {to: '/settings/organizations', title: 'organizations'},
                    {to: '/settings/items', title: 'items'},
                    {to: '/settings/leads', title: 'leads'},
                    {to: '/settings/opportunities', title: 'opportunities'},
                    {to: '/settings/proposals', title: 'proposals'},
                    {to: '/settings/contracts', title: 'contracts'},
                    {to: '/settings/projects', title: 'projects'},
                    {to: '/settings/invoices', title: 'invoices', break: true},
                    {to: '/settings/payments', title: 'payments'},
                    {to: '/settings/expenses', title: 'expenses'},
                    {to: '/settings/vendors', title: 'vendors'},
                    {to: '/settings/web_payment', title: 'web_payment', break: true},
                    {to: '/settings/users', title: 'users', break: true},
                    {to: '/settings/invitations', title: 'invitations'},
                    {to: '/settings/roles', title: 'roles_and_permission'}
                ]
            }
        }
    }
</script>