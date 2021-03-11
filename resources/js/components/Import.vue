 <template>
    <span>
        <button :class="`btn ${theme ? theme : 'btn-sm btn-success'}`" v-on:click="showVentana">
            <slot></slot>
        </button>

        <div class="ventana" v-show="ventana">
            <div class="row h-100 justify-content-center mt-5">
                <div class="col-md-5">
                    <form class="card" v-on:submit="verify" enctype="multipart/form-data" :id="formulario">
                        <div class="card-header">
                            Dialogo de Confirmación
                        </div>
                        <div class="card-body">

                            <div class="form-group">
                                <a :href="formato" class="btn btn-sm btn-outline-success">
                                    <i class="fas fa-file-excel"></i> Formato de importación
                                </a>
                            </div>
                                    
                            <div class="form-group">
                                <label :for="id" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-upload"></i> Subir Archivo de Excel
                                    <input type="file" name="import" :id="id" :disabled="loader" hidden>
                                </label>
                                <small class="text-danger">{{ errors.import ? errors.import[0] : '' }}</small>
                            </div>

                            <div class="form-group">
                                <label for="" class="form-control-label">
                                    Contraseña de Confirmación
                                </label>
                                <input type="password" :disabled="loader" name="password" class="form-control" v-model="form.password">
                                <small class="text-danger">{{ errors.password ? errors.password[0] : '' }}</small>
                            </div>
                            <button v-if="!loader" class="btn btn-primary">
                                <i class="fas fa-check"></i> Confirmar
                            </button>
                            <button v-if="!loader" class="btn btn-danger" v-on:click="hideVentana">
                                <i class="fas fa-ban"></i> Cancelar
                            </button>

                            <div class="w-100 text-center mt-3" v-if="loader">
                                <div class="spinner-border text-primary"></div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </span>
</template>


<style scoped>
    .ventana {
        width: 100%;
        height: 100%;
        position: fixed;
        background: rgba(0, 0, 0, 0.5);
        top: 0px;
        left: 0px;
        z-index: 100;

    }
</style>

<script>

import { unujobs } from '../services/api';
import notify from 'sweetalert';

export default {
    props: ['url', 'id','theme', 'formato', 'formulario', 'param'],
    data() {
        return {
            ventana: false,
            loader: false,
            errors: {},
            form: {
                password: '',
                import: ''
            }
        }
    },
    methods: {
        showVentana(e) {
            e.preventDefault();
            this.ventana = true;
        },
        hideVentana(e) {
            e.preventDefault();
            this.ventana = false
            this.errors = {};
            this.form = {}
        },
        async verify(e) {

            e.preventDefault();
            this.loader = true;
            const form = new FormData(document.getElementById(this.formulario));
            form.append("user", this.param);
            let api = unujobs("post", this.url, form);
            await api.then(res => {
                let { message, status } = res.data;
                let icon = status ? 'success' : 'error'
                notify({icon, text: message}).then(res => {
                    this.ventana = false;
                    this.form.password = '';
                }).catch(err => {
                    this.ventana = false;
                })

            }).catch(err => {
                let { status, data } = err.response;

                if (401 == status) {
                    notify({icon: 'error', text: 'Las credenciales de acceso son invalidas!'});
                    this.form.password = '';
                }else if(404 == status) {
                    notify({icon: 'error', text: 'El usuario no existe!'});
                }else {
                    this.errors = data.errors;
                }

            });
            this.loader = false;
    
        }
    }
}
</script>
