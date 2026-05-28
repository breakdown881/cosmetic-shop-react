import { render, screen, waitFor } from '@testing-library/react';
import userEvent from '@testing-library/user-event';
import { afterEach, beforeEach, describe, expect, it, vi } from 'vitest';
import AdminNewsletterManager from './AdminNewsletterManager.jsx';

describe('AdminNewsletterManager', () => {
    beforeEach(() => {
        window.axios = {
            get: vi.fn().mockResolvedValue({
                data: { data: [{ id: 1, email: 'subscriber@example.test', created_at: '2026-05-28 10:00:00' }] },
            }),
            post: vi.fn().mockResolvedValue({ data: { message: 'Newsletter sent.' } }),
        };
    });

    afterEach(() => {
        vi.restoreAllMocks();
        delete window.axios;
    });

    it('loads subscribers and sends newsletter through admin API', async () => {
        const user = userEvent.setup();

        render(
            <AdminNewsletterManager
                apiUrl="/admin/api/newsletters"
                sendUrl="/admin/api/newsletters/send"
                labels={{ body: 'Body', email: 'Email', send: 'Send', subject: 'Subject' }}
            />,
        );

        expect(await screen.findByText('subscriber@example.test')).toBeInTheDocument();
        expect(window.axios.get).toHaveBeenCalledWith('/admin/api/newsletters', {});

        await user.type(screen.getByLabelText('Subject'), 'Sale thang nay');
        await user.type(screen.getByLabelText('Body'), 'Noi dung khuyen mai');
        await user.click(screen.getByRole('button', { name: 'Send' }));

        await waitFor(() => expect(window.axios.post).toHaveBeenCalledWith('/admin/api/newsletters/send', {
            subject: 'Sale thang nay',
            body: 'Noi dung khuyen mai',
        }, {}));
        expect(screen.getByText('Newsletter sent.')).toBeInTheDocument();
    });
});
