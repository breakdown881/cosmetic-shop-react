import { render, screen } from '@testing-library/react';
import { describe, expect, it } from 'vitest';
import CustomerAccountPage from './CustomerAccountPage.jsx';

describe('CustomerAccountPage', () => {
    it('renders authenticated customer profile form', () => {
        render(
            <CustomerAccountPage
                navItems={[{ label: 'Products', href: '/products' }]}
                profile={{ name: 'Old Customer', email: 'old-customer@example.test' }}
            />,
        );

        expect(screen.getByRole('banner')).toHaveClass('react-customer-layout__header');
        expect(screen.getByRole('heading', { name: 'Tài khoản' })).toBeInTheDocument();
        expect(screen.getByLabelText('Name')).toHaveValue('Old Customer');
        expect(screen.getByLabelText('Email')).toHaveValue('old-customer@example.test');
        expect(screen.getByRole('button', { name: 'Save profile' })).toBeEnabled();
        expect(screen.queryByText(/Admin/i)).not.toBeInTheDocument();
    });

    it('renders guest prompt without profile fields', () => {
        render(<CustomerAccountPage requiresAuth />);

        expect(screen.getByText('Please sign in to manage your account.')).toBeInTheDocument();
        expect(screen.getByRole('link', { name: 'Continue shopping' })).toHaveAttribute('href', '/products');
        expect(screen.queryByLabelText('Email')).not.toBeInTheDocument();
    });
});
