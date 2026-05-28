import { useState } from 'react';

export default function AdminMediaManager({
    items = [],
    uploadAction = '',
    csrfToken = '',
    labels = {},
}) {
    const [previewUrl, setPreviewUrl] = useState('');
    const [checkedIds, setCheckedIds] = useState([]);

    const handleFileChange = (event) => {
        const file = event.target.files?.[0];

        if (previewUrl) {
            URL.revokeObjectURL(previewUrl);
        }

        setPreviewUrl(file ? URL.createObjectURL(file) : '');
    };

    const handleCheckAll = (event) => {
        setCheckedIds(event.target.checked ? items.map((item) => item.id) : []);
    };

    const handleCheckOne = (id) => {
        setCheckedIds((currentIds) =>
            currentIds.includes(id)
                ? currentIds.filter((currentId) => currentId !== id)
                : [...currentIds, id],
        );
    };

    const isAllChecked = items.length > 0 && items.every((item) => checkedIds.includes(item.id));

    return (
        <div className="react-admin-media">
            <div className="action-bar">
                <input type="submit" className="btn btn-danger btn-sm" value={labels.delete ?? 'Xóa'} name="delete" />
            </div>

            <div className="card mb-3">
                <div className="card-body">
                    <div className="table-responsive">
                        <table className="table table-hover react-admin-media__table" width="100%" cellSpacing="0">
                            <thead>
                                <tr>
                                    <th>
                                        <input type="checkbox" checked={isAllChecked} onChange={handleCheckAll} />
                                    </th>
                                    <th>{labels.image ?? 'Hình ảnh'}</th>
                                    <th>{labels.management ?? ''}</th>
                                </tr>
                            </thead>
                            <tbody>
                                {items.map((item) => (
                                    <tr key={item.id}>
                                        <td>
                                            <input
                                                type="checkbox"
                                                checked={checkedIds.includes(item.id)}
                                                onChange={() => handleCheckOne(item.id)}
                                            />
                                        </td>
                                        <td>
                                            <img src={item.src} alt={item.alt ?? labels.image ?? 'Hình ảnh'} />
                                        </td>
                                        <td>
                                            <button type="button" className="btn btn-danger btn-sm" data-id={item.id}>
                                                {labels.delete ?? 'Xóa'}
                                            </button>
                                        </td>
                                    </tr>
                                ))}
                            </tbody>
                        </table>
                    </div>

                    {!items.length && <p className="react-empty-state">{labels.empty ?? 'Chưa có hình ảnh.'}</p>}
                </div>
            </div>

            <form action={uploadAction} method="POST" encType="multipart/form-data" className="react-admin-media__upload">
                {csrfToken && <input type="hidden" name="_token" value={csrfToken} />}

                <div className="form-group">
                    <label htmlFor="image">{labels.uploadImage ?? 'Upload hình'}</label>
                    <input
                        type="file"
                        name="image"
                        id="image"
                        className="form-control"
                        accept=".jpg,.jpeg,.png"
                        onChange={handleFileChange}
                    />
                </div>

                {previewUrl && (
                    <div className="react-admin-media__preview">
                        <span>{labels.preview ?? 'Xem trước'}</span>
                        <img src={previewUrl} alt={labels.preview ?? 'Xem trước'} />
                    </div>
                )}

                <input type="submit" value={labels.upload ?? 'Upload'} className="btn btn-primary btn-sm" />
            </form>
        </div>
    );
}
