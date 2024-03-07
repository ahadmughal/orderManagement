<script setup>
import axios from "axios";
import * as yup from "yup";
import { Form, Field } from "vee-validate";
import { ref, onMounted, reactive, watch } from "vue";
import { useToastr } from "../../toastr.js";
import { debounce } from "lodash";
import moment from 'moment'
import { Bootstrap4Pagination } from "laravel-vue-pagination";

const roles = ref({ data: [] });
const permissions = ref({ data: [] });
const editing = ref(false);
const formValues = ref();
const form = ref(null);
const toastr = useToastr();
const dataToBeDeleted = ref(null);
const searchQuery = ref(null);
const selectedRoles = ref([]);
const selectAll = ref(false);
const selectedAll = ref(false);
const selectedPermissions = ref([]);

const getRoles = (page = 1) => {
    axios
        .get(`/api/roles?page=${page}`, {
            params: {
                query: searchQuery.value,
            },
        })

        .then((response) => {
            roles.value = response.data;
            selectedRoles.value = [];
            selectAll.value = false;
            selectedAll.value = false;
        });
};

const addSchema = yup.object({
    title: yup.string().required(),
    users: yup.string(),
    roles: yup.string(),
    permissions: yup.string(),
    profile: yup.string(),
    pages: yup.string(),
    forms: yup.string(),
    blogs: yup.string(),
    products: yup.string(),
});

const editSchema = yup.object({
    title: yup.string().required(),
});

const createRole = (values, { resetForm, setErrors }) => {

    axios
        .post("/api/roles", values)
        .then((response) => {
            roles.value.data.unshift(response.data);
            $("#roleFormModal").modal("hide");
            toastr.success("Role Added Successfully");
            resetForm();
        })
        .catch((error) => {
            if (error.response.data.errors) {
                setErrors(error.response.data.errors);
            }
        });
};

const updateRole = (values, { setErrors }) => {
    axios
        .put("/api/roles/" + formValues.value.id, values)
        .then((response) => {
            const index = roles.value.data.findIndex(
                (role) => role.id === response.data.id
            );
            roles.value.data[index] = response.data;
            $("#roleFormModal").modal("hide");
            toastr.success("Role Updated Successfully");
        })
        .catch((error) => {
            if (error.response.data.errors) {
                setErrors(error.response.data.errors);
            }
        });
};

const updateRolePermissions = (values, { setErrors }) => {
    axios
        .put("/api/roles-permissions/" + formValues.value.id, values)
        .then((response) => {
            const index = roles.value.data.findIndex(
                (role) => role.id === response.data.id
            );
            roles.value.data[index] = response.data;
            $("#updatePermissionModal").modal("hide");
            toastr.success("Role Permissions Updated Successfully");
        })
        .catch((error) => {
            if (error.response.data.errors) {
                setErrors(error.response.data.errors);
            }
        });
};



const handleSubmit = (values, actions) => {
    if (editing.value) {
        updateRole(values, actions);
    } else {
        createRole(values, actions);
    }
};

const handleSubmitPermissions = (values, actions) => {
  
        updateRolePermissions(values, actions);
};

const addRole = () => {
    editing.value = false;
    $("#roleFormModal").modal("show");
};

const editRoles = (role) => {
    editing.value = true;
    form.value.resetForm();
    $("#roleFormModal").modal("show");
    formValues.value = {
        id: role.id,
        title: role.title,
    };

};

const editPermissions = (role) => {
    editing.value = true;
    form.value.resetForm();
    $("#updatePermissionModal").modal("show");
    formValues.value = {
        id: role.id,
        users: role.users,
        blogs: role.blogs,
        roles: role.roles,
        profile: role.profile,
        pages: role.pages,
        forms: role.submitted_forms,
        permissions: role.permissions,
        products: role.products,
    };

};

const confirmRoleDeletion = (role) => {
    dataToBeDeleted.value = role.id;
    $("#confirmationModal").modal("show");
};

const deleteRole = () => {
    axios.delete(`/api/roles/${dataToBeDeleted.value}`).then(() => {
        $("#confirmationModal").modal("hide");
        roles.value.data = roles.value.data.filter(
            (role) => role.id !== dataToBeDeleted.value
        );
        toastr.success("Role Deleted Successfully");
    });
};

watch(
    searchQuery,
    debounce(() => {
        getRoles();
    }, 300)
);

const toggleSelection = (role) => {
    const index = selectedRoles.value.indexOf(role.id);
    if (index === -1) {
        selectedRoles.value.push(role.id);
    } else {
        selectedRoles.value.splice(index, 1);
    }
};

const bulkDelete = () => {
    axios
        .delete("/api/roles", {
            data: {
                ids: selectedRoles.value,
            },
        })
        .then((response) => {
            roles.value.data = roles.value.data.filter(
                (role) => !selectedRoles.value.includes(role.id)
            );
            selectedRoles.value = [];
            toastr.success("Roles Deleted Successfully");
            location.reload();
        });
};

const selectAllPermissions = () => {
    if (selectAll.value) {
        selectedRoles.value = roles.value.data.map((role) => role.id);
        selectedAll.value = true;
    } else {
        selectedAll.value = false;
        selectedRoles.value = [];
        selectAll.value = false;
    }
};

const getPermissions = (page = 1) => {
    axios
        .get(`/api/permissions?page=${page}`, {
            params: {
                query: searchQuery.value,
            },
        })

        .then((response) => {
            permissions.value = response.data;
        });
};
const togglePermissionSelection = (permission) => {
    const index = selectedPermissions.value.indexOf(permission.id);
    if (index === -1) {
        selectedPermissions.value.push(permission.id);
    } else {
        selectedPermissions.value.splice(index, 1);
    }
};
onMounted(() => {
    getRoles();
    getPermissions();
});
</script>
<template>
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Manage Roles</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item" active-class="active">
                            <router-link to="/admin/dashboard"
                                >Dashboard</router-link
                            >
                        </li>
                        <li class="breadcrumb-item" active-class="active">
                            Roles
                        </li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="content">
        <div class="container-fluid">
            <div class="row hpadding10 vpadding10">
                <div class="col-sm-12 col-md-6 col-lg-6">
                    <button
                        type="button"
                        class="btn btn-primary mb-2"
                        @click="addRole"
                    >
                        <i class="fas fa-plus-circle mr-1"></i>
                        Add New Role
                    </button>

                    <button
                        v-if="selectedRoles.length > 0"
                        type="button"
                        class="btn btn-danger ml-2 mb-2"
                        @click="bulkDelete"
                    >
                        <i class="fas fa-trash mr-1"></i>
                        Delete Selected
                    </button>

                    <span v-if="selectedRoles.length > 0" class="ml-2"
                        >Selected {{ selectedRoles.length }} Roles</span
                    >
                </div>

                <div class="col-sm-12 col-md-6 col-lg-6">
                    <input
                        type="text"
                        class="form-control"
                        v-model="searchQuery"
                        placeholder="Search....."
                    />
                </div>

<!-- Table to show the records -->
                <div class="card fullWidth hmargin10">
                    <div class="card-body">
                        <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>
                                        <input
                                            v-model="selectAll"
                                            type="checkbox"
                                            @change="selectAllPermissions"
                                        />
                                    </th>
                                    <th scope="col">#</th>
                                    <th scope="col">Title</th>
                                    <th scope="col">Permissions</th>
                                    <th scope="col">Creation Date</th>
                                    <th scope="col">Actions</th>
                                </tr>
                            </thead>
                            <tbody v-if="roles.data.length > 0">
                                <tr
                                    v-for="(role, index) in roles.data"
                                    :key="roles.id"
                                >
                                    <td>
                                        <input
                                            :checked="selectedAll"
                                            type="checkbox"
                                            @change="toggleSelection(role)"
                                        />
                                    </td>
                                    <td scope="row">{{ index + 1 }}</td>
                                    <td>{{ role.title }}</td>
                                    <td>
                                        <span class="permissionBadge1" v-if="role.users == 1"> Users </span>
                                        <span class="permissionBadge2" v-if="role.roles == 1"> Roles </span>
                                        <span class="permissionBadge3" v-if="role.submitted_forms == 1"> Forms </span>
                                        <span class="permissionBadge4" v-if="role.blogs == 1"> Blogs </span>
                                        <span class="permissionBadge5" v-if="role.pages == 1"> Pages </span>
                                        <span class="permissionBadge6" v-if="role.profile == 1"> Profile </span>
                                        <span class="permissionBadge7" v-if="role.permissions == 1"> Permissions </span> 
                                        <span class="permissionBadge7" v-if="role.products == 1"> Products </span>
                                    </td>
                                    <td>{{ moment(role.created_at).format("ddd MMM DD, YYYY [at] HH:mm A") }}</td>
                                    
                                    <td>
                                        <a
                                            href="#"
                                            title="Edit Role Title"
                                            @click.prevent="editRoles(role)"
                                            ><i class="fas fa-edit editIcon"></i
                                        ></a>
                                        <a
                                            href="#"
                                            title="Edit Role Permissions"
                                            @click.prevent="editPermissions(role)"
                                            ><i class="fas fa-lock permissionIcon"></i
                                        ></a>
                                        <a
                                            href="#"
                                            @click.prevent="
                                                confirmRoleDeletion(role)
                                            "
                                            ><i class="fas fa-trash delIcon"></i
                                        ></a>
                                    </td>
                                </tr>
                            </tbody>
                            <tbody v-else>
                                <tr>
                                    <td colspan="6" class="text-center">
                                        No Record Found......
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    </div>
                </div>
<!-- Pagination to show the records -->
                <div class="row fullWidth">
                    <div class="col-12 col-sm-12 col-md-12 col-lg-12 hpadding0">
                        <Bootstrap4Pagination
                            :data="roles"
                            @pagination-change-page="getRoles"
                            class="floatRight"
                        />
                    </div>
                </div>


            </div>
        </div>
    </div>
<!-- Add / Edit Record Dialogue -->
    <div
        class="modal fade"
        id="roleFormModal"
        tabindex="-1"
        role="dialog"
        aria-labelledby="exampleModalCenterTitle"
        aria-hidden="true"
    >
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLongTitle">
                        <span v-if="editing">Edit Role</span>
                        <span v-else>Add New Role</span>
                    </h5>
                    <button
                        type="button"
                        class="close"
                        data-dismiss="modal"
                        aria-label="Close"
                    >
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <Form
                    ref="form"
                    @submit="handleSubmit"
                    :validation-schema="editing ? editSchema : addSchema"
                    v-slot="{ errors }"
                    :initial-values="formValues"
                >
                    <div class="modal-body">
                        <div class="form-group">
                            <Field
                                type="text"
                                class="form-control mb-2 mt-2"
                                :class="{ 'is-invalid': errors.title }"
                                id="title"
                                name="title"
                                placeholder="Enter Role Title"
                            />
                            <span class="invalid-feedback">{{
                                errors.title
                            }}</span>
                        </div>
                        <div class="row fullWidth" v-if="! editing">
                            <div v-for="(permission, index) in permissions.data"
                                    :key="permission.id" class="col-12 col-sm-12 col-md-4 col-lg-4">
                                <Field
                                    :name= permission.title 
                                    value="1"
                                    type="checkbox"
                                   
                                /><label class="checkBoxLabel">{{ permission.title }}</label>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button
                            type="button"
                            class="btn btn-secondary"
                            data-dismiss="modal"
                        >
                            Close
                        </button>
                        <button type="submit" class="btn btn-primary">
                            Save
                        </button>
                    </div>
                </Form>
            </div>
        </div>
    </div>


    <div
        class="modal fade"
        id="updatePermissionModal"
        tabindex="-1"
        role="dialog"
        aria-labelledby="exampleModalCenterTitle"
        aria-hidden="true"
    >
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLongTitle">
                        <span>Edit Role Permissions</span>
                    </h5>
                    <button
                        type="button"
                        class="close"
                        data-dismiss="modal"
                        aria-label="Close"
                    >
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <Form
                    ref="form"
                    @submit="handleSubmitPermissions"
                    v-slot="{ errors }"
                    :initial-values="formValues"
                >
                    <div class="modal-body">
                        
                        <div class="row fullWidth">
                            <div v-for="(permission, index) in permissions.data"
                                    :key="permission.id" class="col-12 col-sm-12 col-md-4 col-lg-4">
                                <Field
                                    :name= permission.title 
                                    value="1"
                                    type="checkbox"
                                /><label class="checkBoxLabel">{{ permission.title }}</label>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button
                            type="button"
                            class="btn btn-secondary"
                            data-dismiss="modal"
                        >
                            Close
                        </button>
                        <button type="submit" class="btn btn-primary">
                            Save
                        </button>
                    </div>
                </Form>
            </div>
        </div>
    </div>
<!-- Delete Record Confirmation Dialogue -->
    <div
        class="modal fade"
        id="confirmationModal"
        tabindex="-1"
        role="dialog"
        aria-labelledby="exampleModalCenterTitle"
        aria-hidden="true"
    >
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLongTitle">
                        <span>Delete Role</span>
                    </h5>
                    <button
                        type="button"
                        class="close"
                        data-dismiss="modal"
                        aria-label="Close"
                    >
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    <h5>Are you sure you want to delete this Role?</h5>
                </div>
                <div class="modal-footer">
                    <button
                        type="button"
                        class="btn btn-secondary"
                        data-dismiss="modal"
                    >
                        Close
                    </button>
                    <button
                        type="button"
                        @click.prevent="deleteRole"
                        class="btn btn-primary"
                    >
                        Delete Role
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>
