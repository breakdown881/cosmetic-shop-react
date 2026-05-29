import CustomerLayout from '../../components/customer/CustomerLayout.jsx';

const currencyFormatter = new Intl.NumberFormat('vi-VN', {
    style: 'currency',
    currency: 'VND',
    maximumFractionDigits: 0,
});

export default function CustomerWishlistPage({
    auth = null,
    csrfToken = '',
    items = [],
    navItems = [],
    title = 'Wishlist',
}) {
    return (
        <CustomerLayout auth={auth} navItems={navItems} title={title}>
            <section className="react-customer-wishlist">
                {!items.length ? (
                    <div className="react-customer-wishlist__empty">
                        <p>Your wishlist is empty.</p>
                        <a href="/products">Browse products</a>
                    </div>
                ) : (
                    <div className="react-customer-wishlist__grid">
                        {items.map((item) => (
                            <article className="react-customer-wishlist__card" key={item.id}>
                                <img src={item.featured_image} alt={item.name} />
                                <div>
                                    <a href={item.url}>{item.name}</a>
                                    <strong>{currencyFormatter.format(item.sale_price ?? item.price)}</strong>
                                    <form method="post" action={item.removeUrl}>
                                        <input type="hidden" name="_token" value={csrfToken} />
                                        <input type="hidden" name="_method" value="DELETE" />
                                        <button type="submit">Remove from wishlist</button>
                                    </form>
                                </div>
                            </article>
                        ))}
                    </div>
                )}
            </section>
        </CustomerLayout>
    );
}
