import { useEffect, useState } from 'react';

export default function CustomerLiveChatWidget({ conversationEndpoint = '/live-chat/conversation', messageEndpoint = '/live-chat/messages' }) {
    const [isOpen, setIsOpen] = useState(false);
    const [message, setMessage] = useState('');
    const [messages, setMessages] = useState([]);
    const [isLoading, setIsLoading] = useState(false);
    const [error, setError] = useState('');

    const applyConversation = (conversation) => {
        setMessages(conversation?.messages ?? []);
    };

    useEffect(() => {
        if (!isOpen || !window.axios) {
            return undefined;
        }

        let cancelled = false;
        const loadConversation = () => window.axios.get(conversationEndpoint)
            .then((response) => {
                if (!cancelled) {
                    applyConversation(response.data?.data);
                }
            })
            .catch(() => {});

        loadConversation();
        const timer = window.setInterval(loadConversation, 10000);

        return () => {
            cancelled = true;
            window.clearInterval(timer);
        };
    }, [conversationEndpoint, isOpen]);

    const sendMessage = async (event) => {
        event.preventDefault();
        const trimmed = message.trim();

        if (!trimmed || isLoading || !window.axios) {
            return;
        }

        setIsLoading(true);
        setError('');
        setMessage('');

        try {
            const response = await window.axios.post(messageEndpoint, { message: trimmed });
            applyConversation(response.data?.data);
        } catch (requestError) {
            setError(requestError.response?.data?.message ?? 'Không gửi được tin nhắn. Vui lòng thử lại.');
        } finally {
            setIsLoading(false);
        }
    };

    return (
        <aside className={`react-customer-live-chat ${isOpen ? 'react-customer-live-chat--open' : ''}`}>
            {isOpen && (
                <section className="react-customer-live-chat__panel" aria-label="Live staff chat">
                    <header>
                        <div>
                            <strong>Tư vấn trực tiếp</strong>
                            <span>Nhân viên sẽ trả lời khi online.</span>
                        </div>
                        <button type="button" aria-label="Close live chat" onClick={() => setIsOpen(false)}>
                            ×
                        </button>
                    </header>

                    <div className="react-customer-live-chat__messages">
                        {!messages.length && <p className="react-customer-live-chat__empty">Bạn cần tư vấn thêm? Hãy gửi tin nhắn cho shop.</p>}
                        {messages.map((item) => (
                            <div className={`react-customer-live-chat__message react-customer-live-chat__message--${item.sender_type}`} key={item.id ?? `${item.sender_type}-${item.message}`}>
                                {item.sender_type === 'staff' && item.staff?.name ? <span>{item.staff.name}</span> : null}
                                <p>{item.message}</p>
                            </div>
                        ))}
                        {error && <p className="react-customer-live-chat__error" role="alert">{error}</p>}
                    </div>

                    <form className="react-customer-live-chat__form" onSubmit={sendMessage}>
                        <label htmlFor="customer-live-chat-message">Live chat message</label>
                        <input
                            id="customer-live-chat-message"
                            value={message}
                            placeholder="Nhập nội dung cần tư vấn..."
                            onChange={(event) => setMessage(event.target.value)}
                        />
                        <button type="submit" disabled={isLoading || !message.trim()} aria-label="Send live chat message">
                            {isLoading ? 'Đang gửi...' : 'Gửi'}
                        </button>
                    </form>
                </section>
            )}

            <button
                type="button"
                className="react-customer-live-chat__toggle"
                aria-label="Open live chat"
                onClick={() => setIsOpen(true)}
            >
                Tư vấn trực tiếp
            </button>
        </aside>
    );
}
