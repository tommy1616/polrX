<p align="center">
  <img src="public/favicon.svg" width="96" height="96" alt="polrX Logo" />
</p>

<h1 align="center">polrX</h1>

<p align="center">
  簡潔、高速、可自行架設的現代化開源短網址平台 —— <a href="https://github.com/cydrobolt/polr">Polr</a> 的 Laravel 13 &amp; PHP 8.4+ 重構分支。
</p>

<p align="center">
  <a href="LICENSE"><img src="https://img.shields.io/badge/license-GPLv2%2B-blue.svg" alt="License: GPL v2+" /></a>
  <img src="https://img.shields.io/badge/PHP-8.4%2B-777BB4?logo=php&logoColor=white" alt="PHP 8.4+" />
  <img src="https://img.shields.io/badge/Laravel-13-FF2D20?logo=laravel&logoColor=white" alt="Laravel 13" />
  <a href="https://github.com/cydrobolt/polr"><img src="https://img.shields.io/badge/upstream-cydrobolt%2Fpolr-lightgrey.svg" alt="Upstream" /></a>
</p>

<p align="center">
  <strong>🌐 語言 / Language：</strong>
  <a href="README.md"><strong>繁體中文（台灣）</strong></a> ·
  <a href="README.en.md">English</a> ·
  <a href="README.ja.md">日本語</a> ·
  <a href="README.ko.md">한국어</a>
</p>

> [!NOTE]
> **polrX 是社群維護的現代化重構分支，與 Polr 原作者無隸屬關係。**
> 上游專案 [cydrobolt/polr](https://github.com/cydrobolt/polr) 最後一個正式版本停留在 2021 年 1 月的 `2.3.0b`（基於已停止維護的 Lumen 5.1 與 PHP 7.x）。polrX 將整個系統升級至現代 Laravel 架構與 PHP 8.4+，並加入完整的多國語言（i18n）、現代化多階層主題系統、獨立兩步驟驗證（2FA TOTP）與全方位資安強化。

---

## 目錄

- [簡介](#簡介)
- [為什麼選擇 polrX](#為什麼選擇-polrx)
- [核心功能與重構狀態](#核心功能與重構狀態)
- [多國語言支援 (i18n)](#多國語言支援-i18n)
- [現代化外觀主題系統](#現代化外觀主題系統)
- [系統需求](#系統需求)
- [快速安裝](#快速安裝)
- [從舊版升級](#從舊版升級)
- [REST API](#rest-api)
- [參與貢獻與資安回報](#參與貢獻與資安回報)
- [授權條款](#授權條款)
- [致謝](#致謝)

---

## 簡介

**polrX** 是一套輕量、極速、可自行架設（Self-hosted）的開源短網址服務與管理平台。你可以使用自有網域建立專屬品牌的短網址，並完全掌控所有連結與點擊分析資料。

polrX 採用 **Laravel 13** 與 **PHP 8.4+** 全面改寫，使用 MySQL / MariaDB 作為資料庫，並 **100% 相容原版 Polr 2.x 的資料庫結構與 `/api/v2` REST API**。

## 為什麼選擇 polrX

原版 Polr 建立於 2015 年的 Lumen 5.1 框架（Laravel 官方已正式淘汰 Lumen），且僅能執行於早已終止安全維護（EOL）的 PHP 7.x。

polrX 帶來了全方位的現代化升級：

1. **現代 Laravel 13 + PHP 8.4 / 8.5 架構**：徹底擺脫過時的 Lumen 5.1 與廢棄套件，內建自研輕量級 `DataTablesHelper` 伺服端分頁引擎。
2. **原生多國語言架構（i18n）**：內建 **繁體中文（台灣）**、**English**、**日本語**、**한국어** 四種語言，支援導覽列下拉選單、後台「語言」分頁與網址參數 `?lang=` 即時切換，並自動記憶於使用者帳號與 Cookie。
3. **三階層視覺表面（3-Tier Surface）主題系統**：內建 6 款精心設計的主題（經典暖白、極夜深邃、深海湛藍、森系翠綠、櫻花粉霧、北極冰川），點擊即刻零延遲切換，消除傳統 Bootstrap 元件割裂感。
4. **獨立兩步驟驗證（2FA TOTP）與全方位資安強化**：
   - 後台獨立「兩步驟驗證」(`#totp`) 分頁，支援 1Password、Google Authenticator、Authy 等 RFC 6238 驗證器與本地端 SVG QR Code 產生。
   - API Key 常數時間比對（`hash_equals` 防範時序攻擊）、DataTables 欄位白名單過濾（防範 SQL 注入）、統計頁面 JSON 安全序列化（防範反射型 XSS）、登入後重新產生 Session ID，以及完整的登入／TOTP／註冊速率限制（Rate Limiting）。
5. **零痛點無縫遷移**：相容既有 Polr 2.x 資料庫，直接執行 `php artisan migrate` 即可無損升級。

## 核心功能與重構狀態

| 功能模組 | 狀態 | 說明 |
| :--- | :---: | :--- |
| **Laravel 13 + PHP 8.4 / 8.5 核心重寫** | ✅ 已完成 | 移除 Lumen，全面採用標準 Laravel 路由、Middleware、Blade 與 Eloquent |
| **全站 i18n 多國語言系統** | ✅ 已完成 | 支援 `zh_TW`（預設）、`en`、`ja`、`ko` 四語系（含後端、Blade、前端 JS、彈跳視窗） |
| **獨立兩步驟驗證（2FA TOTP）分頁** | ✅ 已完成 | 左側導覽列獨立 `#totp` 分頁，支援設定、啟用驗證與密碼確認停用 |
| **獨立語言設定（Language）分頁** | ✅ 已完成 | 左側導覽列獨立 `#language` 分頁 + 右上角導覽列即時切換選單 |
| **6 款現代化 CSS 變數主題系統** | ✅ 已完成 | 三階層深淺表面架構，支援即時無刷新預覽與帳號綁定儲存 |
| **全方位資安審查與修補** | ✅ 已完成 | 時序攻擊防護、SQL 排序白名單、XSS 防護、Rate Limiting、Session 強化 |
| **自訂短網址與秘密連結（Secret URLs）** | ✅ 已完成 | 支援 Base32 / Base62 編碼、自訂短網址結尾與隨機私密金鑰連結 |
| **即時點擊統計與視覺化圖表** | ✅ 已完成 | 支援時間序列流量、來源網域（Referers）與全球點擊地圖統計 |
| **REST API (`/api/v2`)** | ✅ 已完成 | 相容上游 API 端點，支援單筆／批次縮短、數據查詢與每分鐘配額控制 |

## 多國語言支援 (i18n)

polrX 內建完整的四語系字典檔（位於 [`lang/`](lang/) 目錄），涵蓋所有前後台頁面、表單提示、系統通知、DataTables 分頁與 Angular 互動視窗：

| 語言代碼 | 語言名稱 | 語言檔路徑 |
| :---: | :--- | :--- |
| `zh_TW` | **繁體中文（台灣）** *(預設)* | [`lang/zh_TW/polr.php`](lang/zh_TW/polr.php) |
| `en` | **English** | [`lang/en/polr.php`](lang/en/polr.php) |
| `ja` | **日本語** | [`lang/ja/polr.php`](lang/ja/polr.php) |
| `ko` | **한국어** | [`lang/ko/polr.php`](lang/ko/polr.php) |

- **切換方式**：
  1. 點擊頂部導覽列右上角的語言下拉選單。
  2. 登入後前往後台左側選單的 **「語言」(`#language`)** 分頁點擊語言卡片。
  3. 於任意網址後方加上 `?lang=zh_TW`、`?lang=en`、`?lang=ja` 或 `?lang=ko`。

## 現代化外觀主題系統

登入後可於後台 **「設定」(`#settings`)** 分頁即時切換 6 款內建主題（定義於 [`app/Helpers/ThemeHelper.php`](app/Helpers/ThemeHelper.php) 與 [`public/css/themes.css`](public/css/themes.css)）：

- **預設經典 (`default`)**：清爽溫潤的米白與琥珀綠配色
- **極夜深邃 (`midnight`)**：現代深色石板與霓虹靛藍強調色
- **深海湛藍 (`ocean`)**：沉穩午夜藍與青色微光
- **森系翠綠 (`forest`)**：自然松針綠與柔和暖白
- **櫻花粉霧 (`rose`)**：優雅輕柔的玫瑰粉與莓果色調
- **北極冰川 (`nord`)**：極簡冷調的北歐極地深色風格

## 系統需求

- **PHP**：8.3 以上（強烈推薦 **PHP 8.4** 或 **8.5**），需啟用 `pdo_mysql`、`mbstring`、`bcmath`、`curl`、`xml` 擴充套件
- **資料庫**：MySQL 5.7+ / 8.0+ 或 MariaDB 10.3+
- **Web 伺服器**：Caddy、Nginx 或 Apache（需啟用 `mod_rewrite`），網站根目錄（Document Root）指向 `public/`
- **套件管理**：[Composer](https://getcomposer.org/) 2.x

## 快速安裝

1. **取得原始碼**：

   ```bash
   git clone https://github.com/tommy1616/polrX.git
   cd polrX
   ```

2. **安裝 Composer 相依套件**：

   ```bash
   composer install --no-dev -o
   ```

3. **設定目錄權限**：
   將 Web 伺服器的根目錄指向 `public/`，並確保 `storage/` 與 `bootstrap/cache/` 可由 Web 伺服器程序（如 `www-data`）寫入：

   ```bash
   chown -R www-data:www-data storage bootstrap/cache
   chmod -R 775 storage bootstrap/cache
   ```

4. **執行初始化設定與資料庫遷移**：
   - 你可以透過瀏覽器開啟 `https://你的網域/setup` 進行圖形化安裝；
   - 或複製環境設定範本並手動執行遷移：

     ```bash
     cp .env.setup .env
     # 編輯 .env 填入資料庫連線資訊與 APP_KEY
     php artisan migrate --force
     ```

> [!IMPORTANT]
> 安裝完成後，系統設定會保存在專案根目錄的 `.env`。**請勿將 `.env` 提交至 Git 版本控制，並確保外部網路無法直接讀取該檔案。**

## 從舊版升級

從原版 **Polr 2.x** 或舊版 **polrX** 升級：

```bash
# 1. 備份資料庫與 .env 設定檔
# 2. 拉取最新程式碼
git pull origin master

# 3. 更新相依套件
composer install --no-dev -o

# 4. 執行資料庫結構遷移（自動新增 TOTP、主題與語言欄位）
php artisan migrate --force

# 5. 清除快取
php artisan optimize:clear
```

## REST API

polrX 完整保留並相容原版 Polr `/api/v2` 端點（需於使用者帳號啟用 API 並取得 API Key）：

- `GET|POST /api/v2/action/shorten` — 縮短單一網址
- `GET|POST /api/v2/action/shorten_bulk` — 批次縮短多筆網址
- `GET|POST /api/v2/action/lookup` — 查詢短網址對應之原始網址與點擊資訊
- `GET|POST /api/v2/data/link` — 查詢短網址時間範圍點擊分析數據

## 參與貢獻與資安回報

歡迎透過 GitHub 提交 Issue 或 Pull Request：

- **問題回報與功能建議**：[GitHub Issues](https://github.com/tommy1616/polrX/issues)
- 請勿在 Issue 或 Pull Request 中張貼 `.env`、API 金鑰、密碼或 TOTP Secret 等機密資訊。
- **資安漏洞通報**：若發現潛在安全性問題，請勿公開揭露細節，請透過本倉庫的 **Security → Report a vulnerability** 私下通報維護者。

## 授權條款

polrX 為 Polr 的衍生作品，依據 **GNU General Public License v2.0 或更新版本（GPL-2.0-or-later）** 授權釋出，完整條文請參閱 [LICENSE](LICENSE)。

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

## 致謝

- 感謝 [Chaoyi Zha (cydrobolt)](https://github.com/cydrobolt) 與所有上游 Polr 貢獻者奠定了優秀的專案基礎。
- 感謝所有協助測試、翻譯與回報問題的開源社群夥伴。
