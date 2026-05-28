import { render, screen } from '@testing-library/react';
import userEvent from '@testing-library/user-event';
import { describe, expect, it, vi } from 'vitest';
import AdminResourceForm from './AdminResourceForm.jsx';

const baseProps = {
    action: '/admin/brands/store',
    backUrl: '/admin/brands',
    csrfToken: 'csrf-token',
    labels: {
        back: 'Quay lại',
        preview: 'Xem trước',
        save: 'Lưu',
    },
    fields: [
        {
            label: 'Tên',
            name: 'name',
            required: true,
            value: 'Goda',
        },
        {
            label: 'Trạng thái',
            name: 'status',
            type: 'select',
            value: 1,
            options: [
                { label: 'Không hoạt động', value: 0 },
                { label: 'Hoạt động', value: 1 },
            ],
        },
    ],
};

describe('AdminResourceForm', () => {
    it('renders a traditional Laravel-compatible form', () => {
        render(<AdminResourceForm {...baseProps} method="PATCH" />);

        const form = screen.getByRole('button', { name: 'Lưu' }).closest('form');

        expect(form).toHaveAttribute('action', '/admin/brands/store');
        expect(form).toHaveAttribute('method', 'post');
        expect(form).toHaveAttribute('enctype', 'multipart/form-data');
        expect(screen.getByDisplayValue('csrf-token')).toHaveAttribute('name', '_token');
        expect(screen.getByDisplayValue('PATCH')).toHaveAttribute('name', '_method');
        expect(screen.getByLabelText(/Tên/)).toHaveValue('Goda');
        expect(screen.getByLabelText(/Trạng thái/)).toHaveValue('1');
        expect(screen.getByRole('link', { name: 'Quay lại' })).toHaveAttribute('href', '/admin/brands');
    });

    it('renders current image and creates a local preview for selected file', async () => {
        const user = userEvent.setup();
        const createObjectUrl = vi.spyOn(URL, 'createObjectURL').mockReturnValue('blob:new-image');

        render(
            <AdminResourceForm
                {...baseProps}
                fields={[
                    ...baseProps.fields,
                    {
                        accept: '.jpg,.jpeg,.png',
                        currentImageAlt: 'Logo hiện tại',
                        currentImageUrl: '/storage/brand.jpg',
                        label: 'Hình ảnh',
                        name: 'image',
                        type: 'file',
                    },
                ]}
            />,
        );

        expect(screen.getByAltText('Logo hiện tại')).toHaveAttribute('src', '/storage/brand.jpg');

        const file = new File(['image'], 'brand.png', { type: 'image/png' });
        await user.upload(screen.getByLabelText(/Hình ảnh/), file);

        expect(createObjectUrl).toHaveBeenCalledWith(file);
        expect(screen.getByText('Xem trước')).toBeInTheDocument();
        expect(screen.getByAltText('Hình ảnh')).toHaveAttribute('src', 'blob:new-image');

        createObjectUrl.mockRestore();
    });

    it('does not render method spoofing input for POST forms', () => {
        render(<AdminResourceForm {...baseProps} method="POST" />);

        const form = screen.getByRole('button', { name: 'Lưu' }).closest('form');

        expect(form).toHaveAttribute('method', 'post');
        expect(screen.queryByDisplayValue('POST')).not.toBeInTheDocument();
        expect(screen.getByLabelText(/Tên/)).toBeRequired();
    });

    it('renders textarea, number and checkbox fields', () => {
        render(
            <AdminResourceForm
                {...baseProps}
                fields={[
                    {
                        label: 'Giá bán lẻ',
                        name: 'price',
                        type: 'number',
                        value: 120000,
                        min: 0,
                    },
                    {
                        label: 'Nổi bật',
                        name: 'featured',
                        type: 'checkbox',
                        value: 1,
                        checked: true,
                    },
                    {
                        label: 'Mô tả',
                        name: 'description',
                        type: 'textarea',
                        value: 'Mô tả sản phẩm',
                        rows: 6,
                    },
                ]}
            />,
        );

        expect(screen.getByLabelText(/Giá bán lẻ/)).toHaveValue(120000);
        expect(screen.getByLabelText(/Giá bán lẻ/)).toHaveAttribute('min', '0');
        expect(screen.getByLabelText(/Nổi bật/)).toBeChecked();
        expect(screen.getByLabelText(/Nổi bật/)).toHaveAttribute('value', '1');
        expect(screen.getByLabelText(/Mô tả/)).toHaveValue('Mô tả sản phẩm');
        expect(screen.getByLabelText(/Mô tả/)).toHaveAttribute('rows', '6');
    });
});
