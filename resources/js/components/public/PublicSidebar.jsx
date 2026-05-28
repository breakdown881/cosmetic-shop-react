const defaultPriceRanges = [
    { id: 'filter-less-100', label: 'Giá dưới 100.000đ', value: '0-100000' },
    { id: 'filter-100-200', label: '100.000đ - 200.000đ', value: '100000-200000' },
    { id: 'filter-200-300', label: '200.000đ - 300.000đ', value: '200000-300000' },
    { id: 'filter-300-500', label: '300.000đ - 500.000đ', value: '300000-500000' },
    { id: 'filter-500-1000', label: '500.000đ - 1.000.000đ', value: '500000-1000000' },
    { id: 'filter-greater-1000', label: 'Giá trên 1.000.000đ', value: '1000000-greater' },
];

export default function PublicSidebar({
    allProductsUrl = '#',
    activeCategoryId = null,
    categories = [],
    currentPriceRange = '',
    labels = {},
    priceRanges = defaultPriceRanges,
}) {
    return (
        <aside className="col-md-3">
            <div className="inner-aside">
                <div className="category">
                    <h5>{labels.categoriesTitle ?? 'Danh mục sản phẩm'}</h5>
                    <ul>
                        <li className={!activeCategoryId ? 'active' : ''}>
                            <a href={allProductsUrl} title={labels.allProducts ?? 'Tất cả sản phẩm'} target="_self">
                                {labels.allProducts ?? 'Tất cả sản phẩm'}
                            </a>
                        </li>
                        {categories.map((category) => (
                            <li
                                key={category.id}
                                className={String(category.id) === String(activeCategoryId) ? 'active' : ''}
                            >
                                <a href={category.url} title={category.name} target="_self">
                                    {category.name}
                                </a>
                            </li>
                        ))}
                    </ul>
                </div>

                <div className="price-range">
                    <h5>{labels.priceRangeTitle ?? 'Khoảng giá'}</h5>
                    <ul>
                        {priceRanges.map((range) => (
                            <li key={range.value}>
                                <label htmlFor={range.id}>
                                    <input
                                        type="radio"
                                        id={range.id}
                                        name="filter-price"
                                        value={range.value}
                                        defaultChecked={currentPriceRange === range.value}
                                    />
                                    <i className="fa" />
                                    {range.label}
                                </label>
                            </li>
                        ))}
                    </ul>
                </div>
            </div>
        </aside>
    );
}
