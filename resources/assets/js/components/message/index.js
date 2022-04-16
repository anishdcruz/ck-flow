import Vue from 'vue'
import MessageList from './MessageList.vue'

const message = new Vue(MessageList).$mount()

document.body.appendChild(message.$el)

export default message
