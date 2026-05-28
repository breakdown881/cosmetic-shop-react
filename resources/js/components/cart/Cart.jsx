import { useMemo, useState } from 'react';
import CartItem from './CartItem.jsx';

const currencyFormatter = new Intl.NumberFormat('vi-VN', {
    style: 'currency',
    currency: 'VND',
    maximumFractionDigits: 0,
});

export default function Cart({ items = [] }) {
    const [cartItems, setCartItems] = useState(items);

    const total = useMemo(
        () =>
            cartItems.reduce((sum, item) => {
                const price = Number(item.price ?? 0);
                const quantity = Number(item.quantity ?? item.qty ?? 1);

                return sum + price * quantity;
            }, 0),
        [cartItems],
    );

    const handleQuantityChange = (id, quantity) => {
        const safeQuantity = Math.max(1, quantity || 1);

        setCartItems((currentItems) =>
            currentItems.map((item) =>
                (item.id ?? item.rowId) === id
                    ? {
                          ...item,
                          quantity: safeQuantity,
                          qty: safeQuantity,
                      }
                    : item,
            ),
        );

        window.dispatchEvent(
            new CustomEvent('react:cart-quantity-change', {
                detail: {
                    id,
                    quantity: safeQuantity,
                },
            }),
        );
    };

    const handleRemove = (id) => {
        setCartItems((currentItems) => currentItems.filter((item) => (item.id ?? item.rowId) !== id));

        window.dispatchEvent(
            new CustomEvent('react:cart-remove', {
                detail: {
                    id,
                },
            }),
        );
    };

    if (!cartItems.length) {
        return <p className="react-empty-state">Giỏ hàng đang trống.</p>;
    }

    return (
        <section className="react-cart">
            {cartItems.map((item) => (
                <CartItem
                    key={item.id ?? item.rowId ?? item.name}
                    item={item}
                    onQuantityChange={handleQuantityChange}
                    onRemove={handleRemove}
                />
            ))}

            <div className="react-cart__total">
                Tổng cộng: <strong>{currencyFormatter.format(total)}</strong>
            </div>
        </section>
    );
}
