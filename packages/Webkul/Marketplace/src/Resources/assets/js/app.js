import VueCarousel from 'vue-carousel';
import Vue         from 'vue';
import VeeValidate from 'vee-validate'; 


Vue.directive("debounce", require("./directives/debounce"));

Vue.use(VueCarousel);
Vue.use(VeeValidate);
Vue.component('seller-category', require('./components/seller-category'));