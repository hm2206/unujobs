<template>
    <form class="card-body" id="form-rpt-descuento">

        <div class="row mb-4">

            <div class="col-md-3" v-for="(type, ty) in tmp_types" :key="`type-descuentos-${ty}`">
                <input type="checkbox" name="type_descuentos[]" :value="type.id"> {{ type.descripcion }}
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
    </form>
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

            const form = new FormData(document.getElementById('form-rpt-descuento'));
            form.append('type_report_id', this.report.id);
            this.loader = true;
            let api = unujobs("post", `/cronograma/${this.cronograma.id}/descuento`, form);
            
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