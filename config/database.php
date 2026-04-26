<?php
declare(strict_types=1);

function volleycup_env(string $key, string $default): string
{
    $value = getenv($key);

    if ($value === false || $value === '') {
        return $default;
    }

    return $value;
}

function volleycup_database(): PDO
{
    static $pdo = null;

    if ($pdo instanceof PDO) {
        return $pdo;
    }

    $host = volleycup_env('VOLLEYCUP_DB_HOST', '127.0.0.1');
    $port = volleycup_env('VOLLEYCUP_DB_PORT', '3306');
    $name = volleycup_env('VOLLEYCUP_DB_NAME', 'volleycup4.0');
    $user = volleycup_env('VOLLEYCUP_DB_USER', 'root');
    $password = volleycup_env('VOLLEYCUP_DB_PASS', '');

    $dsn = sprintf('mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4', $host, $port, $name);

    $pdo = new PDO($dsn, $user, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);

    $pdo->exec(
        'CREATE TABLE IF NOT EXISTS registrations (
            id VARCHAR(32) PRIMARY KEY,
            university_name VARCHAR(255) NOT NULL,
            team_name VARCHAR(255) NOT NULL,
            captain VARCHAR(255) NOT NULL,
            roster_size TINYINT UNSIGNED NOT NULL,
            email VARCHAR(255) NOT NULL,
            phone VARCHAR(50) NOT NULL,
            category VARCHAR(20) NOT NULL,
            services_json TEXT NOT NULL,
            comments TEXT NOT NULL,
            status VARCHAR(20) NOT NULL DEFAULT "confirmed",
            submitted_at DATETIME NOT NULL,
            cancelled_at DATETIME NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci'
    );

    return $pdo;
}
