<p align="center">
  <img src="public/favicon.svg" width="96" height="96" alt="polrX Logo" />
</p>

<h1 align="center">polrX</h1>

<p align="center">
  A minimalist, fast, self-hosted open-source URL shortener — modernized fork of <a href="https://github.com/cydrobolt/polr">Polr</a> powered by Laravel 13 &amp; PHP 8.4+.
</p>

<p align="center">
  <a href="LICENSE"><img src="https://img.shields.io/badge/license-GPLv2%2B-blue.svg" alt="License: GPL v2+" /></a>
  <img src="https://img.shields.io/badge/PHP-8.4%2B-777BB4?logo=php&logoColor=white" alt="PHP 8.4+" />
  <img src="https://img.shields.io/badge/Laravel-13-FF2D20?logo=laravel&logoColor=white" alt="Laravel 13" />
  <a href="https://github.com/cydrobolt/polr"><img src="https://img.shields.io/badge/upstream-cydrobolt%2Fpolr-lightgrey.svg" alt="Upstream" /></a>
</p>

<p align="center">
  <strong>🌐 Language / 語言：</strong>
  <a href="README.md">繁體中文（台灣）</a> ·
  <a href="README.en.md"><strong>English</strong></a> ·
  <a href="README.ja.md">日本語</a> ·
  <a href="README.ko.md">한국어</a>
</p>

> [!NOTE]
> **polrX is an independent, community-maintained modernization fork and is not affiliated with the original Polr authors.**
> Upstream [cydrobolt/polr](https://github.com/cydrobolt/polr) had its last release (`2.3.0b`) in January 2021 on deprecated Lumen 5.1 and EOL PHP 7.x. polrX upgrades the entire stack to modern Laravel 13 and PHP 8.4+, adding full internationalization (i18n), a 3-tier surface theme engine, dedicated Two-Factor Authentication (2FA TOTP), and comprehensive security hardening.

---

## Table of Contents

- [Overview](#overview)
- [Why polrX?](#why-polrx)
- [Features & Modernization Status](#features--modernization-status)
- [Multi-Language Support (i18n)](#multi-language-support-i18n)
- [Modern Theme System](#modern-theme-system)
- [Requirements](#requirements)
- [Quick Installation](#quick-installation)
- [Upgrading](#upgrading)
- [REST API](#rest-api)
- [Contributing & Security](#contributing--security)
- [License](#license)
- [Acknowledgements](#acknowledgements)

---

## Overview

**polrX** is a lightweight, self-hosted open-source URL shortening platform with a robust REST API. It lets you host your own branded short links on your custom domain while retaining complete ownership over your data and click analytics.

Rewritten on **Laravel 13** and **PHP 8.4+** with MySQL / MariaDB, polrX remains **100% compatible with the upstream Polr 2.x database schema and `/api/v2` endpoints**.

## Why polrX?

Original Polr was built on Lumen 5.1 (released in 2015 and officially retired by Laravel) and only runs on end-of-life PHP 7.x versions.

polrX solves these problems with a complete modernization:

1. **Modern Laravel 13 + PHP 8.4 / 8.5 Stack**: Replaces deprecated Lumen 5.1 and abandoned dependencies, featuring a built-in lightweight server-side DataTables engine (`DataTablesHelper`).
2. **Full Internationalization (i18n) Architecture**: Built-in support for **Traditional Chinese (Taiwan)**, **English**, **Japanese**, and **Korean**. Switch languages anytime via the top navbar dropdown, the dedicated **Language (`#language`)** tab in the Admin Dashboard, or the `?lang=` query parameter.
3. **3-Tier Surface Theme Engine**: 6 curated themes (**Default**, **Midnight**, **Ocean**, **Forest**, **Rose**, **Nord**) built on CSS custom properties (`--polrx-bg`, `--polrx-surface`, `--polrx-surface-alt`) with instant zero-reload switching and per-user persistence.
4. **Dedicated 2FA TOTP & Security Hardening**:
   - Dedicated **Two-Factor Authentication (`#totp`)** tab in the Admin sidebar supporting RFC 6238 TOTP apps (1Password, Google Authenticator, Authy) with local SVG QR Code generation.
   - Constant-time API key verification (`hash_equals`), strict column whitelisting in `DataTablesHelper`, XSS-safe JSON encoding in click analytics, session ID regeneration on login, and rate limiting across authentication endpoints.
5. **Drop-In Upgrade Compatibility**: Seamlessly migrates existing Polr 2.x databases via `php artisan migrate`.

## Features & Modernization Status

| Module | Status | Description |
| :--- | :---: | :--- |
| **Laravel 13 + PHP 8.4 / 8.5 Rewrite** | ✅ Done | Full Laravel routing, Middleware, Blade templates, and Eloquent ORM |
| **Full i18n Multi-Language Architecture** | ✅ Done | Supports `zh_TW` (default), `en`, `ja`, and `ko` across views, controllers, JS, and modals |
| **Dedicated 2FA (TOTP) Sidebar Tab** | ✅ Done | Dedicated `#totp` tab with local QR SVG generation and password-verified disable flow |
| **Dedicated Language Sidebar Tab** | ✅ Done | Dedicated `#language` tab + global top-navbar quick-switcher dropdown |
| **6 Modern CSS Variable Themes** | ✅ Done | Unified 3-tier surface hierarchy with instant zero-reload preview and database persistence |
| **Comprehensive Security Hardening** | ✅ Done | Timing-safe comparisons, SQL sort whitelisting, XSS protection, Rate Limiting, Session security |
| **Custom Endings & Secret Links** | ✅ Done | Base32 / Base62 encoding, custom short endings, and secret key URLs |
| **Real-Time Click Analytics** | ✅ Done | Time-series traffic charts, referer breakdowns, and interactive world map |
| **REST API (`/api/v2`)** | ✅ Done | Upstream-compatible endpoints for single/bulk shortening, lookups, and analytics |

## Multi-Language Support (i18n)

polrX ships with complete translation dictionaries in [`lang/`](lang/), covering all public pages, the Admin Dashboard, toast notifications, DataTables pagination, and Angular modals:

| Locale Code | Language | Translation File |
| :---: | :--- | :--- |
| `zh_TW` | **繁體中文（台灣）** *(Default)* | [`lang/zh_TW/polr.php`](lang/zh_TW/polr.php) |
| `en` | **English** | [`lang/en/polr.php`](lang/en/polr.php) |
| `ja` | **日本語** | [`lang/ja/polr.php`](lang/ja/polr.php) |
| `ko` | **한국어** | [`lang/ko/polr.php`](lang/ko/polr.php) |

- **How to switch languages**:
  1. Use the language dropdown in the top-right navigation bar.
  2. Open the **Language (`#language`)** tab in the Admin Dashboard sidebar and click any language card.
  3. Append `?lang=zh_TW`, `?lang=en`, `?lang=ja`, or `?lang=ko` to any URL.

## Modern Theme System

Logged-in users can switch between 6 built-in themes under **Settings (`#settings`)** (powered by [`app/Helpers/ThemeHelper.php`](app/Helpers/ThemeHelper.php) and [`public/css/themes.css`](public/css/themes.css)):

- **Default (`default`)**: Warm ivory and classic sage green
- **Midnight (`midnight`)**: Deep slate dark mode with indigo neon accents
- **Ocean (`ocean`)**: Deep navy dark mode with cyan glow
- **Forest (`forest`)**: Natural pine green and warm cream
- **Rose (`rose`)**: Soft sakura blush and berry tones
- **Nord (`nord`)**: Minimalist Arctic frost dark palette

## Requirements

- **PHP**: 8.3+ (**PHP 8.4** or **8.5** recommended) with `pdo_mysql`, `mbstring`, `bcmath`, `curl`, and `xml` extensions
- **Database**: MySQL 5.7+ / 8.0+ or MariaDB 10.3+
- **Web Server**: Caddy, Nginx, or Apache (`mod_rewrite` enabled) with Document Root pointing to `public/`
- **Dependency Manager**: [Composer](https://getcomposer.org/) 2.x

## Quick Installation

polrX provides both a **5-Step Web Setup Wizard** and an **Interactive CLI Installer Script** (`./install.sh`), with built-in support for **Traditional Chinese / English / Japanese / Korean** starting from Step 1:

### Option A: 5-Step Web Setup Wizard (Recommended)

1. **Clone the repository & install dependencies**:

   ```bash
   git clone https://github.com/tommy1616/polrX.git
   cd polrX
   composer install --no-dev -o
   ```

2. **Configure permissions**:
   Point your web server document root to `public/` and ensure the project root, `storage/`, and `bootstrap/cache/` are writable by the web server user:

   ```bash
   chown -R www-data:www-data . storage bootstrap/cache
   chmod -R 775 storage bootstrap/cache
   ```

3. **Open your browser to launch the Setup Wizard**:
   Visit `https://your-domain/` in your browser (when no `.env` is detected, polrX automatically initializes an encryption key and redirects to `/setup`):
   - **Step 1 — Choose Language**: Select **繁體中文（台灣） / English / 日本語 / 한국어** first.
   - **Step 2 — System Check**: Automatically verifies PHP version, extensions, and write permissions.
   - **Step 3 — Database**: Enter MySQL / MariaDB credentials and click **"Test Database Connection"** (supports automatic `CREATE DATABASE IF NOT EXISTS`).
   - **Step 4 — Site & Theme**: Configure site name, domain, live-preview default themes, and pick Public / Registered Users / Private access mode.
   - **Step 5 — Admin & Install**: Runs database migrations, creates your admin account, and signs you in automatically.

### Option B: CLI Interactive Installer (`./install.sh` — Ideal for SSH / Docker)

Run `./install.sh` from the project root for an interactive terminal wizard or automated non-interactive deployment:

```bash
git clone https://github.com/tommy1616/polrX.git
cd polrX

# Interactive terminal wizard (asks for language first)
./install.sh

# Or non-interactive one-command deployment:
./install.sh --no-interaction \
  --lang=en \
  --db-host=127.0.0.1 \
  --db-port=3306 \
  --db-name=polr \
  --db-user=polr \
  --db-pass="YourStrongDbPassword" \
  --app-name="polrX" \
  --app-protocol="https://" \
  --app-address="u.example.com" \
  --theme=midnight \
  --access-mode=public \
  --admin-user=admin \
  --admin-email="admin@example.com" \
  --admin-pass="YourAdminPassword"
```

> [!IMPORTANT]
> Never commit your `.env` file to version control or expose it publicly via your web server.

## Upgrading

Upgrading from **Polr 2.x** or an earlier version of **polrX**:

```bash
# 1. Back up your database and .env file
# 2. Pull the latest code
git pull origin master

# 3. Update Composer dependencies
composer install --no-dev -o

# 4. Run database migrations (adds TOTP, theme, and locale columns)
php artisan migrate --force

# 5. Clear cached views and config
php artisan optimize:clear
```

## REST API

polrX maintains full compatibility with the upstream Polr `/api/v2` endpoints:

- `GET|POST /api/v2/action/shorten` — Shorten a single URL
- `GET|POST /api/v2/action/shorten_bulk` — Shorten multiple URLs in bulk
- `GET|POST /api/v2/action/lookup` — Look up a shortened URL
- `GET|POST /api/v2/data/link` — Query time-series click analytics for a short URL

## Contributing & Security

Contributions, bug reports, and feature requests are welcome:

- **Issues & Feature Requests**: [GitHub Issues](https://github.com/tommy1616/polrX/issues)
- Never include `.env` files, API keys, passwords, or TOTP secrets in issues or pull requests.
- **Security Vulnerabilities**: Please do not disclose security issues publicly. Use **Security → Report a vulnerability** on GitHub to report vulnerabilities privately.

## License

polrX is a derivative work of Polr, released under the **GNU General Public License v2.0 or later (GPL-2.0-or-later)**. See [LICENSE](LICENSE) for the full text.

```text
Copyright (C) 2013-2018 Chaoyi Zha (Original Polr Author)
Copyright (C) 2026 polrX Contributors

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.
```

## Acknowledgements

- Special thanks to [Chaoyi Zha (cydrobolt)](https://github.com/cydrobolt) and all upstream Polr contributors for creating the original project.
- Thanks to everyone in the open-source community who contributed translations, testing, and feedback.
