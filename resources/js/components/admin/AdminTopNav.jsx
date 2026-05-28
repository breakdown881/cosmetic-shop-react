export default function AdminTopNav({ brandUrl = '#', labels = {}, userName = '' }) {
    return (
        <nav className="navbar navbar-expand navbar-dark bg-dark static-top">
            <a className="navbar-brand mr-1" href={brandUrl}>
                {labels.brand ?? 'Brand'}
            </a>
            <button className="btn btn-link btn-sm text-white order-1 order-sm-0" id="sidebarToggle" type="button">
                <i className="fas fa-bars" />
            </button>

            <ul className="navbar-nav ml-auto">
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
