import './bootstrap';

import AlertMessages from './components/common/AlertMessages.jsx';
import AdminDashboard from './components/admin/AdminDashboard.jsx';
import AdminFooterLogout from './components/admin/AdminFooterLogout.jsx';
import AdminLoginPage from './components/admin/AdminLoginPage.jsx';
import AdminMediaManager from './components/admin/AdminMediaManager.jsx';
import AdminResourceForm from './components/admin/AdminResourceForm.jsx';
import AdminResourceTable from './components/admin/AdminResourceTable.jsx';
import AdminSidebar from './components/admin/AdminSidebar.jsx';
import AdminTopNav from './components/admin/AdminTopNav.jsx';
import { mountReactIslands } from './islands/mountReactIslands.jsx';

mountReactIslands({
    AlertMessages,
    AdminDashboard,
    AdminFooterLogout,
    AdminLoginPage,
    AdminMediaManager,
    AdminResourceForm,
    AdminResourceTable,
    AdminSidebar,
    AdminTopNav,
});
