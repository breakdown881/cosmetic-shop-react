export default function AdminTopNav({ brandUrl = '#', labels = {}, liveChatUnreadCount = 0, userName = '' }) {
    return (
        <nav className="navbar navbar-expand navbar-dark bg-dark static-top">
            <a className="navbar-brand mr-1" href={brandUrl}>
                {labels.brand ?? 'Brand'}
            </a>
            <button className="btn btn-link btn-sm text-white order-1 order-sm-0" id="sidebarToggle" type="button">
                <i className="fas fa-bars" />
            </button>

            <ul className="navbar-nav ml-auto">
                <li className="nav-item no-arrow mr-3">
                    <a className="nav-link text-white" href="/admin/live-chat" aria-label="Live chat notifications">
                        <i className="fas fa-comments" /> Live chat
                        {liveChatUnreadCount > 0 ? (
                            <span className="badge badge-danger ml-1">{liveChatUnreadCount}</span>
                        ) : null}
                    </a>
                </li>
                <li className="nav-item no-arrow text-white">
                    <span>{labels.hello ?? 'Chào'} {userName}</span> |{' '}
                    <a className="text-white nounderline" href="#" data-toggle="modal" data-target="#logoutModal">
                        {labels.logout ?? 'Logout'}
                    </a>
                </li>
            </ul>
        </nav>
    );
}
