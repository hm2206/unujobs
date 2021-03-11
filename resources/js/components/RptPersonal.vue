<template>
    <div class="card-body">

        <form class="row" v-on:submit="generatePDF">
            <div class="col-md-5">
                <select name="condicion" class="form-control" v-model="condicion">
                    <option value="0">Todos</option>
                    <option value="1">Saldos Negativos</option>
                </select>
            </div>
            <div class="col-md-3">
                <button class="btn btn-danger" :disabled="loader">
                    <i class="fas fa-file-pdf"></i>
                    Generar Reporte PDF
                </button>
            </div>
        </form>

        <hr>

        <historial v-if="!loader" :param="cronograma.id" :type="report.id"></historial>
    </div>
</template>

<script>

import ReportHistorial from './ReportHistorial';
import { unujobs } from '../services/api';
import notify from 'sweetalert';

export default {
    props: ['report', 'cronograma'],
    components: {
        'historial': ReportHistorial
    },
    data() {
        return {
            loader: false,
            condicion: 0,
        };
    },
    methods: {
        async generatePDF(e) {

            e.preventDefault();
            this.loader = true;
            let api = unujobs("post", `/cronograma/${this.cronograma.id}/personal`, {
                type_report: this.report.id,
                condicion: this.condicion
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
    }
}
</script>