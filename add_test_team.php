<?php
declare(strict_types=1);

$dataFile = __DIR__ . '/data/registrations.json';

function load_registrations(string $path): array
{
    if (!file_exists($path)) {
        return [];
    }

    $json = file_get_contents($path);

    if ($json === false || trim($json) === '') {
        return [];
    }

    $decoded = json_decode($json, true);

    return is_array($decoded) ? $decoded : [];
}

function save_registrations(string $path, array $registrations): void
{
    $json = json_encode($registrations, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

    if ($json === false) {
        throw new RuntimeException('Failed to encode registration data.');
    }

    $result = file_put_contents($path, $json . PHP_EOL, LOCK_EX);

    if ($result === false) {
        throw new RuntimeException('Failed to save registration data.');
    }
}

try {
    $registrations = load_registrations($dataFile);
    $registrationId = bin2hex(random_bytes(8));

    $registrations[] = [
        'id' => $registrationId,
        'university_name' => 'VolleyCup Test University',
        'captain' => 'Test Captain',
        'roster_size' => 8,
        'email' => 'test-team@volleycup.local',
        'phone' => '+216 11 111 111',
        'category' => 'mixed',
        'services' => ['practice'],
        'comments' => 'Created by add_test_team.php',
        'status' => 'cancelled',
        'submitted_at' => date(DATE_ATOM),
        'cancelled_at' => date(DATE_ATOM),
    ];

    save_registrations($dataFile, $registrations);

    header('Location: success.php?id=' . rawurlencode($registrationId));
    exit;
} catch (Throwable $exception) {
    http_response_code(500);
    header('Content-Type: text/plain; charset=UTF-8');
    echo 'Could not create the test team.';
}
