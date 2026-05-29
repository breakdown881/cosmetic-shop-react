export default function CustomerHeader({ auth = null, navItems = [] }) {
    const customer = auth?.user;

    return (
        <header className="react-customer-layout__header react-customer-header">
            <a className="react-customer-layout__brand" href="/">
                <span>Goda</span> Shop
            </a>
            <nav className="react-customer-layout__nav" aria-label="Customer navigation">
                {navItems.map((item) => (
                    <a key={item.href} href={item.href}>
                        {item.label}
                    </a>
                ))}
            </nav>
            <div className="react-customer-layout__auth">
                {auth?.check ? (
                    <>
                        <a href="/account">Hi, {customer?.name ?? 'Customer'}</a>
                        <form method="post" action={auth.logoutUrl ?? '/logout'}>
                            <button type="submit">Sign out</button>
                        </form>
                    </>
                ) : (
                    <>
                        <a href={auth?.loginUrl ?? '/login'}>Sign in</a>
                        <a href={auth?.registerUrl ?? '/register'}>Register</a>
                    </>
                )}
            </div>
        </header>
    );
}
