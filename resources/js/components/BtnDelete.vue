<template>
    <button :class="`btn ${getType} ${getSize}`" v-on:click="destroy"
        :disabled="loading"
    >
        <i class="fas fa-trash"></i> <span v-text="text"></span>
    </button>
</template>


<script>
export default {
    props: ['tipo', 'text', 'url', 'size'],
    mounted() {
        if (!this.tipo) {
            this.tipo = "danger";
        }

        if (!this.size) {
            this.sizes = "md";
        }
    },
    data() {
        return {
            loading: false,
            types: {
                "danger": "btn-danger",
                "outline": "btn-outline",
            },
            sizes: {
                "md": "btn-md",
                "sm": "btn-sm",
                "lg": "btn-lg"
            }
        }
    },
    methods: {
        destroy(e) {
            e.preventDefault();
            this.loading = true;
            
            let api = axios.delete(this.url);
            api.then(res => {
                this.loading = false;
                alert("El registro se elimino correctamente!");
                location.href = '';
            }).catch(err => {
                this.loading = false;
            });
        }
    },
    computed: {
        getType() {
            return this.types[this.tipo];
        },
        getSize() {
            return this.sizes[this.size]
        }
    }
}
</script>
