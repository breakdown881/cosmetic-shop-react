import './bootstrap';

import AlertMessages from './components/common/AlertMessages.jsx';
import AdminApiResourceManager from './components/admin/AdminApiResourceManager.jsx';
import AdminDashboard from './components/admin/AdminDashboard.jsx';
import AdminFooterLogout from './components/admin/AdminFooterLogout.jsx';
import AdminLoginPage from './components/admin/AdminLoginPage.jsx';
import AdminMediaManager from './components/admin/AdminMediaManager.jsx';
import AdminOrderManager from './components/admin/AdminOrderManager.jsx';
import AdminResourceForm from './components/admin/AdminResourceForm.jsx';
import AdminResourceTable from './components/admin/AdminResourceTable.jsx';
import AdminSidebar from './components/admin/AdminSidebar.jsx';
import AdminTopNav from './components/admin/AdminTopNav.jsx';
import { mountReactIslands } from './islands/mountReactIslands.jsx';

mountReactIslands({
    AdminApiResourceManager,
    AlertMessages,
    AdminDashboard,
    AdminFooterLogout,
    AdminLoginPage,
    AdminMediaManager,
    AdminOrderManager,
    AdminResourceForm,
    AdminResourceTable,
    AdminSidebar,
    AdminTopNav,
});
