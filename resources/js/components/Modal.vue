<template>
    <div class="card" :style="`height: ${height}`" v-if="show">
        <div class="card-header">

            <slot name="header"></slot>
            
            <button class="close" v-on:click="close">
                <small><i class="fas fa-times"></i></small>
            </button>
        </div>

        <span class="scroll">
            <slot name="content"></slot>
        </span>
    </div>
</template>

<style scoped>
    .scroll {
        overflow-y: auto;
    }
</style>


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

