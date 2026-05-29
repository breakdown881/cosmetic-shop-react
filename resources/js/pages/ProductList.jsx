import { useMemo, useState } from 'react';
import ProductGrid from '../components/product/ProductGrid.jsx';
import PaginationControls, { lastPageFor, paginateRows } from '../components/common/PaginationControls.jsx';

const normalizeText = (value) => String(value ?? '').toLowerCase();
const PER_PAGE = 12;

export default function ProductList({ products = [], categories = [], brands = [] }) {
    const [search, setSearch] = useState('');
    const [categoryId, setCategoryId] = useState('');
    const [brandId, setBrandId] = useState('');
    const [currentPage, setCurrentPage] = useState(1);

    const filteredProducts = useMemo(() => {
        const normalizedSearch = normalizeText(search);

        return products.filter((product) => {
            const matchesSearch = !normalizedSearch || normalizeText(product.name).includes(normalizedSearch);
            const matchesCategory = !categoryId || String(product.category_id ?? product.categoryId) === categoryId;
            const matchesBrand = !brandId || String(product.brand_id ?? product.brandId) === brandId;

            return matchesSearch && matchesCategory && matchesBrand;
        });
    }, [brandId, categoryId, products, search]);

    return (
        <section className="react-product-list">
            <div className="react-product-list__filters">
                <input
                    type="search"
                    value={search}
                    onChange={(event) => setSearch(event.target.value)}
                    placeholder="Tìm sản phẩm..."
                />

                {!!categories.length && (
                    <select value={categoryId} onChange={(event) => setCategoryId(event.target.value)}>
                        <option value="">Tất cả danh mục</option>
                        {categories.map((category) => (
                            <option key={category.id} value={category.id}>
                                {category.name}
                            </option>
                        ))}
                    </select>
                )}

                {!!brands.length && (
                    <select value={brandId} onChange={(event) => setBrandId(event.target.value)}>
                        <option value="">Tất cả thương hiệu</option>
                        {brands.map((brand) => (
                            <option key={brand.id} value={brand.id}>
                                {brand.name}
                            </option>
                        ))}
                    </select>
                )}
            </div>

            <ProductGrid products={paginateRows(filteredProducts, currentPage, PER_PAGE)} />
            <PaginationControls
                currentPage={currentPage}
                lastPage={lastPageFor(filteredProducts, PER_PAGE)}
                onPageChange={setCurrentPage}
            />
        </section>
    );
}
