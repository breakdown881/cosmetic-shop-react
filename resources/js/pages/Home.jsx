import { useEffect, useMemo, useState } from 'react';
import CustomerHeader from '../components/customer/CustomerHeader.jsx';
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
    { label: 'Trang chủ', href: '/' },
    { label: 'Tất cả sản phẩm', href: '/products' },
    { label: 'Khuyến mãi', href: '/promotions' },
    { label: 'Giỏ hàng', href: '/cart' },
    { label: 'Đơn hàng', href: '/orders' },
    { label: 'Tài khoản', href: '/account' },
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

    useEffect(() => {
        if (!hasMultipleSlides) {
            return undefined;
        }

        const timerId = window.setInterval(() => {
            setActiveIndex((currentIndex) => (currentIndex + 1) % slides.length);
        }, 4500);

        return () => window.clearInterval(timerId);
    }, [hasMultipleSlides, slides.length]);

    if (!slides.length) {
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
            <div className="react-home__slider-viewport">
                <div
                    className="react-home__slide-track"
                    style={{ transform: `translateX(-${activeIndex * 100}%)` }}
                >
                    {slides.map((slide) => (
                        <div className="react-home__slide" key={slide.title}>
                            <img src={slide.imageUrl} alt={slide.title} />
                            <div className="react-home__slide-content">
                                <span className="react-home__eyebrow">Goda Beauty Deal</span>
                                <h1>{slide.title}</h1>
                                <p>{slide.description}</p>
                                <div className="react-home__slide-actions">
                                    {slide.ctaUrl && (
                                        <a className="react-home__primary-link" href={slide.ctaUrl}>
                                            {slide.ctaLabel ?? 'Xem ngay'}
                                        </a>
                                    )}
                                    <a className="react-home__secondary-link" href="/products">
                                        Tất cả sản phẩm
                                    </a>
                                </div>
                            </div>
                        </div>
                    ))}
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
                <a href="/products">Tất cả sản phẩm</a>
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
                <h2 id="home-promotions-title">Voucher khuyến mãi</h2>
            </div>
            <a href="/promotions">Xem tất cả</a>
        </div>

        {promotions.length ? (
            <div className="react-home__promotion-grid">
                {promotions.map((promotion) => (
                    <article className="react-home__promotion-card" key={promotion.code}>
                        <div className="react-home__promotion-card-header">
                            <span>Voucher</span>
                            <strong>{promotion.code}</strong>
                        </div>
                        <p>{promotion.description}</p>
                        <div className="react-home__promotion-card-footer">
                            <span>{promotion.label}</span>
                            {promotion.expires_at ? <small>Hết hạn: {promotion.expires_at}</small> : null}
                        </div>
                    </article>
                ))}
            </div>
        ) : (
            <div className="react-home__promotion-empty">
                <span>Đang cập nhật ưu đãi</span>
                <p>Voucher mới sẽ được mở lại sớm. Bạn vẫn có thể xem các sản phẩm đang có giá tốt hôm nay.</p>
            </div>
        )}
    </section>
);

const HomeStats = ({ categoriesCount, featuredCount, productsCount }) => (
    <section className="react-home__stats" aria-label="Tổng quan cửa hàng">
        <div>
            <strong>{productsCount}</strong>
            <span>Sản phẩm có sẵn</span>
        </div>
        <div>
            <strong>{categoriesCount}</strong>
            <span>Danh mục chăm sóc</span>
        </div>
        <div>
            <strong>{featuredCount}</strong>
            <span>Gợi ý nổi bật</span>
        </div>
    </section>
);

export default function Home({
    auth = null,
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
    const productsCount = useMemo(
        () => categories.reduce((total, category) => total + Number(category.productsCount ?? 0), 0),
        [categories],
    );

    if (errorMessage) {
        return (
            <div className="react-home" id="home">
                <CustomerHeader auth={auth} navItems={navItems} />
                <div className="react-home__error" role="alert">
                    <h1>Không tải được trang chủ</h1>
                    <p>{errorMessage}</p>
                </div>
            </div>
        );
    }

    return (
        <div className="react-home" id="home">
            <CustomerHeader auth={auth} navItems={navItems} />

            <main>
                <AdvertisementSlider slides={normalizedSlides} />

                <HomeStats
                    categoriesCount={categories.length}
                    featuredCount={featuredProducts.length}
                    productsCount={productsCount}
                />

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

                <section className="react-home__newsletter" aria-labelledby="home-newsletter-title">
                    <div className="react-home__newsletter-copy">
                        <span className="react-home__eyebrow">Goda member</span>
                        <h2 id="home-newsletter-title">Nhận ưu đãi chăm sóc da mỗi tuần</h2>
                        <p>Nhận mã giảm giá mới, gợi ý sản phẩm phù hợp và thông tin restock trong một email gọn nhẹ.</p>
                    </div>
                    <NewsletterSignup
                        buttonLabel="Đăng ký"
                        errorMessage="Chưa đăng ký được. Bạn thử lại sau nhé."
                        label="Email của bạn"
                        placeholder="email@example.com"
                        successMessage="Đã đăng ký nhận ưu đãi."
                    />
                </section>
            </main>
        </div>
    );
}
