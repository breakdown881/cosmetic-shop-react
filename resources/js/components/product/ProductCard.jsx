const currencyFormatter = new Intl.NumberFormat('vi-VN', {
    style: 'currency',
    currency: 'VND',
    maximumFractionDigits: 0,
});

const resolveProductImage = (product) => {
    const image = product.featured_image ?? product.featuredImage ?? product.image;

    if (!image) {
        return '';
    }

    if (image.startsWith('http') || image.startsWith('/')) {
        return image;
    }

    return `/images/${image}`;
};

const resolveProductPrice = (product) => ({
    regularPrice: Number(product.price ?? 0),
    salePrice: Number(product.sale_price ?? product.salePrice ?? product.price ?? 0),
});

export default function ProductCard({ product, onAddToCart }) {
    const { regularPrice, salePrice } = resolveProductPrice(product);
    const productUrl = product.url ?? product.href ?? '#';
    const imageUrl = resolveProductImage(product);
    const hasDiscount = regularPrice > 0 && salePrice > 0 && regularPrice !== salePrice;
    const explicitDiscountPercentage = Number(product.discount_percentage ?? product.discountPercentage ?? 0);
    const discountPercentage = explicitDiscountPercentage > 0
        ? explicitDiscountPercentage
        : (hasDiscount ? Math.round(((regularPrice - salePrice) / regularPrice) * 100) : 0);

    const handleAddToCart = () => {
        onAddToCart?.(product);

        window.dispatchEvent(
            new CustomEvent('react:add-to-cart', {
                detail: {
                    product,
                },
            }),
        );
    };

    return (
        <article className="react-product-card">
            {hasDiscount && discountPercentage > 0 && (
                <span className="react-product-card__sale-badge">
                    -{discountPercentage}%
                </span>
            )}

            {imageUrl && (
                <a className="react-product-card__image" href={productUrl}>
                    <img src={imageUrl} alt={product.name ?? 'Product'} loading="lazy" />
                </a>
            )}

            <div className="react-product-card__body">
                <h3 className="react-product-card__name">
                    <a href={productUrl}>{product.name}</a>
                </h3>

                <div className="react-product-card__price">
                    {hasDiscount && (
                        <span className="react-product-card__regular-price">
                            {currencyFormatter.format(regularPrice)}
                        </span>
                    )}
                    <span className="react-product-card__sale-price">
                        {currencyFormatter.format(salePrice)}
                    </span>
                </div>

                <div className="react-product-card__actions">
                    <button
                        type="button"
                        className="btn btn-outline-inverse buy"
                        product-id={product.id}
                        onClick={handleAddToCart}
                    >
                        Thêm vào giỏ <i className="fa fa-shopping-cart" aria-hidden="true" />
                    </button>
                    <a className="btn btn-outline-inverse" href={productUrl}>
                        Xem chi tiết <i className="fa fa-eye" aria-hidden="true" />
                    </a>
                </div>
            </div>
        </article>
    );
}
