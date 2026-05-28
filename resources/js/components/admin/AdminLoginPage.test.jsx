import { render, screen } from '@testing-library/react';
import { describe, expect, it } from 'vitest';
import AdminLoginPage from './AdminLoginPage.jsx';

describe('AdminLoginPage', () => {
    it('renders Laravel-compatible admin login form', () => {
        render(
            <AdminLoginPage
                action="/admin/login"
                csrfToken="csrf-token"
                logoUrl="/adm/images/logo.jpg"
                labels={{ email: 'Email', login: 'Đăng nhập', password: 'Mật khẩu', rememberMe: 'Ghi nhớ' }}
            />,
        );

        const form = screen.getByRole('button', { name: 'Đăng nhập' }).closest('form');

        expect(screen.getByAltText('Admin logo')).toHaveAttribute('src', '/adm/images/logo.jpg');
        expect(form).toHaveAttribute('action', '/admin/login');
        expect(form).toHaveAttribute('method', 'post');
        expect(screen.getByDisplayValue('csrf-token')).toHaveAttribute('name', '_token');
        expect(screen.getByLabelText('Email')).toHaveAttribute('name', 'email');
        expect(screen.getByLabelText('Mật khẩu')).toHaveAttribute('name', 'password');
        expect(screen.getByLabelText('Ghi nhớ')).toHaveAttribute('name', 'remember-me');
    });
});
