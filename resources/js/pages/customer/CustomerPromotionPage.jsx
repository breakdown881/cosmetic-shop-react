import CustomerLayout from '../../components/customer/CustomerLayout.jsx';

export default function CustomerPromotionPage({
    auth = null,
    navItems = [],
    promotions = [],
    title = 'Khuyen mai',
}) {
    return (
        <CustomerLayout auth={auth} navItems={navItems} title={title}>
            <section className="react-customer-promotions">
                {promotions.length ? (
                    <div className="react-customer-promotions__grid">
                        {promotions.map((promotion) => (
                            <article className="react-customer-promotions__card" key={promotion.code}>
                                <strong>{promotion.code}</strong>
                                <p>{promotion.description}</p>
                                <span>{promotion.label}</span>
                                {promotion.expires_at ? <small>Het han: {promotion.expires_at}</small> : null}
                                <a href="/products">Shop now</a>
                            </article>
                        ))}
                    </div>
                ) : (
                    <p className="react-empty-state">No active promotions right now.</p>
                )}
            </section>
        </CustomerLayout>
    );
}
