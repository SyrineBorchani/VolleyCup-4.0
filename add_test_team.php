<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/registration_repository.php';

try {
    $registrationId = volleycup_create_registration([
        'university_name' => 'VolleyCup Test University',
        'team_name'  => 'Test Spikers',
        'captain' => 'Test Captain',
        'roster_size' => 8,
        'email' => 'test-team@volleycup.local',
        'phone' => '+216 11 111 111',
        'category' => 'mixed',
        'services' => ['practice'],
        'comments' => 'Created by add_test_team.php',
        'team_photo' => null,
        'status' => 'confirmed',
        'submitted_at' => date('Y-m-d H:i:s'),
        'cancelled_at' => date('Y-m-d H:i:s'),
    ]);

    header('Location: success.php?id=' . rawurlencode($registrationId));
    exit;
} catch (Throwable $exception) {
    http_response_code(500);
    header('Content-Type: text/plain; charset=UTF-8');
    echo 'Could not create the test team.';
}
