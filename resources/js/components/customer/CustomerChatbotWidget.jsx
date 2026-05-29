import { useState } from 'react';

const money = new Intl.NumberFormat('vi-VN', {
    style: 'currency',
    currency: 'VND',
    maximumFractionDigits: 0,
});

export default function CustomerChatbotWidget({ endpoint = '/chatbot/messages' }) {
    const [isOpen, setIsOpen] = useState(false);
    const [message, setMessage] = useState('');
    const [messages, setMessages] = useState([
        {
            role: 'assistant',
            text: 'Xin chào! Mình có th? tu v?n s?n ph?m, phí ship, thanh toán và d?i tr?.',
            suggestions: [],
        },
    ]);
    const [isLoading, setIsLoading] = useState(false);
    const [error, setError] = useState('');

    const sendMessage = async (event) => {
        event.preventDefault();
        const trimmedMessage = message.trim();

        if (!trimmedMessage || isLoading) {
            return;
        }

        setMessages((currentMessages) => [...currentMessages, { role: 'user', text: trimmedMessage, suggestions: [] }]);
        setMessage('');
        setError('');
        setIsLoading(true);

        try {
            const response = await window.axios.post(endpoint, { message: trimmedMessage });
            const answer = response.data?.data ?? {};

            setMessages((currentMessages) => [
                ...currentMessages,
                {
                    role: 'assistant',
                    text: answer.reply ?? 'Mình chua có câu tr? l?i phù h?p.',
                    suggestions: answer.suggestions ?? [],
                },
            ]);
        } catch (requestError) {
            setError(requestError.response?.data?.message ?? 'Không g?i du?c câu h?i. Vui lòng th? l?i.');
        } finally {
            setIsLoading(false);
        }
    };

    return (
        <aside className={`react-customer-chatbot ${isOpen ? 'react-customer-chatbot--open' : ''}`}>
            {isOpen && (
                <section className="react-customer-chatbot__panel" aria-label="Customer chatbot">
                    <header>
                        <strong>Goda assistant</strong>
                        <button type="button" aria-label="Close chatbot" onClick={() => setIsOpen(false)}>
                            ×
                        </button>
                    </header>

                    <div className="react-customer-chatbot__messages">
                        {messages.map((item, index) => (
                            <div className={`react-customer-chatbot__message react-customer-chatbot__message--${item.role}`} key={`${item.role}-${index}`}>
                                <p>{item.text}</p>
                                {!!item.suggestions?.length && (
                                    <ul>
                                        {item.suggestions.map((suggestion) => (
                                            <li key={suggestion.id}>
                                                <a href={suggestion.url}>{suggestion.name}</a>
                                                <span>{money.format(suggestion.price ?? 0)}</span>
                                            </li>
                                        ))}
                                    </ul>
                                )}
                            </div>
                        ))}
                        {isLoading && <p className="react-customer-chatbot__loading">Ðang tr? l?i...</p>}
                        {error && <p className="react-customer-chatbot__error">{error}</p>}
                    </div>

                    <form className="react-customer-chatbot__form" onSubmit={sendMessage}>
                        <label htmlFor="customer-chatbot-message">Your question</label>
                        <input
                            id="customer-chatbot-message"
                            value={message}
                            placeholder="H?i v? serum, phí ship..."
                            onChange={(event) => setMessage(event.target.value)}
                        />
                        <button type="submit" disabled={isLoading || !message.trim()} aria-label="Send message">
                            G?i
                        </button>
                    </form>
                </section>
            )}

            <button
                type="button"
                className="react-customer-chatbot__toggle"
                aria-label="Open chatbot"
                onClick={() => setIsOpen(true)}
            >
                Chat
            </button>
        </aside>
    );
}
