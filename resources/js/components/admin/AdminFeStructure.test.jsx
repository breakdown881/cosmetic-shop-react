import { readFileSync, existsSync } from 'node:fs';
import { resolve } from 'node:path';
import { render, screen } from '@testing-library/react';
import { describe, expect, it } from 'vitest';
import AdminLayout from './AdminLayout.jsx';

const root = resolve(__dirname, '../../../..');
const file = (path) => readFileSync(resolve(root, path), 'utf8');

describe('Admin FE structure', () => {
    it('keeps admin layout as a shared component like customer layout', () => {
        render(
            <AdminLayout
                csrfToken="csrf-token"
                logoutUrl="/admin/logout"
                sidebarItems={[{ label: 'Overview', href: '/admin', icon: 'fas fa-home', active: true }]}
                userName="Admin"
            >
                <section>Admin page content</section>
            </AdminLayout>,
        );

        expect(screen.getByRole('navigation')).toBeInTheDocument();
        expect(screen.getByRole('link', { name: /Overview/ })).toHaveAttribute('href', '/admin');
        expect(screen.getByText('Admin page content')).toBeInTheDocument();
        expect(screen.getByText(/Chao Admin/)).toBeInTheDocument();
    });

    it('separates admin pages from reusable admin components', () => {
        expect(existsSync(resolve(root, 'resources/js/pages/admin/AdminSpaApp.jsx'))).toBe(true);
        expect(existsSync(resolve(root, 'resources/js/pages/admin/AdminPageRouter.jsx'))).toBe(true);
        expect(existsSync(resolve(root, 'resources/js/pages/admin/AdminDashboardPage.jsx'))).toBe(true);
        expect(existsSync(resolve(root, 'resources/js/components/admin/AdminLayout.jsx'))).toBe(true);
        expect(existsSync(resolve(root, 'resources/js/components/admin/AdminSpaApp.jsx'))).toBe(false);

        const adminEntry = file('resources/js/admin.jsx');
        const adminPage = file('resources/js/pages/admin/AdminSpaApp.jsx');
        const adminRouter = file('resources/js/pages/admin/AdminPageRouter.jsx');

        expect(adminEntry).toContain("./pages/admin/AdminSpaApp.jsx");
        expect(adminPage).toContain('AdminLayout');
        expect(adminPage).toContain('AdminPageRouter');
        expect(adminRouter).toContain('./AdminDashboardPage.jsx');
        expect(adminPage).not.toContain('function DashboardPage');
        expect(adminPage).not.toContain('function ProductPage');
    });
});
