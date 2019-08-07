<template>
    <span>
        <button :class="`btn btn-${theme} btn-${size} ${config ? config.join(' ') : ''}`"
            v-on:click="getData"
            v-if="!block && !loading"
        >
            <span v-text="text"></span>
        </button>

        <div v-if="block && !loading" class="text-center">
            <small>No se encontró mas recursos
                <i class="fas fa-link"></i>
            </small>
        </div>

        <div v-if="loading" class="text-center">
            <small class="spinner-border text-warning"></small>
        </div>
    </span>
</template>

<script>

import { unujobs } from '../services/api';
import { setTimeout } from 'timers';

export default {
    props: {
        theme: {
            type: String,
            default: 'primary'
        },
        size: {
            type: String,
            default: 'md'
        },
        text: {
            type: String,
            default: 'ver más'
        },
        config: {
            type: Array
        },
        method: {
            type: String,
            default: 'get'
        },
        url: {
            type: String,
            default: ''
        },
        params: {
            type: Object,
            default: function (e) {
                return {
                    _token: "tu_token_de_verificación"
                }
            }
        }
    },
    data() {
        return {
            block: false,
            loading: false
        }
    },
    methods: {
        async getData(e) {
            this.loading = true;
            if (this.url) {

                let api = unujobs(this.method, this.url, this.params);
                await api.then(res => {

                    let { data } = res;

                    if (typeof data == 'object') {
                        this.$emit("get-data", {
                            data: data.data,
                            next: data.next_page_url,
                            path: data.path,
                            total: data.total
                        });
                    }else {
                        this.$emit("get-data", {
                            data: data
                        });
                    }

                }).catch(err => {
                    console.log(err);
                });

                this.loading = false;
            }else {
                this.loading = false;
                this.messageError();
            }
        },
        messageError() {
            this.block = true;
            let that = this;
            setTimeout(function() {
                that.block = false;
            }, 1000);
        }
    }
}
</script>
