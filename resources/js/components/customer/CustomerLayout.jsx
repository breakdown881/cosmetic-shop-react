import CustomerChatbotWidget from './CustomerChatbotWidget.jsx';
import CustomerHeader from './CustomerHeader.jsx';
import CustomerLiveChatWidget from './CustomerLiveChatWidget.jsx';
import NewsletterSignup from './NewsletterSignup.jsx';

export default function CustomerLayout({ auth = null, children, navItems = [], title = 'Goda Shop' }) {
    return (
        <div className="react-customer-layout">
            <CustomerHeader auth={auth} navItems={navItems} />

            <main className="react-customer-layout__main">
                {title && <h1 className="react-customer-layout__title">{title}</h1>}
                {children}
            </main>

            <footer className="react-customer-layout__footer">
                <div>
                    <strong>Goda Shop</strong>
                    <span> Mỹ phẩm chính hãng, chăm sóc sắc đẹp mỗi ngày.</span>
                </div>
                <NewsletterSignup />
            </footer>
            <CustomerChatbotWidget />
            <CustomerLiveChatWidget />
        </div>
    );
}
