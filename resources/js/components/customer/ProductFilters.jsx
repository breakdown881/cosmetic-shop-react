const sortOptions = [
    { value: 'newest', label: 'Newest' },
    { value: 'price_asc', label: 'Price: low to high' },
    { value: 'price_desc', label: 'Price: high to low' },
    { value: 'featured', label: 'Featured' },
];

export default function ProductFilters({ filters = {}, filterOptions = {} }) {
    const categories = filterOptions.categories ?? [];
    const brands = filterOptions.brands ?? [];

    return (
        <form className="react-customer-filters" action="/products" method="get">
            <label>
                Search
                <input type="search" name="q" defaultValue={filters.q ?? ''} placeholder="Search cosmetics..." />
            </label>

            <label>
                Category
                <select name="category_id" defaultValue={String(filters.category_id ?? '')}>
                    <option value="">All categories</option>
                    {categories.map((category) => (
                        <option key={category.id} value={category.id}>
                            {category.name}
                        </option>
                    ))}
                </select>
            </label>

            <label>
                Brand
                <select name="brand_id" defaultValue={String(filters.brand_id ?? '')}>
                    <option value="">All brands</option>
                    {brands.map((brand) => (
                        <option key={brand.id} value={brand.id}>
                            {brand.name}
                        </option>
                    ))}
                </select>
            </label>

            <label>
                Sort
                <select name="sort" defaultValue={filters.sort ?? 'newest'}>
                    {sortOptions.map((option) => (
                        <option key={option.value} value={option.value}>
                            {option.label}
                        </option>
                    ))}
                </select>
            </label>

            <button type="submit">Apply filters</button>
        </form>
    );
}
