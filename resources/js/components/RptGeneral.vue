<template>
    <div class="card-body">
        <button class="btn btn-danger"
            v-if="!loader"
            v-on:click="generatePDF"
        >
            <i class="fas fa-file-pdf"></i> Generar Reporte
        </button>

        <hr>
    </div>
</template>

<script>

import { unujobs, printReport } from '../services/api';
import notify from 'sweetalert';

export default {
    props: ['cronograma'],
    data() {
        return {
            loader: false
        };
    },
    methods: {
        async generatePDF() {
            printReport(`pdf/general-v1/${this.cronograma.id}`, 'GENERAL-v1');
        }
    }
}
</script>