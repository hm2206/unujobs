<template>
    <div class="btn btn-outline-success mb-1 mr-1" v-on:click="edit">
        <b class="text-danger">{{ concepto.key }}</b>
        <i class="fas fa-lock text-dark"></i>
        <b class="text-danger">{{ concepto.descripcion }}</b>
        <i class="fas fa-arrow-right text-dark"></i> 
        <span class="btn btn-warning btn-sm">S./{{ concepto.pivot ? concepto.pivot.monto : null }}</span>
    </div>
</template>

<script>


import { unujobs } from '../services/api';
import notify from 'sweetalert';

export default {
    props: ['concepto'],
    methods: {
        edit(e) {
            let newMonto = prompt("ingrese un nuevo monto valido");

            if (newMonto > 0) {
                
                const form = new FormData();
                form.append('_method', 'PUT');
                form.append('monto', newMonto);
                let api = unujobs("post", `/categoria/${this.concepto.id}/concepto`, form);
                api.then(res => {

                    console.log(this.concepto.id);
                    let { status, message } = res.data; 
                    let icon = status ? 'success' : 'error';
                    notify({icon, text: message});
                }).catch(err => {
                    notify({icon: 'error', text: 'Algo salió mal'});
                })

            } 
        }
    }
}
</script>