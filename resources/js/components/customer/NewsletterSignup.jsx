import { useState } from 'react';

export default function NewsletterSignup({
    buttonLabel = 'Subscribe',
    errorMessage = 'Could not subscribe. Please try again.',
    label = 'Newsletter',
    placeholder = 'you@example.com',
    successMessage = 'Thanks for subscribing.',
}) {
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
            setStatus(successMessage);
        } catch (requestError) {
            setError(requestError.response?.data?.message ?? errorMessage);
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
                {label}
                <input name="email" type="email" placeholder={placeholder} required />
            </label>
            <button type="submit">{buttonLabel}</button>
            {status ? <p role="status">{status}</p> : null}
            {error ? <p role="alert">{error}</p> : null}
        </form>
    );
}
