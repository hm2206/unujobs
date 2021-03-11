<template>
    <span>
        <li class="nav-item" v-for="(modulo, mo) in modulos" :key="`modulo-${mo}`">
          <a class="nav-link collapsed" href="#" data-toggle="collapse" :data-target="`#collapse${mo}`" aria-expanded="true" aria-controls="collapseTwo">
            <i :class="modulo.icono"></i>
            <span v-text="modulo.name"></span>
          </a>
          <div :id="`collapse${mo}`" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
              <h6 class="collapse-header">Modulos:</h6>
            
                <a v-for="(submodulo, sub) in modulo.modulos" :key="`sub-modulo-${sub}`" 
                    class="collapse-item" :href="submodulo.ruta"
                >
                    <span v-text="submodulo.name"></span>
                </a>

            </div>
          </div>
        </li>
    </span>
</template>

<script>

import { unujobs } from '../services/api';

export default {
    props: ['param'],
    data() {
        return {
            modulos: []
        }
    },
    mounted() {
        this.getMenu();
    },
    methods: {
        getMenu() {

            let api = unujobs("get", `/menu/${this.param}`);

            api.then(res => {

                this.modulos = res.data;

            }).catch(err => {
                console.log(err);
            });

        }
    }
}
</script>