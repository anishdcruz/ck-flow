<template>
  <div class="select-form">
    <div class="select-input" :tabindex="disabled ? -1 : tabindex" ref="toggle" :disabled="disabled"
      @click="toggle" @keydown.down.prevent="onKeydownMain">
      <div class="select-text" v-if="multiple">
      	<div class="select-tags" v-if="value.length">
      		<div class="tag tag-primary" v-for="(x, i) in value">
      			<span class="tag-text">
      				{{x.value}}
      			</span>
      			<span class="icon icon-x tag-close" @mousedown.prevent="remove(x, i)"></span>
      		</div>
      	</div>
      	<div v-else>Select</div>
      </div>
      <div class="select-text" v-else>
      	{{value && value.value ? value.value : 'Select'}}
      </div>
      <span :class="[`select-icon icon icon-chevron-${showDropdown ? 'up' : 'down'}`]"></span>
    </div>
    <div class="select-dropdown" v-if="showDropdown">
        <div class="select-inner">
        	<div class="select-search-wrap">
		        	<input type="text" ref="search" class="select-search" placeholder="Search..."
		        		@keydown.down.prevent="onDownKey" @keydown.enter="onEnter"
					      @keydown.up.prevent="onUpKey" @keydown.esc="onBlur"
					      @input="onSearch" @blur="onBlur">
        	</div>
          <div class="select-items" ref="items">
            <div :class="['select-item', selectIndex === i ? 'select-active':'']"
              @mouseover.prevent="onMouse(i)"
              @mousedown.prevent="select(option)"
              v-for="(option, i) in availableOptions"
              >
            <span>{{option.value}}</span>
          </div>
          </div>
        </div>
    </div>
  </div>
</template>
<script>
  export default {
  	name: 'XTypeaheadArray',
  	model: {
  	  prop: 'value',
  	  event: 'input'
  	},
  	props: {
  	  tabindex: {
  	    default: 0
  	  },
  	  value: [Object, Array],
  	  options: Array,
  	  disabled: {
  	    default: false,
  	    type: Boolean
  	  },
  	  multiple: {
  	  	default: false,
  	  	type: Boolean
  	  }
  	},
  	data() {
      return {
        showDropdown: false,
        selectIndex: -1,
        search: ''
      }
    },
    computed: {
      availableOptions() {
      	return this.options.filter((option) => {
      	  var searchRegex = new RegExp(this.search, 'i')
      	  return (
      	    searchRegex.test(option.name) ||
      	    searchRegex.test(option.value)
      	  )
      	})
      }
    },
    methods: {
    	remove(x, i) {
    			const payload = this.value
    			payload.splice(i, 1)
    		  this.$emit('input', payload)
    	},
    	onSearch(e) {
    		this.search = event.target.value
    		// xhr
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