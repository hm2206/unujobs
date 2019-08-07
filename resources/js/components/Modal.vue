<template>
    <div class="card" :style="`height: ${height}`" v-if="show">
        <div class="card-header">

            <slot name="header"></slot>
            
            <button class="close" v-on:click="close">
                <small><i class="fas fa-times"></i></small>
            </button>
        </div>


        <slot name="content"></slot>
        
    </div>
</template>

<script>
export default {
    props: {
        height: {
            type: String,
            default: "90vh"
        }
    },
    data() {
        return {
            show: true
        }
    },
    methods: {
        close() {
            this.$emit("click", false);
            this.show = false;
        }
    }
}
</script>

