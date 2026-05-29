import { render, screen, waitFor } from '@testing-library/react';
import userEvent from '@testing-library/user-event';
import { afterEach, beforeEach, describe, expect, it, vi } from 'vitest';
import CustomerChatbotWidget from './CustomerChatbotWidget.jsx';

describe('CustomerChatbotWidget', () => {
    beforeEach(() => {
        window.axios = {
            post: vi.fn().mockResolvedValue({
                data: {
                    data: {
                        reply: 'Minh tim thay san pham phu hop: Vitamin C Serum.',
                        suggestions: [
                            { id: 1, name: 'Vitamin C Serum', url: '/products/1', price: 320000 },
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

    it('renders floating chatbot and sends messages to customer chatbot API', async () => {
        const user = userEvent.setup();

        render(<CustomerChatbotWidget endpoint="/chatbot/messages" />);

        expect(screen.getByRole('button', { name: 'Open chatbot' })).toBeInTheDocument();

        await user.click(screen.getByRole('button', { name: 'Open chatbot' }));
        await user.type(screen.getByLabelText('Your question'), 'serum vitamin c');
        await user.click(screen.getByRole('button', { name: 'Send message' }));

        await waitFor(() => {
            expect(window.axios.post).toHaveBeenCalledWith('/chatbot/messages', { message: 'serum vitamin c' });
        });

        expect(screen.getByText('serum vitamin c')).toBeInTheDocument();
        expect(screen.getByRole('link', { name: 'Vitamin C Serum' })).toHaveAttribute('href', '/products/1');
    });
});
