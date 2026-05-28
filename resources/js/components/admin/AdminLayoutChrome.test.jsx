import { render, screen, within } from '@testing-library/react';
import { describe, expect, it } from 'vitest';
import AdminFooterLogout from './AdminFooterLogout.jsx';
import AdminSidebar from './AdminSidebar.jsx';
import AdminTopNav from './AdminTopNav.jsx';
import AdminResourceTable from './AdminResourceTable.jsx';

describe('Admin layout chrome', () => {
    it('renders top nav with logout modal trigger', () => {
        render(<AdminTopNav brandUrl="/admin" userName="Admin" labels={{ brand: 'Goda', hello: 'Chào', logout: 'Thoát' }} />);

        expect(screen.getByRole('link', { name: 'Goda' })).toHaveAttribute('href', '/admin');
        expect(screen.getByText(/Chào Admin/)).toBeInTheDocument();
        expect(screen.getByRole('link', { name: 'Thoát' })).toHaveAttribute('data-target', '#logoutModal');
        expect(document.getElementById('sidebarToggle')).toBeInTheDocument();
    });

    it('renders sidebar links and open dropdown state', () => {
        render(
            <AdminSidebar
                items={[
                    { active: true, href: '/admin', icon: 'fas fa-fw fa-tachometer-alt', label: 'Tổng quan' },
                    {
                        icon: 'fas fa-folder',
                        label: 'Thương hiệu',
                        open: true,
                        children: [
                            { active: true, href: '/admin/brands', label: 'Danh sách' },
                            { href: '/admin/brands/create', label: 'Thêm' },
                        ],
                    },
                ]}
            />,
        );

        expect(screen.getByRole('link', { name: /Tổng quan/ })).toHaveAttribute('href', '/admin');
        expect(screen.getByRole('link', { name: /Danh sách/ })).toHaveClass('active');
        expect(screen.getByText('Thương hiệu').closest('li')).toHaveClass('show');
    });

    it('renders footer and Laravel-compatible logout form', () => {
        render(
            <AdminFooterLogout
                csrfToken="csrf-token"
                logoutUrl="/admin/logout"
                labels={{ cancel: 'Hủy', copyright: 'Copyright © Goda', exit: 'Thoát', exitConfirm: 'Bạn muốn thoát?' }}
            />,
        );

        const modal = document.getElementById('logoutModal');

        expect(screen.getByText('Copyright © Goda')).toBeInTheDocument();
        expect(screen.getByRole('link')).toHaveAttribute('href', '#page-top');
        expect(within(modal).getByText('Bạn muốn thoát?')).toBeInTheDocument();
        expect(within(modal).getByDisplayValue('csrf-token')).toHaveAttribute('name', '_token');
        expect(within(modal).getByRole('button', { hidden: true, name: 'Thoát' }).closest('form')).toHaveAttribute('action', '/admin/logout');
    });

    it('renders resource table inside optional page chrome', () => {
        render(
            <AdminResourceTable
                breadcrumbs={[
                    { href: '/admin', label: 'Quản lý' },
                    { active: true, label: 'Danh mục' },
                ]}
                actions={[
                    { href: '/admin/categories/create', label: 'Thêm' },
                    { label: 'Xóa', name: 'delete', type: 'submit' },
                ]}
                rows={[{ id: 1, name: 'Kem', status: 1, created_at: '2026', updated_at: '2026', editUrl: '#', deleteUrl: '#' }]}
            />,
        );

        expect(screen.getByRole('link', { name: 'Quản lý' })).toHaveAttribute('href', '/admin');
        expect(screen.getByRole('link', { name: 'Thêm' })).toHaveAttribute('href', '/admin/categories/create');
        expect(screen.getByDisplayValue('Xóa')).toHaveAttribute('name', 'delete');
        expect(screen.getByText('Kem')).toBeInTheDocument();
    });
});
