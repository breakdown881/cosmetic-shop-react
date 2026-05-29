import CustomerLayout from '../../components/customer/CustomerLayout.jsx';
import ProductGrid from '../../components/product/ProductGrid.jsx';

const currencyFormatter = new Intl.NumberFormat('vi-VN', {
    style: 'currency',
    currency: 'VND',
    maximumFractionDigits: 0,
});

export default function CustomerProductShow({
    auth = null,
    navItems = [],
    product,
    relatedProducts = [],
}) {
    if (!product) {
        return (
            <CustomerLayout auth={auth} navItems={navItems} title="Product not found">
                <p className="react-empty-state">This product is not available.</p>
            </CustomerLayout>
        );
    }

    const hasDiscount = Number(product.price) > Number(product.sale_price);
    const isInStock = Number(product.inventory_qty ?? 0) > 0;

    const handleAddToCart = () => {
        window.dispatchEvent(
            new CustomEvent('react:add-to-cart', {
                detail: { product },
            }),
        );
    };

    return (
        <CustomerLayout auth={auth} navItems={navItems} title="">
            <article className="react-customer-product-detail">
                <div className="react-customer-product-detail__media">
                    <img src={product.featured_image} alt={product.name} />
                </div>

                <div className="react-customer-product-detail__content">
                    <p className="react-customer-product-detail__meta">
                        <span>{product.brand_name}</span>
                        <span>{product.category_name}</span>
                    </p>
                    <h1>{product.name}</h1>

                    <div className="react-customer-product-detail__price">
                        {hasDiscount && (
                            <span className="react-customer-product-detail__regular-price">
                                {currencyFormatter.format(product.price)}
                            </span>
                        )}
                        <span className="react-customer-product-detail__sale-price">
                            {currencyFormatter.format(product.sale_price ?? product.price)}
                        </span>
                        {hasDiscount && (
                            <span className="react-customer-product-detail__discount">
                                -{product.discount_percentage}%
                            </span>
                        )}
                    </div>

                    <dl className="react-customer-product-detail__facts">
                        <div>
                            <dt>Rating</dt>
                            <dd>{product.star ?? 0}/5</dd>
                        </div>
                        <div>
                            <dt>Stock</dt>
                            <dd>{isInStock ? `${product.inventory_qty} available` : 'Out of stock'}</dd>
                        </div>
                    </dl>

                    <p className="react-customer-product-detail__description">
                        {product.description}
                    </p>

                    <button
                        type="button"
                        className="react-customer-product-detail__cart-button"
                        disabled={!isInStock}
                        onClick={handleAddToCart}
                    >
                        {isInStock ? 'Add to cart' : 'Out of stock'}
                    </button>
                </div>
            </article>

            <section className="react-customer-related-products">
                <h2>Related products</h2>
                <ProductGrid
                    products={relatedProducts}
                    emptyMessage="No related products available."
                />
            </section>
        </CustomerLayout>
    );
}
