import http from '@js/lib/Http'

export function enterIndex(url) {
	return (to, from, next) => {
		http.get(`/api/${url}`)
		  .then((res) => {
		  	next((vm) => {
		  		vm.setData(res)
		  	})
		  })
	}
}

export function updateIndex(url) {
	return function(to, from, next) {
		http.get(`/api/${url}`)
		  .then((res) => {
		  	this.setData(res)
		  	next()
		  })
	}
}

export function enterShow(url) {
	return (to, from, next) => {
		http.get(`/api/${url}/${to.params.id}`)
		  .then((res) => {
		  	next((vm) => {
		  		vm.setData(res)
		  	})
		  })
	}
}

export function updateShow(url) {
	return function(to, from, next) {
		this.show = false
		http.get(`/api/${url}/${to.params.id}`)
		  .then((res) => {
		  	this.setData(res)
		  	next()
		  })
	}
}

export function enterForm(url) {
	return (to, from, next) => {
		http.get(`/api/${getURL(url, to)}`)
		  .then((res) => {
		  	next((vm) => {
		  		vm.setData(res)
		  	})
		  })
	}
}

export function updateForm(url) {
	return function(to, from, next) {
		this.show = false
		http.get(`/api/${getURL(url, to)}`)
		  .then((res) => {
		  	this.setData(res)
		  	next()
		  })
	}
}


function getURL(url, to) {
  let urls = {
    'create': `${url}/create`,
    'edit': `${url}/${to.params.id}/edit`,
    'clone': `${url}/${to.params.id}/edit?mode=clone`,
  }
  return (urls[to.meta.mode] || urls['create'])
}