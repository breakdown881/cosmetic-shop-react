import { render, screen } from '@testing-library/react';
import { describe, expect, it } from 'vitest';
import CustomerLoginPage from './CustomerLoginPage.jsx';
import CustomerRegisterPage from './CustomerRegisterPage.jsx';

describe('Customer auth pages', () => {
    it('renders customer login form with auth-aware header', () => {
        render(
            <CustomerLoginPage
                csrfToken="csrf-token"
                navItems={[{ label: 'Products', href: '/products' }]}
            />,
        );

        expect(screen.getByRole('heading', { name: 'Login' })).toBeInTheDocument();
        expect(screen.getByLabelText('Email')).toHaveAttribute('name', 'email');
        expect(screen.getByLabelText('Password')).toHaveAttribute('name', 'password');
        expect(screen.getByDisplayValue('csrf-token')).toHaveAttribute('name', '_token');
        expect(screen.getByRole('link', { name: 'Create account' })).toHaveAttribute('href', '/register');
        expect(screen.getByRole('link', { name: 'Continue with Google' })).toHaveAttribute('href', '/auth/google/redirect');
        expect(screen.getByRole('link', { name: 'Continue with Facebook' })).toHaveAttribute('href', '/auth/facebook/redirect');
        expect(screen.getByRole('link', { name: 'Sign in' })).toHaveAttribute('href', '/login');
        expect(screen.queryByText(/Admin/i)).not.toBeInTheDocument();
    });

    it('renders customer register form separated from admin UI', () => {
        render(<CustomerRegisterPage csrfToken="csrf-token" />);

        expect(screen.getByRole('heading', { name: 'Create account' })).toBeInTheDocument();
        expect(screen.getByLabelText('Name')).toHaveAttribute('name', 'name');
        expect(screen.getByLabelText('Email')).toHaveAttribute('name', 'email');
        expect(screen.getByLabelText('Password')).toHaveAttribute('name', 'password');
        expect(screen.getByLabelText('Confirm password')).toHaveAttribute('name', 'password_confirmation');
        expect(screen.getByRole('link', { name: 'Sign in instead' })).toHaveAttribute('href', '/login');
        expect(screen.getByRole('link', { name: 'Continue with Google' })).toHaveAttribute('href', '/auth/google/redirect');
        expect(screen.getByRole('link', { name: 'Continue with Facebook' })).toHaveAttribute('href', '/auth/facebook/redirect');
        expect(screen.queryByText(/Admin/i)).not.toBeInTheDocument();
    });
});
