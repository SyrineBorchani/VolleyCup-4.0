<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/registration_repository.php';

try {
    $registrationId = isset($_GET['id']) ? trim((string) $_GET['id']) : '';
    $registration = $registrationId !== '' ? volleycup_find_registration($registrationId) : volleycup_find_latest_registration();

    if ($registration === null) {
        http_response_code(404);
        header('Content-Type: text/plain; charset=UTF-8');
        echo 'No registration found to delete.';
        exit;
    }

    $deleted = volleycup_delete_registration($registration['id']);

    if (!$deleted) {
        http_response_code(404);
        header('Content-Type: text/plain; charset=UTF-8');
        echo 'The registration could not be deleted.';
        exit;
    }

    header('Content-Type: text/plain; charset=UTF-8');
    echo 'Deleted registration: ' . $registration['id'];
} catch (Throwable $exception) {
    http_response_code(500);
    header('Content-Type: text/plain; charset=UTF-8');
    echo 'Could not delete the test registration.';
}
