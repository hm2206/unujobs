<template>
    <div class="card-body">
        
        <form action="" class="row justify-content-center" id="register-pagos"
            v-on:submit="generatePDF"
        >
            <div class="col-md-5">
                <select name="condicion" class="form-control" v-model="condicion">
                    <option value="0" selected>Cheque</option>
                    <option value="1">Cuenta</option>
                </select>
            </div>
            <div class="col-md-12 mt-5">
                <div class="row justify-content-center">
                    <div class="col-xs" v-if="!loader">
                        <button class="btn btn-danger">
                            <i class="fas fa-file-pdf"></i> Generar PDF
                        </button>
                    </div>

                    <div class="w-100 text-center" v-else>
                        <div class="spinner-border text-primary"></div>
                    </div>

                </div>
            </div>

            <div class="w-100 text-center" v-if="!loader">

                <hr>

                <a class="btn btn-dark" :href="`/api/v1/file/${cronograma.id}`" target="__blank">
                    <i class="fas fa-file-pdf"></i> Generar txt de las cuentas 
                </a>

                <a class="btn btn-dark" :href="`/api/v1/file/judicial/${cronograma.id}`" target="__blank">
                    <i class="fas fa-file-pdf"></i> Generar txt Judiciales 
                </a>
            </div>
        </form>

    </div>
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
    data() {
        return {
            loader: false,
            condicion: 0,
        };
    },
    methods: {
        async generatePDF(e) {

            e.preventDefault();
            printReport(`pdf/pago/${this.cronograma.id}?cuenta=${this.condicion}`, 'PAGO');

        }
    }
}
</script>