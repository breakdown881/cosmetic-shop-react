export default function CustomerLayout({ children, navItems = [], title = 'Goda Shop' }) {
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
            </header>

            <main className="react-customer-layout__main">
                {title && <h1 className="react-customer-layout__title">{title}</h1>}
                {children}
            </main>

            <footer className="react-customer-layout__footer">
                <strong>Goda Shop</strong>
                <span> Mỹ phẩm chính hãng, chăm sóc sắc đẹp mỗi ngày.</span>
            </footer>
        </div>
    );
}
