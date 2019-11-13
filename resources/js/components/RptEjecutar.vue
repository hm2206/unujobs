<template>
    <div class="card-body">

        <div class="row">
            <div class="col-md-5">
                <select class="form-control" v-model="ejecucion" id="">
                    <option value="">Seleccionar ejecución</option>
                    <option :value="{url: 'ejecucion', neto: '0'}">Ejecución Bruta</option>
                    <option :value="{url: 'ejecucion-detalle', neto: '0'}">Ejecución Bruta Detallada</option>
                    <option :value="{url: 'ejecucion', neto: '1'}">Ejecución Neta</option>
                    <option :value="{url: 'ejecucion-detalle', neto: '1'}">Ejecución Neta Detallada</option>
                </select>
            </div>

            <button class="btn btn-danger btn-sm"
                v-on:click="generatePDF"
                v-if="!loader"
                :disabled="!ejecucion.url"
            >
                <i class="fas fa-file-pdf"></i> Ver Reporte
            </button>
        </div>

    </div>
</template>

<script>

import ReportHistorial from './ReportHistorial';
import { unujobs, printReport } from '../services/api';
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
            ejecucion: ""
        };
    },
    methods: {
        async generatePDF() {

            printReport(`pdf/${this.ejecucion.url}/${this.cronograma.id}?neto=${this.ejecucion.neto}`);

        }
    },
    computed: {
        param() {
            return this.cronograma.id;
        }
    }
}
</script>