import { render, screen, waitFor, within } from '@testing-library/react';
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

    it('paginates long live chat conversation lists', async () => {
        const user = userEvent.setup();
        window.axios.get.mockResolvedValueOnce({
            data: {
                unread_count: 0,
                data: Array.from({ length: 11 }, (_, index) => ({
                    id: index + 1,
                    customer: { name: `Customer ${index + 1}` },
                    latest_message: `Message ${index + 1}`,
                    needs_staff_reply: false,
                    messages: [{ id: index + 1, sender_type: 'customer', message: `Message ${index + 1}` }],
                })),
            },
        });

        render(<AdminLiveChatPage />);

        const list = document.querySelector('.react-admin-live-chat__list');

        expect(await within(list).findByText('Customer 1')).toBeInTheDocument();
        expect(within(list).queryByText('Customer 11')).not.toBeInTheDocument();

        await user.click(screen.getByRole('button', { name: 'Next' }));

        expect(within(list).getByText('Customer 11')).toBeInTheDocument();
        expect(within(list).queryByText('Customer 1')).not.toBeInTheDocument();
    });
});
