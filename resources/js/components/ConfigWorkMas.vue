<template>
    <div class="card-body">

        <div class="card mb-2" v-for="(info, inf) in tmp_infos" :key="`info-${inf}`">
            <div class="card-header">
                Configurar Aportaciones de 
                <span class="btn btn-sm btn-danger">
                    {{ info.categoria ? info.categoria.nombre : '' }}
                </span>
            </div>
            <div class="card-body">
                <div class="row" :id="`type_aportacion_${info.id}`">
                    <div class="col-md-5">
                        <select name="type_aportacion_id" 
                            class="form-control"
                            v-model="type_aportacion_id"
                        >
                            <option value="">Seleccionar...</option>
                            <option :value="type.id" v-for="(type, ty) in type_aportes" :key="`type_aporte_${ty}`">
                                {{ type.descripcion }}
                            </option>
                        </select>
                    </div>
                    <div class="col-md-5">
                        <button class="btn btn-success" 
                            :disabled="block"
                            v-if="!loader"
                            v-on:click="agregarAportacion(info)"
                        >
                            <i class="fas fa-plus"></i> Agregar
                        </button>
                        <div v-if="loader" class="spinner-border text-warning"></div>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <div class="row">
                    <div class="col-xs mr-1" 
                        v-for="(aporte, aport) in info.type_aportaciones" 
                        :key="`aportacion-${aport}`"
                    >
                        <button class="btn btn-sm btn-dark" v-on:click="deleteAportacion(info, aporte.id)">
                            {{ aporte.descripcion }} <i class="fas fa-times text-danger"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        
    </div>
</template>

<script>

import { unujobs } from '../services/api';
import notify from 'sweetalert';

export default {
    props: ['infos'],
    data() {
        return {
            block: true,
            loader: false,
            type_aportes: [],
            tmp_infos: [],
            type_aportacion_id: ''
        };
    },
    mounted() {
        this.tmp_infos = this.infos;
        this.getTypeAportes();
    },
    watch: {
        type_aportacion_id(nuevo) {
            this.block = nuevo ? false : true;
        }
    },
    methods: {
        async getTypeAportes() {
            let api = unujobs('get', '/type_aportacion');
            this.loader = true;
            await api.then(res => {
                this.type_aportes = res.data;
            }).catch(err => {
                console.log('algo salió mal al obtener los tipos de aportaciones');
            });
            this.loader = false;
        },
        async agregarAportacion(info) {
            this.loader = true;
            this.block = true;
            let api = unujobs('post', `/info/${info.id}/type_aportacion`, {
                type_aportacion_id: this.type_aportacion_id
            });
            await api.then(async res => {
                let { aportaciones, status, message } = res.data;
                let icon = status ? 'success' : 'error';
                notify({ icon, text: message });
                this.type_aportacion_id = '';
                // actualizamos los infos
                this.tmp_infos = await this.infos.filter( obj => {
                    obj.type_aportaciones = aportaciones;
                    return obj;
                });
            }).catch(err => {
                notify({ icon: 'error', text: 'Algo salió mal' });
            });
            this.loader = false;
        },
        async deleteAportacion(info, id) {
            this.loader = true;
            let api = unujobs('post', `/info/${info.id}/delete_aportacion`, {
                type_aportacion_id: id,
                _method: 'DELETE'
            });

            await api.then(res => {
                let { status, message, aportaciones } = res.data;
                let icon = status ? 'success' : 'error';
                notify({ icon, text: message });
                info.type_aportaciones = aportaciones;
            }).catch(err => {
                notify({ icon: 'error', text: 'Algo salió mal' });
            });
            this.loader = false;
        } 
    }   
}
</script>