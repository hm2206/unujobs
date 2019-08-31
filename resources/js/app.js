/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

window.Vue = require('vue');

/**
 * The following block of code may be used to automatically register your
 * Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their "basename".
 *
 * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
 */

// const files = require.context('./', true, /\.vue$/i);
// files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default));


Vue.component('ventana', require('./components/Ventana.vue').default);
Vue.component('modal', require('./components/Modal.vue').default);
Vue.component('btn-more', require('./components/BtnMore.vue').default);
Vue.component('authentication', require('./components/Authentication.vue').default);
Vue.component('add-question', require('./components/AddQuestion.vue').default);
Vue.component('add-base', require('./components/AddBase.vue').default);
Vue.component('add-actividades', require('./components/AddActividades.vue').default);
Vue.component('locacion', require('./components/Locacion.vue').default);
Vue.component('notification', require('./components/Notifications.vue').default);
Vue.component('select-change', require('./components/SelectChange.vue').default);
Vue.component('config-work', require('./components/ConfigWork.vue').default);
Vue.component('validacion', require('./components/Validacion.vue').default);
Vue.component('btn-delete', require('./components/BtnDelete.vue').default);
Vue.component('btn-boleta', require('./components/BtnBoleta.vue').default);
Vue.component('btn-cronograma', require('./components/BtnCronograma.vue').default);
Vue.component('btn-cargo', require('./components/BtnCargo.vue').default);
Vue.component('btn-categoria', require('./components/BtnCategoria.vue').default);
Vue.component('btn-concepto', require('./components/BtnConcepto.vue').default);
Vue.component('btn-descuento', require('./components/BtnDescuento.vue').default);
Vue.component('btn-remuneracion', require('./components/BtnRemuneracion.vue').default);
Vue.component('btn-liquidar', require('./components/BtnLiquidar.vue').default);
Vue.component('btn-validate', require('./components/BtnValidate.vue').default);
Vue.component('add-work', require('./components/AddWork.vue').default);
Vue.component('btn-user', require('./components/BtnUser.vue').default);
Vue.component('btn-modulo', require('./components/BtnModulo.vue').default);
Vue.component('btn-role', require('./components/BtnRole.vue').default);
Vue.component('btn-report-cuenta', require('./components/BtnReportCuenta.vue').default);
Vue.component('btn-afp', require('./components/BtnAfp.vue').default);
Vue.component('btn-mef', require('./components/BtnMef.vue').default);
Vue.component('btn-alta-baja', require('./components/BtnAltaBaja.vue').default);
Vue.component('btn-resumen-categoria', require('./components/BtnResumenCategoria.vue').default);
Vue.component('menu-bar', require('./components/Menu.vue').default);
Vue.component('get-personal', require('./components/GetPersonal.vue').default);
Vue.component('btn-detalle', require('./components/BtnDetalle.vue').default);
Vue.component('btn-work-config', require('./components/BtnWorkConfig.vue').default);
Vue.component('config-work-mas', require('./components/ConfigWorkMas.vue').default);
Vue.component('btn-reporte', require('./components/BtnReporte.vue').default);
Vue.component('import', require('./components/Import.vue').default);
Vue.component('search', require('./components/Search.vue').default);
Vue.component('edit-concepto', require('./components/EditConcepto.vue').default);
Vue.component('select-filtro', require('./components/SelectFiltro.vue').default);
Vue.component('pdf', require('./components/pdf.vue').default);

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

import ApolloClient from 'apollo-boost';
import VueApollo from 'vue-apollo';

const connect = new ApolloClient({
    uri: 'http://localhost:3000/graphql',
})

Vue.use(VueApollo);

const apolloProvider = new VueApollo({
    defaultClient: connect,
});

const app = new Vue({
    el: '#app',
    apolloProvider
});
