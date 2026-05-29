import CustomerLayout from '../../components/customer/CustomerLayout.jsx';
import ProductFilters from '../../components/customer/ProductFilters.jsx';
import ProductGrid from '../../components/product/ProductGrid.jsx';
import PaginationControls from '../../components/common/PaginationControls.jsx';

export default function CustomerProductIndex({
    auth = null,
    filterOptions = {},
    filters = {},
    navItems = [],
    products = { data: [], meta: {} },
    title = 'Products',
}) {
    const productRows = products.data ?? [];
    const total = products.meta?.total ?? productRows.length;

    return (
        <CustomerLayout auth={auth} navItems={navItems} title={title}>
            <section className="react-customer-products">
                <div className="react-customer-products__summary">
                    <span>{total} product{total === 1 ? '' : 's'} found</span>
                </div>

                <ProductFilters filters={filters} filterOptions={filterOptions} />

                <ProductGrid
                    products={productRows}
                    emptyMessage="No products match your filters."
                />

                <PaginationControls
                    currentPage={products.meta?.currentPage ?? 1}
                    lastPage={products.meta?.lastPage ?? 1}
                    links={products.links ?? {}}
                />
            </section>
        </CustomerLayout>
    );
}
