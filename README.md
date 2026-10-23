# polrX

:aerial_tramway: 簡潔、輕量、可自行架設的短網址服務 —— [Polr](https://github.com/cydrobolt/polr) 的社群維護分支。

[![License: GPL v2+](https://img.shields.io/badge/license-GPLv2%2B-blue.svg)](LICENSE)
[![Upstream](https://img.shields.io/badge/upstream-cydrobolt%2Fpolr-lightgrey.svg)](https://github.com/cydrobolt/polr)

> [!NOTE]
> **polrX 是非官方的分支，與 Polr 原作者無隸屬關係。**
> 上游專案 [cydrobolt/polr](https://github.com/cydrobolt/polr) 最後一次程式碼提交是 2023 年 9 月，最後一個正式版本是 2021 年 1 月的 `2.3.0b`，目前已無人維護。polrX 的目標是讓這個專案能在新版 PHP 上繼續安全地使用。

---

## 目錄

- [簡介](#簡介)
- [為什麼要有 polrX](#為什麼要有-polrx)
- [目前狀態](#目前狀態)
- [功能](#功能)
- [系統需求](#系統需求)
- [快速安裝](#快速安裝)
- [升級](#升級)
- [API](#api)
- [參與貢獻](#參與貢獻)
- [版本號](#版本號)
- [授權](#授權)
- [致謝](#致謝)

---

## 簡介

polrX 是一套可自行架設的開源短網址網站，附有 API。你可以用自己的網域縮短網址、把短網址做成自己的品牌，並完整掌握自己的資料。

polrX 以現代 **Laravel 13** 框架與 **PHP 8.4+** 改寫，使用 MySQL / MariaDB 作為主要資料庫，完整相容原版 Polr 2.x 的資料庫結構與 `/api/v2` 端點。

## 為什麼要有 polrX

原版 Polr 使用 Lumen 5.1（2015 年的框架，且 Lumen 本身已被 Laravel 官方停止維護），只能跑在早已結束安全支援的 PHP 7.x。

polrX 解決了這些問題：

1. **全面改用現代 Laravel 13 框架**，原生支援 **PHP 8.4 / 8.5**，擺脫已被淘汰的 Lumen 與 PHP 7。
2. **提供完整的繁體中文介面**與多款後台主題切換（預設、深色科技、藍紫漸層、柔和粉彩）。
3. **內建兩步驟驗證（2FA TOTP）**、登入與驗證碼速率限制（Rate Limiting）、登入後自動更換 Session ID，以及統計頁 XSS 防護。
4. **零痛點無縫升級**：100% 相容既有 Polr 資料庫結構與短網址資料。

## 目前狀態

| 項目 | 狀態 |
|---|---|
| 改寫為 Laravel 13 架構，支援 PHP 8.4 / 8.5 | ✅ 已完成並通過端對端驗證 |
| 完整繁體中文介面 | ✅ 已完成 |
| 兩步驟驗證（TOTP） | ✅ 已完成 |
| 後台主題切換 | ✅ 已完成 |
| 登入與 TOTP 速率限制（Rate Limiting） | ✅ 已完成 |
| Session 安全強化（登入更換 ID、Cookie 安全屬性與合理效期） | ✅ 已完成 |
| 內建輕量 DataTables 伺服端分頁引擎（移除過時第三方套件依賴） | ✅ 已完成 |

## 功能

沿用 Polr 原有的功能：

- 縮短網址，可自訂短網址結尾
- 使用者與管理員後台，管理連結與使用者
- 具備配額限制的 REST API（含批次縮短）
- 連結點擊統計（需啟用進階分析）
- 密碼連結（secret key）
- 可關閉公開註冊與匿名使用

## 系統需求

- PHP 8.3 以上（推薦 PHP 8.4 / 8.5），需要 `pdo_mysql`、`mbstring`、`bcmath`、`curl`
- MySQL 5.7+ 或 MariaDB 10.x
- Web 伺服器：Apache（啟用 `mod_rewrite`）、Nginx 或 Caddy，文件根目錄指向 `public/`
- [Composer](https://getcomposer.org/) 2.x

## 快速安裝

1. 取得程式碼：

   ```bash
   git clone https://github.com/tommy1616/polrX.git
   cd polrX
   ```

2. 安裝相依套件：

   ```bash
   composer install --no-dev -o
   ```

3. 建立一個空的資料庫與使用者。
4. 把 Web 伺服器的文件根目錄設定為 `public/`，並確保 `storage/` 可由網站程序寫入。
5. 用瀏覽器開啟 `https://你的網域/setup`，依指示完成安裝。

安裝完成後，設定會寫入專案根目錄的 `.env`。**請勿把 `.env` 提交到版本控制，也不要讓它能被外部讀取。**

詳細步驟可參考上游的[安裝指南](http://docs.polrproject.org/en/latest/user-guide/installation/)，polrX 的安裝流程目前與它相同。

## 升級

*從 Polr 2.x 或舊版 polrX 升級：*

1. 備份資料庫與檔案。
2. 用 `git pull` 取得新版本。
3. 執行 `composer install --no-dev -o` 更新相依套件。
4. 執行 `php artisan migrate` 更新資料庫結構。

*從 Polr 1.x 升級：* 1.x 與 2.x 之間有不相容的變更，無法自動升級。

## API

API 與上游 Polr 相容，說明請見[API 文件](http://docs.polrproject.org/en/latest/developer-guide/api/)。

## 參與貢獻

歡迎回報問題、提出建議或送出 Pull Request：

- 回報問題與建議：[Issues](https://github.com/tommy1616/polrX/issues)
- 送出修改前，請先開 issue 說明想做的事情，避免重複工作。
- 請不要在 issue 或 Pull Request 中貼出 `.env`、金鑰、密碼等機密資料。
- 如果你發現的是**安全性問題**，請不要公開張貼細節，改用本倉庫 **Security → Report a vulnerability**（GitHub 私人漏洞通報）私下回報。

## 版本號

polrX 採用[語意化版本](http://semver.org/lang/zh-TW/)。

## 授權

polrX 是 Polr 的衍生作品，依 **GNU General Public License 第 2 版或（由你選擇的）任何更新版本**釋出，完整條文見 [LICENSE](LICENSE)。

這代表你可以自由使用、修改與散布，但散布時必須使用相同的授權並提供原始碼，同時保留原作者的版權聲明。只是在自己的伺服器上架設服務，不屬於「散布」。

```
Copyright (C) 2013-2018 Chaoyi Zha（Polr 原作者）
Copyright (C) 2026 polrX 貢獻者

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
```

## 致謝

- 感謝 [Chaoyi Zha（cydrobolt）](https://github.com/cydrobolt) 與所有 Polr 貢獻者，建立了這個專案。
- 感謝 Oregon State University Open Source Lab 長期為 Polr 官方網站與示範站提供主機資源。
- 感謝 [lastspark](https://thenounproject.com/lastspark/) 提供 Polr 標誌的圖示。polrX 未沿用 Polr 的標誌。
