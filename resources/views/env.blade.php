APP_ENV=production

# Set to true if debugging
APP_DEBUG=false

# 32-character or base64 key
APP_KEY="{!! $APP_KEY !!}"

# Your app's name (shown on interface)
APP_NAME="{!! addcslashes((string) $APP_NAME, '"\\') !!}"

# Protocol to access your app. e.g https://
APP_PROTOCOL="{!! $APP_PROTOCOL !!}"

# Your app's external address (e.g example.com)
APP_ADDRESS="{!! $APP_ADDRESS !!}"

# Your app's bootstrap stylesheet
APP_STYLESHEET="{!! $APP_STYLESHEET !!}"

# Set to today's date
POLR_GENERATED_AT="{!! $POLR_GENERATED_AT !!}"

# Set to true after running setup script
POLR_SETUP_RAN={!! $POLR_SETUP_RAN !!}

MAXMIND_LICENSE_KEY="{!! $MAXMIND_LICENSE_KEY !!}"

DB_CONNECTION=mysql
DB_HOST="{!! $DB_HOST !!}"
DB_PORT={!! $DB_PORT !!}
DB_DATABASE="{!! $DB_DATABASE !!}"
DB_USERNAME="{!! addcslashes((string) $DB_USERNAME, '"\\') !!}"
DB_PASSWORD="{!! addcslashes((string) $DB_PASSWORD, '"\\') !!}"

# Polr Settings
SETTING_PUBLIC_INTERFACE={!! $ST_PUBLIC_INTERFACE !!}
POLR_ALLOW_ACCT_CREATION={!! $POLR_ALLOW_ACCT_CREATION ? 'true' : 'false' !!}
POLR_ACCT_ACTIVATION={!! $POLR_ACCT_ACTIVATION ? 'true' : 'false' !!}
POLR_ACCT_CREATION_RECAPTCHA={!! $POLR_ACCT_CREATION_RECAPTCHA !!}
SETTING_SHORTEN_PERMISSION={!! $ST_SHORTEN_PERMISSION !!}
SETTING_INDEX_REDIRECT="{!! $ST_INDEX_REDIRECT !!}"
SETTING_REDIRECT_404={!! $ST_REDIRECT_404 !!}
SETTING_PASSWORD_RECOV={!! $ST_PASSWORD_RECOV !!}
SETTING_AUTO_API={!! $ST_AUTO_API !!}
SETTING_ANON_API={!! $ST_ANON_API !!}
SETTING_ANON_API_QUOTA={!! $ST_ANON_API_QUOTA !!}
SETTING_PSEUDORANDOM_ENDING={!! $ST_PSEUDOR_ENDING !!}
SETTING_ADV_ANALYTICS={!! $ST_ADV_ANALYTICS !!}
SETTING_RESTRICT_EMAIL_DOMAIN={!! $ST_RESTRICT_EMAIL_DOMAIN !!}
SETTING_ALLOWED_EMAIL_DOMAINS="{!! $ST_ALLOWED_EMAIL_DOMAINS !!}"

POLR_RECAPTCHA_SITE_KEY="{!! $POLR_RECAPTCHA_SITE_KEY !!}"
POLR_RECAPTCHA_SECRET_KEY="{!! $POLR_RECAPTCHA_SECRET !!}"

@if($MAIL_ENABLED)
MAIL_DRIVER=smtp
MAIL_HOST="{!! $MAIL_HOST !!}"
MAIL_PORT="{!! $MAIL_PORT !!}"
MAIL_USERNAME="{!! addcslashes((string) $MAIL_USERNAME, '"\\') !!}"
MAIL_PASSWORD="{!! addcslashes((string) $MAIL_PASSWORD, '"\\') !!}"
MAIL_FROM_ADDRESS="{!! $MAIL_FROM_ADDRESS !!}"
MAIL_FROM_NAME="{!! addcslashes((string) $MAIL_FROM_NAME, '"\\') !!}"
@endif

APP_LOCALE={!! $APP_LOCALE ?? 'zh_TW' !!}
APP_FALLBACK_LOCALE={!! $APP_LOCALE ?? 'zh_TW' !!}

CACHE_DRIVER=file
SESSION_DRIVER=file
QUEUE_DRIVER=sync

_API_KEY_LENGTH=15
_ANALYTICS_MAX_DAYS_DIFF=365
_PSEUDO_RANDOM_KEY_LENGTH=7

# Set to 32 or 62 -- do not touch after initial configuration
POLR_BASE={!! $ST_BASE !!}

# Do not touch
POLR_RELDATE="{!! env('VERSION_RELMONTH', 'Oct') !!} {!! env('VERSION_RELDAY', '24') !!}, {!! env('VERSION_RELYEAR', '2026') !!}"
POLR_VERSION="{!! env('VERSION', '2.3.0') !!}"
POLR_SECRET_BYTES=16

TMP_SETUP_AUTH_KEY="{!! $TMP_SETUP_AUTH_KEY !!}"
