import { useMemo, useState } from 'react';

const renderHiddenMethod = (method) => {
    if (!method || method.toUpperCase() === 'POST') {
        return null;
    }

    return <input type="hidden" name="_method" value={method.toUpperCase()} />;
};

export default function AdminResourceForm({
    action,
    backUrl,
    breadcrumbs = [],
    csrfToken,
    method = 'POST',
    enctype = 'multipart/form-data',
    fields = [],
    labels = {},
}) {
    const [filePreviews, setFilePreviews] = useState({});

    const normalizedMethod = method.toUpperCase();
    const htmlMethod = normalizedMethod === 'GET' ? 'get' : 'post';

    const filePreviewUrls = useMemo(() => Object.values(filePreviews), [filePreviews]);

    const handleFileChange = (fieldName, event) => {
        const file = event.target.files?.[0];

        setFilePreviews((currentPreviews) => {
            if (currentPreviews[fieldName]) {
                URL.revokeObjectURL(currentPreviews[fieldName]);
            }

            return {
                ...currentPreviews,
                [fieldName]: file ? URL.createObjectURL(file) : '',
            };
        });
    };

    const renderField = (field) => {
        const fieldId = field.id ?? field.name;

        if (field.type === 'select') {
            return (
                <select
                    name={field.name}
                    id={fieldId}
                    className="form-control"
                    defaultValue={String(field.value ?? '')}
                    required={field.required}
                >
                    {(field.options ?? []).map((option) => (
                        <option key={option.value} value={option.value}>
                            {option.label}
                        </option>
                    ))}
                </select>
            );
        }

        if (field.type === 'file') {
            const previewUrl = filePreviews[field.name];

            return (
                <>
                    {field.currentImageUrl && (
                        <div className="react-admin-form__current-image">
                            <img
                                src={field.currentImageUrl}
                                alt={field.currentImageAlt ?? field.label}
                                className="img-thumbnail"
                            />
                        </div>
                    )}
                    <input
                        name={field.name}
                        id={fieldId}
                        type="file"
                        className="form-control"
                        accept={field.accept}
                        required={field.required}
                        onChange={(event) => handleFileChange(field.name, event)}
                    />
                    {previewUrl && (
                        <div className="react-admin-form__current-image">
                            <span>{labels.preview ?? 'Xem trước'}</span>
                            <img src={previewUrl} alt={field.label} className="img-thumbnail" />
                        </div>
                    )}
                </>
            );
        }

        if (field.type === 'textarea') {
            return (
                <textarea
                    name={field.name}
                    id={fieldId}
                    defaultValue={field.value ?? ''}
                    className="form-control"
                    required={field.required}
                    rows={field.rows ?? 5}
                    maxLength={field.maxLength}
                />
            );
        }

        if (field.type === 'checkbox') {
            return (
                <input
                    name={field.name}
                    id={fieldId}
                    type="checkbox"
                    value={field.value ?? 1}
                    defaultChecked={!!field.checked}
                    className={field.className}
                    required={field.required}
                />
            );
        }

        return (
            <input
                name={field.name}
                id={fieldId}
                type={field.type ?? 'text'}
                defaultValue={field.value ?? ''}
                className="form-control"
                required={field.required}
                maxLength={field.maxLength}
                min={field.min}
                max={field.max}
                step={field.step}
            />
        );
    };

    const form = (
        <form method={htmlMethod} action={action} encType={enctype} className="react-admin-form">
            {csrfToken && <input type="hidden" name="_token" value={csrfToken} />}
            {renderHiddenMethod(normalizedMethod)}

            {fields.map((field) => (
                <div className="form-group row" key={field.name}>
                    <label className="col-md-12 control-label" htmlFor={field.id ?? field.name}>
                        {field.label}
                        {field.required && <span className="required">*</span>}
                    </label>
                    <div className="col-md-9 col-lg-6">{renderField(field)}</div>
                </div>
            ))}

            <div className="form-action row">
                <div className="col-md-9 col-lg-6 d-flex justify-content-end">
                    <button type="submit" className="btn btn-primary btn-md mr-2">
                        {labels.save ?? 'Lưu'}
                    </button>
                    <a href={backUrl} className="btn btn-secondary btn-md">
                        {labels.back ?? 'Quay lại'}
                    </a>
                </div>
            </div>

            {filePreviewUrls.length > 0 && (
                <span className="sr-only" aria-live="polite">
                    {labels.previewUpdated ?? 'File preview updated'}
                </span>
            )}
        </form>
    );

    if (!breadcrumbs.length) {
        return form;
    }

    return (
        <div id="content-wrapper">
            <div className="container-fluid">
                <ol className="breadcrumb">
                    {breadcrumbs.map((breadcrumb) => (
                        <li
                            key={breadcrumb.label}
                            className={`breadcrumb-item ${breadcrumb.active ? 'active' : ''}`}
                        >
                            {breadcrumb.href && !breadcrumb.active ? (
                                <a href={breadcrumb.href}>{breadcrumb.label}</a>
                            ) : (
                                breadcrumb.label
                            )}
                        </li>
                    ))}
                </ol>
                {form}
            </div>
        </div>
    );
}
