const renderOptions = (items) =>
    items.map((item) => (
        <option key={item.id} value={item.id}>
            {item.name}
        </option>
    ));

export default function ShippingAddressForm({
    customer = {},
    districts = [],
    provinces = [],
    selectedDistrictId = '',
    selectedProvinceId = '',
    selectedWardId = '',
    wards = [],
    labels = {},
}) {
    return (
        <div className="row">
            <div className="form-group col-sm-6">
                <input
                    type="text"
                    defaultValue={customer.shipping_name ?? ''}
                    className="form-control"
                    name="fullname"
                    placeholder={labels.fullname ?? 'Họ và tên'}
                    required
                />
            </div>
            <div className="form-group col-sm-6">
                <input
                    type="tel"
                    defaultValue={customer.shipping_mobile ?? ''}
                    className="form-control"
                    name="mobile"
                    placeholder={labels.mobile ?? 'Số điện thoại'}
                    required
                    pattern="[0][0-9]{9,}"
                />
            </div>
            <div className="form-group col-sm-4">
                <select name="province" className="form-control province" required defaultValue={selectedProvinceId ?? ''}>
                    <option value="">{labels.province ?? 'Tỉnh / thành phố'}</option>
                    {renderOptions(provinces)}
                </select>
            </div>
            <div className="form-group col-sm-4">
                <select name="district" className="form-control district" required defaultValue={selectedDistrictId ?? ''}>
                    <option value="">{labels.district ?? 'Quận / huyện'}</option>
                    {renderOptions(districts)}
                </select>
            </div>
            <div className="form-group col-sm-4">
                <select name="ward" className="form-control ward" required defaultValue={selectedWardId ?? ''}>
                    <option value="">{labels.ward ?? 'Phường / xã'}</option>
                    {renderOptions(wards)}
                </select>
            </div>
            <div className="form-group col-sm-12">
                <input
                    type="text"
                    defaultValue={customer.housenumber_street ?? ''}
                    className="form-control"
                    placeholder={labels.address ?? 'Địa chỉ'}
                    name="address"
                    required
                />
            </div>
        </div>
    );
}
