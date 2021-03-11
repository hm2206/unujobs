<template>
    <form class="col-md-5" id="form-conceptos" v-on:submit="leave">
        <div class="card-header">
            Seleccionar para eliminar
        </div>
        <div class="card-body">
            <div class="row justify-content-start">
                <div class="btn btn-outline-success mb-1 mr-1" 
                    v-for="concepto in conceptos" :key="`concepto-${concepto.id}`"
                >
                    <input type="checkbox" name="conceptos[]" :disabled="loader" :value="concepto.id">
                    <b class="text-danger">{{ concepto.key }}</b>
                    <i class="fas fa-lock text-dark"></i>
                    <b class="text-danger">{{ concepto.descripcion }}</b>
                    <i class="fas fa-arrow-right text-dark"></i> 
                    <span class="btn btn-warning btn-sm">S./{{ concepto.pivot ? concepto.pivot.monto : null }}</span>
                </div>
            </div>
        </div>
        <div class="card-footer">
            <button class="btn btn-danger" :disabled="loader">
                <i class="fas fa-trash"></i> Eliminar conceptos
            </button>
        </div>
    </form>
</template>

<script>


import { unujobs } from '../services/api';
import notify from 'sweetalert';

export default {
    props: ['conceptos', 'param'],
    data() {
        return {
            loader: false
        }
    },
    methods: {
        async leave(e) {
            e.preventDefault();
            this.loader = true;
            const form = new FormData(document.getElementById('form-conceptos'));
            form.append('_method', 'DELETE');
            let api = unujobs('post', `/categoria/${this.param}/concepto`, form);
            await api.then(async res => {
                let { status, message } = res.data;
                let icon = status ? 'success' : 'error';
                await notify({ icon, text: message });
                if (status) {
                    location.href = location.pathname;
                }
            }).catch(err => {
                notify({ icon: 'error', message: 'Algo salió mal' });
            });
            this.loader = false;
        }
    }
}
</script>