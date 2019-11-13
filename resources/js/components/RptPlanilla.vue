<template>
    <div class="card-body">

        <div class="row">
            <div class="col-md-5">
                <select name="meta_id" class="form-control" v-model="meta_id">
                    <option :value="meta.id" v-for="(meta, me) in metas" :key="`metas-${me}`">
                        {{ meta.metaID }}: {{ meta.meta }}
                    </option>
                </select>
            </div>
        </div>

        <hr>

        <button class="btn btn-danger"
            v-if="!loader"
            v-on:click="generatePDF"
        >
            <i class="fas fa-file-pdf"></i> Generar Planilla
        </button>

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
        };
    },
    mounted() {
        for(let meta of this.metas) {
            this.meta_id = meta.id;
            break;
        }
    },
    methods: {
        async generatePDF() {
            printReport(`pdf/planilla/${this.cronograma.id}/${this.meta_id}`, 'PLANILLA');
        }
    }
}
</script>