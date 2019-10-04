<template>
    <div class="card-body">

        <button class="btn btn-outline-danger"
            v-on:click="generatePDF('descuento-bruto')"
            v-if="!loader"
        >
            <i class="fas fa-file-pdf"></i> Ver Descuento Bruto
        </button>

        <button class="btn btn-outline-danger"
            v-on:click="generatePDF('descuento-bruto-detallado')"
            v-if="!loader"
        >
            <i class="fas fa-file-pdf"></i> Ver Descuento Detallado Bruto
        </button>

        <button class="btn btn-danger"
            v-on:click="generatePDF('descuento-neto')"
            v-if="!loader"
        >
            <i class="fas fa-file-pdf"></i> Ver Descuento Neto
        </button>

        <button class="btn btn-danger"
            v-on:click="generatePDF('descuento-neto-detallado')"
            v-if="!loader"
        >
            <i class="fas fa-file-pdf"></i> Ver Descuento Detallado Neto
        </button>

        <hr>

        <historial v-if="!loader" :param="cronograma.id" :type="report.id"></historial>
    </div>
</template>

<script>

import ReportHistorial from './ReportHistorial';
import { unujobs } from '../services/api';
import notify from 'sweetalert';

export default {
    props: ['report', 'cronograma', 'metas'],
    components: {
        'historial': ReportHistorial
    },
    data() {
        return {
            loader: false,
            meta_id: '',
        };
    },
    methods: {
        async generatePDF(url) {

            this.loader = true;
            let api = unujobs("post", `/cronograma/${this.param}/${url}`, {
                type_report_id: this.report.id
            });
            
            await api.then(res => {
                let { status, message } = res.data;
                let icon = status ? 'success' : 'error';
                notify({icon, text: message});
            }).catch(err => {
                notify({icon: 'error', text: 'Algo salió mal'});
            });

            this.loader = false;

        }
    },
    computed: {
        param() {
            return this.cronograma.id;
        }
    }
}
</script>