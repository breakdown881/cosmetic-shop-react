import { useEffect, useState } from 'react';
import PaginationControls, { lastPageFor, paginateRows } from '../../components/common/PaginationControls.jsx';
import { destroy as destroyRequest, get, post } from '../../services/apiClient.js';

const PER_PAGE = 12;

const normalizeRows = (payload) => Array.isArray(payload?.data) ? payload.data : [];

export default function AdminMediaManager({
    apiUrl = '',
    items = [],
    labels = {},
}) {
    const [previewUrl, setPreviewUrl] = useState('');
    const [checkedIds, setCheckedIds] = useState([]);
    const [rows, setRows] = useState(items);
    const [selectedFile, setSelectedFile] = useState(null);
    const [message, setMessage] = useState('');
    const [error, setError] = useState('');
    const [currentPage, setCurrentPage] = useState(1);

    const loadRows = async () => {
        if (!apiUrl) {
            return;
        }

        try {
            const payload = await get(apiUrl);
            setRows(normalizeRows(payload));
            setCurrentPage(1);
        } catch (requestError) {
            setError(requestError.response?.data?.message ?? 'Could not load media.');
        }
    };

    useEffect(() => {
        loadRows();
    }, [apiUrl]);

    const handleFileChange = (event) => {
        const file = event.target.files?.[0] ?? null;

        if (previewUrl) {
            URL.revokeObjectURL(previewUrl);
        }

        setSelectedFile(file);
        setPreviewUrl(file ? URL.createObjectURL(file) : '');
    };

    const handleCheckAll = (event) => {
        setCheckedIds(event.target.checked ? paginatedRows.map((item) => item.id) : []);
    };

    const handleCheckOne = (id) => {
        setCheckedIds((currentIds) =>
            currentIds.includes(id)
                ? currentIds.filter((currentId) => currentId !== id)
                : [...currentIds, id],
        );
    };

    const uploadFile = async (event) => {
        event.preventDefault();

        if (!selectedFile) {
            return;
        }

        const formData = new FormData();
        formData.append('image', selectedFile);
        setMessage('');
        setError('');

        try {
            const response = await post(apiUrl, formData);
            setMessage(response.message ?? 'Uploaded.');
            setSelectedFile(null);
            setPreviewUrl('');
            await loadRows();
        } catch (requestError) {
            setError(requestError.response?.data?.message ?? 'Could not upload media.');
        }
    };

    const deleteOne = async (id) => {
        setMessage('');
        setError('');

        try {
            await destroyRequest(`${apiUrl}/${id}`);
            setMessage('Deleted.');
            setCheckedIds((currentIds) => currentIds.filter((currentId) => currentId !== id));
            await loadRows();
        } catch (requestError) {
            setError(requestError.response?.data?.message ?? 'Could not delete media.');
        }
    };

    const deleteChecked = async () => {
        await Promise.all(checkedIds.map((id) => deleteOne(id)));
    };

    const paginatedRows = paginateRows(rows, currentPage, PER_PAGE);
    const lastPage = lastPageFor(rows, PER_PAGE);
    const isAllChecked = paginatedRows.length > 0 && paginatedRows.every((item) => checkedIds.includes(item.id));

    return (
        <div className="react-admin-media">
            {message && <div className="alert alert-success">{message}</div>}
            {error && <div className="alert alert-danger">{error}</div>}

            <div className="action-bar">
                <button type="button" className="btn btn-danger btn-sm" disabled={!checkedIds.length} onClick={deleteChecked}>
                    {labels.delete ?? 'Xoa'}
                </button>
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
                                    <th>{labels.image ?? 'Hinh anh'}</th>
                                    <th>{labels.management ?? ''}</th>
                                </tr>
                            </thead>
                            <tbody>
                                {paginatedRows.map((item) => (
                                    <tr key={item.id}>
                                        <td>
                                            <input
                                                type="checkbox"
                                                value={item.id}
                                                checked={checkedIds.includes(item.id)}
                                                onChange={() => handleCheckOne(item.id)}
                                            />
                                        </td>
                                        <td>
                                            <img src={item.src} alt={item.alt ?? labels.image ?? 'Hinh anh'} />
                                        </td>
                                        <td>
                                            <button type="button" className="btn btn-danger btn-sm" onClick={() => deleteOne(item.id)}>
                                                {labels.delete ?? 'Xoa'}
                                            </button>
                                        </td>
                                    </tr>
                                ))}
                            </tbody>
                        </table>
                    </div>

                    {!rows.length && <p className="react-empty-state">{labels.empty ?? 'Chua co hinh anh.'}</p>}
                {rows.length > 0 && (
                    <PaginationControls currentPage={currentPage} lastPage={lastPage} onPageChange={setCurrentPage} />
                )}
                </div>
            </div>

            <form onSubmit={uploadFile} className="react-admin-media__upload">
                <div className="form-group">
                    <label htmlFor="image">{labels.uploadImage ?? 'Upload hinh'}</label>
                    <input
                        type="file"
                        name="image"
                        id="image"
                        className="form-control"
                        accept=".jpg,.jpeg,.png,.webp"
                        onChange={handleFileChange}
                    />
                </div>

                {previewUrl && (
                    <div className="react-admin-media__preview">
                        <span>{labels.preview ?? 'Xem truoc'}</span>
                        <img src={previewUrl} alt={labels.preview ?? 'Xem truoc'} />
                    </div>
                )}

                <button type="submit" className="btn btn-primary btn-sm" disabled={!selectedFile}>
                    {labels.upload ?? 'Upload'}
                </button>
            </form>
        </div>
    );
}
