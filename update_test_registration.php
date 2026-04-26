<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/registration_repository.php';

function request_value(string $key, string $default): string
{
    $value = isset($_GET[$key]) ? trim((string) $_GET[$key]) : '';

    if ($value === '') {
        return $default;
    }

    return $value;
}

try {
    $registrationId = isset($_GET['id']) ? trim((string) $_GET['id']) : '';
    $registration = $registrationId !== '' ? volleycup_find_registration($registrationId) : volleycup_find_latest_registration();

    if ($registration === null) {
        http_response_code(404);
        header('Content-Type: text/plain; charset=UTF-8');
        echo 'No registration found to update.';
        exit;
    }

    $allowedCategories = ['men', 'women', 'mixed'];
    $category = request_value('category', 'women');
    if (!in_array($category, $allowedCategories, true)) {
        $category = 'women';
    }

    $servicesInput = request_value('services', 'transport');
    $services = array_values(array_filter(array_map('trim', explode(',', $servicesInput))));

    $updated = volleycup_update_registration($registration['id'], [
        'university_name' => request_value('uni', 'VolleyCup Updated University'),
        'captain' => request_value('captain', 'Updated Captain'),
        'roster_size' => max(6, min(15, (int) request_value('roster', '10'))),
        'email' => request_value('email', 'updated-registration@volleycup.local'),
        'phone' => request_value('phone', '+216 33 333 333'),
        'category' => $category,
        'services' => $services,
        'comments' => request_value('comments', 'Updated by update_test_registration.php'),
        'status' => 'confirmed',
        'submitted_at' => $registration['submitted_at'],
        'cancelled_at' => null,
    ]);

    if ($updated === null) {
        http_response_code(404);
        header('Content-Type: text/plain; charset=UTF-8');
        echo 'The registration could not be updated.';
        exit;
    }

    header('Location: success.php?id=' . rawurlencode($updated['id']));
    exit;
} catch (Throwable $exception) {
    http_response_code(500);
    header('Content-Type: text/plain; charset=UTF-8');
    echo 'Could not update the test registration.';
}
