import CustomerLayout from '../../components/customer/CustomerLayout.jsx';

const currencyFormatter = new Intl.NumberFormat('vi-VN', {
    style: 'currency',
    currency: 'VND',
    maximumFractionDigits: 0,
});

export default function CustomerOrderDetailPage({
    auth = null,
    csrfToken = '',
    navItems = [],
    order,
    title = order ? `Order #${order.id}` : 'Order detail',
}) {
    if (!order) {
        return (
            <CustomerLayout auth={auth} navItems={navItems} title={title}>
                <p className="react-empty-state">Order not found.</p>
            </CustomerLayout>
        );
    }

    return (
        <CustomerLayout auth={auth} navItems={navItems} title={title}>
            <section className="react-customer-order-detail">
                <div className="react-customer-order-detail__header">
                    <div>
                        <p className="react-customer-order-detail__eyebrow">Order status</p>
                        <strong>{order.status}</strong>
                    </div>
                    {order.canCancel ? (
                        <form method="post" action={order.cancelUrl}>
                            <input type="hidden" name="_token" value={csrfToken} />
                            <input type="hidden" name="_method" value="PATCH" />
                            <button type="submit">Cancel order</button>
                        </form>
                    ) : (
                        <p>This order can no longer be cancelled.</p>
                    )}
                </div>

                <div className="react-customer-order-detail__grid">
                    <article>
                        <h2>Shipping information</h2>
                        <p>{order.shipping_fullname}</p>
                        <p>{order.shipping_mobile}</p>
                        <p>{order.shipping_address}</p>
                        {order.note && <p>{order.note}</p>}
                    </article>

                    <article>
                        <h2>Payment summary</h2>
                        <dl>
                            <div>
                                <dt>Payment method</dt>
                                <dd>{order.payment_method}</dd>
                            </div>
                            <div>
                                <dt>Subtotal</dt>
                                <dd>{currencyFormatter.format(order.sub_total ?? 0)}</dd>
                            </div>
                            <div>
                                <dt>Shipping fee</dt>
                                <dd>{currencyFormatter.format(order.shipping_fee ?? 0)}</dd>
                            </div>
                            <div>
                                <dt>Discount</dt>
                                <dd>{currencyFormatter.format(order.discount_amount ?? 0)}</dd>
                            </div>
                            <div>
                                <dt>Total</dt>
                                <dd>{currencyFormatter.format(order.payment_total ?? 0)}</dd>
                            </div>
                        </dl>
                    </article>
                </div>

                <article className="react-customer-order-detail__items">
                    <h2>Items</h2>
                    <ul>
                        {(order.items ?? []).map((item) => (
                            <li key={`${order.id}-${item.product_id}`}>
                                <span>{item.product_name}</span>
                                <strong>
                                    {item.qty} × {currencyFormatter.format(item.unit_price)} = {currencyFormatter.format(item.total_price)}
                                </strong>
                            </li>
                        ))}
                    </ul>
                </article>

                <a className="react-customer-order-detail__back" href="/orders">Back to orders</a>
            </section>
        </CustomerLayout>
    );
}
