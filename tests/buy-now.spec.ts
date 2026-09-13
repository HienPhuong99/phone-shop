import { test, expect } from '@playwright/test';

const BASE_URL = 'http://127.0.0.1:8000';

test.describe('Mua ngay', () => {
    test('khách vãng lai mua ngay thành công không cần đăng ký tài khoản', async ({ page }) => {
        await page.goto(`${BASE_URL}/san-pham`);
        await page.locator('a[href*="/san-pham/"]').first().click();

        const buyNowButton = page.getByTestId('buy-now-button');
        const colorButtons = page.getByTestId('variant-color-button');
        const colorCount = await colorButtons.count();

        for (let i = 0; i < colorCount; i++) {
            if (!(await buyNowButton.isDisabled())) {
                break;
            }
            await colorButtons.nth(i).click();
        }

        await expect(buyNowButton).toBeEnabled();
        await buyNowButton.click();

        const modal = page.getByRole('heading', { name: 'Mua ngay' }).locator('..');
        await expect(modal).toBeVisible();

        await page.fill('#buy_now_recipient_name', 'Khách Vãng Lai E2E');
        await page.fill('#buy_now_phone', `09${Date.now().toString().slice(-8)}`);
        await page.fill('#buy_now_address_line', '123 Đường Test, Quận 1, TP.HCM');

        await page.getByRole('button', { name: 'Xác nhận đặt hàng' }).click();

        await expect(page.getByText('Đặt hàng thành công! Cảm ơn bạn đã mua sắm tại phuonghihi.')).toBeVisible();
        await expect(page.locator('h1', { hasText: 'Đơn hàng #' })).toBeVisible();
    });
});
