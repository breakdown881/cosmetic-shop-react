import CustomerLayout from '../../components/customer/CustomerLayout.jsx';

const currencyFormatter = new Intl.NumberFormat('vi-VN', {
    style: 'currency',
    currency: 'VND',
    maximumFractionDigits: 0,
});

export default function CustomerCheckoutPage({
    checkout = { cart: { items: [], total: 0 }, feeShips: [], paymentMethods: {} },
    navItems = [],
    title = 'Thanh toán',
}) {
    const cart = checkout.cart ?? { items: [], total: 0 };
    const items = cart.items ?? [];
    const feeShips = checkout.feeShips ?? [];
    const paymentMethods = checkout.paymentMethods ?? {};

    return (
        <CustomerLayout navItems={navItems} title={title}>
            <section className="react-customer-checkout">
                {!items.length ? (
                    <div className="react-customer-checkout__empty">
                        <p>Your cart is empty.</p>
                        <a href="/products">Back to products</a>
                    </div>
                ) : (
                    <>
                        <form className="react-customer-checkout__form" method="post" action="/checkout">
                            <label>
                                Full name
                                <input name="shipping_fullname" type="text" required />
                            </label>
                            <label>
                                Mobile
                                <input name="shipping_mobile" type="tel" required />
                            </label>
                            <label>
                                Ward
                                <input name="shipping_ward_id" type="text" />
                            </label>
                            <label>
                                Address
                                <input name="shipping_housenumber_street" type="text" required />
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

                            <button type="submit">Place order</button>
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
