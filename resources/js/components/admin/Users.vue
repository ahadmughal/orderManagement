<script setup>
import axios from "axios";
import * as yup from "yup";
import { Form, Field } from "vee-validate";
import { ref, onMounted, reactive, watch } from "vue";
import { useToastr } from "../../toastr.js";
import { debounce } from "lodash";
import moment from "moment";
import { Bootstrap4Pagination } from "laravel-vue-pagination";

const users = ref({ data: [] });
const userRoles = ref({ data: [] });
const editing = ref(false);
const formValues = ref();
const form = ref(null);
const toastr = useToastr();
const dataToBeDeleted = ref(null);
const searchQuery = ref(null);
const selectedUsers = ref([]);
const selectAll = ref(false);
const selectedAll = ref(false);

const getRoles = (page = 1) => {
    axios
        .get(`/api/roles?page=${page}`, {
            params: {
                query: searchQuery.value,
            },
        })

        .then((response) => {
            userRoles.value = response.data;
        });
};

const getUsers = (page = 1) => {
    axios
        .get(`/api/users?page=${page}`, {
            params: {
                query: searchQuery.value,
            },
        })

        .then((response) => {
            users.value = response.data;
            selectedUsers.value = [];
            selectAll.value = false;
            selectedAll.value = false;
        });
};

const addSchema = yup.object({
    name: yup.string().required(),
    email: yup.string().email().required(),
    password: yup.string().required().min(8),
    roleAssigned: yup.string().required(),
 
});

const editSchema = yup.object({
    name: yup.string().required(),
    email: yup.string().email().required(),
    password: yup.string().when((password, schema) => {
        return password ? schema.min(8) : schema;
    }),
    roleAssigned: yup.string(),
});

const createUser = (values, { resetForm, setErrors }) => {

        axios
        .post("/api/users", values)
        .then((response) => {
            users.value.data.unshift(response.data);
            $("#userFormModal").modal("hide");
            toastr.success("User Created Successfully");
            resetForm();
        })
        .catch((error) => {
            if (error.response.data.errors) {
                setErrors(error.response.data.errors);
            }
        });
};

const updateUser = (values, { setErrors }) => {
    axios
        .put("/api/users/" + formValues.value.id, values)
        .then((response) => {
            const index = users.value.data.findIndex(
                (user) => user.id === response.data.id
            );
            users.value.data[index] = response.data;
            $("#userFormModal").modal("hide");
            toastr.success("User Updated Successfully");
        })
        .catch((error) => {
            if (error.response.data.errors) {
                setErrors(error.response.data.errors);
            }
        });
};

const handleSubmit = (values, actions) => {
    if (editing.value) {
        updateUser(values, actions);
    } else {
        createUser(values, actions);
    }
};

const addUser = () => {
    editing.value = false;
    $("#userFormModal").modal("show");
};

const editUser = (user) => {
    editing.value = true;
    form.value.resetForm();
    $("#userFormModal").modal("show");
    formValues.value = {
        id: user.id,
        name: user.name,
        email: user.email,
    };
};

const confirmUserDeletion = (user) => {
    dataToBeDeleted.value = user.id;
    $("#confirmationModal").modal("show");
};

const deleteUser = () => {
    axios.delete(`/api/users/${dataToBeDeleted.value}`).then(() => {
        $("#confirmationModal").modal("hide");
        users.value.data = users.value.data.filter(
            (user) => user.id !== dataToBeDeleted.value
        );
        toastr.success("User Deleted Successfully");
    });
};

const roles = ref([
    {
        name: "ADMIN",
        value: 2,
    },

    {
        name: "USER",
        value: 3,
    },
]);

const chnageRole = (user, role) => {
    axios
        .patch(`/api/users/${user.id}/change-role`, {
            role: role,
        })
        .then(() => {
            toastr.success("Role Changed Successfully");
        });
};

watch(
    searchQuery,
    debounce(() => {
        getUsers();
    }, 300)
);

const toggleSelection = (user) => {
    const index = selectedUsers.value.indexOf(user.id);
    if (index === -1) {
        selectedUsers.value.push(user.id);
    } else {
        selectedUsers.value.splice(index, 1);
    }
};

const bulkDelete = () => {
    axios
        .delete("/api/users", {
            data: {
                ids: selectedUsers.value,
            },
        })
        .then((response) => {
            users.value.data = users.value.data.filter(
                (user) => !selectedUsers.value.includes(user.id)
            );
            selectedUsers.value = [];
            toastr.success("Users Deleted Successfully");
            location.reload();
        });
};

const selectAllUsers = () => {
    if (selectAll.value) {
        selectedUsers.value = users.value.data.map((user) => user.id);
        selectedAll.value = true;
    } else {
        selectedAll.value = false;
        selectedUsers.value = [];
        selectAll.value = false;
    }
};

onMounted(() => {
    getUsers();
    getRoles();
});
</script>
<template>
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Users Page</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item" active-class="active">
                            <router-link to="/admin/dashboard"
                                >Dashboard</router-link
                            >
                        </li>
                        <li class="breadcrumb-item" active-class="active">
                            Users
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
                        @click="addUser"
                    >
                        <i class="fas fa-plus-circle mr-1"></i>
                        Add New Users
                    </button>

                    <button
                        v-if="selectedUsers.length > 0"
                        type="button"
                        class="btn btn-danger ml-2 mb-2"
                        @click="bulkDelete"
                    >
                        <i class="fas fa-trash mr-1"></i>
                        Delete Selected
                    </button>

                    <span v-if="selectedUsers.length > 0" class="ml-2"
                        >Selected {{ selectedUsers.length }} Users</span
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
                                                @change="selectAllUsers"
                                            />
                                        </th>
                                        <th scope="col">#</th>
                                        <th scope="col">Name</th>
                                        <th scope="col">Email</th>
                                        <th scope="col">Registered Date</th>
                                        <th scope="col">Role</th>
                                        <th scope="col">Actions</th>
                                    </tr>
                                </thead>
                                <tbody v-if="users.data.length > 0">
                                    <tr
                                        v-for="(user, index) in users.data"
                                        :key="user.id"
                                    >
                                        <td>
                                            <input
                                                :checked="selectedAll"
                                                type="checkbox"
                                                @change="toggleSelection(user)"
                                            />
                                        </td>
                                        <td scope="row">{{ index + 1 }}</td>
                                        <td>{{ user.name }}</td>
                                        <td>{{ user.email }}</td>
                                        <td>
                                            {{
                                                moment(user.created_at).format(
                                                    "ddd MMM DD, YYYY [at] HH:mm A"
                                                )
                                            }}
                                        </td>
                                        <td>
                                           <span v-for="(userRole, index) in userRoles.data"
                                            :key="userRole.id">  <span class="permissionBadge7" v-if="userRole.id == user.role"> {{ userRole.title }} </span>  </span>
                                        </td>
                                        <td>
                                            <a
                                                href="#"
                                                @click.prevent="editUser(user)"
                                                ><i
                                                    class="fas fa-edit editIcon"
                                                ></i
                                            ></a>
                                            <a
                                                href="#"
                                                @click.prevent="
                                                    confirmUserDeletion(user)
                                                "
                                                ><i
                                                    class="fas fa-trash delIcon"
                                                ></i
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

                <div class="row fullWidth">
                    <div class="col-12 col-sm-12 col-md-12 col-lg-12 hpadding0">
                        <Bootstrap4Pagination
                            :data="users"
                            @pagination-change-page="getUsers"
                            class="floatRight"
                        />
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div
        class="modal fade"
        id="userFormModal"
        tabindex="-1"
        role="dialog"
        aria-labelledby="exampleModalCenterTitle"
        aria-hidden="true"
    >
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLongTitle">
                        <span v-if="editing">Edit User</span>
                        <span v-else>Add New User</span>
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
                                :class="{ 'is-invalid': errors.name }"
                                id="name"
                                name="name"
                                placeholder="Enter Full Name"
                            />
                            <span class="invalid-feedback">{{
                                errors.name
                            }}</span>
                        </div>

                        <div class="form-group">
                            <Field
                                type="email"
                                class="form-control mb-2 mt-2"
                                :class="{ 'is-invalid': errors.email }"
                                id="email"
                                name="email"
                                placeholder="Enter Valid Email"
                            />
                            <span class="invalid-feedback">{{
                                errors.email
                            }}</span>
                        </div>

                        <div class="form-group">
                            <Field
                                type="password"
                                class="form-control mb-2 mt-2"
                                :class="{ 'is-invalid': errors.password }"
                                id="password"
                                name="password"
                                placeholder="Enter User Password"
                            />
                            <span class="invalid-feedback">{{
                                errors.password
                            }}</span>
                        </div>

                        <div class="form-group">
                                <Field class="form-control"
                                    :class="{ 'is-invalid': errors.roleAssigned }"
                                    id="roleAssigned"
                                    name="roleAssigned"
                                    as="select"
                                    >
                                    <option v-if="editing" value="">Update User Role</option>
                                    <option v-if="! editing" value="">Select User Role</option>
                                    <option v-for="(userRole, index) in userRoles.data"
                                    :key="userRole.id" :value=userRole.id> {{ userRole.title }}</option>
                                </Field>

                                <span class="invalid-feedback">{{
                                errors.roleAssigned 
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
                        <span>Delete User</span>
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
                    <h5>Are you sure you want to delete this user?</h5>
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
                        @click.prevent="deleteUser"
                        class="btn btn-primary"
                    >
                        Delete User
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>
