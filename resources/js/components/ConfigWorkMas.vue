<template>
    <div class="card-body">
        <form class="card" id="data-sindicatos" v-on:submit="storeSindicato">
            <div class="card-header">
                Configurar Sindicatos
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4" v-for="(sindicato, sin) in tmp_sindicatos" :key="`sindicato-${sin}`">
                        <input type="checkbox" name="sindicatos[]" :value="sindicato.id" :checked="sindicato.checked">
                        {{ sindicato.nombre }}
                    </div>
                    <div class="col-md-12 mt-5">
                        <hr>
                        <button class="btn btn-success">
                            <i class="fas fa-save"></i> Guardar
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</template>

<script>

import { unujobs } from '../services/api';
import notify from 'sweetalert';

export default {
    props: ['sindicatos', 'param'],
    data() {
        return {
            tmp_sindicatos: []
        };
    },
    watch: {
        tmp_sindicatos(nuevo) {
            for(let sin of this.sindicatos) {
                for(let tmp_sin of nuevo) {
                    if (sin.id == tmp_sin.id) {
                        tmp_sin.checked = true;
                        break;
                    }else {
                        tmp_sin.checked = false;
                    }
                }
            }
        }
    },
    mounted() {
        this.getSindicatos();
    },
    methods: {
        getSindicatos() {

            let api = unujobs("get", "/sindicato");
            api.then(res => {
                this.tmp_sindicatos = res.data; 
            }).catch(err => {
                notify({icon: 'error', text: err.message});
            });

        },
        storeSindicato(e) {
            if (e) {
                e.preventDefault();
            }

            const form = new FormData(document.getElementById('data-sindicatos'));
            let api = unujobs("post", `/work/${this.param}/sindicato`, form);
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