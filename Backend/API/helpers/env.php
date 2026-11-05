<?php
// ============================================================
// helpers/env.php
// Pembaca environment variable + penentu mode (lokal / hosting)
// Dipakai oleh config.php (web) dan scripts/init_db.php (CLI)
// ============================================================

if (!function_exists('env')) {
    function env($key, $default = null) {
        $v = getenv($key);
        if ($v === false || $v === '') {
            return $default;
        }
        return $v;
    }
}

if (!function_exists('envBool')) {
    function envBool($key, $default = false) {
        $v = env($key, null);
        if ($v === null) return (bool)$default;
        return filter_var($v, FILTER_VALIDATE_BOOLEAN);
    }
}

/**
 * TRUE jika backend berjalan di hosting (Railway / Docker), FALSE jika lokal (XAMPP/Laragon).
 * Bisa dipaksa lewat APP_ENV=production | local.
 */
if (!function_exists('isHosted')) {
    function isHosted() {
        $appEnv = strtolower((string)env('APP_ENV', ''));
        if ($appEnv === 'production' || $appEnv === 'prod') return true;
        if ($appEnv === 'local' || $appEnv === 'development') return false;

        return (bool)(
            env('RAILWAY_ENVIRONMENT') || env('RAILWAY_PROJECT_ID') ||
            env('MYSQL_URL') || env('MYSQLHOST') || env('DB_HOST')
        );
    }
}

/**
 * Ambil konfigurasi database.
 * Urutan prioritas:
 *   1) MYSQL_URL / DATABASE_URL  (format mysql://user:pass@host:port/dbname)  -> Railway
 *   2) MYSQLHOST, MYSQLUSER, MYSQLPASSWORD, MYSQLDATABASE, MYSQLPORT           -> Railway
 *   3) DB_HOST, DB_USER, DB_PASS, DB_NAME, DB_PORT                             -> bebas
 *   4) Default lokal (XAMPP/Laragon): 127.0.0.1 / root / (kosong) / vincent_sqdb
 */
if (!function_exists('getDbConfig')) {
    function getDbConfig() {
        $url = env('MYSQL_URL') ?: env('DATABASE_URL');
        if ($url && ($p = parse_url($url)) && !empty($p['host'])) {
            return [
                'host'   => $p['host'],
                'user'   => isset($p['user']) ? urldecode($p['user']) : 'root',
                'pass'   => isset($p['pass']) ? urldecode($p['pass']) : '',
                'name'   => isset($p['path']) ? ltrim($p['path'], '/') : 'railway',
                'port'   => (int)($p['port'] ?? 3306),
                'source' => 'MYSQL_URL',
            ];
        }

        if (env('MYSQLHOST')) {
            return [
                'host'   => env('MYSQLHOST'),
                'user'   => env('MYSQLUSER', 'root'),
                'pass'   => env('MYSQLPASSWORD', ''),
                'name'   => env('MYSQLDATABASE', 'railway'),
                'port'   => (int)env('MYSQLPORT', 3306),
                'source' => 'MYSQLHOST',
            ];
        }

        if (env('DB_HOST')) {
            return [
                'host'   => env('DB_HOST'),
                'user'   => env('DB_USER', 'root'),
                'pass'   => env('DB_PASS', ''),
                'name'   => env('DB_NAME', 'vincent_sqdb'),
                'port'   => (int)env('DB_PORT', 3306),
                'source' => 'DB_HOST',
            ];
        }

        return [
            'host'   => '127.0.0.1',
            'user'   => 'root',
            'pass'   => '',
            'name'   => 'vincent_sqdb',
            'port'   => 3306,
            'source' => 'default-lokal',
        ];
    }
}
?>
