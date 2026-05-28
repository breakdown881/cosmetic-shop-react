import { render, screen } from '@testing-library/react';
import userEvent from '@testing-library/user-event';
import { describe, expect, it, vi } from 'vitest';
import AdminMediaManager from './AdminMediaManager.jsx';

describe('AdminMediaManager', () => {
    it('renders media rows and upload form compatible with Laravel', () => {
        render(
            <AdminMediaManager
                uploadAction="/admin/images/upload"
                csrfToken="csrf-token"
                labels={{ delete: 'Xóa', image: 'Hình ảnh', upload: 'Upload', uploadImage: 'Upload hình' }}
                items={[{ id: 25, src: '/images/demo.jpg', alt: 'Demo image' }]}
            />,
        );

        expect(screen.getByAltText('Demo image')).toHaveAttribute('src', '/images/demo.jpg');
        expect(screen.getByDisplayValue('csrf-token')).toHaveAttribute('name', '_token');
        expect(screen.getByLabelText('Upload hình')).toHaveAttribute('name', 'image');
        expect(screen.getByRole('button', { name: 'Upload' }).closest('form')).toHaveAttribute(
            'action',
            '/admin/images/upload',
        );
    });

    it('checks all visible media and previews selected upload image', async () => {
        const user = userEvent.setup();
        const createObjectUrl = vi.spyOn(URL, 'createObjectURL').mockReturnValue('blob:upload-preview');

        render(
            <AdminMediaManager
                labels={{ image: 'Hình ảnh', preview: 'Xem trước', uploadImage: 'Upload hình' }}
                items={[
                    { id: 1, src: '/images/one.jpg' },
                    { id: 2, src: '/images/two.jpg' },
                ]}
            />,
        );

        await user.click(screen.getAllByRole('checkbox')[0]);
        expect(screen.getAllByRole('checkbox')[1]).toBeChecked();
        expect(screen.getAllByRole('checkbox')[2]).toBeChecked();

        const file = new File(['image'], 'upload.png', { type: 'image/png' });
        await user.upload(screen.getByLabelText('Upload hình'), file);

        expect(createObjectUrl).toHaveBeenCalledWith(file);
        expect(screen.getByAltText('Xem trước')).toHaveAttribute('src', 'blob:upload-preview');

        createObjectUrl.mockRestore();
    });
});
