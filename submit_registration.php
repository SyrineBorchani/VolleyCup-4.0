<?php
declare(strict_types=1);

header('Content-Type: application/json; charset=UTF-8');

$dataFile = __DIR__ . '/data/registrations.json';

function normalize_text(string $value): string
{
    return trim(preg_replace('/\s+/', ' ', $value) ?? '');
}

function string_length(string $value): int
{
    return function_exists('mb_strlen') ? mb_strlen($value) : strlen($value);
}

function validate_registration(array $input): array
{
    $errors = [];

    if ($input['uniName'] === '' || string_length($input['uniName']) < 3) {
        $errors['uniName'] = 'Please enter a university name with at least 3 characters.';
    }

    if ($input['captain'] === '' || string_length($input['captain']) < 3) {
        $errors['captain'] = 'Please enter a captain name with at least 3 characters.';
    }

    if ($input['roster'] === '' || filter_var($input['roster'], FILTER_VALIDATE_INT) === false) {
        $errors['roster'] = 'Please enter a valid roster size.';
    } else {
        $rosterSize = (int) $input['roster'];

        if ($rosterSize < 6 || $rosterSize > 15) {
            $errors['roster'] = 'Roster size must be between 6 and 15 players.';
        }
    }

    if ($input['email'] === '' || filter_var($input['email'], FILTER_VALIDATE_EMAIL) === false) {
        $errors['email'] = 'Please enter a valid email address.';
    }

    if ($input['phone'] === '' || preg_match('/^[+\d\s()\-]{8,20}$/', $input['phone']) !== 1) {
        $errors['phone'] = 'Please enter a valid phone number.';
    }

    if (!in_array($input['category'], ['men', 'women', 'mixed'], true)) {
        $errors['category'] = 'Please choose a valid team category.';
    }

    $allowedServices = ['practice', 'transport', 'photos'];
    foreach ($input['services'] as $service) {
        if (!in_array($service, $allowedServices, true)) {
            $errors['services'] = 'One of the selected services is invalid.';
            break;
        }
    }

    if (string_length($input['comments']) > 600) {
        $errors['comments'] = 'Comments must stay under 600 characters.';
    }

    return $errors;
}

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

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'ok' => false,
        'message' => 'This endpoint only accepts POST requests.',
    ]);
    exit;
}

$formData = [
    'uniName' => normalize_text($_POST['uniName'] ?? ''),
    'captain' => normalize_text($_POST['captain'] ?? ''),
    'roster' => trim((string) ($_POST['roster'] ?? '')),
    'email' => trim((string) ($_POST['email'] ?? '')),
    'phone' => normalize_text($_POST['phone'] ?? ''),
    'category' => (string) ($_POST['category'] ?? 'men'),
    'services' => array_values(array_filter(
        array_map('strval', (array) ($_POST['services'] ?? []))
    )),
    'comments' => trim((string) ($_POST['comments'] ?? '')),
];

$errors = validate_registration($formData);

if ($errors !== []) {
    http_response_code(422);
    echo json_encode([
        'ok' => false,
        'message' => 'Please correct the highlighted fields.',
        'errors' => $errors,
    ]);
    exit;
}

try {
    $registrations = load_registrations($dataFile);

    $registrationId = bin2hex(random_bytes(8));
    $registrations[] = [
        'id' => $registrationId,
        'university_name' => $formData['uniName'],
        'captain' => $formData['captain'],
        'roster_size' => (int) $formData['roster'],
        'email' => $formData['email'],
        'phone' => $formData['phone'],
        'category' => $formData['category'],
        'services' => $formData['services'],
        'comments' => $formData['comments'],
        'status' => 'confirmed',
        'submitted_at' => date(DATE_ATOM),
    ];

    save_registrations($dataFile, $registrations);

    echo json_encode([
        'ok' => true,
        'registrationId' => $registrationId,
    ]);
} catch (Throwable $exception) {
    http_response_code(500);
    echo json_encode([
        'ok' => false,
        'message' => 'We could not save your registration right now. Please try again.',
    ]);
}
