<template>
    <button :class="`btn btn-primary ${theme}`"
        v-on:click="send"
    >
        <slot></slot>
    </button>
</template>


<script>

import { unujobs } from '../services/api';
import notify from 'sweetalert';

export default {
    props: ['theme', 'year', 'mes'],
    methods: {
        send(e) {

            let api = unujobs("post", `/export/mef/${this.year}/${this.mes}`);

            api.then(res => {

                let { status, message } = res.data;

                let icon = status ? 'success' : 'error';

                notify({icon, text: message});

            }).catch(err => {

                notify({icon: 'error', text: 'Algo salió mal'});

            });

        }
    }
}
</script>
