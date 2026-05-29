import { useEffect, useMemo, useState } from 'react';
import NewsletterSignup from '../components/customer/NewsletterSignup.jsx';
import ProductGrid from '../components/product/ProductGrid.jsx';

const defaultSlides = [
    {
        title: 'Mỹ phẩm chính hãng cho làn da Việt',
        description: 'Ưu đãi chăm sóc da, trang điểm và dưỡng thể được chọn lọc mỗi tuần.',
        imageUrl: '/adm/images/slider1.jpg',
        ctaLabel: 'Mua ngay',
        ctaUrl: '#categories',
    },
    {
        title: 'Combo dưỡng da tiết kiệm',
        description: 'Khám phá các sản phẩm nổi bật theo từng danh mục chỉ trong một trang.',
        imageUrl: '/adm/images/slider_2.jpg',
        ctaLabel: 'Xem danh mục',
        ctaUrl: '#categories',
    },
    {
        title: 'Tỏa sáng cùng Goda Shop',
        description: 'Sản phẩm bán chạy, giá tốt và giao hàng nhanh cho khách hàng thân thiết.',
        imageUrl: '/adm/images/slider_3.jpg',
        ctaLabel: 'Khám phá ngay',
        ctaUrl: '#featured-products',
    },
];

const defaultNavItems = [
    { label: 'Trang chủ', href: '#home' },
    { label: 'Khuyến mãi', href: '#promotions' },
    { label: 'Danh mục', href: '#categories' },
    { label: 'Sản phẩm nổi bật', href: '#featured-products' },
];

const SkeletonSection = () => (
    <div className="react-home__skeleton" aria-label="Đang tải dữ liệu trang chủ">
        {Array.from({ length: 3 }).map((_, index) => (
            <span key={index} />
        ))}
    </div>
);

const EmptyState = ({ message }) => (
    <p className="react-empty-state react-home__empty">{message}</p>
);

const AdvertisementSlider = ({ slides = [] }) => {
    const [activeIndex, setActiveIndex] = useState(0);
    const hasMultipleSlides = slides.length > 1;
    const activeSlide = slides[activeIndex] ?? slides[0];

    useEffect(() => {
        if (!hasMultipleSlides) {
            return undefined;
        }

        const timerId = window.setInterval(() => {
            setActiveIndex((currentIndex) => (currentIndex + 1) % slides.length);
        }, 4500);

        return () => window.clearInterval(timerId);
    }, [hasMultipleSlides, slides.length]);

    if (!activeSlide) {
        return null;
    }

    const moveSlide = (direction) => {
        if (!hasMultipleSlides) {
            return;
        }

        setActiveIndex((currentIndex) => (
            (currentIndex + direction + slides.length) % slides.length
        ));
    };

    return (
        <section className="react-home__slider" id="promotions" aria-label="Slide quảng cáo">
            <div className="react-home__slide">
                <img src={activeSlide.imageUrl} alt={activeSlide.title} />
                <div className="react-home__slide-content">
                    <span className="react-home__eyebrow">Goda Beauty Deal</span>
                    <h1>{activeSlide.title}</h1>
                    <p>{activeSlide.description}</p>
                    {activeSlide.ctaUrl && (
                        <a className="react-home__primary-link" href={activeSlide.ctaUrl}>
                            {activeSlide.ctaLabel ?? 'Xem ngay'}
                        </a>
                    )}
                </div>
            </div>

            <div className="react-home__slider-controls">
                <button
                    type="button"
                    onClick={() => moveSlide(-1)}
                    disabled={!hasMultipleSlides}
                    aria-label="Slide trước"
                >
                    ‹
                </button>
                <div className="react-home__slider-dots" aria-label="Chọn slide">
                    {slides.map((slide, index) => (
                        <button
                            key={slide.title}
                            type="button"
                            className={index === activeIndex ? 'active' : ''}
                            onClick={() => setActiveIndex(index)}
                            aria-label={`Chọn ${slide.title}`}
                        />
                    ))}
                </div>
                <button
                    type="button"
                    onClick={() => moveSlide(1)}
                    disabled={!hasMultipleSlides}
                    aria-label="Slide sau"
                >
                    ›
                </button>
            </div>
        </section>
    );
};

const CategoryMenu = ({ categories = [] }) => (
    <aside className="react-home__sidebar" aria-label="Điều hướng danh mục">
        <div className="react-home__sidebar-card">
            <h2>Chuyển nhanh</h2>
            <nav>
                <a href="#home">Trang chủ</a>
                <a href="#promotions">Khuyến mãi</a>
                <a href="#categories">Tất cả danh mục</a>
                <a href="#featured-products">Sản phẩm nổi bật</a>
            </nav>
        </div>

        <div className="react-home__sidebar-card">
            <h2>Danh mục sản phẩm</h2>
            {categories.length ? (
                <nav>
                    {categories.map((category) => (
                        <a key={category.id} href={`#category-${category.id}`}>
                            {category.name}
                            <span>{category.productsCount ?? 0}</span>
                        </a>
                    ))}
                </nav>
            ) : (
                <EmptyState message="Chưa có danh mục để hiển thị." />
            )}
        </div>
    </aside>
);

const CategorySection = ({ section }) => (
    <section className="react-home__category-section" id={`category-${section.id}`}>
        <div className="react-home__section-heading">
            <div>
                <span className="react-home__eyebrow">Danh mục</span>
                <h2>{section.name}</h2>
            </div>
            <a href={section.url ?? '#'}>Xem tất cả</a>
        </div>

        <ProductGrid
            products={section.products ?? []}
            emptyMessage="Danh mục này chưa có sản phẩm nổi bật."
        />
    </section>
);

const PromotionSection = ({ promotions = [] }) => (
    <section className="react-home__promotions" aria-labelledby="home-promotions-title">
        <div className="react-home__section-heading">
            <div>
                <span className="react-home__eyebrow">Beauty deal</span>
                <h2 id="home-promotions-title">Voucher khuyen mai</h2>
            </div>
            <a href="/promotions">Xem tat ca</a>
        </div>

        {promotions.length ? (
            <div className="react-home__promotion-grid">
                {promotions.map((promotion) => (
                    <article className="react-home__promotion-card" key={promotion.code}>
                        <strong>{promotion.code}</strong>
                        <p>{promotion.description}</p>
                        <span>{promotion.label}</span>
                        {promotion.expires_at ? <small>Het han: {promotion.expires_at}</small> : null}
                    </article>
                ))}
            </div>
        ) : (
            <EmptyState message="No active promotions right now." />
        )}
    </section>
);

export default function Home({
    categories = [],
    categorySections = [],
    errorMessage = '',
    isLoading = false,
    navItems = defaultNavItems,
    promotions = [],
    slides = defaultSlides,
}) {
    const normalizedSlides = slides.length ? slides : defaultSlides;
    const featuredProducts = useMemo(
        () => categorySections.flatMap((section) => section.products ?? []).slice(0, 6),
        [categorySections],
    );

    if (errorMessage) {
        return (
            <main className="react-home" id="home">
                <div className="react-home__error" role="alert">
                    <h1>Không tải được trang chủ</h1>
                    <p>{errorMessage}</p>
                </div>
            </main>
        );
    }

    return (
        <main className="react-home" id="home">
            <header className="react-home__topbar">
                <a className="react-home__brand" href="#home">
                    <span>Goda</span> Shop
                </a>
                <nav aria-label="Menu chính">
                    {navItems.map((item) => (
                        <a key={item.href} href={item.href}>
                            {item.label}
                        </a>
                    ))}
                </nav>
            </header>

            <AdvertisementSlider slides={normalizedSlides} />

            <PromotionSection promotions={promotions} />

            <div className="react-home__layout">
                <CategoryMenu categories={categories} />

                <div className="react-home__content" id="categories">
                    {isLoading && !categorySections.length ? (
                        <SkeletonSection />
                    ) : categorySections.length ? (
                        <>
                            <section className="react-home__featured" id="featured-products">
                                <div className="react-home__section-heading">
                                    <div>
                                        <span className="react-home__eyebrow">Gợi ý hôm nay</span>
                                        <h2>Sản phẩm nổi bật</h2>
                                    </div>
                                </div>
                                <ProductGrid
                                    products={featuredProducts}
                                    emptyMessage="Chưa có sản phẩm nổi bật để hiển thị."
                                />
                            </section>

                            {categorySections.map((section) => (
                                <CategorySection key={section.id} section={section} />
                            ))}
                        </>
                    ) : (
                        <EmptyState message="Chưa có danh mục hoặc sản phẩm để hiển thị trên trang chủ." />
                    )}
                </div>
            </div>

            <section className="react-home__newsletter">
                <NewsletterSignup />
            </section>
        </main>
    );
}
