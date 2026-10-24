<p align="center">
  <img src="public/favicon.svg" width="96" height="96" alt="polrX Logo" />
</p>

<h1 align="center">polrX</h1>

<p align="center">
  간결하고 빠르며 자체 호스팅이 가능한 오픈소스 URL 단축 플랫폼 —— <a href="https://github.com/cydrobolt/polr">Polr</a>의 Laravel 13 &amp; PHP 8.4+ 현대화 포크.
</p>

<p align="center">
  <a href="LICENSE"><img src="https://img.shields.io/badge/license-GPLv2%2B-blue.svg" alt="License: GPL v2+" /></a>
  <img src="https://img.shields.io/badge/PHP-8.4%2B-777BB4?logo=php&logoColor=white" alt="PHP 8.4+" />
  <img src="https://img.shields.io/badge/Laravel-13-FF2D20?logo=laravel&logoColor=white" alt="Laravel 13" />
  <a href="https://github.com/cydrobolt/polr"><img src="https://img.shields.io/badge/upstream-cydrobolt%2Fpolr-lightgrey.svg" alt="Upstream" /></a>
</p>

<p align="center">
  <strong>🌐 언어 / Language：</strong>
  <a href="README.md">繁體中文（台灣）</a> ·
  <a href="README.en.md">English</a> ·
  <a href="README.ja.md">日本語</a> ·
  <a href="README.ko.md"><strong>한국어</strong></a>
</p>

> [!NOTE]
> **polrX는 커뮤니티에서 유지보수하는 비공식 현대화 포크이며, 기존 Polr 원작자와는 무관합니다.**
> 업스트림 프로젝트 [cydrobolt/polr](https://github.com/cydrobolt/polr)는 2021년 1월 `2.3.0b` 버전(지원이 종료된 Lumen 5.1 및 PHP 7.x 기반) 이후 업데이트가 중단되었습니다. polrX는 전체 아키텍처를 최신 Laravel 13 및 PHP 8.4+로 재작성하고, 다국어(i18n) 지원, 3단계 표면(3-Tier Surface) 테마 엔진, 전용 2단계 인증(2FA TOTP) 탭 및 포괄적인 보안 강화를 제공합니다.

---

## 목차

- [소개](#소개)
- [왜 polrX인가요?](#왜-polrx인가요)
- [주요 기능 및 현황](#주요-기능-및-현황)
- [다국어 지원 (i18n)](#다국어-지원-i18n)
- [모던 테마 시스템](#모던-테마-시스템)
- [시스템 요구사항](#시스템-요구사항)
- [빠른 설치](#빠른-설치)
- [업그레이드](#업그레이드)
- [REST API](#rest-api)
- [기여 및 보안 취약점 제보](#기여-및-보안-취약점-제보)
- [라이선스](#라이선스)
- [감사의 글](#감사의-글)

---

## 소개

**polrX**는 강력한 REST API를 갖춘 가볍고 빠른 자체 호스팅(Self-hosted) 오픈소스 URL 단축 플랫폼입니다. 자체 도메인을 사용하여 브랜드화된 단축 링크를 만들고 모든 링크 데이터와 클릭 통계를 직접 소유하고 관리할 수 있습니다.

**Laravel 13**과 **PHP 8.4+**(MySQL / MariaDB 기반)로 완전히 새롭게 작성되었으며, **기존 Polr 2.x 데이터베이스 구조 및 `/api/v2` API 엔드포인트와 100% 호환**됩니다.

## 왜 polrX인가요?

기존 Polr는 2015년에 출시된 Lumen 5.1(Laravel 공식 지원 종료)로 작성되어 보안 지원이 종료된 PHP 7.x에서만 실행할 수 있었습니다.

polrX는 다음과 같은 현대화를 통해 이 문제를 해결합니다:

1. **최신 Laravel 13 + PHP 8.4 / 8.5 아키텍처**: 지원이 종료된 Lumen 5.1과 방치된 외부 패키지를 제거하고 자체 경량 서버사이드 페이지네이션 엔진(`DataTablesHelper`)을 내장했습니다.
2. **완전한 다국어(i18n) 아키텍처**: **번체 중국어(대만)**, **영어**, **일본어**, **한국어** 4개 언어를 기본 지원합니다. 상단 내비게이션 바 드롭다운, 관리자 패널의 전용 **언어(`#language`)** 탭 또는 `?lang=` 쿼리 파라미터를 통해 언제든지 즉시 전환할 수 있습니다.
3. **3단계 표면(3-Tier Surface) 테마 엔진**: CSS 변수(`--polrx-bg`, `--polrx-surface`, `--polrx-surface-alt`) 기반의 6가지 세련된 테마를 제공하며, 페이지 새로고침 없이 즉시 적용되고 사용자 계정에 저장됩니다.
4. **전용 2단계 인증(2FA TOTP) 탭 및 보안 강화**:
   - 관리자 왼쪽 사이드바에 독립된 **2단계 인증(`#totp`)** 탭을 제공하여 1Password, Google Authenticator, Authy 등 RFC 6238 인증 앱과 로컬 SVG QR 코드 생성을 지원합니다.
   - 상수 시간 API 키 비교(`hash_equals`를 통한 타이밍 공격 방지), DataTables 정렬 컬럼 화이트리스트 검증(SQL 인젝션 방지), 통계 페이지 JSON 안전 인코딩(XSS 방지), 로그인 시 세션 ID 재생성 및 주요 인증 엔드포인트 속도 제한(Rate Limiting)을 적용했습니다.
5. **무중단 호환 업그레이드**: 기존 Polr 2.x 데이터베이스에서 `php artisan migrate` 명령만으로 손쉽게 업그레이드할 수 있습니다.

## 주요 기능 및 현황

| 기능 모듈 | 상태 | 설명 |
| :--- | :---: | :--- |
| **Laravel 13 + PHP 8.4 / 8.5 코어 재작성** | ✅ 완료 | Lumen 제거, 표준 Laravel 라우팅·미들웨어·Blade·Eloquent 적용 |
| **전체 페이지 i18n 다국어 시스템** | ✅ 완료 | `zh_TW`(기본값), `en`, `ja`, `ko` 4개 언어 완벽 지원 (Blade, JS, 모달 포함) |
| **독립된 2단계 인증(2FA TOTP) 탭** | ✅ 완료 | 왼쪽 사이드바 전용 `#totp` 탭, 로컬 SVG QR 생성 및 비밀번호 확인 해제 지원 |
| **독립된 언어(Language) 설정 탭** | ✅ 완료 | 왼쪽 사이드바 전용 `#language` 탭 + 상단 내비게이션 바 언어 전환 드롭다운 |
| **6종 모던 CSS 변수 테마 시스템** | ✅ 완료 | 3단계 표면 계층 설계, 새로고침 없는 즉시 미리보기 및 계정 동기화 |
| **포괄적인 보안 감사 및 패치** | ✅ 완료 | 타이밍 공격 방어, SQL 화이트리스트, XSS 방어, Rate Limiting, 세션 보안 강화 |
| **커스텀 단축 주소 및 비밀 링크** | ✅ 완료 | Base32 / Base62 인코딩, 사용자 지정 단축 주소 및 비밀 키(Secret Key) 링크 지원 |
| **실시간 클릭 통계 및 시각화** | ✅ 완료 | 시간대별 트래픽 차트, 유입 경로(Referer) 분석 및 세계 지도 시각화 |
| **REST API (`/api/v2`)** | ✅ 완료 | 기존 API와 100% 호환되는 단일/대량 단축, 링크 조회 및 통계 API |

## 다국어 지원 (i18n)

[`lang/`](lang/) 디렉터리에 4개 언어 번역 사전이 내장되어 있으며, 모든 공개 페이지, 관리자 대시보드, 알림 메시지, DataTables 페이지네이션 및 Angular 모달이 현지화되어 있습니다:

| 언어 코드 | 언어 이름 | 번역 파일 경로 |
| :---: | :--- | :--- |
| `zh_TW` | **繁體中文（台灣）** *(기본값)* | [`lang/zh_TW/polr.php`](lang/zh_TW/polr.php) |
| `en` | **English** | [`lang/en/polr.php`](lang/en/polr.php) |
| `ja` | **日本語** | [`lang/ja/polr.php`](lang/ja/polr.php) |
| `ko` | **한국어** | [`lang/ko/polr.php`](lang/ko/polr.php) |

- **언어 전환 방법**:
  1. 상단 내비게이션 바 오른쪽의 언어 드롭다운 메뉴에서 선택합니다.
  2. 로그인 후 관리자 대시보드 왼쪽 사이드바의 **「언어」(`#language`)** 탭에서 언어 카드를 클릭합니다.
  3. 모든 URL 뒤에 `?lang=ko`, `?lang=en`, `?lang=ja` 또는 `?lang=zh_TW`를 추가합니다.

## 모던 테마 시스템

로그인 후 관리자 대시보드의 **「설정」(`#settings`)** 탭에서 6가지 기본 테마를 실시간으로 전환할 수 있습니다([`app/Helpers/ThemeHelper.php`](app/Helpers/ThemeHelper.php) 및 [`public/css/themes.css`](public/css/themes.css)):

- **클래식 라이트 (`default`)**: 깔끔하고 정돈된 기본 밝은 테마
- **미드나잇 다크 (`midnight`)**: 눈이 편안한 다크 모드와 인디고 포인트
- **오션 브리즈 (`ocean`)**: 상쾌한 아이스 블루 배경과 시안 포인트
- **웜 샌드 (`warm-sand`)**: 따뜻한 종이 질감의 아이보리와 앰버 포인트
- **에메랄드 포레스트 (`forest`)**: 자연스러운 세이지 민트 배경과 에메랄드 그린 포인트
- **로즈 쿼츠 (`rose`)**: 은은한 사쿠라 핑크 배경과 로즈 베리 포인트

## 시스템 요구사항

- **PHP**: 8.3 이상 (**PHP 8.4** 또는 **8.5** 권장), `pdo_mysql`, `mbstring`, `bcmath`, `curl`, `xml` 확장 모듈 필수
- **데이터베이스**: MySQL 5.7+ / 8.0+ 또는 MariaDB 10.3+
- **웹 서버**: Caddy, Nginx 또는 Apache(`mod_rewrite` 활성화), 웹 루트(Document Root)를 `public/`으로 설정
- **패키지 관리자**: [Composer](https://getcomposer.org/) 2.x

## 빠른 설치

polrX는 **「5단계 웹 설치 마법사」**와 **「CLI 대화형 설치 스크립트 (`./install.sh`)」** 두 가지 설치 방식을 제공합니다 (두 방식 모두 1단계에서 **繁體中文 / English / 日本語 / 한국어** 4개 언어 선택 지원):

### 방법 A: 5단계 웹 설치 마법사 (권장)

1. **소스 코드 클론 및 패키지 설치**:

   ```bash
   git clone https://github.com/tommy1616/polrX.git
   cd polrX
   composer install --no-dev -o
   ```

2. **디렉터리 권한 설정**:
   웹 서버의 Document Root를 `public/`으로 지정하고, 프로젝트 루트, `storage/` 및 `bootstrap/cache/` 디렉터리에 쓰기 권한을 부여합니다:

   ```bash
   chown -R www-data:www-data . storage bootstrap/cache
   chmod -R 775 storage bootstrap/cache
   ```

3. **브라우저에서 5단계 설치 마법사 진행**:
   브라우저에서 `https://귀하의-도메인/`에 접속합니다 (`.env` 파일이 없으면 자동으로 암호화 키를 초기화하고 `/setup` 설치 마법사로 이동합니다):
   - **1단계 — 언어 선택**: 가장 먼저 **繁體中文（台灣） / English / 日本語 / 한국어** 중 원하는 언어를 선택합니다.
   - **2단계 — 환경 점검**: PHP 버전, 필수 확장 모듈 및 디렉터리 쓰기 권한을 자동으로 확인합니다.
   - **3단계 — 데이터베이스 설정**: MySQL / MariaDB 연결 정보를 입력하고 **"데이터베이스 연결 테스트"**를 클릭합니다 (`CREATE DATABASE IF NOT EXISTS` 자동 생성 지원).
   - **4단계 — 사이트 및 테마**: 사이트 이름, 도메인, 실시간 테마 미리보기 및 공개/회원 전용/비공개 모드를 선택합니다.
   - **5단계 — 관리자 생성 및 설치**: 데이터베이스 마이그레이션, 관리자 계정 생성 및 자동 로그인이 한 번에 완료됩니다.

### 방법 B: CLI 대화형 설치 스크립트 (`./install.sh` — SSH / Docker 환경용)

프로젝트 루트에서 `./install.sh`를 실행하면 터미널 대화형 마법사(또는 비대화형 옵션)로 즉시 설치할 수 있습니다:

```bash
git clone https://github.com/tommy1616/polrX.git
cd polrX

# 대화형 터미널 설치 마법사 (1단계에서 언어 선택)
./install.sh

# 또는 비대화형 옵션을 통한 원클릭 자동 배포:
./install.sh --no-interaction \
  --lang=ko \
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
> `.env` 파일은 절대 Git 저장소에 커밋하지 마시고, 외부에서 웹으로 직접 접근할 수 없도록 보호하세요.

## 업그레이드

기존 **Polr 2.x** 또는 이전 버전의 **polrX**에서 업그레이드하는 방법:

```bash
# 1. 데이터베이스 및 .env 파일 백업
# 2. 최신 코드 가져오기
git pull origin master

# 3. Composer 패키지 업데이트
composer install --no-dev -o

# 4. 데이터베이스 마이그레이션 실행 (TOTP, 테마, 언어 컬럼 자동 추가)
php artisan migrate --force

# 5. 캐시 정리
php artisan optimize:clear
```

## REST API

기존 Polr `/api/v2` 엔드포인트와 완벽히 호환됩니다:

- `GET|POST /api/v2/action/shorten` — 단일 URL 단축
- `GET|POST /api/v2/action/shorten_bulk` — 다중 URL 대량 단축
- `GET|POST /api/v2/action/lookup` — 단축 URL 원본 주소 및 클릭 정보 조회
- `GET|POST /api/v2/data/link` — 단축 URL 기간별 클릭 분석 데이터 조회

## 기여 및 보안 취약점 제보

버그 리포트, 기능 제안 및 Pull Request를 환영합니다:

- **이슈 및 기능 제안**: [GitHub Issues](https://github.com/tommy1616/polrX/issues)
- 이슈나 Pull Request에 `.env`, API 키, 비밀번호, TOTP 시크릿 등 민감한 정보를 절대 포함하지 마세요.
- **보안 취약점 제보**: 보안 문제를 발견한 경우 공개 이슈에 게시하지 마시고, 본 저장소의 **Security → Report a vulnerability**를 통해 비공개로 제보해 주세요.

## 라이선스

polrX는 Polr의 파생 저작물로서 **GNU General Public License v2.0 또는 이후 버전(GPL-2.0-or-later)**에 따라 배포됩니다. 전문은 [LICENSE](LICENSE)를 참고하세요.

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

## 감사의 글

- 원작 프로젝트인 Polr를 개발한 [Chaoyi Zha (cydrobolt)](https://github.com/cydrobolt)와 모든 업스트림 기여자분들께 감사드립니다.
- 번역, 테스트 및 피드백에 참여해 주신 오픈소스 커뮤니티 여러분께 감사드립니다.
