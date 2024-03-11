<template>
        <div class="content-header">
               <div class="container-fluid">
                   <div class="row mb-2">
                       <div class="col-sm-6">
                           <h1 class="m-0">Appointments Page</h1>
                       </div>
                       <div class="col-sm-6">
                           <ol class="breadcrumb float-sm-right">
                               <li class="breadcrumb-item"><a href="#">Home</a></li>
                               <li class="breadcrumb-item active">Appointments</li>
                           </ol>
                       </div>
                   </div>
               </div>
           </div>


           <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12">
                    <div class="d-flex justify-content-between mb-2">
                        <div>
                            <a href="">
                                <button class="btn btn-primary"><i class="fa fa-plus-circle mr-1"></i> Add New
                                    Appointment</button>
                            </a>
                        </div>
                        <div class="btn-group">
                            <button type="button" class="btn btn-secondary">
                                <span class="mr-1">All</span>
                                <span class="badge badge-pill badge-info">1</span>
                            </button>

                            <button type="button" class="btn btn-default">
                                <span class="mr-1">Scheduled</span>
                                <span class="badge badge-pill badge-primary">0</span>
                            </button>

                            <button type="button" class="btn btn-default">
                                <span class="mr-1">Confirmed</span>
                                <span class="badge badge-pill badge-success">0</span>
                            </button>

                            <button type="button" class="btn btn-default">
                                <span class="mr-1">Cancelled</span>
                                <span class="badge badge-pill badge-danger">0</span>
                            </button>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-body">
                            <div class="table-responsive">
                            <table class="table table-bordered">
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
                                        <th scope="col">Client Name</th>
                                        <th scope="col">Title</th>
                                        <th scope="col">Description</th>
                                        <th scope="col">Start Time</th>
                                        <th scope="col">Status</th>
                                        <th scope="col">Options</th>
                                    </tr>
                                </thead>
                                <tbody v-if="appoitments.data.length > 0">
                                <tr
                                    v-for="(appointment, index) in appoitments.data"
                                    :key="appointment.id"
                                >
                                    <th>
                                        <input
                                            :checked="selectedAll"
                                            type="checkbox"
                                            @change="toggleSelection(appointment)"
                                        />
                                    </th>
                                    <th scope="row">{{ index + 1 }}</th>
                                    <td>{{ appointment.client.first_name }} {{ appointment.client.last_name }}</td>
                                    <td>{{ appointment.title }}</td>
                                    <td>{{ appointment.description }}</td>
                                    <td>{{ moment(appointment.start_time).format("ddd MMM DD, YYYY [at] HH:mm A") }}</td>
                                    <td>
                                        <span v-if="appointment.status == 1" class="badge badge-primary">Scheduled</span>
                                        <span v-if="appointment.status == 2" class="badge badge-success">Confirmed</span>
                                        <span v-if="appointment.status == 3" class="badge badge-danger">Cancelled</span>
                                    </td>
                                    <td>
                                        <a
                                            href="#"
                                            @click.prevent="editUser(appointment)"
                                            ><i class="fas fa-edit editIcon"></i
                                        ></a>
                                        <a
                                            href="#"
                                            @click.prevent="
                                                confirmUserDeletion(appointment)
                                            "
                                            ><i class="fas fa-trash delIcon"></i
                                        ></a>
                                    </td>
                                </tr>
                            </tbody>
                            <tbody v-else>
                                <tr>
                                    <td colspan="7" class="text-center">
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
                            :data="appoitments"
                            @pagination-change-page="getAppoitments"
                            class="floatRight"
                        />
                    </div>
                </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import axios from "axios";
import * as yup from "yup";
import { Form, Field } from "vee-validate";
import { ref, onMounted, reactive, watch } from "vue";
import { useToastr } from "../../toastr.js";
import { debounce } from "lodash";
import moment from 'moment'
import { Bootstrap4Pagination } from "laravel-vue-pagination";
import VueFilterDateFormat from '@vuejs-community/vue-filter-date-format';

const appoitments = ref({ data: [] });
const editing = ref(false);
const formValues = ref();
const form = ref(null);
const toastr = useToastr();
const dataToBeDeleted = ref(null);
const searchQuery = ref(null);
const selectedappoitments = ref([]);
const selectAll = ref(false);
const selectedAll = ref(false);
const appointmentStatus = {'scheduled':1, 'confirmed':2, 'cancelled':3, }

const getAppoitments = (page = 1) => {
       
    axios.get(`/api/appointments?page=${page}`)

        .then((response) => {
            appoitments.value = response.data;
            selectedappoitments.value = [];
            selectAll.value = false;
            selectedAll.value = false;
        });
};

const addSchema = yup.object({
    name: yup.string().required(),
    email: yup.string().email().required(),
    password: yup.string().required().min(8),
});

const editSchema = yup.object({
    name: yup.string().required(),
    email: yup.string().email().required(),
    password: yup.string().when((password, schema) => {
        return password ? schema.min(8) : schema;
    }),
});

const createUser = (values, { resetForm, setErrors }) => {
    axios
        .post("/api/appoitments", values)
        .then((response) => {
            appoitments.value.data.unshift(response.data);
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
        .put("/api/appoitments/" + formValues.value.id, values)
        .then((response) => {
            const index = appoitments.value.findIndex(
                (user) => user.id === response.data.id
            );
            appoitments.value[index] = response.data;
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
    axios.delete(`/api/appoitments/${dataToBeDeleted.value}`).then(() => {
        $("#confirmationModal").modal("hide");
        appoitments.value = appoitments.value.filter(
            (user) => user.id !== dataToBeDeleted.value
        );
        toastr.success("User Deleted Successfully");
    });
};

const chnageRole = (user, role) => {
    axios
        .patch(`/api/appoitments/${user.id}/change-role`, {
            role: role,
        })
        .then(() => {
            toastr.success("Role Changed Successfully");
        });
};

const search = () => {
    axios
        .get("/api/appoitments/search", {
            params: {
                query: searchQuery.value,
            },
        })
        .then((response) => {
            appoitments.value = response.data;
        })
        .catch((error) => {
            toastr.danger(error);
        });
};

watch(
    searchQuery,
    debounce(() => {
        search();
    }, 300)
);

const toggleSelection = (user) => {
    const index = selectedappoitments.value.indexOf(user.id);
    if (index === -1) {
        selectedappoitments.value.push(user.id);
    } else {
        selectedappoitments.value.splice(index, 1);
    }
};

const bulkDelete = () => {
    axios
        .delete("/api/appoitments", {
            data: {
                ids: selectedappoitments.value,
            },
        })
        .then((response) => {
            appoitments.value.data = appoitments.value.data.filter(
                (user) => !selectedappoitments.value.includes(user.id)
            );
            selectedappoitments.value = [];
            toastr.success("appoitments Deleted Successfully");
        });
};

const selectAllappoitments = () => {
    if (selectAll.value) {
        selectedappoitments.value = appoitments.value.data.map((user) => user.id);
        selectedAll.value = true;
    } else {
        selectedAll.value = false;
        selectedappoitments.value = [];
        selectAll.value = false;
    }
};

onMounted(() => {
    getAppoitments();
});
</script>