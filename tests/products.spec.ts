import { test, expect } from '@playwright/test';

const BASE_URL = 'http://127.0.0.1:8000';

test.describe('Danh sách sản phẩm', () => {
    test('hiển thị danh sách và cho phép lọc theo khoảng giá', async ({ page }) => {
        await page.goto(`${BASE_URL}/san-pham`);

        await expect(page.getByRole('heading', { name: 'Sản phẩm', exact: true })).toBeVisible();
        await expect(page.getByTestId('product-count')).toContainText(/\d+ sản phẩm/);

        await page.fill('input[name="min_price"]', '0');
        await page.fill('input[name="max_price"]', '999999999');
        await page.getByRole('button', { name: 'Áp dụng' }).click();

        await expect(page).toHaveURL(/min_price=0/);
        await expect(page).toHaveURL(/max_price=999999999/);
    });

    test('mở được trang chi tiết sản phẩm từ danh sách', async ({ page }) => {
        await page.goto(`${BASE_URL}/san-pham`);

        const firstProduct = page.locator('a[href*="/san-pham/"]').first();
        const href = await firstProduct.getAttribute('href');
        await firstProduct.click();

        await expect(page).toHaveURL(href!);
        await expect(page.getByTestId('add-to-cart-button')).toBeVisible();
    });
});
