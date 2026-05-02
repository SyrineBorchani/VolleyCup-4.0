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
    static $conn = null;

    if ($conn instanceof PDO) {
        return $conn;
    }

    $servername = volleycup_env('VOLLEYCUP_DB_HOST', '127.0.0.1');
    $port = volleycup_env('VOLLEYCUP_DB_PORT', '3306');
    $dbname = volleycup_env('VOLLEYCUP_DB_NAME', 'volleycup4.0');
    $password = volleycup_env('VOLLEYCUP_DB_PASS', '');
    $username = volleycup_env('VOLLEYCUP_DB_USER', 'root');

    $dsn = sprintf(
        'mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4',
        $servername,
        $port,
        $dbname
    );

    // Create connection in the same PDO style shown in the course slides.
    $conn = new PDO($dsn, $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

    volleycup_ensure_registrations_table($conn);

    return $conn;
}

function volleycup_ensure_registrations_table(PDO $pdo): void
{
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
            team_photo VARCHAR(255) NULL,
            status VARCHAR(20) NOT NULL DEFAULT "confirmed",
            submitted_at DATETIME NOT NULL,
            cancelled_at DATETIME NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci'
    );

    $teamNameColumnCheck = $pdo->query("SHOW COLUMNS FROM registrations LIKE 'team_name'");
    $teamNameColumn = $teamNameColumnCheck === false ? false : $teamNameColumnCheck->fetch();

    if (!is_array($teamNameColumn)) {
        $pdo->exec("ALTER TABLE registrations ADD COLUMN team_name VARCHAR(255) NOT NULL DEFAULT '' AFTER university_name");
    }

    $columnCheck = $pdo->query("SHOW COLUMNS FROM registrations LIKE 'team_photo'");
    $teamPhotoColumn = $columnCheck === false ? false : $columnCheck->fetch();

    if (!is_array($teamPhotoColumn)) {
        $pdo->exec('ALTER TABLE registrations ADD COLUMN team_photo VARCHAR(255) NULL AFTER comments');
    }
}
