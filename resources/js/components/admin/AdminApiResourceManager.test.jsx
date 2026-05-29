import { render, screen } from '@testing-library/react';
import userEvent from '@testing-library/user-event';
import { afterEach, beforeEach, describe, expect, it, vi } from 'vitest';
import AdminApiResourceManager from './AdminApiResourceManager.jsx';

describe('AdminApiResourceManager', () => {
    beforeEach(() => {
        window.axios = {
            get: vi.fn(),
            post: vi.fn(),
            patch: vi.fn(),
            delete: vi.fn(),
        };
    });

    afterEach(() => {
        vi.restoreAllMocks();
        delete window.axios;
    });

    it('paginates long admin resource tables', async () => {
        const user = userEvent.setup();
        window.axios.get.mockResolvedValueOnce({
            data: {
                data: Array.from({ length: 11 }, (_, index) => ({
                    id: index + 1,
                    name: `Resource ${index + 1}`,
                    status: 1,
                })),
            },
        });

        render(
            <AdminApiResourceManager
                apiUrl="/admin/api/resources"
                columns={[
                    { key: 'name', label: 'Name' },
                    { key: 'status', label: 'Status', type: 'boolean' },
                ]}
                fields={[{ name: 'name', label: 'Name' }]}
                labels={{ empty: 'No data.' }}
                title="Resources"
            />,
        );

        expect(await screen.findByText('Resource 1')).toBeInTheDocument();
        expect(screen.queryByText('Resource 11')).not.toBeInTheDocument();

        await user.click(screen.getByRole('button', { name: 'Next' }));

        expect(screen.getByText('Resource 11')).toBeInTheDocument();
        expect(screen.queryByText('Resource 1')).not.toBeInTheDocument();
    });
});
