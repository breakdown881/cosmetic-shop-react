import CustomerLayout from '../../components/customer/CustomerLayout.jsx';
import ProductGrid from '../../components/product/ProductGrid.jsx';

const currencyFormatter = new Intl.NumberFormat('vi-VN', {
    style: 'currency',
    currency: 'VND',
    maximumFractionDigits: 0,
});

export default function CustomerProductShow({
    auth = null,
    csrfToken = '',
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
    const reviews = product.reviews ?? [];
    const reviewSummary = product.reviewSummary ?? { average: product.star ?? 0, count: reviews.length };
    const wishlist = product.wishlist ?? { isWishlisted: false, storeUrl: '/wishlist/items', removeUrl: `/wishlist/items/${product.id}` };

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
                            <dd>{reviewSummary.average || product.star || 0}/5</dd>
                        </div>
                        <div>
                            <dt>Reviews</dt>
                            <dd>{reviewSummary.count ?? 0}</dd>
                        </div>
                        <div>
                            <dt>Stock</dt>
                            <dd>{isInStock ? `${product.inventory_qty} available` : 'Out of stock'}</dd>
                        </div>
                    </dl>

                    <p className="react-customer-product-detail__description">
                        {product.description}
                    </p>

                    <div className="react-customer-product-detail__actions">
                        <button
                            type="button"
                            className="react-customer-product-detail__cart-button"
                            disabled={!isInStock}
                            onClick={handleAddToCart}
                        >
                            {isInStock ? 'Add to cart' : 'Out of stock'}
                        </button>

                        {auth?.check ? (
                            <form method="post" action={wishlist.isWishlisted ? wishlist.removeUrl : wishlist.storeUrl}>
                                <input type="hidden" name="_token" value={csrfToken} />
                                {wishlist.isWishlisted ? (
                                    <input type="hidden" name="_method" value="DELETE" />
                                ) : (
                                    <input type="hidden" name="product_id" value={product.id} />
                                )}
                                <button type="submit" className="react-customer-product-detail__wishlist-button">
                                    {wishlist.isWishlisted ? 'Remove from wishlist' : 'Add to wishlist'}
                                </button>
                            </form>
                        ) : (
                            <a className="react-customer-product-detail__wishlist-link" href={auth?.loginUrl ?? '/login'}>
                                Sign in to add wishlist
                            </a>
                        )}
                    </div>
                </div>
            </article>

            <section className="react-customer-reviews">
                <div className="react-customer-reviews__header">
                    <h2>Customer reviews</h2>
                    <span>{reviewSummary.count ?? 0} approved reviews</span>
                </div>

                {!auth?.check && (
                    <div className="react-customer-reviews__login">
                        <p>Sign in to review or save this product.</p>
                        <a href={auth?.loginUrl ?? '/login'}>Sign in to review</a>
                    </div>
                )}

                {auth?.check && product.canReview && (
                    <form className="react-customer-reviews__form" method="post" action={product.reviewStoreUrl ?? `/products/${product.id}/reviews`}>
                        <input type="hidden" name="_token" value={csrfToken} />
                        <label>
                            Rating
                            <select name="star" defaultValue="5" required>
                                <option value="5">5</option>
                                <option value="4">4</option>
                                <option value="3">3</option>
                                <option value="2">2</option>
                                <option value="1">1</option>
                            </select>
                        </label>
                        <label>
                            Review
                            <textarea name="description" rows="4" required />
                        </label>
                        <button type="submit">Submit review</button>
                    </form>
                )}

                {auth?.check && product.hasReviewed && (
                    <p className="react-customer-reviews__notice">Your review is submitted or already approved.</p>
                )}

                {reviews.length ? (
                    <div className="react-customer-reviews__list">
                        {reviews.map((review) => (
                            <article className="react-customer-reviews__item" key={review.id}>
                                <strong>{review.fullname}</strong>
                                <span>{review.star}/5</span>
                                <p>{review.description}</p>
                            </article>
                        ))}
                    </div>
                ) : (
                    <p className="react-empty-state">No approved reviews yet.</p>
                )}
            </section>

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
