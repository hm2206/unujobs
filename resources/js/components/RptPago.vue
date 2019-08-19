<template>
    <div class="card-body">
        
        <form action="" class="row justify-content-center" id="register-pagos"
            v-on:submit="generatePDF"
        >
            <div class="col-md-4 text-center">
                <input type="checkbox" name="cheque"> Cheque
            </div>
            <div class="col-md-4 text-center">
                <input type="checkbox" name="cuenta"> Cuenta
            </div>
            <div class="col-md-12 mt-5">
                <div class="w-100 text-center" v-if="!loader">
                    <button class="btn btn-danger">
                        <i class="fas fa-file-pdf"></i> Generar 
                    </button>
                </div>

                <div class="w-100 text-center" v-else>
                    <div class="spinner-border text-primary"></div>
                </div>
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
            loader: false
        };
    },
    methods: {
        async generatePDF(e) {

            e.preventDefault();
            const form = new FormData(document.getElementById('register-pagos'));
            form.append('type_report_id', this.report.id);
            this.loader = true; 
            let api = unujobs("post", `/cronograma/${this.cronograma.id}/pago`, form);
            
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