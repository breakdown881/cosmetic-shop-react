import { render, screen, waitFor } from '@testing-library/react';
import userEvent from '@testing-library/user-event';
import { afterEach, beforeEach, describe, expect, it, vi } from 'vitest';
import CustomerLiveChatWidget from './CustomerLiveChatWidget.jsx';

describe('CustomerLiveChatWidget', () => {
    beforeEach(() => {
        window.axios = {
            get: vi.fn().mockResolvedValue({ data: { data: null } }),
            post: vi.fn().mockResolvedValue({
                data: {
                    data: {
                        id: 7,
                        messages: [
                            { id: 1, sender_type: 'customer', message: 'Can tu van serum' },
                            { id: 2, sender_type: 'staff', message: 'Da, staff se tu van ngay.', staff: { name: 'Staff A' } },
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

    it('renders live chat option and sends customer messages to staff endpoint', async () => {
        const user = userEvent.setup();

        render(<CustomerLiveChatWidget />);

        await user.click(screen.getByRole('button', { name: 'Open live chat' }));
        expect(screen.getAllByText('Tư vấn trực tiếp')).toHaveLength(2);

        await user.type(screen.getByLabelText('Live chat message'), 'Can tu van serum');
        await user.click(screen.getByRole('button', { name: 'Send live chat message' }));

        await waitFor(() => {
            expect(window.axios.post).toHaveBeenCalledWith('/live-chat/messages', { message: 'Can tu van serum' });
        });

        expect(screen.getByText('Can tu van serum')).toBeInTheDocument();
        expect(screen.getByText('Da, staff se tu van ngay.')).toBeInTheDocument();
    });
});
