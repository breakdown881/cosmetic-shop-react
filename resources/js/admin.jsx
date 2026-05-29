import './bootstrap';

import AlertMessages from './components/common/AlertMessages.jsx';
import AdminApiResourceManager from './components/admin/AdminApiResourceManager.jsx';
import AdminDashboard from './pages/admin/AdminDashboardPage.jsx';
import AdminFooterLogout from './components/admin/AdminFooterLogout.jsx';
import AdminLoginPage from './pages/admin/AdminLoginPage.jsx';
import AdminMediaManager from './pages/admin/AdminMediaPage.jsx';
import AdminNewsletterManager from './pages/admin/AdminNewsletterPage.jsx';
import AdminOrderManager from './pages/admin/AdminOrderPage.jsx';
import AdminResourceForm from './components/admin/AdminResourceForm.jsx';
import AdminResourceTable from './components/admin/AdminResourceTable.jsx';
import AdminSidebar from './components/admin/AdminSidebar.jsx';
import AdminSpaApp from './pages/admin/AdminSpaApp.jsx';
import AdminTopNav from './components/admin/AdminTopNav.jsx';
import { mountReactIslands } from './islands/mountReactIslands.jsx';

mountReactIslands({
    AdminApiResourceManager,
    AlertMessages,
    AdminDashboard,
    AdminFooterLogout,
    AdminLoginPage,
    AdminMediaManager,
    AdminNewsletterManager,
    AdminOrderManager,
    AdminResourceForm,
    AdminResourceTable,
    AdminSidebar,
    AdminSpaApp,
    AdminTopNav,
});
