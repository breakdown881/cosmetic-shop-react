import { useEffect, useMemo, useState } from 'react';
import { destroy as destroyRequest, get, patch, post } from '../../services/apiClient.js';

const emptyValues = (fields) =>
    fields.reduce((values, field) => ({
        ...values,
        [field.name]: field.defaultValue ?? '',
    }), {});

const normalizeRows = (payload) => Array.isArray(payload?.data) ? payload.data : [];

const formatCell = (row, column) => {
    const value = row[column.key];

    if (column.type === 'boolean') {
        return value ? 'Hoạt động' : 'Không hoạt động';
    }

    return value ?? '';
};

export default function AdminApiResourceManager({
    apiUrl,
    breadcrumbs = [],
    columns = [],
    fields = [],
    labels = {},
    resourceName = 'resources',
    title = '',
}) {
    const shouldOpenCreate = window.location.pathname.endsWith('/create');
    const [rows, setRows] = useState([]);
    const [formValues, setFormValues] = useState(() => emptyValues(fields));
    const [editingRow, setEditingRow] = useState(null);
    const [isFormOpen, setIsFormOpen] = useState(shouldOpenCreate);
    const [isLoading, setIsLoading] = useState(true);
    const [errorMessage, setErrorMessage] = useState('');
    const [successMessage, setSuccessMessage] = useState('');

    const visibleColumns = useMemo(() => columns.filter((column) => column.key !== 'id'), [columns]);

    const loadRows = async () => {
        setIsLoading(true);
        setErrorMessage('');

        try {
            const payload = await get(apiUrl);
            setRows(normalizeRows(payload));
        } catch (error) {
            setErrorMessage(error.response?.data?.message ?? 'Không tải được dữ liệu.');
        } finally {
            setIsLoading(false);
        }
    };

    useEffect(() => {
        loadRows();
    }, [apiUrl]);

    const openCreateForm = () => {
        setEditingRow(null);
        setFormValues(emptyValues(fields));
        setIsFormOpen(true);
        setErrorMessage('');
        setSuccessMessage('');
    };

    const openEditForm = (row) => {
        setEditingRow(row);
        setFormValues({
            ...emptyValues(fields),
            ...row,
            password: '',
            password_confirmation: '',
        });
        setIsFormOpen(true);
        setErrorMessage('');
        setSuccessMessage('');
    };

    const closeForm = () => {
        setEditingRow(null);
        setFormValues(emptyValues(fields));
        setIsFormOpen(false);
        setErrorMessage('');
    };

    const handleChange = (field, event) => {
        setFormValues((currentValues) => ({
            ...currentValues,
            [field.name]: event.target.value,
        }));
    };

    const buildPayload = () => {
        const payload = {};

        fields.forEach((field) => {
            const value = formValues[field.name];

            if (editingRow && field.type === 'password' && !value) {
                return;
            }

            payload[field.name] = value;
        });

        return payload;
    };

    const handleSubmit = async (event) => {
        event.preventDefault();
        setErrorMessage('');
        setSuccessMessage('');

        try {
            const payload = buildPayload();
            const response = editingRow
                ? await patch(`${apiUrl}/${editingRow.id}`, payload)
                : await post(apiUrl, payload);

            setSuccessMessage(response.message ?? 'Lưu thành công.');
            closeForm();
            await loadRows();
        } catch (error) {
            const errors = error.response?.data?.errors;
            const firstError = errors ? Object.values(errors).flat()[0] : null;
            setErrorMessage(firstError ?? error.response?.data?.message ?? 'Không lưu được dữ liệu.');
        }
    };

    const handleDelete = async (row) => {
        if (!window.confirm(labels.deleteConfirm ?? 'Bạn có chắc muốn xóa?')) {
            return;
        }

        setErrorMessage('');
        setSuccessMessage('');

        try {
            await destroyRequest(`${apiUrl}/${row.id}`);
            setSuccessMessage('Xóa thành công.');
            await loadRows();
        } catch (error) {
            setErrorMessage(error.response?.data?.message ?? 'Không xóa được dữ liệu.');
        }
    };

    const renderField = (field) => {
        const fieldId = `${resourceName}-${field.name}`;
        const required = field.required || (!editingRow && field.createOnlyRequired);

        if (field.type === 'select') {
            return (
                <select
                    id={fieldId}
                    className="form-control"
                    value={formValues[field.name] ?? ''}
                    required={required}
                    onChange={(event) => handleChange(field, event)}
                >
                    <option value="">-- Chọn --</option>
                    {(field.options ?? []).map((option) => (
                        <option key={option.value} value={option.value}>
                            {option.label}
                        </option>
                    ))}
                </select>
            );
        }

        return (
            <input
                id={fieldId}
                className="form-control"
                type={field.type ?? 'text'}
                value={formValues[field.name] ?? ''}
                required={required}
                onChange={(event) => handleChange(field, event)}
            />
        );
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
                <button type="button" className="btn btn-primary btn-sm" onClick={openCreateForm}>
                    {labels.add ?? 'Thêm'}
                </button>
            </div>

            {errorMessage && <div className="alert alert-danger">{errorMessage}</div>}
            {successMessage && <div className="alert alert-success">{successMessage}</div>}

            {isFormOpen && (
                <form className="card card-body mb-3" onSubmit={handleSubmit}>
                    <h2 className="h5 mb-3">{editingRow ? labels.edit ?? 'Sửa' : labels.add ?? 'Thêm'}</h2>
                    {fields.map((field) => (
                        <div className="form-group row" key={field.name}>
                            <label className="col-md-12 control-label" htmlFor={`${resourceName}-${field.name}`}>
                                {field.label}{(field.required || (!editingRow && field.createOnlyRequired)) && <span className="required">*</span>}
                            </label>
                            <div className="col-md-9 col-lg-6">{renderField(field)}</div>
                        </div>
                    ))}
                    <div className="form-action row">
                        <div className="col-md-9 col-lg-6 d-flex justify-content-end">
                            <button type="submit" className="btn btn-primary btn-md mr-2">
                                {labels.save ?? 'Lưu'}
                            </button>
                            <button type="button" className="btn btn-secondary btn-md" onClick={closeForm}>
                                {labels.cancel ?? 'Hủy'}
                            </button>
                        </div>
                    </div>
                </form>
            )}

            <div className="card mb-3">
                <div className="card-body">
                    <div className="table-responsive">
                        <table className="table table-hover" width="100%" cellSpacing="0">
                            <thead>
                                <tr>
                                    {visibleColumns.map((column) => (
                                        <th key={column.key} className="text-center">{column.label}</th>
                                    ))}
                                    <th className="text-center" width="120">{labels.management ?? 'Quản lý'}</th>
                                </tr>
                            </thead>
                            <tbody>
                                {rows.map((row) => (
                                    <tr key={row.id}>
                                        {visibleColumns.map((column) => (
                                            <td key={column.key} className="text-center">{formatCell(row, column)}</td>
                                        ))}
                                        <td className="text-center">
                                            <button type="button" className="btn btn-warning btn-sm mr-1" onClick={() => openEditForm(row)}>
                                                <i className="fas fa-edit" aria-hidden="true" />
                                            </button>
                                            <button type="button" className="btn btn-danger btn-sm" onClick={() => handleDelete(row)}>
                                                <i className="fas fa-trash-alt" aria-hidden="true" />
                                            </button>
                                        </td>
                                    </tr>
                                ))}
                            </tbody>
                        </table>
                    </div>

                    {isLoading && <p>Đang tải...</p>}
                    {!isLoading && rows.length === 0 && <p className="react-empty-state">{labels.empty ?? 'Không có dữ liệu.'}</p>}
                </div>
            </div>
        </div>
    );
}
