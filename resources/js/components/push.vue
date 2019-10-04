<template>
    <div class="push" v-if="show">
        <div class="push-content">
                <div class="card">
                    <div class="card-header">
                        Notificación push
                        <button class="close" v-on:click="show = false">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <div class="card-body">
                        <b>{{ notify.data.body }}</b>
                    </div>
                    <div class="card-footer">
                        <a class="btn btn-sm btn-primary text-white" :href="notify.data ? notify.data.url : ''">
                            <i class="fas fa-eye"></i> Ver
                        </a>
                    </div>
                </div>
        </div>
    </div>
</template>

<script>
export default {
    props: ['current'],
    data() {
        return {
            show: false,
            listener: false,
            old: 0,
            notify: {},
            loader: false
        }
    },
    watch: {
        current(nuevo) {
            if (this.listener) {
                if (this.old < this.current.length) {
                    this.show = true;
                    this.notify = this.current[this.current.length - 1];
                }
            }

            this.listener = true;
            this.old = this.current.length;
        }
    }
}
</script>

<style scoped>
    .push {
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        position: fixed;
        top: 0px;
        left: 0px;
        z-index: 100;
    }

    .push-content {
        position: absolute;
        bottom: 20px;
        right: 20px;
        width: 300px;
    }
</style>