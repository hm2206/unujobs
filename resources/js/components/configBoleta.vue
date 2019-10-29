<template>
    <div class="btn-group">
        <button v-if="!show" :class="`btn ${theme}`" v-on:click="show = true">
            <slot></slot>
        </button>

        
        <modal @close="show = false" col="col-md-11" :show="show" height="90vh">
            <template slot="header">
                Configurar boleta
                <i class="fas fa-arrow-right text-danger"></i> 
                <span v-text="cargo.descripcion"></span>
            </template>
            <template slot="content">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 h-100">
                            <form class="row" id="form-add">
                                <div class="col-md-11">
                                    <select name="type_remuneraciones[]" 
                                        class="form-control" 
                                        multiple="" 
                                        :disabled="!isTypes || loader"
                                    >
                                        <option :value="type.id" 
                                            v-for="type in types"
                                            :key="`type-${type.id}`"
                                        >
                                        {{ type.key }}.-{{ type.descripcion }}
                                        </option>
                                    </select>
                                </div>
                                <div class="col-md-1">
                                    <button class="btn btn-sm btn-primary"
                                        v-on:click="add"
                                        :disabled="loader"
                                    >
                                        <i class="fas fa-arrow-right"></i>
                                    </button>
                                </div>
                            </form>
                        </div>
                        <div class="col-md-6 design">
                            <div class="card-boleta">
                                <div class="card-boleta-header">
                                    <table class="w-100 py-0">
                                        <tr>
                                            <th class="py-0">Boleta de Pago N°</th>
                                            <th class="py-0">Fecha de ingreso</th>
                                        </tr>
                                        <tr>
                                            <th colspan="2" class="py-0">A.F.P</th>
                                        </tr>
                                        <tr>
                                            <th class="py-0" colspan="2">Nombres y Apellidos</th>
                                        </tr>
                                        <tr>
                                            <th class="py-0" colspan="2">Condición Laboral</th>
                                        </tr>
                                        <tr>
                                            <th class="py-0" colspan="2">Cargo</th>
                                        </tr>
                                    </table>
                                </div>
                                <div class="card-boleta-body">
                                    <table class="w-100">
                                        <tr>
                                            <th class="py-1 text-center">INGRESOS</th>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                            <div class="card-boleta-content">
                                <div class="row pl-2 py-1 align-items-center"
                                    v-for="(design, d) in designs" :key="`boleta-design-${d}`"
                                >
                                    <div class="col-md-10">
                                       <div class="row pl-2">
                                            <button 
                                                class="btn btn-times btn-sm btn-danger"
                                                :disabled="loader"
                                                v-on:click="leave(design.id)"
                                            >
                                                <i class="fas fa-times"></i>
                                            </button> 
                                        <div class="pl-1">{{ design.key }}.-{{ design.descripcion.substring(0, 40) }}</div>
                                       </div>
                                    </div>
                                    <div class="col-md-2">0.00</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </template>
        </modal>
    </div>
</template>

<script>

import { unujobs } from '../services/api';
import notify from 'sweetalert';

export default {
    props: ["theme", "cargo"],
    data() {
        return {
            show: false,
            loader: true,
            designs: [],
            types: []
        }
    },
    watch: {
        async show(nuevo, old) {
            if (nuevo) {
                await this.getTypeRemuneracion();
                await this.getTypeCargos();
            }
        }
    },
    methods: {
        getTypeRemuneracion() {
            let api = unujobs('get', '/type_remuneracion');
            api.then(res => {
                this.types = res.data;
            }).catch(err => {

            });
        },
        async getTypeCargos() {
            let api = unujobs('get', `/categoria/${this.cargo.id}/type_remuneracion`);
            await api.then(async res => {
                this.designs = await res.data;
                // quitamos los types que ya fuerón agregados
                this.types = await this.types.filter( async type => {
                    return await this.designs.filter(obj => {
                         return obj.type_remuneracion_id != type.id;
                    });
                });
            }).catch(err => {
                console.log('algo salió mal');
            });
            this.loader = false;
        },
        async add(e) {
            e.preventDefault();
            let answer = await confirm('¿Estás seguro en continuar.. ?');
            if (answer) {
                this.loader = true;
                const form = new FormData(document.getElementById('form-add'));
                let api = unujobs('post', `/categoria/${this.cargo.id}/type_remuneracion`, form);
                await api.then(res => {
                    let { status, message } = res.data;
                    let icon = status ? 'success' : 'error';
                    notify({ icon, text: message });
                    this.getTypeCargos();
                }).catch(err => {
                    notify({ icon: 'error', text: 'Algo salió mal' });
                });
            }
            this.loader = false;
        },
        async leave(id) {
            let answer = await confirm('¿ Está seguro en continuar ?');
            if (answer) {
                this.loader = true;
                let api = unujobs('post', `/categoria/${this.cargo.id}/type_remuneracion`, { 
                    _method: 'DELETE',
                    type_remuneracion_id: id
                });

                await api.then(res => {
                    let { status, message } = res.data;
                    let icon = status ? 'success' : 'error';
                    notify({ icon, text: message });
                    this.getTypeCargos();
                }).catch(err => {
                    notify({ icon: 'error', text: 'Algo salió mal' });
                });
            }

            this.loader = false;
        } 
    },
    computed: {
        isTypes() {
            return this.types.length > 0 ? true : false;
        }
    }
}
</script>


<style scoped>

    .form-control {
        min-height: 400px;
    }

    .card-body {
        position: relative;
        overflow-y: auto;
    }

    .design {
        color: rgba(0, 0, 0, 0.8);
        font-size: 10px;
        position: relative;
        overflow-y: auto;
    }

    .card-boleta {
        width: 100%;
        border: 1px solid rgba(0, 0, 0, 0.8);
        border-radius: 0.5em;
        border-right: 0px;
        border-top-right-radius: 0px;
        border-bottom-right-radius: 0px;
    }

    .card-boleta-header {
        border-bottom: 1px solid rgba(0, 0, 0, 0.8);
    }

    .card-boleta-header table {
        border: 0px;
        margin-top: 0.3em; 
    }

        .card-boleta-header table th {
            border: 0px;
        }

    .border-right {
        border-right: 1px solid rgba(0, 0, 0, 0.8)!important;
    }

    .btn-times {
        width: 20px;
        height: 20px;
        padding: 0px;
        display: flex;
        justify-content: center;
        align-items: center;
    }

    .card-boleta-content {
        font-size: 12px;
        height: auto;
    }

</style>
