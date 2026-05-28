import { render, screen, within } from '@testing-library/react';
import { describe, expect, it } from 'vitest';
import PublicAuthModals from './PublicAuthModals.jsx';

const props = {
    captchaHtml: '<div data-testid="captcha">captcha</div>',
    csrfToken: 'csrf-token',
    facebookLoginUrl: '/auth/facebook',
    forgotPasswordUrl: '/password/email',
    googleLoginUrl: '/auth/google',
    loginUrl: '/login',
    registerUrl: '/register',
};

describe('PublicAuthModals', () => {
    it('renders register modal with Laravel-compatible fields', () => {
        render(<PublicAuthModals {...props} />);

        const modal = document.getElementById('modal-register');
        const form = within(modal).getByRole('button', { name: 'Đăng ký' }).closest('form');

        expect(modal).toHaveClass('modal', 'fade');
        expect(form).toHaveAttribute('action', '/register');
        expect(form).toHaveAttribute('method', 'post');
        expect(within(modal).getByDisplayValue('csrf-token')).toHaveAttribute('name', '_token');
        expect(within(modal).getByPlaceholderText('Họ và tên')).toHaveAttribute('name', 'name');
        expect(within(modal).getByPlaceholderText('Số điện thoại')).toHaveAttribute('name', 'mobile');
        expect(within(modal).getByPlaceholderText('Nhập lại mật khẩu')).toHaveAttribute('name', 'password_confirmation');
        expect(modal.querySelector('[data-testid="captcha"]')).not.toBeNull();
        expect(within(modal).getByRole('button', { name: 'Hủy' })).toHaveAttribute('data-dismiss', 'modal');
    });

    it('renders login modal with social links and switch links used by legacy JS', () => {
        render(<PublicAuthModals {...props} />);

        const modal = document.getElementById('modal-login');
        const form = within(modal).getByRole('button', { name: 'Đăng Nhập' }).closest('form');

        expect(form).toHaveAttribute('id', 'login');
        expect(form).toHaveAttribute('action', '/login');
        expect(within(modal).getByRole('link', { name: /Google/ })).toHaveAttribute('href', '/auth/google');
        expect(within(modal).getByRole('link', { name: /Facebook/ })).toHaveAttribute('href', '/auth/facebook');
        expect(modal.querySelector('.btn-register')).not.toBeNull();
        expect(modal.querySelector('.btn-forgot-password')).not.toBeNull();
    });

    it('renders forgot password modal with required email field', () => {
        render(<PublicAuthModals {...props} />);

        const modal = document.getElementById('modal-forgot-password');
        const form = within(modal).getByRole('button', { name: 'GỬI' }).closest('form');

        expect(form).toHaveAttribute('action', '/password/email');
        expect(within(modal).getByPlaceholderText('Email')).toBeRequired();
    });
});
