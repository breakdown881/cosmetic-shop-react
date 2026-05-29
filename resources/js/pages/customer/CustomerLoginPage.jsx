import CustomerLayout from '../../components/customer/CustomerLayout.jsx';
import SocialAuthLinks from '../../components/customer/SocialAuthLinks.jsx';

export default function CustomerLoginPage({
    auth = null,
    csrfToken = '',
    navItems = [],
    title = 'Login',
}) {
    return (
        <CustomerLayout auth={auth} navItems={navItems} title={title}>
            <section className="react-customer-auth">
                <div className="react-customer-auth__panel">
                    <p className="react-customer-auth__eyebrow">Customer account</p>
                    <h2>Welcome back</h2>
                    <p>Sign in to checkout faster, manage profile information and track your orders.</p>
                </div>

                <form className="react-customer-auth__form" method="post" action="/login">
                    <SocialAuthLinks />
                    <div className="react-customer-auth__divider">or sign in with email</div>
                    <input type="hidden" name="_token" value={csrfToken} />
                    <label>
                        Email
                        <input name="email" type="email" autoComplete="email" required />
                    </label>
                    <label>
                        Password
                        <input name="password" type="password" autoComplete="current-password" required />
                    </label>
                    <button type="submit">Sign in</button>
                    <p>
                        New to Goda Shop? <a href="/register">Create account</a>
                    </p>
                </form>
            </section>
        </CustomerLayout>
    );
}
