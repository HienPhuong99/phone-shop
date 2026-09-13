import { test, expect } from '@playwright/test';

const BASE_URL = 'http://127.0.0.1:8000';

test.describe('Trang chủ', () => {
    test('tải thành công và hiển thị sản phẩm nổi bật', async ({ page }) => {
        await page.goto(`${BASE_URL}/`);

        await expect(page).toHaveTitle(/phuonghihi/);
        await expect(page.getByRole('heading', { name: 'Sản phẩm mới nhất' })).toBeVisible();

        const productLinks = page.locator('a[href*="/san-pham/"]');
        await expect(productLinks.first()).toBeVisible();
    });

    test('điều hướng sang trang danh sách sản phẩm', async ({ page }) => {
        await page.goto(`${BASE_URL}/`);

        await page.getByRole('link', { name: 'Xem tất cả sản phẩm' }).click();

        await expect(page).toHaveURL(`${BASE_URL}/san-pham`);
        await expect(page.getByRole('heading', { name: 'Sản phẩm', exact: true })).toBeVisible();
    });
});
