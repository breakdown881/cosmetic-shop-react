import CustomerLayout from '../../components/customer/CustomerLayout.jsx';

export default function CustomerPlaceholderPage({
    auth = null,
    description = '',
    navItems = [],
    title = 'Customer page',
}) {
    return (
        <CustomerLayout auth={auth} navItems={navItems} title={title}>
            <section className="react-customer-placeholder">
                <p>{description}</p>
            </section>
        </CustomerLayout>
    );
}
