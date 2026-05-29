import AdminFooterLogout from './AdminFooterLogout.jsx';
import AdminSidebar from './AdminSidebar.jsx';
import AdminTopNav from './AdminTopNav.jsx';

export default function AdminLayout({
    children,
    csrfToken = '',
    footerLabels = { cancel: 'Cancel', copyright: 'Copyright Hoang Hai', exit: 'Exit', exitConfirm: 'Logout?' },
    liveChatUnreadCount = 0,
    logoutUrl = '/admin/logout',
    sidebarItems = [],
    topNavLabels = { brand: 'Goda', hello: 'Chao', logout: 'Logout' },
    userName = '',
}) {
    return (
        <>
            <AdminTopNav
                brandUrl="/admin"
                liveChatUnreadCount={liveChatUnreadCount}
                userName={userName}
                labels={topNavLabels}
            />
            <div id="wrapper">
                <AdminSidebar items={sidebarItems} />
                <div id="content-wrapper">{children}</div>
                <AdminFooterLogout
                    csrfToken={csrfToken}
                    logoutUrl={logoutUrl}
                    labels={footerLabels}
                />
            </div>
        </>
    );
}
