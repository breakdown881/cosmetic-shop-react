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
import Home from './pages/Home.jsx';
import ProductList from './pages/ProductList.jsx';
import { mountReactIslands } from './islands/mountReactIslands.jsx';

mountReactIslands({
    AlertMessages,
    Cart,
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
