<template>
    <li class="nav-item dropdown no-arrow mx-1">
        <a :class="`nav-link dropdown-toggle ${className}`" href="#" id="alertsDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <i class="fas fa-bell fa-fw"></i>
            <!-- Counter - Alerts -->
            <span class="badge badge-danger badge-counter" v-text="count"></span>
        </a>
        <!-- Dropdown - Alerts -->
        <div class="dropdown-list dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="alertsDropdown">
            <h6 class="dropdown-header">
                Notificaciones
            </h6>
                
            <span v-if="hasNotify">
                <a class="dropdown-item d-flex align-items-center" 
                    target="__blank" :href="notify.data ? notify.data.url : '#'" 
                    v-for="(notify, n) in notifications" :key="notify.id"
                    v-on:click="markAsRead(notify.id, n)"
                >
                    <div class="mr-3">
                        <div :class="`icon-circle ${notify.data ? notify.data.background : 'bg-primary'}`">
                            <i :class="`${notify.data ? notify.data.icono : 'fas fa-file-alt'} text-white`"></i>
                        </div>
                    </div>
                    <div>
                        <div class="small text-gray-500" v-text="getDate(notify.created_at)"></div>
                        <span class="font-weight-bold" v-text="notify.data.body"></span>
                    </div>
                </a>
            </span>
            
            <div class="dropdown-item  text-center" v-else>
                No hay Notificaciones disponibles
            </div>

            <a class="dropdown-item text-center small text-gray-500" href="#">Todas las notificaciones</a>
        </div>
    </li>
</template>

<script>
import { setInterval, setTimeout, clearTimeout } from 'timers';
export default {
    data() {
        return {
            notifications: [],
            className: ""
        }
    },
    mounted() {
        let that = this;
        this.getNotify();
        this.intervalo = setInterval(function() {
            that.getNotify();
        }, 3000);
    },
    methods: {
        getNotify(){
            let api = axios.get('/user/unread');
            api.then(res => {

                if (this.notifications.length < res.data.length) {
                    let that = this;
                    this.className = 'animate-notify';
                    setTimeout(() => that.className = '', 1000);
                }

                this.notifications = res.data;

            }).catch(err => {
                alert("No se pudó obtener las notificaciones, Actualize la página");
                clearTimeout(this.intervalo);
            })
        },
        getDate(timestap){
            let date = new Date();
            return date.toDateString(timestap)
        },
        markAsRead(id, index) {
            this.notifications.splice(index, 1);
            let api = axios.post(`/user/${id}/markasread`);
            api.then(res => {
                this.notifications = res.data;
            }).catch(err => {
                alert("Ocurrio un error al actualizar las notificationes, Actualize la página");
                clearTimeout(this.intervalo);
            });
        }
    },
    computed: {
        count() {
            return this.notifications.length > 0 ? this.notifications.length : "";
        },
        hasNotify() {
            return this.notifications.length > 0 ? true : false;
        }
    },
    destroyed() {
        clearTimeout(this.intervalo);
    }
}
</script>