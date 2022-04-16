import Vue from 'vue'
import Bar from './Bar.vue'

const bar = new Vue(Bar).$mount()

document.body.appendChild(bar.$el)

export default bar
