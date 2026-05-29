import CustomerChatbotWidget from './CustomerChatbotWidget.jsx';
import CustomerLiveChatWidget from './CustomerLiveChatWidget.jsx';

export default function CustomerLayout({ auth = null, children, navItems = [], title = 'Goda Shop' }) {
    const customer = auth?.user;

    return (
        <div className="react-customer-layout">
            <header className="react-customer-layout__header">
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

            <main className="react-customer-layout__main">
                {title && <h1 className="react-customer-layout__title">{title}</h1>}
                {children}
            </main>

            <footer className="react-customer-layout__footer">
                <strong>Goda Shop</strong>
                <span> Mỹ phẩm chính hãng, chăm sóc sắc đẹp mỗi ngày.</span>
            </footer>
            <CustomerChatbotWidget />
            <CustomerLiveChatWidget />
        </div>
    );
}
