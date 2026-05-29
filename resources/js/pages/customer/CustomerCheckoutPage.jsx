import { useState } from 'react';
import CustomerLayout from '../../components/customer/CustomerLayout.jsx';

const QUEUED_STATUSES = new Set(['QUEUED', 'PROCESSING']);
const CHECKOUT_POLL_INTERVAL = 1500;
const CHECKOUT_POLL_ATTEMPTS = 20;

const currencyFormatter = new Intl.NumberFormat('vi-VN', {
    style: 'currency',
    currency: 'VND',
    maximumFractionDigits: 0,
});

export default function CustomerCheckoutPage({
    auth = null,
    checkout = { cart: { items: [], total: 0 }, feeShips: [], paymentMethods: {} },
    navItems = [],
    title = 'Thanh toán',
}) {
    const [isSubmitting, setIsSubmitting] = useState(false);
    const [errorMessage, setErrorMessage] = useState('');
    const [statusMessage, setStatusMessage] = useState('');
    const cart = checkout.cart ?? { items: [], total: 0 };
    const items = cart.items ?? [];
    const feeShips = checkout.feeShips ?? [];
    const paymentMethods = checkout.paymentMethods ?? {};
    const prefill = checkout.prefill ?? {};

    const handleSubmit = async (event) => {
        event.preventDefault();

        if (!window.axios) {
            event.currentTarget.submit();
            return;
        }

        const form = event.currentTarget;
        const payload = Object.fromEntries(new FormData(form).entries());

        setIsSubmitting(true);
        setErrorMessage('');

        try {
            const response = await window.axios.post('/checkout', payload);
            let checkoutRequest = response.data?.data;

            if (shouldPollCheckoutRequest(checkoutRequest)) {
                setStatusMessage(checkoutRequest.message ?? 'Your order is queued and will be processed shortly.');
                checkoutRequest = await pollCheckoutRequest(checkoutRequest.status_url, setStatusMessage);
            }

            const order = checkoutRequest?.order;
            const redirectUrl = checkoutRequest?.payment?.redirect_url ?? (order?.id ? `/orders/${order.id}` : '/orders');

            window.location.assign(redirectUrl);
        } catch (error) {
            setErrorMessage(error.response?.data?.message ?? error.message ?? 'Could not place order. Please try again.');
            setStatusMessage('');
            setIsSubmitting(false);
        }
    };

    return (
        <CustomerLayout auth={auth} navItems={navItems} title={title}>
            <section className="react-customer-checkout">
                {!items.length ? (
                    <div className="react-customer-checkout__empty">
                        <p>Your cart is empty.</p>
                        <a href="/products">Back to products</a>
                    </div>
                ) : (
                    <>
                        <form className="react-customer-checkout__form" method="post" action="/checkout" onSubmit={handleSubmit}>
                            {errorMessage ? (
                                <p className="react-customer-checkout__error" role="alert">
                                    {errorMessage}
                                </p>
                            ) : null}
                            {statusMessage ? (
                                <p className="react-customer-checkout__status" role="status">
                                    {statusMessage}
                                </p>
                            ) : null}
                            <label>
                                Full name
                                <input
                                    name="shipping_fullname"
                                    type="text"
                                    defaultValue={prefill.shipping_fullname ?? ''}
                                    required
                                />
                            </label>
                            <label>
                                Mobile
                                <input
                                    name="shipping_mobile"
                                    type="tel"
                                    defaultValue={prefill.shipping_mobile ?? ''}
                                    required
                                />
                            </label>
                            <label>
                                Ward
                                <input
                                    name="shipping_ward_id"
                                    type="text"
                                    defaultValue={prefill.shipping_ward_id ?? ''}
                                />
                            </label>
                            <label>
                                Address
                                <input
                                    name="shipping_housenumber_street"
                                    type="text"
                                    defaultValue={prefill.shipping_housenumber_street ?? ''}
                                    required
                                />
                            </label>
                            <label>
                                Shipping fee
                                <select name="feeship_id" defaultValue={feeShips[0]?.id ? String(feeShips[0].id) : ''}>
                                    <option value="">No shipping fee</option>
                                    {feeShips.map((feeShip) => (
                                        <option key={feeShip.id} value={feeShip.id}>
                                            {feeShip.label} - {currencyFormatter.format(feeShip.price)}
                                        </option>
                                    ))}
                                </select>
                            </label>
                            <label>
                                Discount code
                                <input name="discount_code" type="text" />
                            </label>
                            <label>
                                Payment method
                                <select name="payment_method" defaultValue="0">
                                    {Object.entries(paymentMethods).map(([value, label]) => (
                                        <option key={value} value={value}>
                                            {label}
                                        </option>
                                    ))}
                                </select>
                            </label>
                            <label>
                                Note
                                <textarea name="note" rows="4" />
                            </label>

                            <button type="submit" disabled={isSubmitting}>
                                {isSubmitting ? 'Placing order...' : 'Place order'}
                            </button>
                        </form>

                        <aside className="react-customer-checkout__summary">
                            <h2>Order summary</h2>
                            {items.map((item) => (
                                <div className="react-customer-checkout__item" key={item.product_id}>
                                    <span>{item.name}</span>
                                    <strong>
                                        {item.quantity} × {currencyFormatter.format(item.sale_price)}
                                    </strong>
                                </div>
                            ))}
                            <div className="react-customer-checkout__total">
                                <span>Subtotal</span>
                                <strong>{currencyFormatter.format(cart.total ?? 0)}</strong>
                            </div>
                        </aside>
                    </>
                )}
            </section>
        </CustomerLayout>
    );
}

function shouldPollCheckoutRequest(checkoutRequest) {
    return checkoutRequest?.status_url && QUEUED_STATUSES.has(checkoutRequest?.status);
}

async function pollCheckoutRequest(statusUrl, setStatusMessage) {
    for (let attempt = 0; attempt < CHECKOUT_POLL_ATTEMPTS; attempt += 1) {
        await wait(CHECKOUT_POLL_INTERVAL);

        const response = await window.axios.get(statusUrl);
        const checkoutRequest = response.data?.data;

        if (!checkoutRequest) {
            continue;
        }

        if (checkoutRequest.status === 'COMPLETED') {
            return checkoutRequest;
        }

        if (checkoutRequest.status === 'FAILED') {
            throw new Error(checkoutRequest.error_message ?? 'Could not place order. Please try again.');
        }

        if (QUEUED_STATUSES.has(checkoutRequest.status)) {
            setStatusMessage(checkoutRequest.message ?? 'Your order is being processed. Please wait...');
        }
    }

    throw new Error('Order queue is taking longer than expected. Please check your orders later.');
}

function wait(milliseconds) {
    return new Promise((resolve) => {
        window.setTimeout(resolve, milliseconds);
    });
}
