@php
    $shippingCustomer = [
        'shipping_name' => $customer->shipping_name ?? '',
        'shipping_mobile' => $customer->shipping_mobile ?? '',
        'housenumber_street' => $customer->housenumber_street ?? '',
    ];

    $provincePayload = collect($provinces ?? [])->map(fn ($province) => [
        'id' => $province->id,
        'name' => $province->name,
    ])->values();

    $districtPayload = collect($districts ?? [])->map(fn ($district) => [
        'id' => $district->id,
        'name' => $district->name,
    ])->values();

    $wardPayload = collect($wards ?? [])->map(fn ($ward) => [
        'id' => $ward->id,
        'name' => $ward->name,
    ])->values();
@endphp

<div
    data-react-component="ShippingAddressForm"
    data-props='@json([
        "customer" => $shippingCustomer,
        "provinces" => $provincePayload,
        "districts" => $districtPayload,
        "wards" => $wardPayload,
        "selectedProvinceId" => $selected_province_id ?? "",
        "selectedDistrictId" => $selected_district_id ?? "",
        "selectedWardId" => $selected_ward_id ?? "",
        "labels" => [
            "fullname" => "Họ và tên",
            "mobile" => "Số điện thoại",
            "province" => "Tỉnh / thành phố",
            "district" => "Quận / huyện",
            "ward" => "Phường / xã",
            "address" => "Địa chỉ",
        ],
    ])'
></div>
