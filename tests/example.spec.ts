import { test, expect } from '@playwright/test';

test('trang chủ phone-shop tải thành công', async ({ page }) => {
  await page.goto('http://127.0.0.1:8000');
  await expect(page).toHaveTitle(/Laravel/);
});
