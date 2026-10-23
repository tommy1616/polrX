<p align="center">
  <img src="public/favicon.svg" width="96" height="96" alt="polrX Logo" />
</p>

<h1 align="center">polrX</h1>

<p align="center">
  シンプル・高速・セルフホスト可能なオープンソース URL 短縮プラットフォーム —— <a href="https://github.com/cydrobolt/polr">Polr</a> の Laravel 13 &amp; PHP 8.4+ モダナイズ版フォーク。
</p>

<p align="center">
  <a href="LICENSE"><img src="https://img.shields.io/badge/license-GPLv2%2B-blue.svg" alt="License: GPL v2+" /></a>
  <img src="https://img.shields.io/badge/PHP-8.4%2B-777BB4?logo=php&logoColor=white" alt="PHP 8.4+" />
  <img src="https://img.shields.io/badge/Laravel-13-FF2D20?logo=laravel&logoColor=white" alt="Laravel 13" />
  <a href="https://github.com/cydrobolt/polr"><img src="https://img.shields.io/badge/upstream-cydrobolt%2Fpolr-lightgrey.svg" alt="Upstream" /></a>
</p>

<p align="center">
  <strong>🌐 言語 / Language：</strong>
  <a href="README.md">繁體中文（台灣）</a> ·
  <a href="README.en.md">English</a> ·
  <a href="README.ja.md"><strong>日本語</strong></a> ·
  <a href="README.ko.md">한국어</a>
</p>

> [!NOTE]
> **polrX はコミュニティによって維持されている非公式のモダナイズ版フォークであり、オリジナルの Polr 開発者とは提携していません。**
> 上流プロジェクト [cydrobolt/polr](https://github.com/cydrobolt/polr) は 2021 年 1 月の `2.3.0b`（廃止された Lumen 5.1 およびサポート終了済みの PHP 7.x ベース）以降リリースが停止しています。polrX はシステム全体を最新の Laravel 13 と PHP 8.4+ へ刷新し、多言語対応（i18n）、モダンな 3 階層サーフェステーマシステム、専用タブによる 2 段階認証（2FA TOTP）、および包括的なセキュリティ強化を提供します。

---

## 目次

- [概要](#概要)
- [なぜ polrX なのか](#なぜ-polrx-なのか)
- [主な機能と対応状況](#主な機能と対応状況)
- [多言語サポート (i18n)](#多言語サポート-i18n)
- [モダンテーマシステム](#モダンテーマシステム)
- [動作要件](#動作要件)
- [クイックインストール](#クイックインストール)
- [アップグレード](#アップグレード)
- [REST API](#rest-api)
- [コントリビュートとセキュリティ報告](#コントリビュートとセキュリティ報告)
- [ライセンス](#ライセンス)
- [謝辞](#謝辞)

---

## 概要

**polrX** は、REST API を備えた軽量・高速なセルフホスト型オープンソース URL 短縮プラットフォームです。独自ドメインでブランド化された短縮 URL を運用し、すべてのリンクデータとクリック解析統計を完全に自己管理できます。

**Laravel 13** と **PHP 8.4+**（データベースは MySQL / MariaDB）で全面的に書き直されており、**オリジナルの Polr 2.x のデータベース構造および `/api/v2` エンドポイントと 100% 互換性**があります。

## なぜ polrX なのか

オリジナルの Polr は 2015 年の Lumen 5.1（Laravel 公式により開発終了）上で構築されており、セキュリティサポートが終了した PHP 7.x でしか動作しませんでした。

polrX は以下の進化によってこれらの課題を解決します：

1. **最新 Laravel 13 + PHP 8.4 / 8.5 アーキテクチャ**：廃止された Lumen 5.1 や古いサードパーティ製パッケージを排除し、軽量な独自サーバーサイドページネーションエンジン（`DataTablesHelper`）を内蔵。
2. **完全な多言語（i18n）アーキテクチャ**：**繁体字中国語（台湾）**、**英語**、**日本語**、**韓国語**の 4 言語を標準搭載。ナビゲーションバーのドロップダウン、管理画面の「言語」(`#language`) タブ、または `?lang=` クエリパラメータからいつでも即座に切り替え可能。
3. **3 階層サーフェス（3-Tier Surface）テーマシステム**：CSS 変数（`--polrx-bg`、`--polrx-surface`、`--polrx-surface-alt`）に基づく 6 種類のモダンテーマを内蔵し、ページ再読み込みなしで瞬時に切り替え可能。
4. **専用 2 段階認証（2FA TOTP）タブとセキュリティ強化**：
   - 管理画面左サイドバーに独立した「2 段階認証」(`#totp`) タブを配置し、1Password・Google Authenticator・Authy などの RFC 6238 認証アプリとローカル SVG QR コード生成に対応。
   - 定数時間での API キー比較（`hash_equals` によるタイミング攻撃対策）、DataTables ソート列のホワイトリスト検証（SQL インジェクション対策）、統計ページの JSON エスケープ（XSS 対策）、ログイン時のセッション ID 再生成、および各種エンドポイントのレート制限（Rate Limiting）を実装。
5. **シームレスな移行**：既存の Polr 2.x データベースから `php artisan migrate` を実行するだけで無停止アップグレードが可能。

## 主な機能と対応状況

| 機能モジュール | 状態 | 説明 |
| :--- | :---: | :--- |
| **Laravel 13 + PHP 8.4 / 8.5 コア刷新** | ✅ 完了 | Lumen を廃止し、標準の Laravel ルーティング・Middleware・Blade・Eloquent を採用 |
| **全ページ対応 i18n 多言語システム** | ✅ 完了 | `zh_TW`（デフォルト）、`en`、`ja`、`ko` の 4 言語に完全対応（Blade・JS・モーダル含む） |
| **独立した 2 段階認証（2FA TOTP）タブ** | ✅ 完了 | サイドバーの `#totp` タブにて TOTP セットアップ・有効化・パスワード確認付き無効化が可能 |
| **独立した言語設定（Language）タブ** | ✅ 完了 | サイドバーの `#language` タブおよび上部ナビゲーションバーの言語切替メニュー |
| **6 種類のモダン CSS 変数テーマ** | ✅ 完了 | 3 階層サーフェス設計により、リロードなしの即時プレビューとアカウント保存に対応 |
| **包括的なセキュリティ監査・強化** | ✅ 完了 | タイミング攻撃対策、SQL ホワイトリスト、XSS 対策、Rate Limiting、セッション保護 |
| **カスタムスラッグ＆シークレットリンク** | ✅ 完了 | Base32 / Base62 エンコード、カスタム短縮 URL 末尾、秘密キー付き URL に対応 |
| **リアルタイムクリック解析・統計** | ✅ 完了 | 時系列トラフィックグラフ、リファラー内訳、世界地図ヒートマップ表示 |
| **REST API (`/api/v2`)** | ✅ 完了 | 上流互換の単一／一括短縮、URL 照会、クリック統計取得 API |

## 多言語サポート (i18n)

[`lang/`](lang/) ディレクトリに 4 言語の翻訳辞書を内蔵しており、公開ページ、管理パネル、トースト通知、DataTables、Angular モーダルまで完全にローカライズされています：

| 言語コード | 表示名 | 翻訳ファイル |
| :---: | :--- | :--- |
| `zh_TW` | **繁體中文（台灣）** *(デフォルト)* | [`lang/zh_TW/polr.php`](lang/zh_TW/polr.php) |
| `en` | **English** | [`lang/en/polr.php`](lang/en/polr.php) |
| `ja` | **日本語** | [`lang/ja/polr.php`](lang/ja/polr.php) |
| `ko` | **한국어** | [`lang/ko/polr.php`](lang/ko/polr.php) |

- **言語の切り替え方法**：
  1. 画面上部ナビゲーションバー右側の言語ドロップダウンから選択。
  2. ログイン後、管理パネル左サイドバーの **「言語」(`#language`)** タブから言語カードをクリック。
  3. 任意の URL に `?lang=ja`、`?lang=en`、`?lang=ko`、`?lang=zh_TW` を付与。

## モダンテーマシステム

ログイン後、管理パネルの **「設定」(`#settings`)** タブから 6 種類のテーマをリアルタイムに切り替えられます（[`app/Helpers/ThemeHelper.php`](app/Helpers/ThemeHelper.php) および [`public/css/themes.css`](public/css/themes.css)）：

- **デフォルト (`default`)**：温かみのあるアイボリーとクラシックなセージグリーン
- **ミッドナイト (`midnight`)**：モダンなダークスレートとインディゴネオンのアクセント
- **オーシャン (`ocean`)**：落ち着いたディープネイビーとシアンの輝き
- **フォレスト (`forest`)**：自然なパイングリーンと柔らかなウォームクリーム
- **ローズ (`rose`)**：上品で優しいサクラピンクとベリーカラー
- **ノード (`nord`)**：ミニマルでクールな北欧風アークティックダーク

## 動作要件

- **PHP**：8.3 以上（**PHP 8.4** または **8.5** を推奨）、`pdo_mysql`、`mbstring`、`bcmath`、`curl`、`xml` 拡張モジュールが必要
- **データベース**：MySQL 5.7+ / 8.0+ または MariaDB 10.3+
- **Web サーバー**：Caddy、Nginx、または Apache（`mod_rewrite` 有効化）、ドキュメントルートを `public/` に設定
- **パッケージ管理**：[Composer](https://getcomposer.org/) 2.x

## クイックインストール

polrX は **「5 ステップ Web セットアップウィザード」** と **「CLI 対話型インストーラースクリプト (`./install.sh`)」** の 2 通りのインストール方法を提供しています（どちらもステップ 1 で **繁體中文 / English / 日本語 / 한국어** の 4 言語を選択可能）：

### 方法 A：5 ステップ Web セットアップウィザード（推奨）

1. **リポジトリのクローンと依存関係のインストール**：

   ```bash
   git clone https://github.com/tommy1616/polrX.git
   cd polrX
   composer install --no-dev -o
   ```

2. **ディレクトリ権限の設定**：
   Web サーバーのドキュメントルートを `public/` に向け、プロジェクトルート、`storage/`、`bootstrap/cache/` に書き込み権限を付与します：

   ```bash
   chown -R www-data:www-data . storage bootstrap/cache
   chmod -R 775 storage bootstrap/cache
   ```

3. **ブラウザから 5 ステップウィザードを実行**：
   ブラウザで `https://あなたのドメイン/` にアクセスします（`.env` が存在しない場合、自動的に暗号化キーを生成し `/setup` へリダイレクトされます）：
   - **ステップ 1 — 言語選択**：最初に **繁體中文（台灣） / English / 日本語 / 한국어** を選択します。
   - **ステップ 2 — 環境チェック**：PHP バージョン、必須拡張モジュール、書き込み権限を自動検証します。
   - **ステップ 3 — データベース設定**：MySQL / MariaDB の接続情報を入力し **「データベース接続テスト」** を実行します（`CREATE DATABASE IF NOT EXISTS` による自動作成に対応）。
   - **ステップ 4 — サイトと外観**：サイト名、ドメイン、デフォルトテーマのリアルタイムプレビュー、公開／会員限定／プライベートモードを選択します。
   - **ステップ 5 — 管理者作成＆インストール**：マイグレーション実行、管理者アカウント作成、および自動ログインが一括で完了します。

### 方法 B：CLI 対話型インストーラー（`./install.sh` — SSH / Docker 環境向け）

プロジェクトルートで `./install.sh` を実行すると、ターミナルの対話形式（または非対話オプション）でセットアップを完了できます：

```bash
git clone https://github.com/tommy1616/polrX.git
cd polrX

# 対話型インストーラー（最初に言語を選択）
./install.sh

# または非対話オプションによる 1 コマンド自動構築：
./install.sh --no-interaction \
  --lang=ja \
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
> `.env` ファイルは Git リポジトリにコミットせず、外部からブラウザで直接アクセスできないように保護してください。

## アップグレード

オリジナルの **Polr 2.x** または旧バージョンの **polrX** からのアップグレード手順：

```bash
# 1. データベースと .env ファイルをバックアップ
# 2. 最新コードを取得
git pull origin master

# 3. 依存パッケージを更新
composer install --no-dev -o

# 4. データベースマイグレーションを実行（TOTP・テーマ・言語カラムを自動追加）
php artisan migrate --force

# 5. キャッシュをクリア
php artisan optimize:clear
```

## REST API

オリジナルの Polr `/api/v2` エンドポイントと完全な互換性があります：

- `GET|POST /api/v2/action/shorten` — 単一 URL の短縮
- `GET|POST /api/v2/action/shorten_bulk` — 複数 URL の一括短縮
- `GET|POST /api/v2/action/lookup` — 短縮 URL の元 URL・クリック数照会
- `GET|POST /api/v2/data/link` — 短縮 URL の期間別クリック統計取得

## コントリビュートとセキュリティ報告

バグ報告、機能提案、Pull Request を歓迎します：

- **Issue・機能リクエスト**：[GitHub Issues](https://github.com/tommy1616/polrX/issues)
- Issue や Pull Request には `.env`、API キー、パスワード、TOTP シークレットなどの機密情報を絶対に貼り付けないでください。
- **セキュリティ脆弱性の報告**：セキュリティ上の問題を発見した場合は公開 Issue に書き込まず、GitHub の **Security → Report a vulnerability** から非公開でご報告ください。

## ライセンス

polrX は Polr の派生著作物であり、**GNU General Public License v2.0 またはそれ以降のバージョン（GPL-2.0-or-later）** の下で提供されています。全文は [LICENSE](LICENSE) をご覧ください。

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

## 謝辞

- オリジナルの Polr プロジェクトを開発された [Chaoyi Zha (cydrobolt)](https://github.com/cydrobolt) 氏およびすべての貢献者に深く感謝いたします。
- 翻訳、テスト、フィードバックにご協力いただいたオープンソースコミュニティの皆様に感謝いたします。
