<template>
    <form class="card-body" id="form-rpt-descuento">

        <div class="row mb-4 align-items-center">

            <div class="col-md-4">
                <select name="type_descuento" class="form-control" v-model="descuento_id">
                    <option value="">TODOS LOS DESCUENTOS</option>
                    <option :value="type.id" v-for="(type, ty) in tmp_types" :key="`type-descuentos-${ty}`">
                        {{ type.descripcion }}
                    </option>
                </select>
            </div>

            <div class="col-md-3">
                <input type="checkbox" name="detalle" value="1"> Detalles
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
import { unujobs, printReport } from '../services/api';
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
            tmp_types: [],
            descuento_id: '',
        };
    },
    methods: {
        async generatePDF() {

            let ruta = this.descuento_id 
                ? `pdf/descuento/${this.cronograma.id}/${this.descuento_id}`
                : `pdf/descuentos/${this.cronograma.id}`;

            printReport(ruta, 'Descuento');
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