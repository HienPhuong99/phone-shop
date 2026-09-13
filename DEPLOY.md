# Deploy lên Railway

Các bước dưới đây cần thực hiện trên trang railway.app (yêu cầu tài khoản của bạn) — phần này mình không tự làm thay được. Repo đã có sẵn `nixpacks.toml` để Railway build/chạy đúng mà không cần cấu hình thêm.

## 1. Tạo project

1. Đăng nhập [railway.app](https://railway.app) (có thể dùng tài khoản GitHub).
2. **New Project → Deploy from GitHub repo** → chọn `HienPhuong99/phone-shop`.
3. Railway tự phát hiện `nixpacks.toml` và build theo đó (composer install, npm build, sau đó chạy migrate + serve).

## 2. Thêm MySQL

1. Trong project, bấm **+ New → Database → Add MySQL**.
2. Railway tự tạo các biến `MYSQLHOST`, `MYSQLPORT`, `MYSQLUSER`, `MYSQLPASSWORD`, `MYSQLDATABASE` cho service MySQL đó.

## 3. Cấu hình biến môi trường cho service web

Vào service web (chứa code Laravel) → tab **Variables**, thêm:

| Biến | Giá trị |
|---|---|
| `APP_NAME` | Phone Shop |
| `APP_ENV` | production |
| `APP_DEBUG` | false |
| `APP_KEY` | (xem bước 4) |
| `APP_URL` | `https://<domain-railway-cap-cho-ban>` |
| `DB_CONNECTION` | mysql |
| `DB_HOST` | `${{MySQL.MYSQLHOST}}` |
| `DB_PORT` | `${{MySQL.MYSQLPORT}}` |
| `DB_DATABASE` | `${{MySQL.MYSQLDATABASE}}` |
| `DB_USERNAME` | `${{MySQL.MYSQLUSER}}` |
| `DB_PASSWORD` | `${{MySQL.MYSQLPASSWORD}}` |
| `SESSION_DRIVER` | database |
| `CACHE_STORE` | database |
| `QUEUE_CONNECTION` | database |
| `FILESYSTEM_DISK` | local |
| `VNPAY_TMN_CODE` | (để trống, hoặc điền khi có tài khoản sandbox thật) |
| `VNPAY_HASH_SECRET` | (để trống, hoặc điền khi có) |
| `VNPAY_RETURN_URL` | `https://<domain-railway>/vnpay/return` |

Cú pháp `${{MySQL.MYSQLHOST}}` là cách Railway tham chiếu biến từ service MySQL sang service web — gõ đúng như vậy trong ô value.

## 4. Sinh APP_KEY

Chạy lệnh này ở máy local rồi copy giá trị `base64:...` dán vào biến `APP_KEY` ở Railway (đơn giản hơn là cài Railway CLI):

```bash
php artisan key:generate --show
```

## 5. Lấy domain public

Trong service web → tab **Settings → Networking → Generate Domain**. Railway cấp một domain dạng `phone-shop-production.up.railway.app`. Cập nhật lại `APP_URL` và `VNPAY_RETURN_URL` ở bước 3 cho khớp domain này rồi redeploy.

## 6. Kiểm tra sau khi deploy

- Truy cập domain, xác nhận trang chủ load được sản phẩm (nghĩa là đã kết nối MySQL và migrate/seed thành công).
- Thử đăng ký tài khoản, thêm giỏ hàng, đặt hàng COD.
- Đăng nhập `admin@phoneshop.test` / `password` (đổi mật khẩu này ngay sau khi demo xong) để vào `/admin`.

## Lưu ý quan trọng

- **Dev dependencies vẫn được cài ở production:** `nixpacks.toml` không dùng `--no-dev` vì seeder dùng `fakerphp/faker` (dev dependency) để tạo dữ liệu demo sau khi deploy. Đây là đánh đổi hợp lý cho dự án demo/CV; một app production thật nên tách seeder ra và dùng `--no-dev`.
- **Ảnh upload không bền vững:** Railway không giữ lại file trên đĩa giữa các lần deploy/restart (container ephemeral). Ảnh sản phẩm upload qua admin panel sẽ mất khi service restart. Với dự án demo/CV thì chấp nhận được; nếu cần bền vững thật, nên chuyển `FILESYSTEM_DISK` sang một dịch vụ object storage như Cloudflare R2 hoặc AWS S3 (Laravel hỗ trợ sẵn qua `league/flysystem-aws-s3-v3`).
- **Seed dữ liệu mẫu:** `nixpacks.toml` chỉ chạy `migrate`, không tự `--seed` (tránh seed lại mỗi lần deploy). Sau lần deploy đầu tiên, vào Railway → service web → tab **Deploy → run command** (hoặc dùng Railway CLI `railway run php artisan db:seed`) để có dữ liệu mẫu.
- Đổi mật khẩu tài khoản admin mẫu trước khi chia sẻ link demo công khai.
