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

polrX 以 PHP 與 Lumen 框架撰寫，使用 MySQL / MariaDB 作為主要資料庫。

## 為什麼要有 polrX

原版 Polr 使用 Lumen 5.1（2015 年的框架），只能跑在 PHP 7.x。PHP 7.4 已於 2022 年 11 月結束支援，不會再有安全修補，而原專案也沒有繼續更新。

polrX 要解決的事情：

1. **讓 Polr 能在新版 PHP（8.4）上執行**，不再依賴已經停止支援的 PHP 7。
2. **提供完整的繁體中文介面**。
3. **補強安全性**，例如兩步驟驗證（TOTP）。
4. **持續維護**：回應 issue、更新相依套件。

## 目前狀態

> [!IMPORTANT]
> polrX 還在整理初期。**`master` 分支目前的程式碼與上游 Polr 相同**，仍然是 Lumen 5.1 / PHP 7.x，尚未包含下列項目。請不要把目前的 `master` 當成已經支援 PHP 8 的版本。

| 項目 | 狀態 |
|---|---|
| 升級到 Lumen 11，支援 PHP 8.4 | 🚧 開發中，已能在測試環境執行首頁、登入、後台、轉址等主要流程，仍有項目待驗證 |
| 完整繁體中文介面 | 🚧 整理中，尚未合併到 `master` |
| 兩步驟驗證（TOTP） | 🚧 整理中，尚未合併到 `master` |
| 後台主題切換 | 🚧 整理中，尚未合併到 `master` |
| 登入失敗次數限制 | 📝 規劃中 |
| 縮短 session 有效期、強化 cookie 設定 | 📝 規劃中 |
| 自動化測試 | 📝 規劃中 |

各項目完成並驗證後，會更新這張表。

## 功能

沿用 Polr 原有的功能：

- 縮短網址，可自訂短網址結尾
- 使用者與管理員後台，管理連結與使用者
- 具備配額限制的 REST API（含批次縮短）
- 連結點擊統計（需啟用進階分析）
- 密碼連結（secret key）
- 可關閉公開註冊與匿名使用

## 系統需求

> 下列為 polrX 的目標環境。在 PHP 8 相容修改合併之前，`master` 仍然只能在 PHP 7.x 上執行。

- PHP 8.2 以上（目標 8.4），需要 `pdo_mysql`、`mbstring`、`bcmath`、`curl`
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
