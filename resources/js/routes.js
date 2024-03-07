import Dashboard from './components/Dashboard.vue';
import Appointments from './components/admin/Appointments.vue';
import Users from './components/admin/Users.vue';
import Settings from './components/admin/Settings.vue';
import Profile from './components/admin/profile.vue';
import UserPermissions from './components/admin/UserPermissions.vue';
import Roles from './components/admin/Roles.vue';
import Login from './pages/auth/Login.vue';



export default[
    {
        path: '/login',
        name: 'login',
        component: Login,
    },

    {
        path: '/admin/dashboard',
        name: 'admin.dashboard',
        component: Dashboard,
    },

    {
        path: '/admin/users',
        name: 'admin.users',
        component: Users,
    },

    {
        path: '/admin/permissions',
        name: 'admin.permissions',
        component: UserPermissions,
    },

    {
        path: '/admin/roles',
        name: 'admin.roles',
        component: Roles,
    },

    {
        path: '/admin/appointments',
        name: 'admin.appointments',
        component: Appointments,
    },

    

    {
        path: '/admin/settings',
        name: 'admin.settings',
        component: Settings,
    },

    {
        path: '/admin/profile',
        name: 'admin.profile',
        component: Profile,
    }
]