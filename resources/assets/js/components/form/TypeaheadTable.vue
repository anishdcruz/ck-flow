<template>
  <div class="select-form">
    <div class="select-input" :tabindex="disabled ? -1 : tabindex" ref="toggle" :disabled="disabled"
      @click="toggle" @keydown.down.prevent="onKeydownMain">
      <div class="select-text">
        {{selectVal}}
      </div>
      <span v-if="removable && value && value.id" class="select-remove icon icon-trash-a" @click="remove"></span>
      <span v-else :class="[`select-icon icon icon-arrow-${showDropdown ? 'up-b' : 'down-b'}`]"></span>
    </div>
    <div class="select-table-dropdown" v-if="showDropdown">
        <div class="select-inner">
          <div class="select-search-wrap">
              <input type="text" :value="search" ref="search" class="select-search" placeholder="Search..."
                @keydown.down.prevent="onDownKey" @keydown.enter="onEnter"
                @keydown.up.prevent="onUpKey" @keydown.esc="onBlur"
                @input="onSearch" @blur="onBlur">
          </div>
          <div class="select-items" ref="items">
            <table class="select-table">
              <thead>
                <tr>
                  <th v-for="col in columns">{{$t(col)}}</th>
                </tr>
              </thead>
              <tbody>
                <tr :class="['select-item', selectIndex === i ? 'select-active':'']"
                  @mouseover.prevent="onMouse(i)"
                  @mousedown.prevent="select(option)"
                  v-for="(option, i) in availableOptions"
                  >
                  <td v-for="col in columns">{{option[col] | trim}}</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
    </div>
  </div>
</template>
<script>
  import {debounce} from 'lodash'
  export default {
    name: 'XTypeaheadTable',
    model: {
      prop: 'value',
      event: 'input'
    },
    props: {
      url: String,
      columns: {
        type: Array,
        default() {
          return []
        }
      },
      name: String,
      tabindex: {
        default: 0
      },
      value: [Object, Array, String],
      disabled: {
        default: false,
        type: Boolean
      },
      removable: {
        default: false,
        type: Boolean
      },
      params: {
        type: [Object, Array],
        default() {
          return {}
        }
      }
    },
    data() {
      return {
        isLoading: false,
        showDropdown: false,
        selectIndex: -1,
        search: '',
        options: []
      }
    },
    computed: {
      selectVal() {
        if(this.value) {
          if(typeof this.value === 'string') {
            return this.value
          } else if(this.value.id) {
            return this.value[this.name]
          }
        }

        return this.$t('select')
      },
      availableOptions() {
        return this.options
      }
    },
    methods: {
      remove() {
          const payload = {
            id: null
          }
          this.$emit('input', payload)
      },
      onSearch(e) {
        this.search = event.target.value
        // xhr
        this.fetch(this.search)

      },
      fetch: debounce(function(q) {
        this.isLoading = true
        this.$http.get(`/api/typeahead/${this.url}`, {
            params: {
                query: q,
                ...this.params
            }
        })
          .then((res) => {
              if(res.data) {
                this.$set(this.$data, 'options', res.data.results)
              }
              this.isLoading = false
          })
      }, 300),
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
      onKeydownMain(e) {
        this.open()
      },
      select(option) {
        if(this.multiple) {
            const payload = this.value
            payload.push(option)
            this.$emit('input', payload)
          } else {
            this.$emit('input', option)
          }
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
      onMouse(index) {
        this.selectIndex = index
      },
      close() {
        this.showDropdown = false
        this.selectIndex = -1
        this.search = ''
      },
      open() {
        this.showDropdown = true
        this.$nextTick(() => {
          // cannot used key from parent due to macrotask in vue,
          // will be microtask in 2.6
          this.$refs.search.focus()
          if(!this.options.length) {
            this.fetch()
          }
        })
      },
      toggle() {
        if(this.disabled) return
        if(this.showDropdown) {
          this.close()
        } else {
          this.open()
        }
      },
    }
  }
</script>