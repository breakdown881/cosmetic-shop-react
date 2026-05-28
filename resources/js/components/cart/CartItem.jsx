const currencyFormatter = new Intl.NumberFormat('vi-VN', {
    style: 'currency',
    currency: 'VND',
    maximumFractionDigits: 0,
});

export default function CartItem({ item, onQuantityChange, onRemove }) {
    const itemId = item.id ?? item.rowId;
    const price = Number(item.price ?? 0);
    const quantity = Number(item.quantity ?? item.qty ?? 1);
    const subtotal = price * quantity;

    const handleQuantityChange = (event) => {
        if (itemId && window.updateProductInCart) {
            window.updateProductInCart(event.target, itemId);
            return;
        }

        onQuantityChange(itemId, Number(event.target.value));
    };

    const handleRemove = () => {
        if (itemId && window.deleteProductInCart) {
            window.deleteProductInCart(itemId);
            return;
        }

        onRemove(itemId);
    };

    return (
        <div className="react-cart-item">
            {item.image && (
                <img className="react-cart-item__image" src={item.image} alt={item.name ?? 'Cart item'} />
            )}
            <a className="react-cart-item__name" href={item.url ?? '#'}>
                {item.name}
            </a>
            <span>{currencyFormatter.format(price)}</span>
            <input type="number" min="1" value={quantity} onChange={handleQuantityChange} />
            <strong>{currencyFormatter.format(subtotal)}</strong>
            <button type="button" onClick={handleRemove} aria-label="Xóa sản phẩm">
                ×
            </button>
        </div>
    );
}
