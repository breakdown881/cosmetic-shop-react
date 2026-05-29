import { useState } from 'react';

const currencyFormatter = new Intl.NumberFormat('vi-VN', {
    style: 'currency',
    currency: 'VND',
    maximumFractionDigits: 0,
});

const metricClassNames = {
    cancelledOrders: 'bg-danger',
    orders: 'bg-warning',
    revenue: 'bg-success',
};

const metricIcons = {
    cancelledOrders: 'fas fa-fw fa-life-ring',
    orders: 'fas fa-fw fa-list',
    revenue: 'fas fa-fw fa-shopping-cart',
};

const formatMetricValue = (metric) => {
    if (metric.type === 'currency') {
        return currencyFormatter.format(Number(metric.value ?? 0));
    }

    return metric.value ?? 0;
};

export default function AdminDashboard({ metrics = [], orders = [], periods = [], labels = {} }) {
    const [activePeriod, setActivePeriod] = useState(periods[0]?.key ?? '');

    return (
        <div className="react-admin-dashboard">
            <div className="mb-3 my-3 react-admin-dashboard__periods">
                {periods.map((period) => (
                    <button
                        key={period.key}
                        type="button"
                        className={`btn btn-primary ${activePeriod === period.key ? 'active' : ''}`}
                        onClick={() => setActivePeriod(period.key)}
                    >
                        {period.label}
                    </button>
                ))}

                <div className="react-admin-dashboard__date-range">
                    <label>
                        {labels.fromDate ?? 'Từ ngày'}
                        <input type="date" className="form-control" />
                    </label>
                    <label>
                        {labels.toDate ?? 'Đến ngày'}
                        <input type="date" className="form-control" />
                    </label>
                    <button type="button" className="btn btn-primary">
                        {labels.find ?? 'Tìm'}
                    </button>
                </div>
            </div>

            <div className="row">
                {metrics.map((metric) => (
                    <div className="col-xl-4 col-sm-6 mb-3" key={metric.key}>
                        <div className={`card text-white ${metricClassNames[metric.key] ?? 'bg-primary'} o-hidden h-100`}>
                            <div className="card-body">
                                <div className="card-body-icon">
                                    <i className={metricIcons[metric.key] ?? 'fas fa-fw fa-chart-bar'} />
                                </div>
                                <div className="mr-5">
                                    {metric.label} {formatMetricValue(metric)}
                                </div>
                            </div>
                            <a className="card-footer text-white clearfix small z-1" href={metric.href ?? '#'}>
                                <span className="float-left">{labels.detail ?? 'Chi tiết'}</span>
                                <span className="float-right">
                                    <i className="fas fa-angle-right" />
                                </span>
                            </a>
                        </div>
                    </div>
                ))}
            </div>

            <div className="card mb-3">
                <div className="card-header">
                    <i className="fas fa-table" /> {labels.orders ?? 'Đơn hàng'}
                </div>
                <div className="card-body">
                    <div className="table-responsive">
                        <table className="table table-hover react-admin-dashboard__orders" width="100%" cellSpacing="0">
                            <thead>
                                <tr>
                                    <th>{labels.code ?? 'Mã'}</th>
                                    <th>{labels.customerName ?? 'Khách hàng'}</th>
                                    <th>{labels.customerPhone ?? 'Điện thoại'}</th>
                                    <th>{labels.status ?? 'Trạng thái'}</th>
                                    <th>{labels.orderDate ?? 'Ngày đặt'}</th>
                                    <th>{labels.paymentMethod ?? 'Thanh toán'}</th>
                                    <th>{labels.total ?? 'Tổng'}</th>
                                    <th>{labels.deliveryAddress ?? 'Địa chỉ'}</th>
                                    <th />
                                </tr>
                            </thead>
                            <tbody>
                                {orders.map((order) => (
                                    <tr key={order.id}>
                                        <td>#{order.id}</td>
                                        <td>{order.customerName}</td>
                                        <td>{order.customerPhone}</td>
                                        <td>{order.status}</td>
                                        <td>{order.orderDate}</td>
                                        <td>{order.paymentMethod}</td>
                                        <td>{currencyFormatter.format(Number(order.total ?? 0))}</td>
                                        <td>{order.deliveryAddress}</td>
                                        <td className="react-admin-dashboard__actions">
                                            <button type="button" className="btn btn-primary btn-sm">
                                                {labels.confirm ?? 'Xác nhận'}
                                            </button>
                                            <button type="button" className="btn btn-warning btn-sm">
                                                {labels.edit ?? 'Sửa'}
                                            </button>
                                            <button type="button" className="btn btn-danger btn-sm">
                                                {labels.delete ?? 'Xóa'}
                                            </button>
                                        </td>
                                    </tr>
                                ))}
                            </tbody>
                        </table>
                    </div>

                    {!orders.length && <p className="react-empty-state">{labels.emptyOrders ?? 'Chưa có đơn hàng.'}</p>}
                </div>
            </div>
        </div>
    );
}
