<template>
    <form class="row" id="form-cafig-work" method="POST" :action="`/planilla/job/${work_id}/config`">

        <input type="hidden" :value="token" name="_token">

        <div class="col-md-4">
            <div class="form-group">
                <label for="">Planilla <span class="text-danger">*</span></label>
                <select name="planilla_id" id="" class="form-control" v-on:change="changePlanilla">
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
                <select name="cargo_id" id="" class="form-control" v-on:change="changeCargo">
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
                <select name="categoria_id" id="" class="form-control">
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
                <select name="meta_id" id="" class="form-control">
                    <option value="">Seleccionar...</option>
                    <option :value="meta.id" v-for="(meta, met) in metas" :key="`meta-${met}`"
                        v-text="meta.meta"
                    >
                    </option>
                </select>
                <small class="text-danger" v-text="errors.meta_id ? errors.meta_id[0] : ''"></small>
            </div>
        </div>


        <div class="col-md-4">
            <div class="form-group">
                <label for="">Perfil <span class="text-danger">*</span></label>
                <input type="text" class="form-control" name="perfil">
                <small class="text-danger" v-text="errors.perfil ? errors.perfil[0] : ''"></small>
            </div>
        </div>


        <div class="col-md-4">
            <div class="form-group">
                <label for="">Plaza</label>
                <input type="text" class="form-control" name="plaza">
            </div>
        </div>

        <div class="col-md-4">
            <div class="form-group">
                <label for="">Escuela </label>
                <input type="text" class="form-control" name="escuela">
            </div>
        </div>

        <div class="col-md-8">
            <div class="form-group">
                <label for="">Observación</label>
                <textarea name="observacion" class="form-control"></textarea>
            </div>
        </div>

        <div class="col-md-12">
            <input type="checkbox" v-model="cas"> Configuración de Cas
        </div>

        <div class="row col-md-12" v-if="cas">
            <div class="col-md-12">
                <hr>
                <h5>SOLO CAS</h5>
            </div>

            <div class="col-md-4">
                <div class="form-group">
                    <label for="">Funte de Ingreso </label>
                    <input type="text" class="form-control" name="fuente_id">
                </div>
            </div>

            <div class="col-md-4">
                <div class="form-group">
                    <label for="">RUC</label>
                    <input type="text" class="form-control" name="ruc">
                </div>
            </div>
        </div>

        <input type="hidden" name="work_id" :value="work_id">
        <div class="col-md-12 mt-4">
            <button class="btn btn-success" v-on:click="guardar" :disabled="loading">
                <i class="fas fa-save"></i> Guardar
            </button>
        </div>

    </form>
</template>

<script>

export default {
    props: ['work_id', 'token', 'errors'],
    data() {
        return {
            cas: false,
            loading: false,
            planillas: [],
            cargos: [],
            categorias: [],
            metas: []
        }
    },
    mounted() {
        this.getPlanillas();
        this.getMetas();
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
        changePlanilla(e) {
            let { name, value } = e.target;
            this.loading = true;
            this.cargos = [];
            this.categorias = [];

            if (value) {
                this.getPlanillaID(value);
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
        changeCargo(e) {
            let { name, value } = e.target;
            this.loading = true;
            this.categorias = [];

            if (value) {
                this.getCargoID(value);
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
        guardar(e){
            let formulario = document.getElementById('form-cafig-work');
            form.sumit();
        }
        
    }
}

</script>