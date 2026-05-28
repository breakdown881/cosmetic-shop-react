import { render, screen } from '@testing-library/react';
import { describe, expect, it } from 'vitest';
import ShippingAddressForm from './ShippingAddressForm.jsx';

describe('ShippingAddressForm', () => {
    it('renders Laravel-compatible shipping fields and selected location values', () => {
        render(
            <ShippingAddressForm
                customer={{
                    housenumber_street: '12 Nguyễn Trãi',
                    shipping_mobile: '0900000000',
                    shipping_name: 'Nguyễn Văn A',
                }}
                selectedProvinceId={1}
                selectedDistrictId={2}
                selectedWardId={3}
                provinces={[{ id: 1, name: 'Hà Nội' }]}
                districts={[{ id: 2, name: 'Thanh Xuân' }]}
                wards={[{ id: 3, name: 'Khương Trung' }]}
            />,
        );

        expect(screen.getByPlaceholderText('Họ và tên')).toHaveAttribute('name', 'fullname');
        expect(screen.getByPlaceholderText('Họ và tên')).toHaveValue('Nguyễn Văn A');
        expect(screen.getByPlaceholderText('Số điện thoại')).toHaveAttribute('pattern', '[0][0-9]{9,}');
        expect(screen.getByDisplayValue('Hà Nội')).toBeInTheDocument();
        expect(screen.getByDisplayValue('Thanh Xuân')).toBeInTheDocument();
        expect(screen.getByDisplayValue('Khương Trung')).toBeInTheDocument();
        expect(screen.getByPlaceholderText('Địa chỉ')).toHaveValue('12 Nguyễn Trãi');
    });
});
