import { existsSync, readdirSync, readFileSync } from 'node:fs';
import { resolve } from 'node:path';
import { describe, expect, it } from 'vitest';

const root = resolve(__dirname);
const path = (file) => resolve(root, file);
const read = (file) => readFileSync(path(file), 'utf8');

const adminPageFiles = [
    'AdminDashboardPage.jsx',
    'AdminLoginPage.jsx',
    'AdminMediaPage.jsx',
    'AdminNewsletterPage.jsx',
    'AdminOrderPage.jsx',
    'AdminPageRouter.jsx',
    'AdminSpaApp.jsx',
];

const pageLevelFilesThatShouldNotLiveInComponents = [
    'AdminDashboard.jsx',
    'AdminLoginPage.jsx',
    'AdminMediaManager.jsx',
    'AdminNewsletterManager.jsx',
    'AdminOrderManager.jsx',
    'AdminSpaApp.jsx',
];

describe('admin FE page/component boundaries', () => {
    it('stores page-level admin UI under resources/js/pages/admin', () => {
        adminPageFiles.forEach((file) => {
            expect(existsSync(path(`pages/admin/${file}`)), `${file} should be an admin page`).toBe(true);
        });
    });

    it('keeps resources/js/components/admin for reusable components only', () => {
        const componentFiles = readdirSync(path('components/admin'));

        pageLevelFilesThatShouldNotLiveInComponents.forEach((file) => {
            expect(componentFiles, `${file} should move out of components/admin`).not.toContain(file);
        });

        expect(componentFiles).toContain('AdminLayout.jsx');
        expect(componentFiles).toContain('AdminSidebar.jsx');
        expect(componentFiles).toContain('AdminTopNav.jsx');
        expect(componentFiles).toContain('AdminResourceForm.jsx');
        expect(componentFiles).toContain('AdminResourceTable.jsx');
    });

    it('imports page-level admin modules from pages/admin instead of components/admin', () => {
        const adminEntry = read('admin.jsx');
        const pageRouter = read('pages/admin/AdminPageRouter.jsx');

        expect(adminEntry).toContain("./pages/admin/AdminLoginPage.jsx");
        expect(adminEntry).toContain("./pages/admin/AdminSpaApp.jsx");
        expect(pageRouter).toContain("./AdminDashboardPage.jsx");
        expect(pageRouter).toContain("./AdminMediaPage.jsx");
        expect(pageRouter).toContain("./AdminNewsletterPage.jsx");
        expect(pageRouter).toContain("./AdminOrderPage.jsx");
        expect(pageRouter).not.toContain("../../components/admin/AdminDashboard.jsx");
        expect(pageRouter).not.toContain("../../components/admin/AdminMediaManager.jsx");
        expect(pageRouter).not.toContain("../../components/admin/AdminNewsletterManager.jsx");
        expect(pageRouter).not.toContain("../../components/admin/AdminOrderManager.jsx");
    });
});
