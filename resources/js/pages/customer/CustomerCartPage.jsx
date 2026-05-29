import CustomerLayout from '../../components/customer/CustomerLayout.jsx';

const currencyFormatter = new Intl.NumberFormat('vi-VN', {
    style: 'currency',
    currency: 'VND',
    maximumFractionDigits: 0,
});

export default function CustomerCartPage({
    auth = null,
    cart = { items: [], total: 0 },
    navItems = [],
    title = 'Giỏ hàng',
}) {
    const items = cart.items ?? [];

    return (
        <CustomerLayout auth={auth} navItems={navItems} title={title}>
            <section className="react-customer-cart">
                {!items.length ? (
                    <div className="react-customer-cart__empty">
                        <p>Your cart is empty.</p>
                        <a href="/products">Continue shopping</a>
                    </div>
                ) : (
                    <>
                        <div className="react-customer-cart__items">
                            {items.map((item) => (
                                <article className="react-customer-cart__item" key={item.product_id}>
                                    {item.image && (
                                        <img src={item.image} alt={item.name} />
                                    )}
                                    <div>
                                        <h2>
                                            <a href={item.url}>{item.name}</a>
                                        </h2>
                                        <p>{currencyFormatter.format(item.sale_price)}</p>
                                    </div>
                                    <form method="post" action={`/cart/items/${item.product_id}`}>
                                        <input type="hidden" name="_method" value="PATCH" />
                                        <label>
                                            Quantity
                                            <input
                                                type="number"
                                                name="quantity"
                                                min="1"
                                                max={item.inventory_qty}
                                                defaultValue={item.quantity}
                                            />
                                        </label>
                                    </form>
                                    <strong>{currencyFormatter.format(item.subtotal)}</strong>
                                    <form method="post" action={`/cart/items/${item.product_id}`}>
                                        <input type="hidden" name="_method" value="DELETE" />
                                        <button type="submit">Remove</button>
                                    </form>
                                </article>
                            ))}
                        </div>

                        <aside className="react-customer-cart__summary">
                            <span>Total</span>
                            <strong>{currencyFormatter.format(cart.total ?? 0)}</strong>
                            <form
                                method="post"
                                action="/cart/vouchers/validate"
                                aria-label="Validate voucher"
                                className="react-customer-cart__voucher-form"
                            >
                                <label>
                                    Voucher code
                                    <input name="discount_code" type="text" />
                                </label>
                                <button type="submit">Apply voucher</button>
                            </form>
                            <a href="/checkout">Checkout</a>
                        </aside>
                    </>
                )}
            </section>
        </CustomerLayout>
    );
}
