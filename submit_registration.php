<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/registration_repository.php';

header('Content-Type: application/json; charset=UTF-8');

function normalize_text(string $value): string
{
    return trim(preg_replace('/\s+/', ' ', $value) ?? '');
}

function string_length(string $value): int
{
    return function_exists('mb_strlen') ? mb_strlen($value) : strlen($value);
}

function validate_team_photo(?array $file): ?string
{
    if (!is_array($file) || !isset($file['error'])) {
        return null;
    }

    $errorCode = (int) $file['error'];

    if ($errorCode === UPLOAD_ERR_NO_FILE) {
        return null;
    }

    if ($errorCode !== UPLOAD_ERR_OK) {
        return 'The team photo upload could not be completed.';
    }

    $tmpName = isset($file['tmp_name']) ? (string) $file['tmp_name'] : '';

    if ($tmpName === '' || !is_uploaded_file($tmpName)) {
        return 'The uploaded team photo is invalid.';
    }

    if ((int) ($file['size'] ?? 0) > 3 * 1024 * 1024) {
        return 'Team photo size must stay under 3 MB.';
    }

    $mimeType = mime_content_type($tmpName);
    $allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];

    if (!in_array($mimeType, $allowedTypes, true)) {
        return 'Please upload a JPG, PNG, or WEBP team photo.';
    }

    return null;
}

function store_team_photo(array $file): string
{
    $uploadDirectory = __DIR__ . '/uploads/team-photos';

    if (!is_dir($uploadDirectory) && !mkdir($uploadDirectory, 0777, true) && !is_dir($uploadDirectory)) {
        throw new RuntimeException('Could not create the team photo upload directory.');
    }

    $mimeToExtension = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/webp' => 'webp',
    ];
    $tmpName = (string) $file['tmp_name'];
    $mimeType = mime_content_type($tmpName);
    $extension = $mimeToExtension[$mimeType] ?? null;

    if ($extension === null) {
        throw new RuntimeException('Unsupported team photo format.');
    }

    $filename = 'team-' . bin2hex(random_bytes(8)) . '.' . $extension;
    $destination = $uploadDirectory . '/' . $filename;

    if (!move_uploaded_file($tmpName, $destination)) {
        throw new RuntimeException('Could not save the uploaded team photo.');
    }

    return 'uploads/team-photos/' . $filename;
}

function delete_team_photo_file(?string $relativePath): void
{
    if ($relativePath === null || $relativePath === '') {
        return;
    }

    $fullPath = __DIR__ . '/' . str_replace('/', DIRECTORY_SEPARATOR, $relativePath);

    if (is_file($fullPath)) {
        @unlink($fullPath);
    }
}

function validate_registration(array $input, ?array $teamPhoto): array
{
    $errors = [];

    if ($input['uniName'] === '' || string_length($input['uniName']) < 3) {
        $errors['uniName'] = 'Please enter a university name with at least 3 characters.';
    }
    if ($input['teamName'] === '' || string_length($input['teamName']) < 3) {
    $errors['teamName'] = 'Please enter a team name with at least 3 characters.';
    } elseif (string_length($input['teamName']) > 30) {
        $errors['teamName'] = 'Team name must be under 30 characters.';
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

    $teamPhotoError = validate_team_photo($teamPhoto);
    if ($teamPhotoError !== null) {
        $errors['teamPhoto'] = $teamPhotoError;
    }

    return $errors;
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
    'teamName' => normalize_text($_POST['teamName'] ?? ''),
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

$teamPhoto = isset($_FILES['teamPhoto']) && is_array($_FILES['teamPhoto']) ? $_FILES['teamPhoto'] : null;
$errors = validate_registration($formData, $teamPhoto);

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
    $storedTeamPhoto = validate_team_photo($teamPhoto) === null && is_array($teamPhoto) && (int) ($teamPhoto['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_OK
        ? store_team_photo($teamPhoto)
        : null;

    $registrationId = volleycup_create_registration([
        'university_name' => $formData['uniName'],
        'team_name' => $formData['teamName'],
        'captain' => $formData['captain'],
        'roster_size' => (int) $formData['roster'],
        'email' => $formData['email'],
        'phone' => $formData['phone'],
        'category' => $formData['category'],
        'services' => $formData['services'],
        'comments' => $formData['comments'],
        'team_photo' => $storedTeamPhoto,
        'status' => 'confirmed',
    ]);

    echo json_encode([
        'ok' => true,
        'registrationId' => $registrationId,
    ]);
} catch (Throwable $exception) {
    if (isset($storedTeamPhoto)) {
        delete_team_photo_file($storedTeamPhoto);
    }

    http_response_code(500);
    echo json_encode([
        'ok' => false,
        'message' => 'We could not save your registration right now. Please try again.',
    ]);
}
