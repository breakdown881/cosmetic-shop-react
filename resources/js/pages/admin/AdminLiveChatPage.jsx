import { useEffect, useState } from 'react';
import { get, post } from '../../services/apiClient.js';

export default function AdminLiveChatPage() {
    const [inbox, setInbox] = useState({ unread_count: 0, data: [] });
    const [selectedId, setSelectedId] = useState(null);
    const [selectedConversation, setSelectedConversation] = useState(null);
    const [reply, setReply] = useState('');
    const [error, setError] = useState('');
    const [isSending, setIsSending] = useState(false);

    useEffect(() => {
        let cancelled = false;

        get('/admin/api/live-chat/conversations')
            .then((response) => {
                if (cancelled) {
                    return;
                }

                setInbox(response);
                const firstConversation = response.data?.[0] ?? null;
                setSelectedId((currentId) => currentId ?? firstConversation?.id ?? null);
                setSelectedConversation((currentConversation) => currentConversation ?? firstConversation);
            })
            .catch((requestError) => {
                if (!cancelled) {
                    setError(requestError.response?.data?.message ?? 'Could not load live chat inbox.');
                }
            });

        return () => {
            cancelled = true;
        };
    }, []);

    const selectConversation = (conversation) => {
        setSelectedId(conversation.id);
        setSelectedConversation(conversation);
    };

    const sendReply = async (event) => {
        event.preventDefault();
        const trimmed = reply.trim();

        if (!trimmed || !selectedId || isSending) {
            return;
        }

        setIsSending(true);
        setError('');

        try {
            const response = await post(`/admin/api/live-chat/conversations/${selectedId}/messages`, { message: trimmed });
            const conversation = response.data;
            const data = inbox.data.map((item) => (item.id === conversation.id ? conversation : item));

            setSelectedConversation(conversation);
            setInbox({
                unread_count: data.filter((item) => item.needs_staff_reply).length,
                data,
            });
            setReply('');
        } catch (requestError) {
            setError(requestError.response?.data?.message ?? 'Could not send reply.');
        } finally {
            setIsSending(false);
        }
    };

    return (
        <div className="container-fluid react-admin-live-chat">
            <div className="d-flex align-items-center justify-content-between mb-3">
                <h1 className="h4 mb-0">Live chat tư vấn</h1>
                <span className="badge badge-danger">{inbox.unread_count ?? 0} tin nhắn mới</span>
            </div>

            {error && <div className="alert alert-danger">{error}</div>}

            <div className="row">
                <aside className="col-md-4 react-admin-live-chat__list">
                    {inbox.data.map((conversation) => (
                        <button
                            type="button"
                            className={`react-admin-live-chat__conversation ${selectedId === conversation.id ? 'active' : ''}`}
                            key={conversation.id}
                            onClick={() => selectConversation(conversation)}
                        >
                            <strong>{conversation.customer?.name ?? 'Guest customer'}</strong>
                            {conversation.needs_staff_reply ? <span className="badge badge-warning">Mới</span> : null}
                            <small>{conversation.latest_message}</small>
                        </button>
                    ))}
                    {!inbox.data.length && <p className="react-empty-state">Chưa có tin nhắn tư vấn.</p>}
                </aside>

                <section className="col-md-8 react-admin-live-chat__thread">
                    {selectedConversation ? (
                        <>
                            <div className="react-admin-live-chat__messages">
                                {selectedConversation.messages?.map((item) => (
                                    <div className={`react-admin-live-chat__message react-admin-live-chat__message--${item.sender_type}`} key={item.id}>
                                        <span>{item.sender_type === 'staff' ? (item.staff?.name ?? 'Staff') : (selectedConversation.customer?.name ?? 'Customer')}</span>
                                        <p>{item.message}</p>
                                    </div>
                                ))}
                            </div>
                            <form className="react-admin-live-chat__reply" onSubmit={sendReply}>
                                <label htmlFor="admin-live-chat-reply">Staff reply</label>
                                <textarea
                                    id="admin-live-chat-reply"
                                    rows="3"
                                    value={reply}
                                    onChange={(event) => setReply(event.target.value)}
                                />
                                <button type="submit" className="btn btn-primary" disabled={isSending || !reply.trim()}>
                                    Gửi trả lời
                                </button>
                            </form>
                        </>
                    ) : (
                        <p className="react-empty-state">Chọn một hội thoại để trả lời.</p>
                    )}
                </section>
            </div>
        </div>
    );
}
