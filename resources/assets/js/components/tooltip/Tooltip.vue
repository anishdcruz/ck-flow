<template>
  <div class="tooltip">
    <span class="tooltip-trigger" ref="trigger"
            @mouseenter="toggle"
            @mouseleave="toggle">
      <slot></slot>
    </span>
    <transition name="fade" @after-leave="doDestory">
      <div class="tooltip-popover"
        v-if="show"
        ref="popover">
        <div class="tooltip-arrow"></div>
        <div class="tooltip-content">
          <slot name="content"><div>{{content}}</div></slot>
        </div>
      </div>
    </transition>
  </div>
</template>
<script>
  export default {
    name: 'XTooltip',
    props: {
      content: {
        default: null
      }
    },
    data() {
      return {
        show: false
      }
    },
    methods: {
      toggle() {
        this.show = !this.show
      },
      doDestory() {
        // alert('ok')
      }
    }
  }
</script>