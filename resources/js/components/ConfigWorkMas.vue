<template>
    <div class="card-body">

        <form class="card mt-4" id="store-retencion" v-on:submit="storeRetencion">
            <div class="card-header">
                <small class="btn btn-sm btn-dark btn-circle">
                    <i class="fas fa-cog"></i>    
                </small> Configurar Retenciones y Aportes automáticos
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4 mb-2" v-for="(retencion, ret) in retenciones" :key="`retencion-${ret}`">
                        <label :class="`btn btn-sm btn-${retencion.checked ? 'primary' : 'danger'}`" 
                            :for="`ret-${ret}`"
                        >
                           {{ retencion.base ? 'APORT' : 'RET' }}
                            <input type="checkbox" :id="`ret-${ret}`" name="retenciones[]" 
                                :value="retencion.id"
                                :checked="retencion.checked"
                                :disabled="loader"
                            >
                       </label>
                       {{ retencion.descripcion }}
                    </div>
                </div>
            </div>
            <div class="card-footer text-right">
                <button class="btn btn-primary"
                    :disabled="loader"
                    v-on:click="storeRetencion"
                >
                    <i class="fas fa-sync"></i> Actualizar
                </button>
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
            tmp_sindicatos: [],
            retenciones: [],
            loader: false
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
        this.getRentenciones();
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

        },
        getRentenciones(e) {

            let api = unujobs("get", `/work/${this.param}/retencion`);
            api.then(res => {
                this.retenciones = res.data;
            }).catch(err => {
                console.log("algo salió mal en al traer las retenciones");
            });

        },
        async storeRetencion(e) {

            if (e) {
                e.preventDefault();
            }
            
            this.loader = true;
            const form = new FormData(document.getElementById('store-retencion'));
            let api = unujobs("post", `/work/${this.param}/retencion`, form);
            await api.then(res => {
                let { status, message } = res.data;
                let icon = status ? 'success' : 'error';
                notify({icon, text: message});
                console.log(res.data);
                this.getRentenciones();
            }).catch(err => {
                notify({icon: 'error', text: 'Algo salió mal'});
            });

            this.loader = false;

        }
    }
}
</script>