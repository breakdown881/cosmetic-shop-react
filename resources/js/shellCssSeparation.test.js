import { readFileSync } from 'node:fs';
import path from 'node:path';
import { describe, expect, it } from 'vitest';

const readProjectFile = (filePath) => readFileSync(path.join(process.cwd(), filePath), 'utf8');

describe('shell CSS separation', () => {
    it('loads customer and admin CSS from separate Vite entrypoints', () => {
        const publicShell = readProjectFile('app/Support/PublicReactShell.php');
        const adminShell = readProjectFile('app/Support/AdminReactShell.php');
        const viteConfig = readProjectFile('vite.config.js');

        expect(publicShell).toContain("'resources/css/public.css'");
        expect(publicShell).not.toContain("'resources/css/admin.css'");
        expect(publicShell).not.toContain("'resources/css/app.css'");

        expect(adminShell).toContain("'resources/css/admin.css'");
        expect(adminShell).not.toContain("'resources/css/public.css'");
        expect(adminShell).not.toContain("'resources/css/app.css'");

        expect(viteConfig).toContain("'resources/css/public.css'");
        expect(viteConfig).toContain("'resources/css/admin.css'");
        expect(viteConfig).not.toContain("'resources/css/app.css'");
    });

    it('marks each shell with a site-specific body class', () => {
        const publicShell = readProjectFile('app/Support/PublicReactShell.php');
        const adminShell = readProjectFile('app/Support/AdminReactShell.php');

        expect(publicShell).toContain('class="customer-site antialiased"');
        expect(adminShell).toContain("'admin-site '");
    });

    it('keeps customer-only styles out of the admin bundle and admin-only styles out of the customer bundle', () => {
        const publicCss = readProjectFile('resources/css/public.css');
        const adminCss = readProjectFile('resources/css/admin.css');

        expect(publicCss).not.toContain('.react-admin-');
        expect(adminCss).not.toContain('.react-home');
        expect(adminCss).not.toContain('.react-product');
        expect(adminCss).not.toContain('.react-welcome');
    });
});
