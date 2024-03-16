<script setup>
import axios from "axios";
import * as yup from "yup";
import { Form, Field } from "vee-validate";
import { ref, onMounted, reactive, watch } from "vue";
import { useToastr } from "../../toastr.js";
import { debounce } from "lodash";
import moment from 'moment'
import { Bootstrap4Pagination } from "laravel-vue-pagination";

const permissions = ref({ data: [] });
const searchQuery = ref(null);

const getPermissions = (page = 1) => {
    axios
        .get(`/api/data/user?page=${page}`, {
            params: {
                query: searchQuery.value,
            },
        })

        .then((response) => {
            permissions.value = response.data;
        });
};

onMounted(() => {
    getPermissions();
});
</script>



<template>
    <span style="color:#ffffff;" v-for="(role, index) in permissions.data" :key="permissions.id"> 
             <li class="nav-item" v-if="role.users == 1">
                <a href="#" class="nav-link" active-class="active">
                    <i class="nav-icon fas fa-users"></i>
                    <p>
                        Users Management
                        <i class="right fas fa-angle-left"></i>
                    </p>
                </a>
                <ul class="nav nav-treeview">
                    <li class="nav-item">
                        <router-link to="/admin/users" active-class="active" class="nav-link">
                            <i class="nav-icon fas fa-users"></i>
                            <p>
                                users
                            </p>
                        </router-link>
                    </li>
                </ul>
            </li> 

            <li class="nav-item" v-if="role.permissions == 1">
                <a href="#" class="nav-link" active-class="active">
                    <i class="nav-icon fas fa-lock"></i>
                    <p>
                        Users Permissions 
                        <i class="right fas fa-angle-left"></i>
                    </p>
                </a>
                <ul class="nav nav-treeview">
                    <li class="nav-item">
                        <router-link to="/admin/permissions" active-class="active" class="nav-link">
                            <i class="nav-icon fas fa-unlock-alt"></i>
                            <p>
                                Permissions
                            </p>
                        </router-link>
                    </li>
                </ul>
            </li>

            <li class="nav-item" v-if="role.permissions == 1">
                <a href="#" class="nav-link" active-class="active">
                    <i class="nav-icon fas fa-box"></i>
                    <p>
                        Products Management 
                        <i class="right fas fa-angle-left"></i>
                    </p>
                </a>
                <ul class="nav nav-treeview">
                    <li class="nav-item">
                        <router-link to="/admin/products" active-class="active" class="nav-link">
                            <i class="nav-icon fas fa-box"></i>
                            <p>
                                Products
                            </p>
                        </router-link>
                    </li>
                </ul>
            </li>

            <li class="nav-item" v-if="role.permissions == 1">
                <a href="#" class="nav-link" active-class="active">
                    <i class="nav-icon fas fa-box"></i>
                    <p>
                        Orders Management 
                        <i class="right fas fa-angle-left"></i>
                    </p>
                </a>
                <ul class="nav nav-treeview">
                    <li class="nav-item">
                        <router-link to="/admin/orders" active-class="active" class="nav-link">
                            <i class="nav-icon fas fa-box"></i>
                            <p>
                                Orders
                            </p>
                        </router-link>
                    </li>
                </ul>
            </li>

            <li class="nav-item" v-if="role.permissions == 1">
                <a href="#" class="nav-link" active-class="active">
                    <i class="nav-icon fas fa-envelope"></i>
                    <p>
                        Contact Management 
                        <i class="right fas fa-angle-left"></i>
                    </p>
                </a>
                <ul class="nav nav-treeview">
                    <li class="nav-item">
                        <router-link to="/admin/contact" active-class="active" class="nav-link">
                            <i class="nav-icon fas fa-envelope"></i>
                            <p>
                                Submitted Forms
                            </p>
                        </router-link>
                    </li>
                </ul>
            </li>
            
            <li class="nav-item" v-if="role.roles == 1">
                <a href="#" class="nav-link" active-class="active">
                    <i class="nav-icon fas fa-shield-alt"></i>
                    <p>
                        Users Roles 
                        <i class="right fas fa-angle-left"></i>
                    </p>
                </a>
                <ul class="nav nav-treeview">
                    <li class="nav-item">
                        <router-link to="/admin/roles" active-class="active" class="nav-link">
                            <i class="nav-icon fas fa-user-tag"></i>
                            <p>
                                Roles
                            </p>
                        </router-link>
                    </li>
                </ul>
            </li>

            <li class="nav-item" v-if="role.profile == 1">
                <router-link to="/admin/profile" active-class="active" class="nav-link">
                    <i class="nav-icon fas fa-user"></i>
                    <p>
                        Profile
                    </p>
                </router-link>
            </li>
        </span>
</template>