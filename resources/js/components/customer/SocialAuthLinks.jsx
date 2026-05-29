export default function SocialAuthLinks() {
    return (
        <div className="react-customer-auth__social" aria-label="Social login options">
            <a className="react-customer-auth__social-link" href="/auth/google/redirect">
                Continue with Google
            </a>
            <a className="react-customer-auth__social-link" href="/auth/facebook/redirect">
                Continue with Facebook
            </a>
        </div>
    );
}
