<template>
    <div class="card-body">

        <form class="row" v-on:submit="generateAfp">
            <div class="col-md-5">
                <select name="afp_id" class="form-control" v-model="afp_id">
                    <option value="">Selecionar AFP</option>
                    <option :value="afp.id" v-for="(afp, af) in afps" :key="`afp-item-${af}`">
                        {{ afp.nombre }}
                    </option>
                </select>
            </div>
            <div class="col-md-3">
                <button class="btn btn-danger" :disabled="afp_id ? false : true">
                    <i class="fas fa-file-pdf"></i>
                    Generar Reporte de AFP
                </button>
            </div>
        </form>

        <br>

        <hr>

        <button class="btn btn-success"
            v-if="!loader"
            v-on:click="generatePDF"
        >
            <i class="fas fa-file-excel"></i> Generar Reporte para el AFP NET
        </button>
    </div>
</template>

<script>

import ReportHistorial from './ReportHistorial';
import { unujobs, printReport } from '../services/api';
import notify from 'sweetalert';

export default {
    props: ['cronograma'],
    components: {
        'historial': ReportHistorial
    },
    data() {
        return {
            loader: false,
            afps: [],
            afp_id: ''
        };
    },
    mounted() {
        this.getAfps();
    },
    methods: {
        getAfps() {
            let api = unujobs("get", "/afp");
            api.then(res => {
                this.afps = res.data;
            }).catch(err => {
                console.log("error en afp");
            });
        },
        async generatePDF() {

            printReport(`pdf/afp/${this.cronograma.id}/${this.afp_id}`, 'AFP NET');

        },
        async generateAfp(e) {
            e.preventDefault();
            printReport(`pdf/afp/${this.cronograma.id}/${this.afp_id}`, 'AFP');
        }
    }
}
</script>