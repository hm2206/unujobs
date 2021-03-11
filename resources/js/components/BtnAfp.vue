<template>
    <button :class="`btn ${theme}`" v-on:click="send" :disabled="loader">
        <slot></slot>
    </button>
</template>


<script>

import { unujobs } from '../services/api';
import notify from 'sweetalert';

export default {
    props: ['theme', "id"],
    data() {
        return {
            loader: false
        }
    },
    methods: {
        send() {

            let api = unujobs("post", `/export/cronograma/${this.id}/afp`);

            api.then(res => {
                
                let { status, message } = res.data;

                let icon = status ? "success" : "error";

                notify({icon, text: message});

            }).catch(err => {
                
                notify({icon: 'error', text: "Algo salió mal"});
                
            });

        }
    }
}
</script>


<style scoped>
    .p-relative {
        position: relative;
    }

    .btn-fixed {
        position: absolute;
        bottom: 20px;
        right: 20px;
    }

    .min-height {
        height: 100%;
    }
</style>

