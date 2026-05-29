import { useEffect, useMemo, useState } from 'react';
import AdminLayout from '../../components/admin/AdminLayout.jsx';
import AdminPageRouter, { buildSidebarItems, normalizePath } from './AdminPageRouter.jsx';

export default function AdminSpaApp({
    csrfToken = '',
    logoutUrl = '/admin/logout',
    role = '',
    userName = '',
}) {
    const [path, setPath] = useState(() => normalizePath(window.location.pathname));
    const sidebarItems = useMemo(() => buildSidebarItems(role, path), [path, role]);

    useEffect(() => {
        const handlePopState = () => setPath(normalizePath(window.location.pathname));
        const handleClick = (event) => {
            if (event.defaultPrevented || event.button !== 0 || event.metaKey || event.ctrlKey || event.shiftKey || event.altKey) {
                return;
            }

            const anchor = event.target.closest('a[href]');

            if (!anchor || anchor.getAttribute('href').startsWith('#') || anchor.target) {
                return;
            }

            const url = new URL(anchor.href, window.location.origin);

            if (url.origin !== window.location.origin || !url.pathname.startsWith('/admin') || url.pathname.startsWith('/admin/api') || url.pathname === '/admin/login') {
                return;
            }

            event.preventDefault();
            window.history.pushState({}, '', `${url.pathname}${url.search}${url.hash}`);
            setPath(normalizePath(url.pathname));
        };

        window.addEventListener('popstate', handlePopState);
        document.addEventListener('click', handleClick);

        return () => {
            window.removeEventListener('popstate', handlePopState);
            document.removeEventListener('click', handleClick);
        };
    }, []);

    return (
        <AdminLayout
            csrfToken={csrfToken}
            logoutUrl={logoutUrl}
            sidebarItems={sidebarItems}
            userName={userName}
        >
            <AdminPageRouter key={path} path={path} role={role} />
        </AdminLayout>
    );
}
