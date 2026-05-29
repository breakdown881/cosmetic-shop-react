import CustomerLayout from '../../components/customer/CustomerLayout.jsx';

export default function CustomerAccountPage({
    auth = null,
    navItems = [],
    profile = null,
    requiresAuth = false,
    title = 'Tài khoản',
}) {
    return (
        <CustomerLayout auth={auth} navItems={navItems} title={title}>
            <section className="react-customer-account">
                {requiresAuth ? (
                    <div className="react-customer-account__empty">
                        <p>Please sign in to manage your account.</p>
                        <a href="/products">Continue shopping</a>
                    </div>
                ) : (
                    <form className="react-customer-account__form" method="post" action="/account">
                        <input type="hidden" name="_method" value="PATCH" />
                        <label>
                            Name
                            <input
                                name="name"
                                type="text"
                                defaultValue={profile?.name ?? ''}
                                required
                            />
                        </label>
                        <label>
                            Email
                            <input
                                name="email"
                                type="email"
                                defaultValue={profile?.email ?? ''}
                                required
                            />
                        </label>
                        <button type="submit">Save profile</button>
                    </form>
                )}
            </section>
        </CustomerLayout>
    );
}
