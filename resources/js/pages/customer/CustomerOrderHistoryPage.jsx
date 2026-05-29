import { useState } from 'react';
import CustomerLayout from '../../components/customer/CustomerLayout.jsx';
import PaginationControls, { lastPageFor, paginateRows } from '../../components/common/PaginationControls.jsx';

const PER_PAGE = 10;

const currencyFormatter = new Intl.NumberFormat('vi-VN', {
    style: 'currency',
    currency: 'VND',
    maximumFractionDigits: 0,
});

export default function CustomerOrderHistoryPage({
    auth = null,
    navItems = [],
    orders = [],
    requiresAuth = false,
    title = 'Đơn hàng của tôi',
}) {
    const [currentPage, setCurrentPage] = useState(1);
    const paginatedOrders = paginateRows(orders, currentPage, PER_PAGE);
    const lastPage = lastPageFor(orders, PER_PAGE);

    return (
        <CustomerLayout auth={auth} navItems={navItems} title={title}>
            <section className="react-customer-orders">
                {requiresAuth ? (
                    <div className="react-customer-orders__empty">
                        <p>Please sign in to view your orders.</p>
                        <a href="/products">Continue shopping</a>
                    </div>
                ) : !orders.length ? (
                    <div className="react-customer-orders__empty">
                        <p>You have no orders yet.</p>
                        <a href="/products">Continue shopping</a>
                    </div>
                ) : (
                    <>
                    <div className="react-customer-orders__list">
                        {paginatedOrders.map((order) => (
                            <article className="react-customer-orders__card" key={order.id}>
                                <div className="react-customer-orders__card-header">
                                    <div>
                                        <h2>Order #{order.id}</h2>
                                        <p>{order.shipping_fullname}</p>
                                    </div>
                                    <span>{order.status}</span>
                                </div>

                                <ul>
                                    {(order.items ?? []).map((item) => (
                                        <li key={`${order.id}-${item.product_id}`}>
                                            <span>{item.product_name}</span>
                                            <strong>
                                                {item.qty} × {currencyFormatter.format(item.unit_price)}
                                            </strong>
                                        </li>
                                    ))}
                                </ul>

                                <footer>
                                    <span>{order.created_at}</span>
                                    <strong>{currencyFormatter.format(order.payment_total)}</strong>
                                    <a href={order.detailUrl ?? `/orders/${order.id}`}>View details</a>
                                </footer>
                            </article>
                        ))}
                    </div>
                        <PaginationControls currentPage={currentPage} lastPage={lastPage} onPageChange={setCurrentPage} />
                    </>
                )}
            </section>
        </CustomerLayout>
    );
}
