import { test, expect, Page } from '@playwright/test';

const BASE_URL = 'http://127.0.0.1:8000';

async function addFirstAvailableVariantToCart(page: Page): Promise<void> {
    await page.goto(`${BASE_URL}/san-pham`);
    await page.locator('a[href*="/san-pham/"]').first().click();

    const addButton = page.getByTestId('add-to-cart-button');
    const colorButtons = page.getByTestId('variant-color-button');
    const colorCount = await colorButtons.count();

    for (let i = 0; i < colorCount; i++) {
        if (!(await addButton.isDisabled())) {
            break;
        }
        await colorButtons.nth(i).click();
    }

    await expect(addButton).toBeEnabled();
    await addButton.click();
    await expect(page.getByText('Đã thêm vào giỏ hàng.')).toBeVisible();
}

test.describe('Giỏ hàng & thanh toán', () => {
    test('khách vãng lai thêm sản phẩm vào giỏ hàng', async ({ page }) => {
        await addFirstAvailableVariantToCart(page);

        await page.goto(`${BASE_URL}/gio-hang`);
        await expect(page.getByText('Giỏ hàng của bạn đang trống')).not.toBeVisible();
    });

    test('đăng ký, thêm giỏ hàng và đặt hàng COD thành công', async ({ page }) => {
        await addFirstAvailableVariantToCart(page);

        const uniqueEmail = `e2e-${Date.now()}@example.com`;

        await page.goto(`${BASE_URL}/register`);
        await page.fill('#name', 'Người Dùng Test');
        await page.fill('#email', uniqueEmail);
        await page.fill('#password', 'password123');
        await page.fill('#password_confirmation', 'password123');
        await page.getByRole('button', { name: 'Register' }).click();

        await expect(page).toHaveURL(`${BASE_URL}/dashboard`);

        await page.goto(`${BASE_URL}/thanh-toan`);
        await page.fill('#recipient_name', 'Người Dùng Test');
        await page.fill('#phone', '0900000000');
        await page.fill('#address_line', '123 Đường Test, Quận 1, TP.HCM');
        await page.selectOption('#province', 'Thành phố Hồ Chí Minh');

        await page.getByRole('button', { name: 'Đặt hàng' }).click();

        await expect(page.getByText('Đặt hàng thành công! Cảm ơn bạn đã mua sắm tại phuonghihi.')).toBeVisible();
        await expect(page.locator('h1', { hasText: 'Đơn hàng #' })).toBeVisible();
    });
});
