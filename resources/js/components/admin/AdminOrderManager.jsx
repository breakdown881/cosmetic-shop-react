import { useEffect, useMemo, useState } from 'react';
import { destroy as destroyRequest, get, patch, post } from '../../services/apiClient.js';

const emptyOrder = {
    customer_id: '',
    shipping_fullname: '',
    shipping_mobile: '',
    payment_method: 0,
    shipping_ward_id: '',
    shipping_housenumber_street: '',
    delivered_date: '',
    discount_code: '',
    feeship_id: '',
    status: 'PENDING',
    note: '',
    items: [{ product_id: '', qty: 1 }],
};

const money = new Intl.NumberFormat('vi-VN', {
    style: 'currency',
    currency: 'VND',
    maximumFractionDigits: 0,
});

const normalizeRows = (payload) => Array.isArray(payload?.data) ? payload.data : [];

export default function AdminOrderManager({
    apiUrl,
    breadcrumbs = [],
    canCreate = true,
    canDelete = true,
    canEdit = true,
    customers = [],
    discounts = [],
    feeships = [],
    paymentMethods = {},
    products = [],
    statusOptions = [],
    title = 'Orders',
}) {
    const shouldOpenCreate = canCreate && window.location.pathname.endsWith('/create');
    const [rows, setRows] = useState([]);
    const [formValues, setFormValues] = useState(emptyOrder);
    const [editingRow, setEditingRow] = useState(null);
    const [isFormOpen, setIsFormOpen] = useState(shouldOpenCreate);
    const [isLoading, setIsLoading] = useState(true);
    const [message, setMessage] = useState('');
    const [error, setError] = useState('');

    const productById = useMemo(() => new Map(products.map((product) => [String(product.id), product])), [products]);
    const discountByCode = useMemo(() => new Map(discounts.map((discount) => [discount.code, discount])), [discounts]);
    const feeShipById = useMemo(() => new Map(feeships.map((feeShip) => [String(feeShip.id), feeShip])), [feeships]);

    const totals = useMemo(() => {
        const subTotal = formValues.items.reduce((sum, item) => {
            const product = productById.get(String(item.product_id));
            return sum + Number(product?.price ?? 0) * Number(item.qty ?? 0);
        }, 0);
        const discount = discountByCode.get(formValues.discount_code);
        const discountAmount = discount
            ? Math.min(subTotal, Number(discount.is_fixed) === 1
                ? Number(discount.discount_amount)
                : Math.floor(subTotal * (Number(discount.discount_amount) / 100)))
            : 0;
        const shippingFee = Number(feeShipById.get(String(formValues.feeship_id))?.price ?? 0);

        return {
            subTotal,
            discountAmount,
            shippingFee,
            paymentTotal: Math.max(0, subTotal - discountAmount + shippingFee),
        };
    }, [discountByCode, feeShipById, formValues.discount_code, formValues.feeship_id, formValues.items, productById]);

    const loadRows = async () => {
        setIsLoading(true);
        setError('');

        try {
            const payload = await get(apiUrl);
            setRows(normalizeRows(payload));
        } catch (requestError) {
            setError(requestError.response?.data?.message ?? 'Could not load orders.');
        } finally {
            setIsLoading(false);
        }
    };

    useEffect(() => {
        loadRows();
    }, [apiUrl]);

    const resetForm = () => {
        setEditingRow(null);
        setFormValues(emptyOrder);
        setIsFormOpen(false);
    };

    const openCreate = () => {
        setEditingRow(null);
        setFormValues(emptyOrder);
        setMessage('');
        setError('');
        setIsFormOpen(true);
    };

    const openEdit = (row) => {
        setEditingRow(row);
        setFormValues({
            ...emptyOrder,
            ...row,
            discount_code: row.discount_code ?? '',
            feeship_id: row.feeship_id ?? '',
            delivered_date: row.delivered_date ?? '',
            items: row.items?.length ? row.items.map((item) => ({
                product_id: item.product_id,
                qty: item.qty,
            })) : emptyOrder.items,
        });
        setMessage('');
        setError('');
        setIsFormOpen(true);
    };

    const setField = (field, value) => {
        setFormValues((currentValues) => ({
            ...currentValues,
            [field]: value,
        }));
    };

    const setItemField = (index, field, value) => {
        setFormValues((currentValues) => ({
            ...currentValues,
            items: currentValues.items.map((item, itemIndex) => (
                itemIndex === index ? { ...item, [field]: value } : item
            )),
        }));
    };

    const addItem = () => {
        setFormValues((currentValues) => ({
            ...currentValues,
            items: [...currentValues.items, { product_id: '', qty: 1 }],
        }));
    };

    const removeItem = (index) => {
        setFormValues((currentValues) => ({
            ...currentValues,
            items: currentValues.items.length === 1
                ? currentValues.items
                : currentValues.items.filter((_, itemIndex) => itemIndex !== index),
        }));
    };

    const payload = () => ({
        ...formValues,
        customer_id: Number(formValues.customer_id),
        payment_method: Number(formValues.payment_method),
        feeship_id: formValues.feeship_id ? Number(formValues.feeship_id) : null,
        discount_code: formValues.discount_code || null,
        items: formValues.items.map((item) => ({
            product_id: Number(item.product_id),
            qty: Number(item.qty),
        })),
    });

    const saveOrder = async (event) => {
        event.preventDefault();
        setMessage('');
        setError('');

        try {
            const response = editingRow
                ? await patch(`${apiUrl}/${editingRow.id}`, payload())
                : await post(apiUrl, payload());
            setMessage(response.message ?? 'Saved.');
            resetForm();
            await loadRows();
        } catch (requestError) {
            const errors = requestError.response?.data?.errors;
            const firstError = errors ? Object.values(errors).flat()[0] : null;
            setError(firstError ?? requestError.response?.data?.message ?? 'Could not save order.');
        }
    };

    const deleteOrder = async (row) => {
        if (!window.confirm('Delete this order?')) {
            return;
        }

        setMessage('');
        setError('');

        try {
            await destroyRequest(`${apiUrl}/${row.id}`);
            setMessage('Deleted.');
            await loadRows();
        } catch (requestError) {
            setError(requestError.response?.data?.message ?? 'Could not delete order.');
        }
    };

    return (
        <div className="container-fluid">
            {!!breadcrumbs.length && (
                <ol className="breadcrumb">
                    {breadcrumbs.map((breadcrumb) => (
                        <li key={breadcrumb.label} className={`breadcrumb-item ${breadcrumb.active ? 'active' : ''}`}>
                            {breadcrumb.href && !breadcrumb.active ? <a href={breadcrumb.href}>{breadcrumb.label}</a> : breadcrumb.label}
                        </li>
                    ))}
                </ol>
            )}

            <div className="d-flex justify-content-between align-items-center mb-3">
                <h1 className="h4 mb-0">{title}</h1>
                {canCreate && (
                    <button type="button" className="btn btn-primary btn-sm" onClick={openCreate}>
                        Add
                    </button>
                )}
            </div>

            {message && <div className="alert alert-success">{message}</div>}
            {error && <div className="alert alert-danger">{error}</div>}

            {isFormOpen && (
                <form className="card card-body mb-3" onSubmit={saveOrder}>
                    <h2 className="h5 mb-3">{editingRow ? 'Edit order' : 'Add order'}</h2>
                    <div className="form-row">
                        <div className="form-group col-md-6">
                            <label htmlFor="order-customer">Customer</label>
                            <select id="order-customer" className="form-control" value={formValues.customer_id} required onChange={(event) => setField('customer_id', event.target.value)}>
                                <option value="">-- Select --</option>
                                {customers.map((customer) => (
                                    <option key={customer.id} value={customer.id}>{customer.name} ({customer.email})</option>
                                ))}
                            </select>
                        </div>
                        <div className="form-group col-md-3">
                            <label htmlFor="order-payment">Payment</label>
                            <select id="order-payment" className="form-control" value={formValues.payment_method} required onChange={(event) => setField('payment_method', event.target.value)}>
                                {Object.entries(paymentMethods).map(([value, label]) => (
                                    <option key={value} value={value}>{label}</option>
                                ))}
                            </select>
                        </div>
                        <div className="form-group col-md-3">
                            <label htmlFor="order-status">Status</label>
                            <select id="order-status" className="form-control" value={formValues.status} required onChange={(event) => setField('status', event.target.value)}>
                                {statusOptions.map((status) => (
                                    <option key={status} value={status}>{status}</option>
                                ))}
                            </select>
                        </div>
                    </div>

                    <div className="form-row">
                        <div className="form-group col-md-4">
                            <label htmlFor="shipping-fullname">Shipping name</label>
                            <input id="shipping-fullname" className="form-control" value={formValues.shipping_fullname} required onChange={(event) => setField('shipping_fullname', event.target.value)} />
                        </div>
                        <div className="form-group col-md-4">
                            <label htmlFor="shipping-mobile">Shipping mobile</label>
                            <input id="shipping-mobile" className="form-control" value={formValues.shipping_mobile} required onChange={(event) => setField('shipping_mobile', event.target.value)} />
                        </div>
                        <div className="form-group col-md-4">
                            <label htmlFor="delivered-date">Delivered date</label>
                            <input id="delivered-date" type="date" className="form-control" value={formValues.delivered_date} onChange={(event) => setField('delivered_date', event.target.value)} />
                        </div>
                    </div>

                    <div className="form-row">
                        <div className="form-group col-md-4">
                            <label htmlFor="shipping-ward">Ward id</label>
                            <input id="shipping-ward" className="form-control" value={formValues.shipping_ward_id} onChange={(event) => setField('shipping_ward_id', event.target.value)} />
                        </div>
                        <div className="form-group col-md-8">
                            <label htmlFor="shipping-address">Shipping address</label>
                            <input id="shipping-address" className="form-control" value={formValues.shipping_housenumber_street} required onChange={(event) => setField('shipping_housenumber_street', event.target.value)} />
                        </div>
                    </div>

                    <div className="form-row">
                        <div className="form-group col-md-6">
                            <label htmlFor="discount-code">Discount</label>
                            <select id="discount-code" className="form-control" value={formValues.discount_code} onChange={(event) => setField('discount_code', event.target.value)}>
                                <option value="">No discount</option>
                                {discounts.map((discount) => (
                                    <option key={discount.code} value={discount.code}>{discount.code}</option>
                                ))}
                            </select>
                        </div>
                        <div className="form-group col-md-6">
                            <label htmlFor="feeship-id">Fee ship</label>
                            <select id="feeship-id" className="form-control" value={formValues.feeship_id} onChange={(event) => setField('feeship_id', event.target.value)}>
                                <option value="">No fee ship</option>
                                {feeships.map((feeShip) => (
                                    <option key={feeShip.id} value={feeShip.id}>{feeShip.label} - {money.format(Number(feeShip.price ?? 0))}</option>
                                ))}
                            </select>
                        </div>
                    </div>

                    <div className="form-group">
                        <label htmlFor="order-note">Note</label>
                        <textarea id="order-note" className="form-control" value={formValues.note ?? ''} onChange={(event) => setField('note', event.target.value)} />
                    </div>

                    <h3 className="h6">Items</h3>
                    {formValues.items.map((item, index) => (
                        <div className="form-row align-items-end" key={`order-item-${index}`}>
                            <div className="form-group col-md-8">
                                <label htmlFor={`order-item-product-${index}`}>Product</label>
                                <select id={`order-item-product-${index}`} className="form-control" value={item.product_id} required onChange={(event) => setItemField(index, 'product_id', event.target.value)}>
                                    <option value="">-- Select --</option>
                                    {products.map((product) => (
                                        <option key={product.id} value={product.id}>{product.name} - {money.format(Number(product.price ?? 0))}</option>
                                    ))}
                                </select>
                            </div>
                            <div className="form-group col-md-2">
                                <label htmlFor={`order-item-qty-${index}`}>Qty</label>
                                <input id={`order-item-qty-${index}`} type="number" min="1" className="form-control" value={item.qty} required onChange={(event) => setItemField(index, 'qty', event.target.value)} />
                            </div>
                            <div className="form-group col-md-2">
                                <button type="button" className="btn btn-secondary btn-sm" onClick={() => removeItem(index)}>Remove</button>
                            </div>
                        </div>
                    ))}
                    <button type="button" className="btn btn-outline-primary btn-sm mb-3" onClick={addItem}>Add item</button>

                    <div className="alert alert-info">
                        Subtotal: {money.format(totals.subTotal)} | Discount: {money.format(totals.discountAmount)} | Fee ship: {money.format(totals.shippingFee)} | Total: {money.format(totals.paymentTotal)}
                    </div>

                    <div className="d-flex justify-content-end">
                        <button type="submit" className="btn btn-primary btn-md mr-2">Save</button>
                        <button type="button" className="btn btn-secondary btn-md" onClick={resetForm}>Cancel</button>
                    </div>
                </form>
            )}

            <div className="card mb-3">
                <div className="card-body">
                    <div className="table-responsive">
                        <table className="table table-hover" width="100%" cellSpacing="0">
                            <thead>
                                <tr>
                                    <th className="text-center">ID</th>
                                    <th className="text-center">Customer</th>
                                    <th className="text-center">Status</th>
                                    <th className="text-center">Payment</th>
                                    <th className="text-center">Total</th>
                                    <th className="text-center">Note</th>
                                    <th className="text-center">Created</th>
                                    <th className="text-center" width="120">Manage</th>
                                </tr>
                            </thead>
                            <tbody>
                                {rows.map((row) => (
                                    <tr key={row.id}>
                                        <td className="text-center">{row.id}</td>
                                        <td className="text-center">{row.customer_name}</td>
                                        <td className="text-center">{row.status}</td>
                                        <td className="text-center">{row.payment_method_label}</td>
                                        <td className="text-center">{money.format(Number(row.payment_total ?? 0))}</td>
                                        <td className="text-center">{row.note}</td>
                                        <td className="text-center">{row.created_at}</td>
                                        <td className="text-center">
                                            {canEdit && (
                                                <button type="button" className="btn btn-warning btn-sm mr-1" onClick={() => openEdit(row)}>
                                                    <i className="fas fa-edit" aria-hidden="true" />
                                                </button>
                                            )}
                                            {canDelete && (
                                                <button type="button" className="btn btn-danger btn-sm" onClick={() => deleteOrder(row)}>
                                                    <i className="fas fa-trash-alt" aria-hidden="true" />
                                                </button>
                                            )}
                                        </td>
                                    </tr>
                                ))}
                            </tbody>
                        </table>
                    </div>
                    {isLoading && <p>Loading...</p>}
                    {!isLoading && rows.length === 0 && <p className="react-empty-state">No orders.</p>}
                </div>
            </div>
        </div>
    );
}
