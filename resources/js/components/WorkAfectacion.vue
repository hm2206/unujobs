<template>
    <div class="card-body">
        <form class="row" id="form-config-work" v-on:submit="submit">

            <div class="col-md-4">
                <div class="form-group">
                    <label for="">AFP</label>
                    <select name="afp_id" v-on:change="changeSelect($event, 'getTypeAfp')" v-model="form.afp_id" :disabled="loading" class="form-control">
                        <option value="">Seleccionar...</option>
                        <option :value="afp.id" v-for="(afp, af) in afps" :key="`afp-${af}`"
                            v-text="afp.nombre"
                        >
                        </option>
                    </select>
                    <small class="text-danger" v-text="errors.afp_id ? errors.afp_id[0] : ''"></small>
                </div>
            </div>

            <div class="col-md-4">
                <div class="form-group">
                    <label for="">Tipo de AFP</label>
                    <select name="type_afp_id" v-model="form.type_afp_id" :disabled="loading" class="form-control">
                        <option value="">Seleccionar...</option>
                        <option :value="type.id" v-for="(type, ty) in typeAfps" :key="`type-afp-${ty}`"
                            v-text="type.descripcion"
                        >
                        </option>
                    </select>
                    <small class="text-danger" v-text="errors.type_afp_id ? errors.type_afp_id[0] : ''"></small>
                </div>
            </div>

            <div class="col-md-4">
                <div class="form-group">
                    <label for="">Numero de Cussp</label>
                    <input type="text" name="numero_de_cussp" v-model="form.numero_de_cussp" class="form-control">
                    <small class="text-danger" v-text="errors.numero_de_cussp ? errors.numero_de_cussp[0] : ''"></small>
                </div>
            </div>

            <div class="col-md-4">
                <div class="form-group">
                    <label for="">Fecha de Afiliación</label>
                    <input type="date" class="form-control" name="fecha_de_afiliacion" v-model="form.fecha_de_afiliacion">
                    <small class="text-danger" v-text="errors.fecha_de_afiliacion ? errors.fecha_de_afiliacion[0] : ''"></small>
                </div>
            </div>

            <div class="col-md-4">
                <div class="form-group">
                    <label for="">Banco</label>
                    <select name="banco_id" v-model="form.banco_id" class="form-control">
                        <option value="">Seleccionar...</option>
                        <option :value="banco.id" v-for="(banco, ban) in bancos" :key="`banco-${ban}`"
                            v-text="banco.nombre"
                        >
                        </option>
                    </select>
                    <small class="text-danger" v-text="errors.planilla_id ? errors.planilla_id[0] : ''"></small>
                </div>
            </div>

            <div class="col-md-4">
                <div class="form-group">
                    <label for="">Número de Cuenta</label>
                    <input type="text" class="form-control" v-model="form.numero_de_cuenta" name="numero_de_cuenta">
                    <small class="text-danger" v-text="errors.numero_de_cuenta ? errors.numero_de_cuenta[0] : ''"></small>
                </div>
            </div>

            <div class="col-md-4">
                <div class="form-group">
                    <label for="">Número Autogenerado</label>
                    <input type="text" class="form-control" name="numero_de_essalud" v-model="form.numero_de_essalud">
                    <small class="text-danger" v-text="errors.numero_de_essalud ? errors.numero_de_essalud[0] : ''"></small>
                </div>
            </div>

            <div class="col-md-4">
                <div class="form-group">
                    <label for="">Fecha de ingreso <span class="text-danger">*</span></label>
                    <input type="date" class="form-control" name="fecha_de_ingreso" v-model="form.fecha_de_ingreso">
                    <small class="text-danger" v-text="errors.fecha_de_ingreso ? errors.fecha_de_ingreso[0] : ''"></small>
                </div>
            </div>

            <div class="col-md-4">
                <div class="form-group">
                    <label for="">Fecha de cese</label>
                    <input type="date" class="form-control" value="numero_de_cese" v-model="form.numero_de_cese">
                    <small class="text-danger" v-text="errors.numero_de_cese ? errors.numero_de_cese[0] : ''"></small>
                </div>
            </div>

            <div class="col-md-4">
                <div class="form-group">
                    <label for="">Planilla <span class="text-danger">*</span></label>
                    <select name="planilla_id" v-model="form.planilla_id" v-on:change="changeSelect($event, 'changePlanilla')" class="form-control">
                        <option value="">Seleccionar...</option>
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
                    <label for="">Cargo <span class="text-danger">*</span></label>
                    <select name="cargo_id" 
                        v-model="form.cargo_id" 
                        class="form-control" 
                        v-on:change="changeSelect($event, 'changeCargo')"
                    >
                        <option value="">Seleccionar...</option>
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
                    <label for="">Categoria <span class="text-danger">*</span></label>
                    <select name="categoria_id" v-model="form.categoria_id" class="form-control" :disabled="loading">
                        <option value="">Seleccionar...</option>
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
                    <label for="">Meta <span class="text-danger">*</span></label>
                    <select name="meta_id" v-model="form.meta_id" class="form-control" :disabled="loading">
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
                    <label for="">Perfil <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" v-model="form.perfil" name="perfil" :disabled="loading">
                    <small class="text-danger" v-text="errors.perfil ? errors.perfil[0] : ''"></small>
                </div>
            </div>

            <div class="col-md-4">
                <div class="form-group">
                    <label for="">P.A.P<span class="text-danger">*</span></label>
                    <input type="text" v-model="form.pap" class="form-control" name="pap" :disabled="loading">
                    <small class="text-danger" v-text="errors.pap ? errors.pap[0] : ''"></small>
                </div>
            </div>


            <div class="col-md-4">
                <div class="form-group">
                    <label for="">Sindicato</label>
                    <select name="sindicato_id" v-model="form.sindicato_id" class="form-control" :disabled="loading">
                        <option value="">Seleccionar...</option>
                        <option :value="sindicato.id" v-for="(sindicato, sin) in sindicatos" :key="`sindicato-${sin}`"
                            v-text="sindicato.nombre"
                        >
                        </option>
                    </select>
                    <small class="text-danger" v-text="errors.meta_id ? errors.meta_id[0] : ''"></small>
                </div>
            </div>


            <div class="col-md-4">
                <div class="form-group">
                    <label for="">Plaza</label>
                    <input type="text" v-model="form.plaza" class="form-control" name="plaza" :disabled="loading">
                </div>
            </div>

            <div class="col-md-4">
                <div class="form-group">
                    <label for="">Escuela </label>
                    <input type="text" v-model="form.escuela" class="form-control" name="escuela" :disabled="loading">
                </div>
            </div>

            <div class="col-md-8">
                <div class="form-group">
                    <label for="">Observación</label>
                    <textarea name="observacion" v-model="form.observacion" :disabled="loading" class="form-control"></textarea>
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
                        <label for="">Funte de Ingreso </label>
                        <input type="text" :disabled="loading" v-model="form.fuente_id" class="form-control" name="fuente_id">
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="form-group">
                        <label for="">RUC</label>
                        <input type="text" :disabled="loading" class="form-control" v-model="form.ruc" name="ruc">
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
    props: ['param', 'send', 'history'],
    data() {
        return {
            cas: false,
            loading: false,
            planillas: [],
            afps: [],
            typeAfps: [],
            bancos: [],
            cargos: [],
            categorias: [],
            metas: [],
            errors: {},
            tmp_infos: [],
            sindicatos: [],
            form: {}
        }
    },
    async mounted() {
        await this.getPlanillas();
        await this.getMetas();
        await this.getSindicatos();
        await this.getAfps();
        await this.getBancos();
        await this.inicializar(this.history);
    },
    watch: { 
        async history() {
            await this.inicializar(this.history);
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
        },
        afps(nuevo) {
            this.getTypeAfp({ target: { value: this.history.type_afp_id } });
        }
    },  
    methods : {
        changeSelect(e, func) {
            if (typeof this[func] == 'function') {
                this[func](e);
            }
        },
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

            if (this.form.planilla_id) {
                this.getPlanillaID(this.form.planilla_id);
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
            if (this.form.cargo_id) {
                this.getCargoID(this.form.cargo_id);
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
        getSindicatos() {
            let api = axios.get('/api/v1/sindicato');
            this.loading = true;
            api.then(res => {
                this.sindicatos = res.data;
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
        getAfps() {
            let api = unujobs('get', '/afp');
            api.then(res => {
                this.afps = res.data;
            }).catch(err => {

            });
        },
        async getTypeAfp(e) {
            let { value, name } = e.target;
            for (let afp of this.afps) {
                if (afp.id == value) {
                    this.typeAfps = await afp.type_afps;
                    break;
                }
            }
        },
        getBancos(e) {
            let api = unujobs('get', '/banco');
            api.then(res => {
                this.bancos = res.data;
            }).catch(err => {

            });
        },
        async submit(e){

            if (e) {
                e.preventDefault();
            }

            const form = new FormData(document.getElementById('form-config-work'));
            form.append('work_id', this.param);
            form.append('_method', 'PUT');
            
            this.loading = true;
            this.errors = {};
            let api = unujobs("post", `/info/${this.info.id}`, form);

            await api.then(res => {

                let { status, message, body } = res.data;
                let icon = status ? 'success' : 'error';
                notify({icon: icon, text: message});
            }).catch(err => {

                let { data } = err.response;
                this.errors = data.errors;

                if (!data.message) {
                    notify({icon: 'error', text: 'Algo salió mal'});
                }

            });

            this.loading = false;
            this.$emit('ready');
        },
        async inicializar(history) {

            for(let key in history) {
                if (history[key]) {
                    this.form[key] = history[key];
                }else {
                    this.form[key] = "";
                }
            }

            // campos de cambio de estado
            let changes = await [
                { key: 'afp_id', func: 'getTypeAfp' }, 
                { key: 'planilla_id', func: 'changePlanilla' }, 
                { key: 'cargo_id', func: 'changeCargo' }
            ];

            for(let change of changes) {
                if (history[change.key]) {
                    if (await typeof this[change.func] == 'function') {
                        await this[change.func]({ target: { value: history[change.key] } });
                    }
                }
            }

        }
    }
}

</script>


<style scoped>

    .form-control {
        font-size: 14px;
    }

    .card {
        font-size: 14px;
    }

</style>