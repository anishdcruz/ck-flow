import Vue from 'vue'

import format from 'date-fns/format'
import parse from 'date-fns/parse'

Vue.filter('trim', (value, length = 50) => {
    if(value.length > length) {
        return value.substring(0, length) + '...'
    }
    return value
})

Vue.filter('toDate', (value) => {
    const formats = {
        'd-m-Y': 'DD-MM-YYYY',
        'Y-m-d': 'YYYY-MM-DD',
        'd-M-Y': 'DD-MMM-YYYY',
        'Y-M-d': 'YYYY-MMM-DD',
        'd/m/Y': 'DD/MM/YYYY',
        'Y/m/d': 'YYYY/MM/DD'
    }

    let f = window.FLOW.application.date_format

    let result = format(
        parse(value),
        formats[f]
    )

    return result
})

Vue.filter('formatDate', (value, f) => {
    const formats = {
        'd-m-Y': 'DD-MM-YYYY',
        'Y-m-d': 'YYYY-MM-DD',
        'd-M-Y': 'DD-MMM-YYYY',
        'Y-M-d': 'YYYY-MMM-DD',
        'd/m/Y': 'DD/MM/YYYY',
        'Y/m/d': 'YYYY/MM/DD'
    }

    let result = format(
        parse(value),
        formats[f]
    )

    return result
})

Vue.filter('formatMoney', function(value, code = true) {

    let currency = window.FLOW.currency

    let amount = Number(value)
            .toFixed(currency.precision)
            .replace(/(\d)(?=(\d{3})+(?:\.\d+)?$)/g, `$1${currency.thousands_separator}`)

    if(code) {
        if(currency.placement === 'before') {
            return `${currency.code} ${amount}`
        }else {
            return `${amount} ${currency.code} `
        }
    }

    return amount
})

Vue.filter('formatCurrency', function(value, currency, code = true) {

    if(currency) {
        let amount = Number(value)
            .toFixed(currency.precision)
            .replace(/(\d)(?=(\d{3})+(?:\.\d+)?$)/g, `$1${currency.thousands || ','}`)

        if(code) {
            if(currency.placement === 'before') {
                return `${currency.code} ${amount}`
            }else {
                return `${amount} ${currency.code} `
            }
        }

        return amount
    }

    return value
})

// Vue.filter('fromNow', (value) => {
//     return distanceInWordsToNow(
//         parse(value),
//         {addSuffix: true}
//     )
// })