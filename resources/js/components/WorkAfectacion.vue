<template>
    <div class="card-body">
        <form class="row" :id="`form-config-${param}`">

            <div class="col-md-4">
                <div class="form-group">
                    <label for="">Planilla <span class="text-danger">*</span></label>
                    <select name="planilla_id" v-model="form_planilla" :disabled="loading" class="form-control">
                        <option value="">Seleccionar...</option>
                        <option :value="planilla.id" v-for="(planilla, pla) in planillas" :key="`planilla-${pla}`"
                            v-text="`${planilla.id}: ${planilla.descripcion}`"
                        >
                        </option>
                    </select>
                    <small class="text-danger" v-text="errors.planilla_id ? errors.planilla_id[0] : ''"></small>
                </div>
            </div>

            <div class="col-md-4">
                <div class="form-group">
                    <label for="">Cargo <span class="text-danger">*</span></label>
                    <select name="cargo_id" v-model="form_cargo" class="form-control" :disabled="loading">
                        <option value="">Seleccionar...</option>
                        <option :value="cargo.id" v-for="(cargo, car) in cargos" :key="`cargo-${car}`"
                            v-text="`${cargo.id}: ${cargo.descripcion}`"
                        >
                        </option>
                    </select>
                    <small class="text-danger" v-text="errors.cargo_id ? errors.cargo_id[0] : ''"></small>
                </div>
            </div>

            <div class="col-md-4">
                <div class="form-group">
                    <label for="">Categoria <span class="text-danger">*</span></label>
                    <select name="categoria_id" v-model="form_categoria" class="form-control" :disabled="loading">
                        <option value="">Seleccionar...</option>
                        <option :value="categoria.id" v-for="(categoria, cat) in categorias" :key="`categoria-${cat}`"
                            v-text="`${categoria.key}: ${categoria.nombre}`"
                        >
                        </option>
                    </select>
                    <small class="text-danger" v-text="errors.categoria_id ? errors.categoria_id[0] : ''"></small>
                </div>
            </div>

            <div class="col-md-4">
                <div class="form-group">
                    <label for="">Meta <span class="text-danger">*</span></label>
                    <select name="meta_id" v-model="meta_id" class="form-control" :disabled="loading">
                        <option value="">Seleccionar...</option>
                        <option :value="meta.id" v-for="(meta, met) in metas" :key="`meta-${met}`"
                            v-text="`${meta.metaID}: ${meta.meta}`"
                        >
                        </option>
                    </select>
                    <small class="text-danger" v-text="errors.meta_id ? errors.meta_id[0] : ''"></small>
                </div>
            </div>

            <div class="col-md-4">
                <div class="form-group">
                    <label for="">Actividad <span class="text-danger">*</span></label>
                    <select v-model="meta_id" class="form-control" :disabled="true">
                        <option value="">Seleccionar...</option>
                        <option :value="meta.id" v-for="(meta, met) in metas" :key="`meta-${met}`"
                            v-text="`${meta.actividadID}`"
                        >
                        </option>
                    </select>
                    <small class="text-danger" v-text="errors.meta_id ? errors.meta_id[0] : ''"></small>
                </div>
            </div>

            <div class="col-md-4">
                <div class="form-group">
                    <label for="">Ext Pptto <span class="text-danger">*</span></label>
                    <select v-model="form_cargo" class="form-control" :disabled="true">
                        <option value="">Seleccionar...</option>
                        <option :value="cargo.id" v-for="(cargo, car) in cargos" :key="`cargo-${car}`"
                            v-text="cargo.ext_pptto"
                        >
                        </option>
                    </select>
                    <small class="text-danger" v-text="errors.cargo_id ? errors.cargo_id[0] : ''"></small>
                </div>
            </div>

            <div class="col-md-4">
                <div class="form-group">
                    <label for="">Condición P.A.P <span class="text-danger">*</span></label>
                    <select name="pap" v-model="info.pap" class="form-control" :disabled="loading">
                        <option value="">---------</option>
                        <option value="0">Nombrado</option>
                        <option value="1">Contratado</option>
                    </select>
                    <small class="text-danger" v-text="errors.pap ? errors.pap[0] : ''"></small>
                </div>
            </div>

            <div class="col-md-4">
                <div class="form-group">
                    <label for="">Perfil <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" v-model="info.perfil" name="perfil" :disabled="loading">
                    <small class="text-danger" v-text="errors.perfil ? errors.perfil[0] : ''"></small>
                </div>
            </div>

            <div class="col-md-2">
                <div class="form-group">
                    <label for="">Plaza</label>
                    <input type="text" v-model="info.plaza" class="form-control" name="plaza" :disabled="loading">
                </div>
            </div>

            <div class="col-md-2">
                <div class="form-group">
                    <label for="">Escuela </label>
                    <input type="text" v-model="info.escuela" class="form-control" name="escuela" :disabled="loading">
                </div>
            </div>

            <div class="col-md-12">
                <div class="form-group">
                    <label for="">Observación</label>
                    <textarea name="observacion" v-model="observacion" :disabled="loading" class="form-control"></textarea>
                </div>
            </div>

            <div class="col-md-12">
                <input type="checkbox" :disabled="loading" v-model="cas"> Configuración de Cas
            </div>

            <div class="row col-md-12" v-if="cas">
                <div class="col-md-12">
                    <hr>
                    <h5>SOLO CAS</h5>
                </div>

                <div class="col-md-4">
                    <div class="form-group">
                        <label for="">Cod Fuente </label>
                        <input type="text" :disabled="loading" v-model="fuente_id" class="form-control" name="fuente_id">
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="form-group">
                        <label for="">Nom Fuente </label>
                        <input type="text" :disabled="loading" v-model="fuente" class="form-control" name="fuente">
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="form-group">
                        <label for="">RUC</label>
                        <input type="text" :disabled="loading" class="form-control" v-model="ruc" name="ruc">
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
    props: ['param', 'info', 'send', 'mes', 'year', 'adicional', 'numero', 'tmp_cronograma'],
    data() {
        return {
            cas: false,
            loading: false,
            planillas: [],
            cargos: [],
            categorias: [],
            metas: [],
            errors: {},
            form_planilla: '',
            form_cargo: '',
            form_categoria: '',
            meta_id: '',
            fuente_id: '',
            ruc: '',
            tmp_infos: [],
            observacion: ''
        }
    },
    mounted() {
        this.form_planilla = this.info.planilla_id;
        this.form_cargo = this.info.cargo_id;
        this.form_categoria = this.info.categoria_id;
        this.meta_id = this.info.meta_id;
        this.getPlanillas();
        this.getMetas();
        this.getObservacion();
    },
    watch: {
        form_planilla(nuevo) {
            this.changePlanilla();
        },
        form_cargo(nuevo) {
            this.changeCargo();
        },
        fuente_id(nuevo) {
            if (nuevo) {
                this.cas = true;
            }
        },
        ruc(nuevo) {
            if (nuevo) {
                this.cas = true;
            }
        },
        send(nuevo) {
            if (nuevo) {
                this.setObservacion();
            }
        }
    },  
    methods : {
        getPlanillas() {
            let api = axios.get('/api/v1/planilla');
            this.loading = true;
            api.then(res => {
                let { data } = res;
                this.planillas = data;
                this.loading = false;
            }).catch(err => {
                this.loading = false;
            });
        },
        changePlanilla() {
            this.loading = true;
            this.cargos = [];
            this.categorias = [];

            if (this.form_planilla) {
                this.getPlanillaID(this.form_planilla);
            }

        },
        getPlanillaID(id) {
            let api = axios.get(`/api/v1/planilla/${id}`);
            this.loading = true;
            api.then(res => {
                this.loading = false;
                let { data } = res;
                this.cargos = data.cargos;
                this.loading = false;
            }).catch(err => {

            });
        },
        changeCargo() {
            this.loading = true;
            this.categorias = [];

            if (this.form_cargo) {
                this.getCargoID(this.form_cargo);
            }
        },
        getCargoID(id) {
            let api = axios.get(`/api/v1/cargo/${id}`);
            this.loading = true;
            api.then(res => {
                let { data } = res;
                this.categorias = data.categorias;
                this.loading = false;
            }).catch(err => {
                this.loading = false;
            });
        },
        getMetas() {
            let api = axios.get('/api/v1/meta');
            this.loading = true;
            api.then(res => {
                let { data } = res;
                this.metas = data;
                this.loading = false;
            }).catch(err => {
                this.loading = false;
            });
        },
        getObservacion() {
            let adicional = this.adicional ? 1 : 0;
            let api = unujobs("get", `/info/${this.param}/observacion?mes=${this.mes}&year=${this.year}&adicional=${adicional}&numero=${this.numero}`)
            api.then(res => {
                this.observacion = res.data;
            }).catch(err => {
                console.log("no se pudo obtener la observación");
            });
        },
        async setObservacion() {
            const form = new FormData(document.getElementById(`form-config-${this.param}`));
            this.loading = true;
            this.errors = {};
            form.append("cronograma_id", this.tmp_cronograma.id);
            form.append("_method", "PUT");
            
            let api = unujobs("post", `/info/${this.info.id}`, form);
            await api.then(res => {
                let { status, message } = res.data;
                let icon = status ? 'success' : 'error';
                notify({icon, text: message});
            }).catch(err => {
                let { data } = err.response;

                if (Object.keys(data.errors).length > 0) {
                    this.errors = data.errors;
                }else {
                    notify({icon: 'error', text: 'Algo salió mal :('});
                }
            });

            this.loading = false;
            this.$emit("ready");
        }
        
    }
}

</script>

<style scoped>
    .form-control {
        font-size: 14px !important;
        font-weight: bold;
    }
</style>