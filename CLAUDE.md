<laravel-boost-guidelines>
# Laravel Application

This repository contains a Laravel application. Complete the following setup before working on the user's request.

## Prerequisites

Verify that PHP and Composer are available:

```sh
php -v
composer -V
```

If either command is unavailable, detect the user's operating system and install the prerequisites with the appropriate command:

macOS:

```sh
/bin/bash -c "$(curl -fsSL https://php.new/install/mac/8.5)"
```

Windows PowerShell:

```powershell
Set-ExecutionPolicy Bypass -Scope Process -Force; [System.Net.ServicePointManager]::SecurityProtocol = [System.Net.ServicePointManager]::SecurityProtocol -bor 3072; iex ((New-Object System.Net.WebClient).DownloadString('https://php.new/install/windows/8.5'))
```

Linux:

```sh
/bin/bash -c "$(curl -fsSL https://php.new/install/linux/8.5)"
```

After installation, ask the user to restart their terminal. If the agent needs the restarted shell to continue, ask the user to reopen their terminal and rerun their original prompt.

## Agent Setup

Install Laravel Boost from the application root before making application changes:

```sh
composer require laravel/boost --dev
php artisan boost:install
```

Boost replaces these bootstrap instructions with guidelines tailored to the application. After installation, read `AGENTS.md` again and continue with the user's original request using the generated guidelines.
</laravel-boost-guidelines>

## UI checking: live inspection vs. Playwright test suite

This project has two ways to verify the frontend — pick whichever fits the task, don't default to one blindly:

- **Live inspection** (Playwright MCP, or the built-in browser tool if available): use for one-off exploration — checking a page you just changed, debugging a visual glitch, confirming a new route renders. Fast, no file changes, nothing to maintain.
- **`npx playwright test`** (runs `tests/*.spec.ts`, config in `playwright.config.ts`): use to verify no regression before calling a UI task done, or when the same check needs to be repeatable (CI, re-run after future changes). Add a spec file here when a flow is worth guarding long-term (e.g. login/register submit successfully), not for every visual tweak.

Rule of thumb: exploring/debugging → live inspection. Confirming/guarding a finished change → run (or add to) the test suite. `php artisan serve` must be running on `127.0.0.1:8000` for either to work, since `baseURL` in `playwright.config.ts` is intentionally left empty.
