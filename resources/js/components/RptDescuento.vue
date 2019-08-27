<template>
    <div class="card-body">

        <div class="row mb-4">

            <div class="col-md-3" v-for="(type, ty) in tmp_types" :key="`type-descuentos-${ty}`">
                <input type="checkbox" name="type_descuentos[]" id=""> {{ type.descripcion }}
            </div>

        </div>

        <hr>

        <button class="btn btn-danger"
            v-if="!loader"
            v-on:click="generatePDF"
        >
            <i class="fas fa-file-pdf"></i> Generar Reporte de Descuentos
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
    props: ['report', 'cronograma'],
    components: {
        'historial': ReportHistorial
    },
    mounted() {
        this.getDescuentos();
    },
    data() {
        return {
            loader: false,
            tmp_types: []
        };
    },
    methods: {
        async generatePDF() {

            this.loader = true;
            let api = unujobs("post", `/cronograma/${this.cronograma.id}/descuento`, {
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

        },
        async getDescuentos() {
            let api = unujobs("get", '/type_descuento');
            this.loader = true;
            await api.then(res => {
                this.tmp_types = res.data;
            }).catch(err => {
                console.log("error: no se pudó obtener los tipos de descuentos");
            });
            this.loader = false;
        }
    }
}
</script>