<template>
  <div class="select-form">
    <div class="select-input" :tabindex="disabled ? -1 : tabindex" ref="toggle" :disabled="disabled"
      @click="toggle"  @keydown.down.prevent="onDownKey"
      @keydown.enter="onEnter"
      @keydown.up.prevent="onUpKey" @keydown.esc="onBlur"
      @blur="onBlur">
      <span class="select-text">{{selectedText}}</span>
      <span :class="[`select-icon icon icon-chevron-${showDropdown ? 'up' : 'down'}`]"></span>
    </div>
    <div class="select-dropdown" v-if="showDropdown">
        <div class="select-inner">
          <div class="select-items" ref="items">
            <div :class="['select-item', selectIndex === i ? 'select-active':'']"
              @mouseover.prevent="onMouse(i)"
              @mousedown.prevent="select(option)"
              v-for="(option, i) in options"
              >
            <span>{{option[column]}}</span>
          </div>
          </div>
        </div>
    </div>
  </div>
</template>
<script>

  export default {
    name: 'XTypeahead',
    model: {
      prop: 'value',
      event: 'input'
    },
    props: {
      tabindex: {
        default: 0
      },
      value: Object,
      url: String,
      disabled: {
        default: false,
        type: Boolean
      },
      column: {
        default: 'value'
      }
    },
    data() {
      return {
        showDropdown: false,
        selectIndex: -1,
        options: []
      }
    },
    computed: {
      selectedText() {
        return this.value && this.value[this.column] ? this.value[this.column] : 'Select'
      }
    },
    methods: {
      fetch() {
        this.$http.get(this.url)
          .then((res) => {
            this.$set(this.$data, 'options', res.data.options)
          })
      },
      select(option) {
        this.$emit('input', option)
        this.close()
      },
      onEnter() {
        if(this.disabled) return
        if(this.selectIndex < 0) return
        const option = this.options[this.selectIndex]
        this.select(option)
      },
      onBlur() {
        this.close()
      },
      close() {
        this.showDropdown = false
        this.selectIndex = -1
      },
      open() {
        this.showDropdown = true
        this.fetch()
      },
      toggle() {
        if(this.disabled) return
        if(this.showDropdown) {
          this.close()
        } else {
          this.open()
        }
      },
      onMouse(index) {
        this.selectIndex = index
      },
      onUpKey(e) {
        if(this.disabled) return
        if (this.selectIndex > 0) {
          this.selectIndex--
          if(this.selectIndex > 4) {
            this.$nextTick(() => {
              // todo: algo to find best scroll position
              this.$refs.items.scrollTop -= 28
            })
          }
        } else {
          this.selectIndex = this.options.length - 1
          this.$nextTick(() => {
            this.$refs.items.scrollTop = this.selectIndex * 28
          })
        }
      },
      onDownKey(e) {
        if(this.disabled) return
        if(!this.showDropdown) {
          this.open()
        }
        if (this.options.length - 1 > this.selectIndex) {
          this.selectIndex++
          if(this.selectIndex > 4) {
            this.$nextTick(() => {
              this.$refs.items.scrollTop += 28
            })
          }
        } else {
          this.selectIndex = 0
          this.$nextTick(() => {
            this.$refs.items.scrollTop = 0
          })
        }

      },
      handleKeyOnFocus(e) {
        const keyCode = e.keyCode || e.which
        if (!e.shiftKey && keyCode !== 9 && !this.showDropdown) {
          this.open()
        }
      }
    }
  }
</script>