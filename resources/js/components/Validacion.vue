<template>
    <span>
        <button class="btn btn-sm btn-success" v-on:click="showVentana">
            <i class="fas fa-file-excel"></i>
            <span v-text="btn_text"></span>
        </button>

        <div class="ventana" v-if="ventana">
            <div class="row h-100 justify-content-center mt-5">
                <div class="col-md-5">
                    <div class="card">
                        <div class="card-header">
                            Dialogo de Confirmación
                        </div>
                        <div class="card-body">
                            <div class="form-group">
                                <label for="" class="form-control-label">
                                    Contraseña de Confirmación
                                </label>
                                <input type="password" name="password" class="form-control">
                            </div>
                            <button class="btn btn-primary">
                                <i class="fas fa-check"></i> Confirmar
                            </button>
                            <button class="btn btn-danger" v-on:click="hideVentana">
                                <i class="fas fa-ban"></i> Cancelar
                            </button>
                        </div>
                    </div>
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
export default {
    props: ['btn_text'],
    data() {
        return {
            ventana: false
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
        }
    }
}
</script>
