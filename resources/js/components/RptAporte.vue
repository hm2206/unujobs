<template>
    <form class="card-body" id="form-rpt-descuento">

        <div class="row mb-4 align-items-center">

            <div class="col-md-4">
                <select name="type_descuento" class="form-control" v-model="aporte_id">
                    <option value="">Selecionar aportación empleador</option>
                    <option :value="type.id" v-for="(type, ty) in tmp_types" :key="`type-aportes-${ty}`">
                        {{ type.descripcion }}
                    </option>
                </select>
            </div>

            <div class="col-xs">
                <button class="btn btn-danger"
                    v-on:click="generatePDF"
                    :disabled="!aporte_id"
                >
                    <i class="fas fa-file-pdf"></i> Generar Reporte de Aportación
                </button>
            </div>

        </div>
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
            aporte_id: '',
        };
    },
    methods: {
        async generatePDF(e) {

            e.preventDefault();
            printReport(`pdf/aportacion/${this.cronograma.id}/${this.aporte_id}`, 'Descuento');
        },
        async getDescuentos() {
            let api = unujobs("get", '/type_aportacion');
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