import CustomerLayout from '../../components/customer/CustomerLayout.jsx';

export default function CustomerPlaceholderPage({
    description = '',
    navItems = [],
    title = 'Customer page',
}) {
    return (
        <CustomerLayout navItems={navItems} title={title}>
            <section className="react-customer-placeholder">
                <p>{description}</p>
            </section>
        </CustomerLayout>
    );
}
