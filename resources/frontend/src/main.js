let page = window.location.pathname.split('/')[1]
/*
 let handler
 try {
 let context = require.context('bundle!./pages/', true, /index\.js/)
 handler = context('./' + page + '/index.js')
 } catch (e) {
 console.log(e)
 }

 if (handler) {
 handler(function(page) {
 })
 }*/

if (page === '') import('./pages/dashboard')
if (page === 'buildings') import('./pages/buildings')
if (page === 'orders') import('./pages/orders')
if (page === 'sales') import('./pages/sales')
if (page === 'price-groups') import('./pages/price-groups')
if (page === 'deliveries') import('./pages/deliveries')
if (page === 'reports') import('./pages/reports')
if (page === 'building-models') import('./pages/building-models')
if (page === 'options') import('./pages/options')
if (page === 'option-categories') import('./pages/option-categories')

if (page === 'building-packages') import('./pages/building-packages')
if (page === 'building-package-categories') import('./pages/building-package-categories')
if (page === 'dealers') import('./pages/dealers')
if (page === 'plants') import('./pages/plants')
if (page === 'dealer-order-form') import('./pages/dealer-order-form')
if (page === 'customer-order-form') import('./pages/customer-order-form')
if (page === 'dealer-map') import('./pages/dealer-map')
if (page === 'employees') import('./pages/employees')

if (page === 'styles') import('./pages/styles')
if (page === 'settings') import('./pages/settings')
if (page === 'colors') import('./pages/colors')
if (page === 'locations') import('./pages/locations')
if (page === 'building-statuses') import('./pages/building-statuses')
if (page === 'delivery-trucks') import('./pages/delivery-trucks')
if (page === 'delivery-trailers') import('./pages/delivery-trailers')

if (page === 'qr-build') import('./pages/qr-build')
if (page === 'qr-location') import('./pages/qr-location')