<template>
    <div class="card-body">

        <form class="row" v-on:submit="generatePDF">
            <div class="col-md-5">
                <select name="condicion" class="form-control" v-model="condicion">
                    <option value="0">Todos los saldos</option>
                    <option value="1">Saldos Negativos</option>
                </select>
            </div>

            <div class="col-md-3">
                <select name="condicion" class="form-control" v-model="cargo_id">
                    <option value="">Todos los cargos</option>
                    <option :value="cargo.id" v-for="cargo in cargos" :key="cargo.id">{{ cargo.descripcion }}</option>
                </select>
            </div>
            
            <div class="col-xs">
                <button class="btn btn-danger" :disabled="loader">
                    <i class="fas fa-file-pdf"></i>
                    Generar Reporte PDF
                </button>
            </div>
        </form>
    </div>
</template>

<script>

import ReportHistorial from './ReportHistorial';
import { unujobs, printReport } from '../services/api';
import notify from 'sweetalert';

export default {
    props: ['report', 'cronograma', 'cargos'],
    components: {
        'historial': ReportHistorial
    },
    data() {
        return {
            loader: false,
            condicion: 0,
            cargo_id: "",
        };
    },
    methods: {
        async generatePDF(e) {

            e.preventDefault();
            printReport(`pdf/relacion-personal/${this.cronograma.id}?negativo=${this.condicion}&cargo_id=${this.cargo_id}`, 'RELACION PERSONAL');

        }
    }
}
</script>