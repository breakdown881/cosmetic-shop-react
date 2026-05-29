import { render, screen, waitFor } from '@testing-library/react';
import userEvent from '@testing-library/user-event';
import { afterEach, beforeEach, describe, expect, it, vi } from 'vitest';
import AdminLiveChatPage from './AdminLiveChatPage.jsx';

const inbox = {
    unread_count: 1,
    data: [
        {
            id: 12,
            customer: { name: 'Guest customer' },
            latest_message: 'Can tu van kem duong',
            needs_staff_reply: true,
            messages: [{ id: 1, sender_type: 'customer', message: 'Can tu van kem duong' }],
        },
    ],
};

describe('AdminLiveChatPage', () => {
    beforeEach(() => {
        window.axios = {
            get: vi.fn().mockResolvedValue({ data: inbox }),
            post: vi.fn().mockResolvedValue({
                data: {
                    data: {
                        ...inbox.data[0],
                        needs_staff_reply: false,
                        messages: [
                            ...inbox.data[0].messages,
                            { id: 2, sender_type: 'staff', message: 'Shop tu van kem duong phuc hoi a.', staff: { name: 'Staff A' } },
                        ],
                    },
                },
            }),
        };
    });

    afterEach(() => {
        vi.restoreAllMocks();
        delete window.axios;
    });

    it('shows staff notifications and lets staff reply directly', async () => {
        const user = userEvent.setup();

        render(<AdminLiveChatPage />);

        expect(await screen.findByText('Live chat tư vấn')).toBeInTheDocument();
        expect(screen.getByText('1 tin nhắn mới')).toBeInTheDocument();
        await user.click(screen.getByRole('button', { name: /Guest customer/ }));
        expect(screen.getAllByText('Can tu van kem duong')).toHaveLength(2);

        await user.type(screen.getByLabelText('Staff reply'), 'Shop tu van kem duong phuc hoi a.');
        await user.click(screen.getByRole('button', { name: 'Gửi trả lời' }));

        await waitFor(() => {
            expect(window.axios.post).toHaveBeenCalledWith('/admin/api/live-chat/conversations/12/messages', {
                message: 'Shop tu van kem duong phuc hoi a.',
            }, {});
        });

        expect(screen.getByText('Shop tu van kem duong phuc hoi a.')).toBeInTheDocument();
    });
});
