import { render, screen, waitFor } from '@testing-library/react';
import userEvent from '@testing-library/user-event';
import { afterEach, beforeEach, describe, expect, it, vi } from 'vitest';
import AdminMediaManager from './AdminMediaManager.jsx';

describe('AdminMediaManager', () => {
    beforeEach(() => {
        window.axios = {
            delete: vi.fn().mockResolvedValue({ data: null }),
            get: vi.fn().mockResolvedValue({
                data: { data: [{ id: 25, src: '/images/demo.jpg', alt: 'Demo image' }] },
            }),
            post: vi.fn().mockResolvedValue({ data: { message: 'Uploaded.' } }),
        };
    });

    afterEach(() => {
        vi.restoreAllMocks();
        delete window.axios;
    });

    it('loads media rows from the admin API and deletes through axios', async () => {
        const user = userEvent.setup();

        render(
            <AdminMediaManager
                apiUrl="/admin/api/media"
                labels={{ delete: 'Delete', image: 'Image', upload: 'Upload', uploadImage: 'Upload image' }}
            />,
        );

        expect(await screen.findByAltText('Demo image')).toHaveAttribute('src', '/images/demo.jpg');
        expect(window.axios.get).toHaveBeenCalledWith('/admin/api/media', {});

        await user.click(screen.getAllByRole('button', { name: 'Delete' })[1]);

        expect(window.axios.delete).toHaveBeenCalledWith('/admin/api/media/25', {});
    });

    it('checks all visible media and uploads selected image through axios', async () => {
        const user = userEvent.setup();
        const createObjectUrl = vi.spyOn(URL, 'createObjectURL').mockReturnValue('blob:upload-preview');
        window.axios.get.mockResolvedValueOnce({
            data: {
                data: [
                    { id: 1, src: '/images/one.jpg' },
                    { id: 2, src: '/images/two.jpg' },
                ],
            },
        });

        render(
            <AdminMediaManager
                apiUrl="/admin/api/media"
                labels={{ image: 'Image', preview: 'Preview', upload: 'Upload', uploadImage: 'Upload image' }}
            />,
        );

        await screen.findAllByAltText('Image');
        await user.click(screen.getAllByRole('checkbox')[0]);
        expect(screen.getAllByRole('checkbox')[1]).toBeChecked();
        expect(screen.getAllByRole('checkbox')[2]).toBeChecked();

        const file = new File(['image'], 'upload.png', { type: 'image/png' });
        await user.upload(screen.getByLabelText('Upload image'), file);

        expect(createObjectUrl).toHaveBeenCalledWith(file);
        expect(screen.getByAltText('Preview')).toHaveAttribute('src', 'blob:upload-preview');

        await user.click(screen.getByRole('button', { name: 'Upload' }));

        await waitFor(() => expect(window.axios.post).toHaveBeenCalled());
        expect(window.axios.post.mock.calls[0][0]).toBe('/admin/api/media');
        expect(window.axios.post.mock.calls[0][1]).toBeInstanceOf(FormData);
    });
});
