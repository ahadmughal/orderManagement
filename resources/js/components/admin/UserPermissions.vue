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
const editing = ref(false);
const formValues = ref();
const form = ref(null);
const toastr = useToastr();
const dataToBeDeleted = ref(null);
const searchQuery = ref(null);
const selectedPermissions = ref([]);
const selectAll = ref(false);
const selectedAll = ref(false);

const getPermissions = (page = 1) => {
    axios
        .get(`/api/permissions?page=${page}`, {
            params: {
                query: searchQuery.value,
            },
        })

        .then((response) => {
            permissions.value = response.data;
            selectedPermissions.value = [];
            selectAll.value = false;
            selectedAll.value = false;
        });
};

const addSchema = yup.object({
    title: yup.string().required(),
});

const editSchema = yup.object({
    title: yup.string().required(),
});

const createPermission = (values, { resetForm, setErrors }) => {
    axios
        .post("/api/permissions", values)
        .then((response) => {
            permissions.value.data.unshift(response.data);
            $("#permissionFormModal").modal("hide");
            toastr.success("Permission Added Successfully");
            resetForm();
        })
        .catch((error) => {
            if (error.response.data.errors) {
                setErrors(error.response.data.errors);
            }
        });
};

const updatePermission = (values, { setErrors }) => {
    axios
        .put("/api/permissions/" + formValues.value.id, values)
        .then((response) => {
            const index = permissions.value.data.findIndex(
                (permission) => permission.id === response.data.id
            );
            permissions.value.data[index] = response.data;
            $("#permissionFormModal").modal("hide");
            toastr.success("Permission Updated Successfully");
        })
        .catch((error) => {
            if (error.response.data.errors) {
                setErrors(error.response.data.errors);
            }
        });
};

const handleSubmit = (values, actions) => {
    if (editing.value) {
        updatePermission(values, actions);
    } else {
        createPermission(values, actions);
    }
};

const addPermission = () => {
    editing.value = false;
    $("#permissionFormModal").modal("show");
};

const editPermissions = (permission) => {
    editing.value = true;
    form.value.resetForm();
    $("#permissionFormModal").modal("show");
    formValues.value = {
        id: permission.id,
        title: permission.title,
    };
};

const confirmPermissionDeletion = (permission) => {
    dataToBeDeleted.value = permission.id;
    $("#confirmationModal").modal("show");
};

const deletePermission = () => {
    axios.delete(`/api/permissions/${dataToBeDeleted.value}`).then(() => {
        $("#confirmationModal").modal("hide");
        permissions.value.data = permissions.value.data.filter(
            (permission) => permission.id !== dataToBeDeleted.value
        );
        toastr.success("Permission Deleted Successfully");
    });
};

watch(
    searchQuery,
    debounce(() => {
        getPermissions();
    }, 300)
);

const toggleSelection = (permission) => {
    const index = selectedPermissions.value.indexOf(permission.id);
    if (index === -1) {
        selectedPermissions.value.push(permission.id);
    } else {
        selectedPermissions.value.splice(index, 1);
    }
};

const bulkDelete = () => {
    axios
        .delete("/api/permissions", {
            data: {
                ids: selectedPermissions.value,
            },
        })
        .then((response) => {
            permissions.value.data = permissions.value.data.filter(
                (permission) => !selectedPermissions.value.includes(permission.id)
            );
            selectedPermissions.value = [];
            toastr.success("Permissions Deleted Successfully");
            location.reload();
        });
};

const selectAllPermissions = () => {
    if (selectAll.value) {
        selectedPermissions.value = permissions.value.data.map((permission) => permission.id);
        selectedAll.value = true;
    } else {
        selectedAll.value = false;
        selectedPermissions.value = [];
        selectAll.value = false;
    }
};

onMounted(() => {
    getPermissions();
});
</script>
<template>
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Manage Permissions</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item" active-class="active">
                            <router-link to="/admin/dashboard"
                                >Dashboard</router-link
                            >
                        </li>
                        <li class="breadcrumb-item" active-class="active">
                            Permissions
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
                        @click="addPermission"
                    >
                        <i class="fas fa-plus-circle mr-1"></i>
                        Add New Permission
                    </button>

                    <button
                        v-if="selectedPermissions.length > 0"
                        type="button"
                        class="btn btn-danger ml-2 mb-2"
                        @click="bulkDelete"
                    >
                        <i class="fas fa-trash mr-1"></i>
                        Delete Selected
                    </button>

                    <span v-if="selectedPermissions.length > 0" class="ml-2"
                        >Selected {{ selectedPermissions.length }} Permissions</span
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
                                    <th scope="col">Creation Date</th>
                                    <th scope="col">Actions</th>
                                </tr>
                            </thead>
                            <tbody v-if="permissions.data.length > 0">
                                <tr
                                    v-for="(permission, index) in permissions.data"
                                    :key="permissions.id"
                                >
                                    <td>
                                        <input
                                            :checked="selectedAll"
                                            type="checkbox"
                                            @change="toggleSelection(permission)"
                                        />
                                    </td>
                                    <td scope="row">{{ index + 1 }}</td>
                                    <td>{{ permission.title }}</td>
                                    <td>{{ moment(permission.created_at).format("ddd MMM DD, YYYY [at] HH:mm A") }}</td>
                                    
                                    <td>
                                        <a
                                            href="#"
                                            @click.prevent="editPermissions(permission)"
                                            ><i class="fas fa-edit editIcon"></i
                                        ></a>
                                        <a
                                            href="#"
                                            @click.prevent="
                                                confirmPermissionDeletion(permission)
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
                            :data="permissions"
                            @pagination-change-page="getPermissions"
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
        id="permissionFormModal"
        tabindex="-1"
        role="dialog"
        aria-labelledby="exampleModalCenterTitle"
        aria-hidden="true"
    >
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLongTitle">
                        <span v-if="editing">Edit Permission</span>
                        <span v-else>Add New Permission</span>
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
                                placeholder="Enter Permission Title in small letters"
                            />
                            <span class="invalid-feedback">{{
                                errors.title
                            }}</span>
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
                        <span>Delete Permission</span>
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
                    <h5>Are you sure you want to delete this Permission?</h5>
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
                        @click.prevent="deletePermission"
                        class="btn btn-primary"
                    >
                        Delete Permission
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>
