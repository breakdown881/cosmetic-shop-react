const isActive = (activeKey, keys) => keys.includes(activeKey) ? 'active' : '';

const SocialLinks = ({ links = [] }) => (
    <ul className="list-inline">
        {links.map((link) => (
            <li key={link.href}>
                <a href={link.href}>
                    <i className={link.icon} />
                </a>
            </li>
        ))}
    </ul>
);

export default function PublicHeader({
    activeRoute = '',
    cartCount = 0,
    csrfToken = '',
    isAuthenticated = false,
    labels = {},
    searchValue = '',
    socialLinks = [],
    urls = {},
    userName = '',
}) {
    const navLinks = [
        { activeRoutes: ['index'], href: urls.home, label: labels.home ?? 'Trang chủ' },
        { activeRoutes: ['product.index', 'category.show'], href: urls.products, label: labels.products ?? 'Sản phẩm' },
        { href: 'chinh-sach-doi-tra.html', label: labels.returnPolicy ?? 'Chính sách đổi trả' },
        { href: 'chinh-sach-thanh-toan.html', label: labels.paymentPolicy ?? 'Chính sách thanh toán' },
        { href: 'chinh-sach-giao-hang.html', label: labels.shippingPolicy ?? 'Chính sách giao hàng' },
        { activeRoutes: ['contact.show'], href: urls.contact, label: labels.contact ?? 'Liên hệ' },
    ];

    return (
        <>
            <header>
                <input type="hidden" id="reference" value="" />
                <div className="top-navbar container-fluid">
                    <div className="menu-mb">
                        <a href="javascript:void(0)" className="btn-close" onClick={() => window.closeMenuMobile?.()}>
                            ×
                        </a>
                        {navLinks.map((link) => (
                            <a
                                key={link.label}
                                className={isActive(activeRoute, link.activeRoutes ?? [])}
                                href={link.href}
                            >
                                {link.label}
                            </a>
                        ))}
                    </div>
                    <div className="row">
                        <div className="hidden-lg hidden-md col-sm-2 col-xs-1">
                            <span className="btn-menu-mb" onClick={() => window.openMenuMobile?.()}>
                                <i className="glyphicon glyphicon-menu-hamburger" />
                            </span>
                        </div>
                        <div className="col-md-6 hidden-sm hidden-xs">
                            <SocialLinks links={socialLinks} />
                        </div>
                        <div className="col-md-6 col-sm-10 col-xs-11">
                            <ul className="list-inline pull-right top-right">
                                <li className="account-login">
                                    {isAuthenticated ? (
                                        <a href={urls.customerOrders} className="btn-logout">
                                            {labels.myOrders ?? 'Đơn hàng của tôi'}
                                        </a>
                                    ) : (
                                        <a href="javascript:void(0)" className="btn-register">
                                            {labels.register ?? 'Đăng Ký'}
                                        </a>
                                    )}
                                </li>
                                <li>
                                    {isAuthenticated ? (
                                        <>
                                            <a href="javascript:void(0)" className="btn-account dropdown-toggle" data-toggle="dropdown" id="dropdownMenu">
                                                {userName}
                                            </a>
                                            <ul className="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenu">
                                                <li><a href={urls.customerShow}>{labels.accountInfo ?? 'Thông tin tài khoản'}</a></li>
                                                <li><a href={urls.customerAddress}>{labels.shippingAddress ?? 'Địa chỉ giao hàng'}</a></li>
                                                <li><a href={urls.customerOrders}>{labels.myOrders ?? 'Đơn hàng của tôi'}</a></li>
                                                <li role="separator" className="divider" />
                                                <li>
                                                    <a
                                                        href="javascript:void(0)"
                                                        onClick={(event) => {
                                                            event.preventDefault();
                                                            document.getElementById('logout-form')?.submit();
                                                        }}
                                                    >
                                                        {labels.logout ?? 'Thoát'}
                                                    </a>
                                                    <form id="logout-form" action={urls.logout} method="post" className="d-none">
                                                        <input type="hidden" name="_token" value={csrfToken} />
                                                    </form>
                                                </li>
                                            </ul>
                                        </>
                                    ) : (
                                        <a href="javascript:void(0)" className="btn-login">
                                            {labels.login ?? 'Đăng Nhập'}
                                        </a>
                                    )}
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div className="container">
                    <div className="row">
                        <div className="col-lg-4 col-md-4 col-sm-12 col-xs-12 logo">
                            <a href={urls.home}><img src={urls.logoImage} className="img-responsive" alt={labels.logoAlt ?? 'Goda'} /></a>
                        </div>
                        <div className="col-lg-4 col-md-4 hidden-sm hidden-xs call-action">
                            <a href={urls.home}><img src={urls.bannerImage} className="img-responsive" alt={labels.bannerAlt ?? 'Goda'} /></a>
                        </div>
                        <div className="col-lg-4 col-md-4 hotline-search">
                            <div>
                                <p className="hotline-phone"><span><strong>Hotline: </strong><a href="tel:0932.538.468">0932.538.468</a></span></p>
                                <p className="hotline-email"><span><strong>Email: </strong><a href="mailto:nguyenhuulocla2006@gmail.com">nguyenhuulocla2006@gmail.com</a></span></p>
                            </div>
                            <form className="header-form" action={urls.products}>
                                <div className="input-group">
                                    <input type="search" className="form-control search" placeholder={labels.searchPlaceholder ?? 'Nhập từ khóa tìm kiếm'} name="search" autoComplete="off" defaultValue={searchValue} />
                                    <div className="input-group-btn">
                                        <button className="btn bt-search bg-color" type="submit">
                                            <i className="fa fa-search" style={{ color: '#fff' }} />
                                        </button>
                                    </div>
                                </div>
                                <div className="search-result" />
                            </form>
                        </div>
                    </div>
                </div>
            </header>

            <nav className="navbar navbar-default desktop-menu">
                <div className="container">
                    <ul className="nav navbar-nav navbar-left hidden-sm hidden-xs">
                        {navLinks.map((link) => (
                            <li key={link.label} className={isActive(activeRoute, link.activeRoutes ?? [])}>
                                <a href={link.href}>{link.label}</a>
                            </li>
                        ))}
                    </ul>
                    <span className="hidden-lg hidden-md experience">{labels.experience ?? 'Trải nghiệm cùng sản phẩm của Goda'}</span>
                    <ul className="nav navbar-nav navbar-right">
                        <li className="cart">
                            <a href="javascript:void(0)" className="btn-cart-detail" title={labels.cart ?? 'Giỏ Hàng'}>
                                <i className="fa fa-shopping-cart" /> <span className="number-total-product">{cartCount}</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>
        </>
    );
}
