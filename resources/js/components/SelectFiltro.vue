<template>
    <div class="row" id="form-cafig-work">

            <div class="col-md-4">
                <div class="form-group">
                    <select name="planilla_id" v-model="form_planilla" :disabled="loading" class="form-control">
                        <option value="">Planilla...</option>
                        <option :value="planilla.id" v-for="(planilla, pla) in planillas" :key="`planilla-${pla}`"
                            v-text="planilla.descripcion"
                        >
                        </option>
                    </select>
                    <small class="text-danger" v-text="errors.planilla_id ? errors.planilla_id[0] : ''"></small>
                </div>
            </div>

            <div class="col-md-4">
                <div class="form-group">
                    <select name="cargo_id" v-model="form_cargo" class="form-control" :disabled="loading">
                        <option value="">Cargo...</option>
                        <option :value="cargo.id" v-for="(cargo, car) in cargos" :key="`cargo-${car}`"
                            v-text="cargo.descripcion"
                        >
                        </option>
                    </select>
                    <small class="text-danger" v-text="errors.cargo_id ? errors.cargo_id[0] : ''"></small>
                </div>
            </div>

            <div class="col-md-4">
                <div class="form-group">
                    <select name="categoria_id" v-model="form_categoria" class="form-control" :disabled="loading">
                        <option value="">Categoria...</option>
                        <option :value="categoria.id" v-for="(categoria, cat) in categorias" :key="`categoria-${cat}`"
                            v-text="categoria.nombre"
                        >
                        </option>
                    </select>
                    <small class="text-danger" v-text="errors.categoria_id ? errors.categoria_id[0] : ''"></small>
                </div>
            </div>

            <div class="col-md-4">
                <div class="form-group">
                    <select name="meta_id" v-model="form_meta" class="form-control" :disabled="loading">
                        <option value="">Meta...</option>
                        <option :value="meta.id" v-for="(meta, met) in metas" :key="`meta-${met}`"
                            v-text="`${meta.metaID}: ${meta.meta}`"
                        >
                        </option>
                    </select>
                    <small class="text-danger" v-text="errors.meta_id ? errors.meta_id[0] : ''"></small>
                </div>
            </div>

            <div class="col-md-4">
                <select name="estado" v-model="estado" class="form-control">
                    <option value="1">Activo</option>
                    <option value="0">Inactivo</option>
                </select>
            </div>
        
    </div>
</template>

<script>

import { unujobs } from '../services/api';
import notify from 'sweetalert';

export default {
    props: ['param', 'infos', 'send', 'planilla_id', 'cargo_id', 'categoria_id', 'meta_id', 'estado_id'],
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
            form_meta: '',
            perfil: '',
            plaza: '',
            escuela: '',
            observacion: '',
            fuente_id: '',
            ruc: '',
            tmp_infos: [],
            estado: ''
        }
    },
    mounted() {
        this.form_planilla = this.planilla_id;
        this.form_cargo = this.cargo_id
        this.form_categoria = this.categoria_id;
        this.form_meta = this.meta_id;
        this.estado = this.estado_id;
        this.getPlanillas();
        this.getMetas();
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
                this.submit();
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
            }else {
                this.loading = false;
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
        async submit(e){

            if (e) {
                e.preventDefault();
            }

            const form = new FormData;
            form.append("planilla_id", this.form_planilla);
            form.append("cargo_id", this.form_cargo);
            form.append("categoria_id", this.form_categoria);
            form.append("meta_id", this.form_meta);
            form.append("perfil", this.perfil);
            form.append("plaza", this.plaza); 
            form.append("escuela", this.escuela);
            form.append("observacion", this.observacion);
            
            if (this.cas) {
                form.append("fuente_id", this.fuente_id); 
                form.append("ruc", this.ruc);
            }
            
            this.loading = true;
            this.errors = {};

            let api = unujobs("post", `/work/${this.param}/config`, form);

            await api.then(res => {

                let { status, message, body } = res.data;
                let icon = status ? 'success' : 'error';
                notify({icon: icon, text: message});

                if (body) {
                    this.tmp_infos = body;
                }

            }).catch(err => {

                let { data } = err.response;
                this.errors = data.errors;

                if (!data.message) {
                    notify({icon: 'error', text: 'Algo salió mal'});
                }

            });

            this.$emit('ready');
            this.loading = false;
        },
        editar(e, object) {
            if (e) {
                e.preventDefault();
            }

            this.form_planilla = object.planilla_id;
            this.form_cargo = object.cargo_id,
            this.form_categoria = object.categoria_id;
            this.form_meta = object.meta_id;
            this.perfil = object.perfil;
            this.plaza = object.plaza;
            this.escuela = object.escuela;
            this.observacion = object.observacion;
            this.fuente_id = object.fuente_id;
            this.ruc = object.ruc;
        },
        destroy(e, object, index) {

            if (e) {
                e.preventDefault();
            }

            let api = unujobs("post", `/work/${this.param}/config`, {
                _method: 'DELETE',
                info: object.id
            });
            api.then(res => {
                let { status, message } = res.data;
                let icon = status ? 'success' : 'error';
                notify({icon: icon, text: message});
                this.infos.splice(index, 1);
            }).catch(err => {
                notify({icon: 'error', text: 'Algo salió mal'});
            });

        }
        
    }
}

</script>

<style scoped>

    .form-control {
        font-size: 14px;
    }

</style>