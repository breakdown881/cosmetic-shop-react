import { useMemo, useState } from 'react';

const normalizeText = (value) => String(value ?? '').toLowerCase();

const sortRows = (rows, sortBy) => {
    const sortedRows = [...rows];

    if (sortBy === 'name_desc') {
        return sortedRows.sort((first, second) => normalizeText(second.name).localeCompare(normalizeText(first.name)));
    }

    if (sortBy === 'created_desc') {
        return sortedRows.sort((first, second) => String(second.created_at ?? '').localeCompare(String(first.created_at ?? '')));
    }

    if (sortBy === 'created_asc') {
        return sortedRows.sort((first, second) => String(first.created_at ?? '').localeCompare(String(second.created_at ?? '')));
    }

    return sortedRows.sort((first, second) => normalizeText(first.name).localeCompare(normalizeText(second.name)));
};

const currencyFormatter = new Intl.NumberFormat('vi-VN', {
    style: 'currency',
    currency: 'VND',
    maximumFractionDigits: 0,
});

const renderCellValue = (row, column) => {
    const value = row[column.key];

    if (column.type === 'currency') {
        return currencyFormatter.format(Number(value ?? 0));
    }

    if (column.type === 'boolean') {
        return value ? column.trueLabel ?? 'Có' : column.falseLabel ?? 'Không';
    }

    return value ?? '';
};

export default function AdminResourceTable({
    actions = [],
    breadcrumbs = [],
    rows = [],
    labels = {},
    resourceName = 'resource',
    csrfToken = '',
    columns = [],
    showLogo = false,
    showListAction = false,
}) {
    const [search, setSearch] = useState('');
    const [status, setStatus] = useState('');
    const [sortBy, setSortBy] = useState('name_asc');
    const [checkedIds, setCheckedIds] = useState([]);

    const filteredRows = useMemo(() => {
        const normalizedSearch = normalizeText(search);

        return sortRows(
            rows.filter((row) => {
                const matchesSearch = !normalizedSearch || normalizeText(row.name).includes(normalizedSearch);
                const matchesStatus = status === '' || String(row.status ?? 0) === status;

                return matchesSearch && matchesStatus;
            }),
            sortBy,
        );
    }, [rows, search, sortBy, status]);

    const filteredIds = useMemo(() => filteredRows.map((row) => row.id), [filteredRows]);
    const isAllChecked = filteredIds.length > 0 && filteredIds.every((id) => checkedIds.includes(id));

    const handleCheckAll = (event) => {
        setCheckedIds(event.target.checked ? filteredIds : []);
    };

    const handleCheckOne = (id) => {
        setCheckedIds((currentIds) =>
            currentIds.includes(id)
                ? currentIds.filter((currentId) => currentId !== id)
                : [...currentIds, id],
        );
    };

    const tableContent = (
        <>
            <div className="react-admin-resource__toolbar">
                <input
                    type="search"
                    value={search}
                    onChange={(event) => setSearch(event.target.value)}
                    placeholder={labels.searchPlaceholder ?? 'Tìm theo tên...'}
                />

                <select value={status} onChange={(event) => setStatus(event.target.value)}>
                    <option value="">{labels.allStatus ?? 'Tất cả trạng thái'}</option>
                    <option value="1">{labels.active ?? 'Hoạt động'}</option>
                    <option value="0">{labels.inactive ?? 'Không hoạt động'}</option>
                </select>

                <select value={sortBy} onChange={(event) => setSortBy(event.target.value)}>
                    <option value="name_asc">{labels.sortNameAsc ?? 'Tên A-Z'}</option>
                    <option value="name_desc">{labels.sortNameDesc ?? 'Tên Z-A'}</option>
                    <option value="created_desc">{labels.sortNewest ?? 'Mới nhất'}</option>
                    <option value="created_asc">{labels.sortOldest ?? 'Cũ nhất'}</option>
                </select>

                <span className="react-admin-resource__summary">
                    {filteredRows.length}/{rows.length} {labels.items ?? resourceName}
                    {checkedIds.length > 0 ? ` · ${checkedIds.length} ${labels.selected ?? 'đã chọn'}` : ''}
                </span>
            </div>

            <div className="table-responsive">
                <table className="table table-hover react-admin-resource__table" width="100%" cellSpacing="0">
                    <thead>
                        <tr>
                            <th className="text-center" width="50">
                                <input type="checkbox" checked={isAllChecked} onChange={handleCheckAll} />
                            </th>
                            {showLogo && (
                                <th className="text-center" width="100">
                                    {labels.logo ?? 'Logo'}
                                </th>
                            )}
                            <th className="text-center" width="300">
                                {labels.name ?? 'Tên'}
                            </th>
                            {columns.map((column) => (
                                <th
                                    key={column.key}
                                    className={column.headerClassName ?? 'text-center'}
                                    width={column.width}
                                >
                                    {column.label}
                                </th>
                            ))}
                            <th className="text-center" width="100">
                                {labels.status ?? 'Trạng thái'}
                            </th>
                            <th className="text-center" width="100">
                                {labels.createdAt ?? 'Ngày tạo'}
                            </th>
                            <th className="text-center" width="100">
                                {labels.updatedAt ?? 'Ngày cập nhật'}
                            </th>
                            <th className="text-center" width="100">
                                {labels.management ?? 'Quản lý'}
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        {filteredRows.map((row) => (
                            <tr key={row.id}>
                                <td className="text-center">
                                    <input
                                        type="checkbox"
                                        checked={checkedIds.includes(row.id)}
                                        data-id={row.id}
                                        onChange={() => handleCheckOne(row.id)}
                                    />
                                </td>
                                {showLogo && (
                                    <td className="text-center">
                                        {row.logoUrl && (
                                            <img
                                                src={row.logoUrl}
                                                alt={row.logoAlt ?? row.name}
                                                className="img-thumbnail react-admin-resource__logo"
                                            />
                                        )}
                                    </td>
                                )}
                                <td className="text-center">{row.name}</td>
                                {columns.map((column) => (
                                    <td key={column.key} className={column.className ?? 'text-center'}>
                                        {renderCellValue(row, column)}
                                    </td>
                                ))}
                                <td className="text-center">
                                    <button
                                        type="button"
                                        className={`btn ${row.status ? 'btn-success' : 'btn-danger'} btn-sm btn-change-status`}
                                        data-id={row.id}
                                        data-status={row.status ? 0 : 1}
                                        data-url={row.changeStatusUrl}
                                    >
                                        {row.status ? labels.active ?? 'Hoạt động' : labels.inactive ?? 'Không hoạt động'}
                                    </button>
                                </td>
                                <td className="text-center">{row.created_at}</td>
                                <td className="text-center">{row.updated_at}</td>
                                <td className="text-center">
                                    <div className="react-admin-resource__actions">
                                        {showListAction && row.listUrl && (
                                            <a href={row.listUrl} className="btn btn-warning btn-sm">
                                                <i className="fas fa-list" aria-hidden="true" />
                                            </a>
                                        )}
                                        <a href={row.editUrl} className="btn btn-warning btn-sm">
                                            <i className="fas fa-edit" aria-hidden="true" />
                                        </a>
                                        <form id={`delete-form-${row.id}`} className="hidden" action={row.deleteUrl} method="POST">
                                            <input type="hidden" name="_token" value={csrfToken} />
                                            <input type="hidden" name="_method" value="DELETE" />
                                        </form>
                                        <button type="button" className="btn btn-danger btn-sm btn-remove" data-id={row.id}>
                                            <i className="fas fa-trash-alt" aria-hidden="true" />
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        ))}
                    </tbody>
                </table>
            </div>

            {!filteredRows.length && (
                <p className="react-empty-state">
                    {labels.emptyMessage ?? 'Không tìm thấy dữ liệu phù hợp.'}
                </p>
            )}
        </>
    );

    if (breadcrumbs.length || actions.length) {
        return (
            <div id="content-wrapper">
                <div className="container-fluid">
                    {!!breadcrumbs.length && (
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
                    )}

                    {!!actions.length && (
                        <div className="action-bar">
                            {actions.map((action) =>
                                action.type === 'submit' ? (
                                    <input
                                        key={action.label}
                                        type="submit"
                                        className={action.className ?? 'btn btn-danger btn-sm'}
                                        value={action.label}
                                        name={action.name}
                                    />
                                ) : (
                                    <a
                                        key={action.label}
                                        href={action.href}
                                        className={action.className ?? 'btn btn-primary btn-sm'}
                                    >
                                        {action.label}
                                    </a>
                                ),
                            )}
                        </div>
                    )}

                    <div className="card mb-3">
                        <div className="card-body">
                            <div className="react-admin-resource">{tableContent}</div>
                        </div>
                    </div>
                </div>
            </div>
        );
    }

    return (
        <div className="react-admin-resource">
            {tableContent}
        </div>
    );
}
