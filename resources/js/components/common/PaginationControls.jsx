const pageNumbers = (lastPage) => Array.from({ length: Math.max(0, lastPage) }, (_, index) => index + 1);

export default function PaginationControls({
    currentPage = 1,
    lastPage = 1,
    links = {},
    onPageChange = null,
}) {
    if (lastPage <= 1) {
        return null;
    }

    const safeCurrentPage = Math.min(Math.max(1, currentPage), lastPage);
    const previousPage = safeCurrentPage - 1;
    const nextPage = safeCurrentPage + 1;

    return (
        <nav className="react-pagination" aria-label="Pagination">
            {onPageChange ? (
                <button
                    type="button"
                    disabled={safeCurrentPage === 1}
                    onClick={() => onPageChange(previousPage)}
                >
                    Previous
                </button>
            ) : (
                <a
                    aria-disabled={safeCurrentPage === 1}
                    href={links.prev ?? pageHref(previousPage)}
                >
                    Previous
                </a>
            )}

            {pageNumbers(lastPage).map((page) => (
                onPageChange ? (
                    <button
                        type="button"
                        aria-current={page === safeCurrentPage ? 'page' : undefined}
                        key={page}
                        onClick={() => onPageChange(page)}
                    >
                        {page}
                    </button>
                ) : (
                    <a
                        aria-current={page === safeCurrentPage ? 'page' : undefined}
                        href={pageHref(page)}
                        key={page}
                    >
                        {page}
                    </a>
                )
            ))}

            {onPageChange ? (
                <button
                    type="button"
                    disabled={safeCurrentPage === lastPage}
                    onClick={() => onPageChange(nextPage)}
                >
                    Next
                </button>
            ) : (
                <a
                    aria-disabled={safeCurrentPage === lastPage}
                    href={links.next ?? pageHref(nextPage)}
                >
                    Next
                </a>
            )}
        </nav>
    );
}

export function paginateRows(rows, currentPage, perPage) {
    const safeRows = rows ?? [];
    const safeCurrentPage = Math.min(Math.max(1, currentPage), lastPageFor(safeRows, perPage));
    const start = (safeCurrentPage - 1) * perPage;

    return safeRows.slice(start, start + perPage);
}

export function lastPageFor(rows, perPage) {
    return Math.max(1, Math.ceil((rows?.length ?? 0) / perPage));
}

function pageHref(page) {
    if (typeof window === 'undefined') {
        return `?page=${page}`;
    }

    const url = new URL(window.location.href);
    url.searchParams.set('page', page);

    return `${url.pathname}${url.search}${url.hash}`;
}
