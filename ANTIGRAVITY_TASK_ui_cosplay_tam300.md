# Task: reskin storefront UI to match the real tam300.com look

## Context
Laravel 13 + Blade + Tailwind + Alpine.js e-commerce project (`phone-shop`). The storefront currently uses an **editorial redesign** (serif "Fraunces" headings, oklch teal accent, sharp `rounded-[2px]` corners) from a previous design pass — you are **replacing that entire visual language**, not layering on top of it. Search for and remove the old tokens/classes described below wherever they appear.

Goal: make the storefront look like the real business **tam300.com** (an iPhone trade-in/resale site) — clean, corporate-clean, navy-and-white, rounded, card-based. This is a visual style reference only — do not copy their logo, copy, or product photos, and do not claim affiliation with the real business anywhere in the copy.

## Design tokens (observed directly from tam300.com's live computed styles)

Add/replace in `tailwind.config.js` → `theme.extend`:

```js
colors: {
  brand: '#0B4174',        // primary navy — buttons, links, active states (measured: rgb(11,65,116))
  'brand-dark': '#083150', // hover/pressed state, darker navy
  ink: '#0f172a',          // headings, body text (slate-900)
  'ink-soft': '#64748b',   // secondary/muted text (slate-500)
  paper: '#f8fafc',        // page background tint (slate-50) — NOT white; use white only for cards/header
  line: '#e2e8f0',         // borders (slate-200)
},
fontFamily: {
  sans: ['Inter', ...defaultTheme.fontFamily.sans], // real site's body font is Inter — REMOVE the Fraunces serif font entirely
},
```

Remove `fontFamily.serif` (Fraunces) from the config — this reskin uses **no serif font anywhere**, unlike the previous editorial pass.

Google Fonts link (replace whatever font `<link>` is currently in `layouts/shop.blade.php` head):
```html
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
```

### Global replacements (find-and-replace across all storefront Blade files)
- Any `font-serif` class on headings → remove it (just use `font-bold` or `font-semibold` on the default sans font).
- Any `rounded-[2px]` or `rounded-none` (sharp editorial corners) → `rounded-2xl` (16px, matches tam300.com's measured button radius exactly) for buttons and cards; `rounded-xl` (12px) is fine for smaller elements like input fields and small chips.
- Any `text-accent` / `bg-accent` / `border-accent` / `accent-dark` (old oklch teal tokens) → `text-brand` / `bg-brand` / `border-brand` / `hover:bg-brand-dark`.
- Any `text-ink` / `text-ink-soft` / `bg-paper` / `border-line` tokens can stay **by name** but must now resolve to the new hex values above (i.e. just update the config, don't rename classes if they already say `ink`/`paper`/`line`).
- Uppercase eyebrow labels (e.g. "BỘ SƯU TẬP MỚI", section labels): keep the pattern `text-xs font-bold tracking-wide uppercase`, color `text-brand` (real site uses navy for these, not a light tint).
- Card shadows: real site uses a soft, subtle shadow, not a hard border-only style. Use `shadow-sm border border-line` on cards (keep the border too, real site cards have both a faint border and a soft shadow).

## Screens to update

### 1. Header/Nav — `resources/views/layouts/shop.blade.php`
- Header background: `bg-brand` (solid navy), not white. Nav links, logo, and icons switch to white/light text on this dark bar (`text-white` for logo, `text-white/70 hover:text-white` for inactive nav links, `text-white` + a white underline for the active link — no more colored underline, use white).
- Logo: keep the current app name, `font-extrabold text-xl text-white tracking-tight` (real site logo is bold sans, not serif, with a small square-dot icon before the text — you may add a simple two-tone square/dot glyph before the wordmark if you want to match closer, optional).
- Cart icon and "Đăng nhập" link: `text-white/80 hover:text-white`.
- "Đăng ký" button: on a navy header a white pill reads best — `bg-white text-brand hover:bg-white/90 rounded-xl font-semibold`.
- Footer: `bg-white border-t border-line`, plain `text-ink-soft` copyright text — footer on the real site is a normal light footer, not navy.

### 2. Trang chủ — `resources/views/home.blade.php`
- Hero section background: **light**, `bg-paper` (NOT dark — this is the biggest change from the previous editorial pass, which used a dark `bg-ink` hero). Real tam300.com hero is a light gray/off-white panel.
- Hero heading: `text-ink font-extrabold`, no serif, sizes similar to before are fine (`text-[clamp(32px,4.5vw,48px)]`).
- Eyebrow above heading: `text-xs font-bold tracking-[2px] uppercase text-brand` (navy, not the old teal-light tint — real site's eyebrow text on a light background is the same navy as everything else, not a special lighter shade).
- CTA button: `bg-brand hover:bg-brand-dark text-white rounded-2xl px-8 py-4 font-semibold`.
- Keep the 2-column layout (text left / image placeholder right) from the previous pass — that structural choice already matches tam300.com's hero layout (text + product visual side by side). Just re-skin colors/radius/font as above.
- "Tại sao chọn TAM300"-style trio of value-prop cards is a good pattern **you may add** if there's time (3 white `rounded-2xl border border-line shadow-sm p-6` cards with a short bold heading + 1-2 sentences each, e.g. reusing the site's copy structure "Chính sách rõ ràng", "Giao hàng nhanh", "Bảảo hành uy tín" — write your own copy suited to this phone-shop project, do not copy tam300.com's actual policy text since it describes their real trade-in business model which doesn't apply here). This is optional polish, not required.
- Category/brand chip pills: `rounded-2xl border border-line bg-white hover:border-brand hover:text-brand`.
- Product grid section heading: plain `font-bold text-2xl text-ink` (no serif).

### 3. Sản phẩm (listing) — `resources/views/products/index.blade.php`
- Filter sidebar `<aside>`: `bg-white border border-line rounded-2xl shadow-sm p-5`.
- Section labels inside filters: `text-xs font-bold tracking-wide uppercase text-ink-soft` (unchanged pattern, fine as-is).
- "Áp dụng" button and any other solid buttons: `bg-brand hover:bg-brand-dark text-white rounded-xl`.
- Sort `<select>` and price inputs: `rounded-xl border-line`.
- Keep all GET-form filter/sort/pagination logic untouched — styling only.

### 4. Chi tiết sản phẩm — `resources/views/products/show.blade.php`
- Image frame: `border border-line rounded-2xl bg-white`.
- Price: `text-brand text-3xl font-extrabold` (navy, not teal).
- Color/storage option buttons: `rounded-xl border-[1.5px]`, selected state `border-brand text-brand bg-brand/5`, unselected `border-line text-ink`.
- "Thêm vào giỏ hàng" button: `bg-brand hover:bg-brand-dark text-white rounded-2xl`.
- Keep all Alpine `x-data` variant-selection logic exactly as-is — only change the Tailwind class strings on the buttons/price/etc.

### 5. Giỏ hàng — `resources/views/cart/index.blade.php`
- Cart container: `bg-white border border-line rounded-2xl shadow-sm divide-y divide-line`.
- "Tiến hành thanh toán" CTA: `bg-brand hover:bg-brand-dark text-white rounded-2xl`.
- Keep all quantity-update/delete form logic untouched.

### 6. Thanh toán — `resources/views/checkout/index.blade.php`
- Address/payment cards: `bg-white border border-line rounded-2xl shadow-sm p-5`.
- Selected radio row: `border-brand border-2 rounded-xl` (was accent border before — same pattern, new color).
- Grand total: `text-brand font-extrabold`.
- "Đặt hàng" submit button: `bg-brand hover:bg-brand-dark text-white rounded-2xl`.

### 7. Product card — `resources/views/components/product-card.blade.php`
- Card: `bg-white border border-line rounded-2xl shadow-sm hover:shadow-md transition overflow-hidden` (real site's cards lift slightly on hover — a touch more shadow than the flat editorial version is correct here).
- Price: `text-brand font-bold` (was accent teal).
- Keep the striped placeholder block for products without a real photo — just make sure its container now has `rounded-2xl` corners to match the new card style (was sharp `rounded-[2px]` before).

## What NOT to change
- No server-side logic: routes, controllers, Alpine `x-data` variant-selection state, filter/sort/pagination GET forms, CSRF tokens, cart/checkout form actions — all untouched, styling only.
- Admin panel and auth (login/register/Breeze) pages — out of scope, leave as they currently are.
- Don't copy tam300.com's actual copy/policy text (it describes a real iPhone trade-in business model that doesn't apply to this generic phone-shop project) — write original copy in the same tone/structure where new copy is needed.

## After making changes
1. Rebuild frontend assets: `npm run build` (or `npm run dev` while iterating).
2. Start `php artisan serve`, open the storefront and visually check all 5 screens plus header/footer: navy header with white text, light page background, `rounded-2xl` cards/buttons everywhere, Inter font, no serif anywhere, no teal color anywhere.
3. Confirm no console errors and that Alpine variant selection / cart / checkout still function (add to cart, change quantity, go through checkout with a logged-in test user).

## Report back
Summarize what changed per screen, any deviations you made from this spec and why, and confirm the visual + functional check passed.
