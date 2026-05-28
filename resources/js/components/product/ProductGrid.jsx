import ProductCard from './ProductCard.jsx';

export default function ProductGrid({ products = [], emptyMessage = 'Chưa có sản phẩm để hiển thị.' }) {
    if (!products.length) {
        return <p className="react-empty-state">{emptyMessage}</p>;
    }

    return (
        <div className="react-product-grid">
            {products.map((product) => (
                <ProductCard key={product.id ?? product.slug ?? product.name} product={product} />
            ))}
        </div>
    );
}
