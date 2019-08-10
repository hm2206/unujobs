<template>
    <li class="nav-item dropdown no-arrow mx-1">
        <a :class="`nav-link dropdown-toggle ${className}`" href="#" id="alertsDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <i class="fas fa-bell fa-fw"></i>
            <!-- Counter - Alerts -->
            <span class="badge badge-danger badge-counter" v-if="count > 0" v-text="count"></span>
        </a>
        <!-- Dropdown - Alerts -->
        <div class="dropdown-list dropdown-menu dropdown-menu-right shadow animated--grow-in alert-height" aria-labelledby="alertsDropdown">
            <h6 class="dropdown-header">
                Notificaciones
            </h6>
                
            <span v-if="hasNotify">
                <div class="dropdown-item d-flex align-items-center" 
                    v-for="(notify, n) in notifications" :key="notify.id"
                    v-on:click="markAsRead(notify.id, n)"
                >
                    <a class="mr-3" :href="notify.data ? notify.data.url : '#'" >
                        <div :class="`icon-circle ${notify.data ? notify.data.background : 'bg-primary'}`">
                            <i :class="`${notify.data ? notify.data.icono : 'fas fa-file-alt'} text-white`"></i>
                        </div>
                    </a>
                    <div>
                        <div class="small text-gray-500" v-text="getDate(notify.created_at)"></div>
                        <a class="font-weight-bold" :href="notify.data ? notify.data.url : '#'" 
                            v-text="notify.data.body">
                        </a>
                    </div>
                    <button class="btn btn-sm text-danger" v-on:click="leaveNotify(notify.id, n)">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
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
import notify from 'sweetalert';
export default {
    data() {
        return {
            notifications: [],
            className: "",
            count: "",
        }
    },
    mounted() {
        let that = this;
        that.countUnread();
        this.countUnread();
        this.intervalo = setInterval(function() {
            that.getNotify();
            that.countUnread();
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
                clearTimeout(this.intervalo);
            });
        },
        countUnread() {
            let api = axios.get('/user/unread/count');
            api.then(res => {
                this.count = res.data;
            }).catch(err => {   
                console.log(err);
            });
        },
        leaveNotify(id, index) {
            this.notifications.splice(index, 1);
            this.count -= 1;
            this.markAsRead(id, index);
        }
    },
    computed: {
        hasNotify() {
            return this.notifications.length > 0 ? true : false;
        }
    },
    destroyed() {
        clearTimeout(this.intervalo);
    }
}
</script>


<style scoped>

    .alert-height {
        max-height: 300px;
        overflow-y: auto;
        scrollbar-width: 0.2em;
    }

</style>
