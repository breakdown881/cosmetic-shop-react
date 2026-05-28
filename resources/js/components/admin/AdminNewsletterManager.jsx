import { useEffect, useState } from 'react';
import { get, post } from '../../services/apiClient.js';

const normalizeRows = (payload) => Array.isArray(payload?.data) ? payload.data : [];

export default function AdminNewsletterManager({
    apiUrl,
    sendUrl,
    labels = {},
}) {
    const [body, setBody] = useState('');
    const [error, setError] = useState('');
    const [message, setMessage] = useState('');
    const [rows, setRows] = useState([]);
    const [subject, setSubject] = useState('');

    const loadRows = async () => {
        try {
            const payload = await get(apiUrl);
            setRows(normalizeRows(payload));
        } catch (requestError) {
            setError(requestError.response?.data?.message ?? 'Could not load subscribers.');
        }
    };

    useEffect(() => {
        loadRows();
    }, [apiUrl]);

    const sendNewsletter = async (event) => {
        event.preventDefault();
        setError('');
        setMessage('');

        try {
            const response = await post(sendUrl, { subject, body });
            setMessage(response.message ?? 'Sent.');
            setSubject('');
            setBody('');
        } catch (requestError) {
            const errors = requestError.response?.data?.errors;
            const firstError = errors ? Object.values(errors).flat()[0] : null;
            setError(firstError ?? requestError.response?.data?.message ?? 'Could not send newsletter.');
        }
    };

    return (
        <div className="card mb-3">
            <div className="card-header">
                <i className="fas fa-file-alt" /> {labels.title ?? 'Newsletter'}
            </div>
            <div className="card-body">
                {message && <div className="alert alert-success">{message}</div>}
                {error && <div className="alert alert-danger">{error}</div>}

                <form className="mb-4" onSubmit={sendNewsletter}>
                    <div className="form-group">
                        <label htmlFor="newsletter-subject">{labels.subject ?? 'Subject'}</label>
                        <input
                            id="newsletter-subject"
                            className="form-control"
                            value={subject}
                            required
                            onChange={(event) => setSubject(event.target.value)}
                        />
                    </div>
                    <div className="form-group">
                        <label htmlFor="newsletter-body">{labels.body ?? 'Body'}</label>
                        <textarea
                            id="newsletter-body"
                            className="form-control"
                            rows="6"
                            value={body}
                            required
                            onChange={(event) => setBody(event.target.value)}
                        />
                    </div>
                    <button type="submit" className="btn btn-primary btn-sm">{labels.send ?? 'Send'}</button>
                </form>

                <div className="table-responsive">
                    <table className="table table-hover" width="100%" cellSpacing="0">
                        <thead>
                            <tr>
                                <th>{labels.email ?? 'Email'}</th>
                                <th>{labels.createdAt ?? 'Created at'}</th>
                            </tr>
                        </thead>
                        <tbody>
                            {rows.map((row) => (
                                <tr key={row.id}>
                                    <td>{row.email}</td>
                                    <td>{row.created_at}</td>
                                </tr>
                            ))}
                        </tbody>
                    </table>
                </div>

                {!rows.length && <p className="react-empty-state">{labels.empty ?? 'No subscribers.'}</p>}
            </div>
        </div>
    );
}
