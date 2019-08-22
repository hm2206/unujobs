<template>
    <div class="row">
        <h6 class="col-md-12 text-dark"><i class="fas fa-history"></i> Historial de archivos Generados</h6>

        <div class="text-center w-100" v-if="loader">
            <div class="spinner-border text-primary"></div>
        </div>

        <div class="col-md-12" v-for="(file, fi) in files" :key="`file-${fi}-${file.id}`">
            <hr>
            <div class="row">
                <div class="col-md-2">
                   <span :class="`btn btn-sm btn-${file.type == 'pdf' ? 'danger' : 'primary'}`">
                        <i :class="file.icono"></i>
                   </span>
                </div>
                <div class="col-md-6 text-dark">
                    {{ file.name }} 
                    <span class="badge badge-warning" v-if="!file.read">nuevo</span>
                </div>
                <div class="col-md-3">
                    <small class="text-primary">Creado el {{ file.created_at }}</small>
                </div>
                <div class="col-md-1">
                    <a target="__blank" :href="file.path" class="btn btn-sm btn-success"
                        v-on:click="markAsRead($event, file)"
                    >
                        <i class="fas fa-download"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</template>

<script>

import { unujobs } from '../services/api';

export default {
    props: ['param', 'type'],
    data() {
        return {
            files: [],
            loader: false
        };
    },
    mounted() {
        this.getFiles();
    },
    methods: {
        async markAsRead(e, file) {
            this.loader = true;
            let api = unujobs("post", `/report/${file.id}/markasread`);
            await api.then(res => {
                this.files.map((fil, iter) => {
                    if (fil.id == file.id) {
                        fil.read = 1;
                    }
                    return fil;
                });
            }).catch(err => {
                console.log("algo salió mal");
            });
            this.loader = false;
        },
        async getFiles(e) {

            this.loader = true;
            let api = unujobs("get", `/file/${this.param}/type/${this.type}`);
            await api.then(res => {
                this.files = res.data;
            }).catch(err => {
                this.files = [];
                console.log("algo salió mal al obtener los ficheros");
            });

            this.loader = false;

        }
    }
}
</script>