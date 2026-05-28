const LinkList = ({ title, links = [] }) => (
    <div className="footerLink">
        <h4>{title}</h4>
        <ul className="list-unstyled">
            {links.map((link) => (
                <li key={link.label}>
                    <a href={link.href}>{link.label}</a>
                </li>
            ))}
        </ul>
    </div>
);

export default function PublicFooter({ categoryLinks = [], policyLinks = [], socialLinks = [], labels = {} }) {
    return (
        <>
            <footer className="container-fluid">
                <div className="row">
                    <div className="col-xs-12">
                        <div className="container">
                            <div className="row">
                                <div className="col-md-3 col-sm-3 col-xs-4 list">
                                    <LinkList title={labels.categories ?? 'Danh mục'} links={categoryLinks} />
                                </div>
                                <div className="col-md-3 col-sm-3 col-xs-4 list">
                                    <LinkList title={labels.links ?? 'Liên kết'} links={policyLinks} />
                                </div>
                                <div className="col-md-3 col-sm-3 col-xs-4 list">
                                    <div className="footerLink">
                                        <h4>{labels.contactUs ?? 'Liên hệ với chúng tôi'}</h4>
                                        <ul className="list-unstyled">
                                            <li>Phone: 0932.538.468</li>
                                            <li><a href="mailto:nguyenhuulocla2006@gmail.com">Mail: nguyenhuulocla2006@gmail.com</a></li>
                                        </ul>
                                        <ul className="list-inline">
                                            {socialLinks.map((link) => (
                                                <li key={link.href}><a href={link.href}><i className={link.icon} /></a></li>
                                            ))}
                                        </ul>
                                    </div>
                                </div>
                                <div className="col-md-3 col-sm-3 col-xs-12 list">
                                    <div className="newsletter clearfix">
                                        <h4>{labels.newsletter ?? 'Bản tin'}</h4>
                                        <p>{labels.newsletterDescription ?? 'Nhập Email của bạn để chúng tôi cung cấp thông tin nhanh nhất cho bạn về những sản phẩm mới!!'}</p>
                                        <form action="" method="post">
                                            <div className="input-group">
                                                <input type="text" className="form-control" placeholder={labels.emailPlaceholder ?? 'Nhập email của bạn..'} name="email" />
                                                <button type="submit" className="btn btn-primary send pull-right">{labels.send ?? 'Gửi'}</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </footer>
            <div className="back-to-top bg-color">▲</div>
        </>
    );
}
