export default function PublicWelcomePage({
    authLinks = [],
    cards = [],
    description = '',
    title = '',
    version = '',
}) {
    return (
        <div className="react-welcome min-h-screen">
            {!!authLinks.length && (
                <nav className="react-welcome__auth">
                    {authLinks.map((link) => (
                        <a key={link.href} href={link.href} className="text-sm text-gray-700 underline">
                            {link.label}
                        </a>
                    ))}
                </nav>
            )}

            <main className="react-welcome__main">
                <section className="react-welcome__hero">
                    <h1>{title}</h1>
                    <p>{description}</p>
                </section>

                <section className="react-welcome__cards">
                    {cards.map((card) => (
                        <a key={card.title} href={card.href} className="react-welcome__card">
                            <h2>{card.title}</h2>
                            <p>{card.description}</p>
                        </a>
                    ))}
                </section>

                {version && <p className="react-welcome__version">{version}</p>}
            </main>
        </div>
    );
}
