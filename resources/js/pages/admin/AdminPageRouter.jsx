import { useEffect, useMemo, useState } from 'react';
import { get } from '../../services/apiClient.js';
import AdminApiResourceManager from '../../components/admin/AdminApiResourceManager.jsx';
import AdminDashboard from './AdminDashboardPage.jsx';
import AdminMediaManager from './AdminMediaPage.jsx';
import AdminNewsletterManager from './AdminNewsletterPage.jsx';
import AdminOrderManager from './AdminOrderPage.jsx';

const commonLabels = {
    add: 'Add',
    cancel: 'Cancel',
    delete: 'Delete',
    deleteConfirm: 'Delete this item?',
    edit: 'Edit',
    empty: 'No data.',
    management: 'Management',
    save: 'Save',
};

const yesNoOptions = [
    { value: 1, label: 'Yes' },
    { value: 0, label: 'No' },
];

const statusOptions = [
    { value: 1, label: 'Active' },
    { value: 0, label: 'Inactive' },
];

const roleOptions = [
    { value: 'MANAGER', label: 'MANAGER' },
    { value: 'ADMIN', label: 'ADMIN' },
    { value: 'STAFF', label: 'STAFF' },
];

const optionRows = (rows) => rows.map((row) => ({ value: row.id, label: row.name }));

export const normalizePath = (path) => path.replace(/\/+$/, '') || '/admin';

const canWriteSales = (role) => ['MANAGER', 'ADMIN'].includes(role);

const canManageCatalog = (role) => ['MANAGER', 'ADMIN'].includes(role);

const isActive = (currentPath, targetPath) => (
    currentPath === targetPath || currentPath.startsWith(`${targetPath}/`)
);

export const buildSidebarItems = (role, currentPath) => {
    const items = [
        { label: 'Overview', href: '/admin', icon: 'fas fa-fw fa-tachometer-alt', active: currentPath === '/admin' },
        {
            label: 'Orders',
            icon: 'fas fa-shopping-cart',
            open: isActive(currentPath, '/admin/orders'),
            children: [
                { label: 'List', href: '/admin/orders', active: currentPath === '/admin/orders' },
                { label: 'Add', href: '/admin/orders/create', active: currentPath === '/admin/orders/create' },
            ],
        },
    ];

    if (canManageCatalog(role)) {
        items.push(
            {
                label: 'Products',
                icon: 'fab fa-product-hunt',
                open: isActive(currentPath, '/admin/products'),
                children: [
                    { label: 'List', href: '/admin/products', active: currentPath === '/admin/products' },
                    { label: 'Add', href: '/admin/products/create', active: currentPath === '/admin/products/create' },
                    { label: 'Comments', href: '/admin/comments', active: currentPath === '/admin/comments' },
                ],
            },
            {
                label: 'Media',
                icon: 'far fa-image',
                open: isActive(currentPath, '/admin/images'),
                children: [{ label: 'List', href: '/admin/images', active: currentPath === '/admin/images' }],
            },
            {
                label: 'Brands',
                icon: 'fas fa-folder',
                open: isActive(currentPath, '/admin/brands'),
                children: [
                    { label: 'List', href: '/admin/brands', active: currentPath === '/admin/brands' },
                    { label: 'Add', href: '/admin/brands/create', active: currentPath === '/admin/brands/create' },
                ],
            },
            {
                label: 'Categories',
                icon: 'fas fa-folder',
                open: isActive(currentPath, '/admin/categories'),
                children: [
                    { label: 'List', href: '/admin/categories', active: currentPath === '/admin/categories' },
                    { label: 'Add', href: '/admin/categories/create', active: currentPath === '/admin/categories/create' },
                ],
            },
            {
                label: 'Newsletter',
                icon: 'fas fa-file-alt',
                open: isActive(currentPath, '/admin/newsletters'),
                children: [{ label: 'List', href: '/admin/newsletters', active: currentPath === '/admin/newsletters' }],
            },
        );
    }

    items.push(
        {
            label: 'Customers',
            icon: 'fas fa-user-alt',
            open: isActive(currentPath, '/admin/customers'),
            children: [{ label: 'List', href: '/admin/customers', active: currentPath === '/admin/customers' }],
        },
        {
            label: 'Discounts',
            icon: 'fas fa-percentage',
            open: isActive(currentPath, '/admin/discounts'),
            children: [
                { label: 'List', href: '/admin/discounts', active: currentPath === '/admin/discounts' },
                ...(canWriteSales(role) ? [{ label: 'Add', href: '/admin/discounts/create', active: currentPath === '/admin/discounts/create' }] : []),
            ],
        },
        {
            label: 'Fee Ships',
            icon: 'fas fa-shipping-fast',
            open: isActive(currentPath, '/admin/feeships'),
            children: [
                { label: 'List', href: '/admin/feeships', active: currentPath === '/admin/feeships' },
                ...(canWriteSales(role) ? [{ label: 'Add', href: '/admin/feeships/create', active: currentPath === '/admin/feeships/create' }] : []),
            ],
        },
    );

    if (role === 'MANAGER') {
        items.push(
            {
                label: 'Staffs',
                icon: 'fas fa-users',
                open: isActive(currentPath, '/admin/staffs'),
                children: [
                    { label: 'List', href: '/admin/staffs', active: currentPath === '/admin/staffs' },
                    { label: 'Add', href: '/admin/staffs/create', active: currentPath === '/admin/staffs/create' },
                ],
            },
            {
                label: 'Roles',
                icon: 'fas fa-user-shield',
                open: isActive(currentPath, '/admin/roles'),
                children: [
                    { label: 'List', href: '/admin/roles', active: currentPath === '/admin/roles' },
                    { label: 'Add', href: '/admin/roles/create', active: currentPath === '/admin/roles/create' },
                ],
            },
        );
    }

    return items;
};

const baseResource = (overrides) => ({
    breadcrumbs: [{ href: '/admin', label: 'Management' }, { active: true, label: overrides.title }],
    labels: commonLabels,
    ...overrides,
});

const resourceConfigs = (role) => ({
    brands: baseResource({
        apiUrl: '/admin/api/brands',
        columns: [
            { key: 'name', label: 'Name' },
            { key: 'status', label: 'Status', type: 'boolean' },
            { key: 'created_at', label: 'Created at' },
            { key: 'updated_at', label: 'Updated at' },
        ],
        fields: [
            { name: 'name', label: 'Name', type: 'text', required: true },
            { name: 'status', label: 'Status', type: 'select', required: true, defaultValue: 1, options: statusOptions },
        ],
        resourceName: 'brands',
        title: 'Brands',
    }),
    categories: baseResource({
        apiUrl: '/admin/api/categories',
        columns: [
            { key: 'name', label: 'Name' },
            { key: 'parent_id', label: 'Parent' },
            { key: 'status', label: 'Status', type: 'boolean' },
            { key: 'created_at', label: 'Created at' },
            { key: 'updated_at', label: 'Updated at' },
        ],
        resourceName: 'categories',
        title: 'Categories',
    }),
    comments: baseResource({
        apiUrl: '/admin/api/comments',
        canCreate: false,
        canDelete: false,
        columns: [
            { key: 'product_name', label: 'Product' },
            { key: 'fullname', label: 'Customer' },
            { key: 'email', label: 'Email' },
            { key: 'star', label: 'Star' },
            { key: 'active', label: 'Active', type: 'boolean' },
        ],
        fields: [{ name: 'active', label: 'Active', type: 'select', required: true, options: yesNoOptions }],
        resourceName: 'comments',
        title: 'Comments',
    }),
    customers: baseResource({
        apiUrl: '/admin/api/customers',
        canCreate: false,
        columns: [
            { key: 'name', label: 'Name' },
            { key: 'email', label: 'Email' },
            { key: 'email_verified_at', label: 'Email verified at' },
            { key: 'created_at', label: 'Created at' },
        ],
        fields: [
            { name: 'name', label: 'Name', type: 'text', required: true },
            { name: 'email', label: 'Email', type: 'email', required: true },
        ],
        resourceName: 'customers',
        title: 'Customers',
    }),
    discounts: baseResource({
        apiUrl: '/admin/api/discounts',
        canCreate: canWriteSales(role),
        canDelete: canWriteSales(role),
        canEdit: canWriteSales(role),
        columns: [
            { key: 'code', label: 'Code' },
            { key: 'description', label: 'Description' },
            { key: 'is_fixed', label: 'Fixed', type: 'boolean' },
            { key: 'discount_amount', label: 'Amount' },
            { key: 'starts_at', label: 'Starts at' },
            { key: 'expires_at', label: 'Expires at' },
        ],
        fields: [
            { name: 'code', label: 'Code', type: 'text', required: true },
            { name: 'description', label: 'Description', type: 'text', required: true },
            { name: 'is_fixed', label: 'Fixed', type: 'select', required: true, options: yesNoOptions },
            { name: 'discount_amount', label: 'Amount', type: 'number', required: true },
            { name: 'starts_at', label: 'Starts at', type: 'text' },
            { name: 'expires_at', label: 'Expires at', type: 'text' },
        ],
        resourceName: 'discounts',
        title: 'Discounts',
    }),
    feeships: baseResource({
        apiUrl: '/admin/api/feeships',
        canCreate: canWriteSales(role),
        canDelete: canWriteSales(role),
        canEdit: canWriteSales(role),
        columns: [
            { key: 'province_name', label: 'Province' },
            { key: 'province_type', label: 'Province type' },
            { key: 'price', label: 'Price' },
            { key: 'created_at', label: 'Created at' },
        ],
        fields: [
            { name: 'province_name', label: 'Province', type: 'text', required: true },
            { name: 'province_type', label: 'Province type', type: 'text', required: true },
            { name: 'price', label: 'Price', type: 'number', required: true },
        ],
        resourceName: 'feeships',
        title: 'Fee Ships',
    }),
    roles: baseResource({
        apiUrl: '/admin/api/roles',
        columns: [
            { key: 'name', label: 'Name' },
            { key: 'created_at', label: 'Created at' },
        ],
        fields: [{ name: 'name', label: 'Name', type: 'select', required: true, options: roleOptions }],
        resourceName: 'roles',
        title: 'Roles',
    }),
    staffs: baseResource({
        apiUrl: '/admin/api/staffs',
        columns: [
            { key: 'name', label: 'Name' },
            { key: 'email', label: 'Email' },
            { key: 'role', label: 'Role' },
            { key: 'is_active', label: 'Status', type: 'boolean' },
            { key: 'created_at', label: 'Created at' },
        ],
        fields: [
            { name: 'name', label: 'Name', type: 'text', required: true },
            { name: 'email', label: 'Email', type: 'email', required: true },
            { name: 'password', label: 'Password', type: 'password', createOnlyRequired: true },
            { name: 'password_confirmation', label: 'Confirm password', type: 'password', createOnlyRequired: true },
            { name: 'phone_number', label: 'Phone', type: 'text' },
            { name: 'address', label: 'Address', type: 'text' },
            { name: 'role', label: 'Role', type: 'select', required: true, options: roleOptions },
            { name: 'is_active', label: 'Status', type: 'select', required: true, defaultValue: 1, options: statusOptions },
        ],
        resourceName: 'staffs',
        title: 'Staffs',
    }),
});

function DashboardPage() {
    const [payload, setPayload] = useState(null);
    const [error, setError] = useState('');

    useEffect(() => {
        let cancelled = false;

        get('/admin/api/dashboard')
            .then((response) => {
                if (!cancelled) {
                    setPayload(response);
                }
            })
            .catch((requestError) => {
                if (!cancelled) {
                    setError(requestError.response?.data?.message ?? 'Could not load dashboard.');
                }
            });

        return () => {
            cancelled = true;
        };
    }, []);

    if (error) {
        return <div className="container-fluid"><div className="alert alert-danger">{error}</div></div>;
    }

    if (!payload) {
        return <div className="container-fluid"><p>Loading...</p></div>;
    }

    return <AdminDashboard {...payload} />;
}

function ProductPage() {
    const [options, setOptions] = useState({ brands: [], categories: [] });
    const [error, setError] = useState('');

    useEffect(() => {
        let cancelled = false;

        Promise.all([get('/admin/api/brands'), get('/admin/api/categories')])
            .then(([brands, categories]) => {
                if (!cancelled) {
                    setOptions({
                        brands: optionRows(brands.data ?? []),
                        categories: optionRows(categories.data ?? []),
                    });
                }
            })
            .catch((requestError) => {
                if (!cancelled) {
                    setError(requestError.response?.data?.message ?? 'Could not load product options.');
                }
            });

        return () => {
            cancelled = true;
        };
    }, []);

    if (error) {
        return <div className="container-fluid"><div className="alert alert-danger">{error}</div></div>;
    }

    return (
        <AdminApiResourceManager
            {...baseResource({
                apiUrl: '/admin/api/products',
                columns: [
                    { key: 'code', label: 'Code' },
                    { key: 'name', label: 'Name' },
                    { key: 'brand_id', label: 'Brand' },
                    { key: 'category_id', label: 'Category' },
                    { key: 'price', label: 'Price' },
                    { key: 'inventory_qty', label: 'Inventory' },
                    { key: 'status', label: 'Status', type: 'boolean' },
                ],
                fields: [
                    { name: 'code', label: 'Code', type: 'text', required: true },
                    { name: 'name', label: 'Name', type: 'text', required: true },
                    { name: 'brand_id', label: 'Brand', type: 'select', required: true, options: options.brands },
                    { name: 'category_id', label: 'Category', type: 'select', required: true, options: options.categories },
                    { name: 'price', label: 'Price', type: 'number', required: true, defaultValue: 0 },
                    { name: 'discount_percentage', label: 'Discount %', type: 'number', required: true, defaultValue: 0 },
                    { name: 'discount_from_date', label: 'Discount from', type: 'date', required: true },
                    { name: 'discount_to_date', label: 'Discount to', type: 'date', required: true },
                    { name: 'media_id', label: 'Media ID', type: 'number', required: true, defaultValue: 1 },
                    { name: 'inventory_qty', label: 'Inventory', type: 'number', required: true, defaultValue: 0 },
                    { name: 'description', label: 'Description', type: 'text', required: true },
                    { name: 'star', label: 'Star', type: 'number', required: true, defaultValue: 0 },
                    { name: 'featured', label: 'Featured', type: 'select', required: true, defaultValue: 0, options: yesNoOptions },
                    { name: 'status', label: 'Status', type: 'select', required: true, defaultValue: 1, options: statusOptions },
                ],
                resourceName: 'products',
                title: 'Products',
            })}
        />
    );
}

function CategoryPage() {
    const [categories, setCategories] = useState([]);

    useEffect(() => {
        let cancelled = false;

        get('/admin/api/categories').then((response) => {
            if (!cancelled) {
                setCategories(optionRows(response.data ?? []));
            }
        });

        return () => {
            cancelled = true;
        };
    }, []);

    return (
        <AdminApiResourceManager
            {...resourceConfigs('MANAGER').categories}
            fields={[
                { name: 'name', label: 'Name', type: 'text', required: true },
                { name: 'parent_id', label: 'Parent', type: 'select', options: categories },
                { name: 'status', label: 'Status', type: 'select', required: true, defaultValue: 1, options: statusOptions },
            ]}
        />
    );
}

function OrderPage() {
    const [options, setOptions] = useState(null);
    const [error, setError] = useState('');

    useEffect(() => {
        let cancelled = false;

        get('/admin/api/order-options')
            .then((response) => {
                if (!cancelled) {
                    setOptions(response);
                }
            })
            .catch((requestError) => {
                if (!cancelled) {
                    setError(requestError.response?.data?.message ?? 'Could not load order options.');
                }
            });

        return () => {
            cancelled = true;
        };
    }, []);

    if (error) {
        return <div className="container-fluid"><div className="alert alert-danger">{error}</div></div>;
    }

    if (!options) {
        return <div className="container-fluid"><p>Loading...</p></div>;
    }

    return (
        <AdminOrderManager
            apiUrl="/admin/api/orders"
            breadcrumbs={[{ href: '/admin', label: 'Management' }, { active: true, label: 'Orders' }]}
            customers={options.customers ?? []}
            discounts={options.discounts ?? []}
            feeships={options.feeships ?? []}
            paymentMethods={options.paymentMethods ?? {}}
            products={options.products ?? []}
            statusOptions={options.statusOptions ?? []}
            title="Orders"
        />
    );
}

function ProductCommentsPage({ productId }) {
    return (
        <AdminApiResourceManager
            {...resourceConfigs('MANAGER').comments}
            apiUrl={`/admin/api/products/${productId}/comments`}
            breadcrumbs={[
                { href: '/admin', label: 'Management' },
                { href: '/admin/products', label: 'Products' },
                { active: true, label: 'Comments' },
            ]}
        />
    );
}

function NotFoundPage() {
    return (
        <div className="container-fluid">
            <h1 className="h4">Page not found</h1>
        </div>
    );
}

export default function AdminPageRouter({ path, role }) {
    const configs = useMemo(() => resourceConfigs(role), [role]);
    const productComments = path.match(/^\/admin\/products\/(\d+)\/comments$/);

    if (path === '/admin') {
        return <DashboardPage />;
    }

    if (path === '/admin/orders' || path.startsWith('/admin/orders/')) {
        return <OrderPage />;
    }

    if (productComments) {
        return <ProductCommentsPage productId={productComments[1]} />;
    }

    if (path === '/admin/products' || path.startsWith('/admin/products/')) {
        return <ProductPage />;
    }

    if (path === '/admin/categories' || path.startsWith('/admin/categories/')) {
        return <CategoryPage />;
    }

    if (path === '/admin/images') {
        return <AdminMediaManager apiUrl="/admin/api/media" labels={{ delete: 'Delete', image: 'Image', upload: 'Upload', uploadImage: 'Upload image' }} />;
    }

    if (path === '/admin/newsletters') {
        return <AdminNewsletterManager apiUrl="/admin/api/newsletters" sendUrl="/admin/api/newsletters/send" labels={{ body: 'Body', email: 'Email', send: 'Send', subject: 'Subject' }} />;
    }

    const resourceKey = Object.keys(configs).find((key) => path === `/admin/${key}` || path.startsWith(`/admin/${key}/`));

    if (resourceKey) {
        return <AdminApiResourceManager {...configs[resourceKey]} />;
    }

    return <NotFoundPage />;
}

