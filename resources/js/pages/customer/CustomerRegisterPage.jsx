import CustomerLayout from '../../components/customer/CustomerLayout.jsx';
import SocialAuthLinks from '../../components/customer/SocialAuthLinks.jsx';

export default function CustomerRegisterPage({
    auth = null,
    csrfToken = '',
    navItems = [],
    title = 'Create account',
}) {
    return (
        <CustomerLayout auth={auth} navItems={navItems} title={title}>
            <section className="react-customer-auth">
                <div className="react-customer-auth__panel">
                    <p className="react-customer-auth__eyebrow">Beauty membership</p>
                    <h2>Start your skincare journey</h2>
                    <p>Create an account to save your profile, review products and follow every order.</p>
                </div>

                <form className="react-customer-auth__form" method="post" action="/register">
                    <SocialAuthLinks />
                    <div className="react-customer-auth__divider">or create account with email</div>
                    <input type="hidden" name="_token" value={csrfToken} />
                    <label>
                        Name
                        <input name="name" type="text" autoComplete="name" required />
                    </label>
                    <label>
                        Email
                        <input name="email" type="email" autoComplete="email" required />
                    </label>
                    <label>
                        Password
                        <input name="password" type="password" autoComplete="new-password" required />
                    </label>
                    <label>
                        Confirm password
                        <input
                            name="password_confirmation"
                            type="password"
                            autoComplete="new-password"
                            required
                        />
                    </label>
                    <button type="submit">Create account</button>
                    <p>
                        Already have an account? <a href="/login">Sign in instead</a>
                    </p>
                </form>
            </section>
        </CustomerLayout>
    );
}
