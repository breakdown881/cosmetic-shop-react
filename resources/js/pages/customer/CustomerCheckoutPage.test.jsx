import { render, screen, waitFor } from '@testing-library/react';
import userEvent from '@testing-library/user-event';
import { afterEach, beforeEach, describe, expect, it, vi } from 'vitest';
import CustomerCheckoutPage from './CustomerCheckoutPage.jsx';

const checkout = {
    cart: {
        items: [
            {
                product_id: 3,
                name: 'Checkout Serum',
                quantity: 2,
                sale_price: 270000,
                subtotal: 540000,
                image: '/adm/images/godakeben450x170.jpg',
                url: '/products/3',
            },
        ],
        total: 540000,
    },
    feeShips: [{ id: 9, label: 'City HCM', price: 25000 }],
    paymentMethods: { 0: 'Cash', 1: 'Bank transfer', 2: 'VNPay', 3: 'MoMo' },
};

describe('CustomerCheckoutPage', () => {
    beforeEach(() => {
        window.axios = {
            get: vi.fn(),
            post: vi.fn(),
        };
    });

    afterEach(() => {
        vi.restoreAllMocks();
        delete window.axios;
    });

    it('renders checkout form, cart summary and payment options', () => {
        render(
            <CustomerCheckoutPage
                checkout={checkout}
                navItems={[{ label: 'Cart', href: '/cart' }]}
            />,
        );

        expect(screen.getByRole('banner')).toHaveClass('react-customer-layout__header');
        expect(screen.getByRole('heading', { name: 'Thanh toán' })).toBeInTheDocument();
        expect(screen.getByLabelText('Full name')).toHaveAttribute('name', 'shipping_fullname');
        expect(screen.getByLabelText('Mobile')).toHaveAttribute('name', 'shipping_mobile');
        expect(screen.getByLabelText('Address')).toHaveAttribute('name', 'shipping_housenumber_street');
        expect(screen.getByLabelText('Shipping fee')).toHaveValue('9');
        expect(screen.getByLabelText('Payment method')).toHaveValue('0');
        expect(screen.getByText('Checkout Serum')).toBeInTheDocument();
        expect(screen.getByText(/540\.000/)).toBeInTheDocument();
        expect(screen.getByRole('button', { name: 'Place order' })).toBeEnabled();
        expect(screen.queryByText(/Admin/i)).not.toBeInTheDocument();
    });

    it('renders empty cart checkout state', () => {
        render(<CustomerCheckoutPage checkout={{ ...checkout, cart: { items: [], total: 0 } }} />);

        expect(screen.getByText('Your cart is empty.')).toBeInTheDocument();
        expect(screen.getByRole('link', { name: 'Back to products' })).toHaveAttribute('href', '/products');
    });

    it('redirects customers to online payment gateway after checkout', async () => {
        const user = userEvent.setup();
        const assign = vi.spyOn(window.location, 'assign').mockImplementation(() => {});
        window.axios.post.mockResolvedValue({
            data: {
                data: {
                    id: 42,
                    status: 'COMPLETED',
                    order: { id: 42 },
                    payment: {
                        redirect_url: 'https://test-payment.momo.vn/pay/demo',
                    },
                },
            },
        });

        render(<CustomerCheckoutPage checkout={checkout} />);

        await user.type(screen.getByLabelText('Full name'), 'Nguyen Van A');
        await user.type(screen.getByLabelText('Mobile'), '0900111222');
        await user.type(screen.getByLabelText('Address'), '123 Beauty Street');
        await user.selectOptions(screen.getByLabelText('Payment method'), '3');
        await user.click(screen.getByRole('button', { name: 'Place order' }));

        await waitFor(() => {
            expect(window.axios.post).toHaveBeenCalledWith('/checkout', expect.objectContaining({
                payment_method: '3',
                shipping_fullname: 'Nguyen Van A',
            }));
            expect(assign).toHaveBeenCalledWith('https://test-payment.momo.vn/pay/demo');
        });
    });

    it('polls the queued checkout request before redirecting customers', async () => {
        const user = userEvent.setup();
        const assign = vi.spyOn(window.location, 'assign').mockImplementation(() => {});

        window.axios.post.mockResolvedValue({
            data: {
                data: {
                    id: 7,
                    status: 'QUEUED',
                    message: 'Your order is queued and will be processed shortly.',
                    status_url: '/checkout/requests/7',
                },
            },
        });
        window.axios.get
            .mockResolvedValueOnce({ data: { data: { id: 7, status: 'PROCESSING' } } })
            .mockResolvedValueOnce({
                data: {
                    data: {
                        id: 7,
                        status: 'COMPLETED',
                        order: { id: 42 },
                        payment: null,
                    },
                },
            });

        render(<CustomerCheckoutPage checkout={checkout} />);

        await user.type(screen.getByLabelText('Full name'), 'Nguyen Van A');
        await user.type(screen.getByLabelText('Mobile'), '0900111222');
        await user.type(screen.getByLabelText('Address'), '123 Beauty Street');
        await user.click(screen.getByRole('button', { name: 'Place order' }));

        expect(await screen.findByText('Your order is queued and will be processed shortly.')).toBeInTheDocument();

        await waitFor(() => {
            expect(window.axios.get).toHaveBeenCalledWith('/checkout/requests/7');
            expect(assign).toHaveBeenCalledWith('/orders/42');
        }, { timeout: 5000 });
    }, 10000);
});
