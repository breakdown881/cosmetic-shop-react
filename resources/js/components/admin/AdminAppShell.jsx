import AdminApiResourceManager from './AdminApiResourceManager.jsx';
import AdminDashboard from './AdminDashboard.jsx';
import AdminFooterLogout from './AdminFooterLogout.jsx';
import AdminMediaManager from './AdminMediaManager.jsx';
import AdminNewsletterManager from './AdminNewsletterManager.jsx';
import AdminOrderManager from './AdminOrderManager.jsx';
import AdminSidebar from './AdminSidebar.jsx';
import AdminTopNav from './AdminTopNav.jsx';

const pageComponents = {
    AdminApiResourceManager,
    AdminDashboard,
    AdminMediaManager,
    AdminNewsletterManager,
    AdminOrderManager,
};

export default function AdminAppShell({
    footer = {},
    page = {},
    sidebar = {},
    topNav = {},
}) {
    const PageComponent = pageComponents[page.component] ?? null;

    return (
        <>
            <AdminTopNav {...topNav} />
            <div id="wrapper">
                <AdminSidebar {...sidebar} />
                <div id="content-wrapper">
                    {PageComponent && <PageComponent {...(page.props ?? {})} />}
                </div>
                <AdminFooterLogout {...footer} />
            </div>
        </>
    );
}
