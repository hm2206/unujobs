<template>
    <div>
        <ventana v-if="!sesion">
            <div class="col-md-4">
                <div class="card mt-5">
                    <div class="card-header">
                        Continuar con la sesión actual
                        <button class="close" v-on:click="close">
                            <small><i class="fas fa-times"></i></small>
                        </button>
                    </div>
                    <form class="card-body" id="form-authentication" v-on:submit="login">

                        <div class="text-center mb-3">
                            <img src="/img/perfil.png" alt="" class="img-circle">
                            <div class="capitalize" v-text="auth.nombre_completo"></div>
                        </div>

                        <div class="form-group">
                            <input type="password" class="form-control" 
                                placeholder="Ingrese su contraseña"
                                name="password"
                            >
                            <input type="hidden" name="email" v-model="auth.email">
                        </div>

                        <button class="btn btn-primary" :disabled="loading">
                            <i class="fas fa-check"></i>
                            Confirmar
                        </button>
                    </form>
                    <div class="card-footer" v-if="loading">
                        Verificando autenticación...
                    </div>
                </div>
            </div>
        </ventana>
    </div>
</template>


<script>
import { setInterval, clearInterval } from 'timers';
import notify from 'sweetalert';

export default {
    data() {
        return {
            auth: {},
            count: 0,
            sesion: true,
            loading: true,
            intereval: ""
        }
    },
    mounted() {
        this.current();
        this.setTimeCurrent();
    },
    methods: {
        close() {
            let api = this.current();
            api.then(res => {
                notify({icon: "success", text: "La sesión fue recuperada correctamente"});
                this.setTimeCurrent();
            }).catch(err => {
                let validate = notify({icon: "error", text: "Redirigiendo al login..."});
                validate.then(res => {
                    location.href = "/login";
                })
            });
        },
        current() {
            this.loading = true;
            let api = axios.post("/current");
            api.then(res => {

                this.auth = res.data;
                localStorage.email = this.auth.email;
                this.sesion = true;
                this.loading = false;

            }).catch(err => {

                this.sesion = false;
                this.loading = false;
                clearInterval(this.interval);
            
            });

            return api;
        },
        login(e) {
            e.preventDefault();
            this.loading = true;
            const form = new FormData(document.getElementById('form-authentication'));
            axios.post("/login", form).then(res => {
                this.sesion = true;
                this.loading = false;
                notify({icon: "success", text: "La sesión fue recuperada correctamente"});
                this.setTimeCurrent();
            }).catch(err => {
                this.loading = false;
                notify({icon: "error", text: "Las credenciales son incorrectas"});
            });
        },
        setTimeCurrent()
        {
            let that = this;
            this.interval = setInterval(function () {
                that.current();
            }, 3000);
        }
    }
}
</script>


<style scoped>

    .img-circle {
        width: 70px;
        height: 70px;
        object-fit: cover;
        border-radius: 0.5em;
        padding: 5px;
        border: 1px solid rgba(0, 0, 0, 0.08);
    }

    .hidden {
        display: none;
    }
</style>
