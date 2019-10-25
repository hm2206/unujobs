<template>
    <button :class="`btn btn-circle btn-sm btn-${active ? 'danger' : 'success'}`"
        :titulo="active ? 'Desactivar' : 'Activar'"
        v-on:click="switchState"
    >
        <i :class="`fas fa-${active ? 'times' : 'check'}`"></i>
    </button>
</template>

<script>

import { unujobs } from '../services/api';
import notify from 'sweetalert';

export default {
    props: ['active', 'param'],
    methods: {
        async switchState() {
            let newActive = this.active ? 0 : 1;
            let answer = await confirm(`Está seguro en continuar la operación (${this.active ? 'Desactivar' : 'Activar'})`);
            let api = unujobs('post', `/info/${this.param}/active`, {active: newActive});
            // realizar petición
            if (answer) {
                await api.then(res => {
                    location.href = `/planilla/job?estado=${res.data}`;
                }).catch(err => notify({ icon: 'error', text: 'Algo salió mal' }));
            }
        }
    }
}
</script>