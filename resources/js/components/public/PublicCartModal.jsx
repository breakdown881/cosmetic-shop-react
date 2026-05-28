import Cart from '../cart/Cart.jsx';

export default function PublicCartModal({
    cartUrl = '#',
    checkoutUrl = '#',
    items = [],
    labels = {},
    subtotal = '',
}) {
    return (
        <div className="modal fade" id="modal-cart-detail" role="dialog">
            <div className="modal-dialog">
                <div className="modal-content">
                    <div className="modal-header bg-color">
                        <button type="button" className="close" data-dismiss="modal" aria-hidden="true">
                            x
                        </button>
                        <h3 className="modal-title text-center">{labels.title ?? 'Giỏ hàng'}</h3>
                    </div>
                    <div className="modal-body">
                        <div className="page-content">
                            <div className="clearfix hidden-sm hidden-xs">
                                <div className="col-xs-1" />
                                <div className="col-xs-3">
                                    <div className="header">{labels.product ?? 'Sản phẩm'}</div>
                                </div>
                                <div className="col-xs-2">
                                    <div className="header">{labels.price ?? 'Đơn giá'}</div>
                                </div>
                                <div className="label_item col-xs-3">
                                    <div className="header">{labels.quantity ?? 'Số lượng'}</div>
                                </div>
                                <div className="col-xs-2">
                                    <div className="header">{labels.subtotal ?? 'Thành tiền'}</div>
                                </div>
                                <div className="lcol-xs-1" />
                            </div>
                            <div className="cart-product">
                                <Cart items={items} />
                            </div>
                        </div>
                    </div>
                    <div className="modal-footer">
                        <div className="clearfix">
                            <div className="col-xs-12 text-right">
                                <p>
                                    <span>{labels.total ?? 'Tổng tiền'}</span>{' '}
                                    <span className="price-total">{subtotal}</span>
                                </p>
                                <a className="btn btn-default" href={cartUrl}>
                                    {labels.continueShopping ?? 'Tiếp tục mua sắm'}
                                </a>{' '}
                                <a className="btn btn-primary" href={checkoutUrl}>
                                    {labels.checkout ?? 'Đặt hàng'}
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    );
}
