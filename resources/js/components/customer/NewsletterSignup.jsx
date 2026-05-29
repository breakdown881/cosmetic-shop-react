import { useState } from 'react';

export default function NewsletterSignup() {
    const [status, setStatus] = useState('');
    const [error, setError] = useState('');

    const handleSubmit = async (event) => {
        if (!window.axios) {
            return;
        }

        event.preventDefault();
        setStatus('');
        setError('');

        const form = event.currentTarget;
        const payload = Object.fromEntries(new FormData(form).entries());

        try {
            await window.axios.post('/newsletter/subscribe', payload);
            form.reset();
            setStatus('Thanks for subscribing.');
        } catch (requestError) {
            setError(requestError.response?.data?.message ?? 'Could not subscribe. Please try again.');
        }
    };

    return (
        <form
            className="react-newsletter-signup"
            method="post"
            action="/newsletter/subscribe"
            aria-label="Newsletter signup"
            onSubmit={handleSubmit}
        >
            <label>
                Newsletter
                <input name="email" type="email" placeholder="you@example.com" required />
            </label>
            <button type="submit">Subscribe</button>
            {status ? <p role="status">{status}</p> : null}
            {error ? <p role="alert">{error}</p> : null}
        </form>
    );
}
