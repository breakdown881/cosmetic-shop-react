import './bootstrap';

import Cart from './components/cart/Cart.jsx';
import AlertMessages from './components/common/AlertMessages.jsx';
import ProductCard from './components/product/ProductCard.jsx';
import ProductGrid from './components/product/ProductGrid.jsx';
import PublicAuthModals from './components/public/PublicAuthModals.jsx';
import PublicCartModal from './components/public/PublicCartModal.jsx';
import PublicFooter from './components/public/PublicFooter.jsx';
import PublicHeader from './components/public/PublicHeader.jsx';
import PublicSidebar from './components/public/PublicSidebar.jsx';
import PublicWelcomePage from './components/public/PublicWelcomePage.jsx';
import ShippingAddressForm from './components/public/ShippingAddressForm.jsx';
import CustomerAccountPage from './pages/customer/CustomerAccountPage.jsx';
import CustomerCartPage from './pages/customer/CustomerCartPage.jsx';
import CustomerCheckoutPage from './pages/customer/CustomerCheckoutPage.jsx';
import CustomerLoginPage from './pages/customer/CustomerLoginPage.jsx';
import CustomerOrderDetailPage from './pages/customer/CustomerOrderDetailPage.jsx';
import CustomerOrderHistoryPage from './pages/customer/CustomerOrderHistoryPage.jsx';
import CustomerPlaceholderPage from './pages/customer/CustomerPlaceholderPage.jsx';
import CustomerProductIndex from './pages/customer/CustomerProductIndex.jsx';
import CustomerProductShow from './pages/customer/CustomerProductShow.jsx';
import CustomerRegisterPage from './pages/customer/CustomerRegisterPage.jsx';
import CustomerWishlistPage from './pages/customer/CustomerWishlistPage.jsx';
import Home from './pages/Home.jsx';
import ProductList from './pages/ProductList.jsx';
import { mountReactIslands } from './islands/mountReactIslands.jsx';

mountReactIslands({
    AlertMessages,
    Cart,
    CustomerAccountPage,
    CustomerCartPage,
    CustomerCheckoutPage,
    CustomerLoginPage,
    CustomerOrderDetailPage,
    CustomerOrderHistoryPage,
    CustomerPlaceholderPage,
    CustomerProductIndex,
    CustomerProductShow,
    CustomerRegisterPage,
    CustomerWishlistPage,
    Home,
    ProductCard,
    ProductGrid,
    ProductList,
    PublicAuthModals,
    PublicCartModal,
    PublicFooter,
    PublicHeader,
    PublicSidebar,
    PublicWelcomePage,
    ShippingAddressForm,
});
