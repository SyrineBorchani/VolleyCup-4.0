<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/database.php';

function volleycup_decode_registration(array $row): array
{
    $services = json_decode((string) ($row['services_json'] ?? '[]'), true);

    return [
        'id' => (string) ($row['id'] ?? ''),
        'university_name' => (string) ($row['university_name'] ?? ''),
        'team_name' => (string) ($row['team_name'] ?? ''),
        'captain' => (string) ($row['captain'] ?? ''),
        'roster_size' => (int) ($row['roster_size'] ?? 0),
        'email' => (string) ($row['email'] ?? ''),
        'phone' => (string) ($row['phone'] ?? ''),
        'category' => (string) ($row['category'] ?? ''),
        'services' => is_array($services) ? array_values(array_map('strval', $services)) : [],
        'comments' => (string) ($row['comments'] ?? ''),
        'status' => (string) ($row['status'] ?? 'confirmed'),
        'submitted_at' => (string) ($row['submitted_at'] ?? ''),
        'cancelled_at' => isset($row['cancelled_at']) ? (string) $row['cancelled_at'] : null,
    ];
}

function volleycup_find_registration(string $registrationId): ?array
{
    $statement = volleycup_database()->prepare(
        'SELECT id, university_name, team_name, captain, roster_size, email, phone, category, services_json, comments, status, submitted_at, cancelled_at
         FROM registrations
         WHERE id = :id
         LIMIT 1'
    );
    $statement->execute(['id' => $registrationId]);
    $row = $statement->fetch();

    if (!is_array($row)) {
        return null;
    }

    return volleycup_decode_registration($row);
}

function volleycup_create_registration(array $data): string
{
    $registrationId = bin2hex(random_bytes(8));
    $submittedAt = date('Y-m-d H:i:s');

    $statement = volleycup_database()->prepare(
        'INSERT INTO registrations (
            id,
            university_name,
            team_name,
            captain,
            roster_size,
            email,
            phone,
            category,
            services_json,
            comments,
            status,
            submitted_at,
            cancelled_at
        ) VALUES (
            :id,
            :university_name,
            :team_name,
            :captain,
            :roster_size,
            :email,
            :phone,
            :category,
            :services_json,
            :comments,
            :status,
            :submitted_at,
            :cancelled_at
        )'
    );

    $statement->execute([
        'id' => $registrationId,
        'university_name' => $data['university_name'],
        'team_name' => $data['team_name'],
        'captain' => $data['captain'],
        'roster_size' => $data['roster_size'],
        'email' => $data['email'],
        'phone' => $data['phone'],
        'category' => $data['category'],
        'services_json' => json_encode($data['services'], JSON_UNESCAPED_SLASHES),
        'comments' => $data['comments'],
        'status' => $data['status'] ?? 'confirmed',
        'submitted_at' => $data['submitted_at'] ?? $submittedAt,
        'cancelled_at' => $data['cancelled_at'] ?? null,
    ]);

    return $registrationId;
}

function volleycup_cancel_registration(string $registrationId): ?array
{
    $registration = volleycup_find_registration($registrationId);

    if ($registration === null || $registration['status'] === 'cancelled') {
        return $registration;
    }

    $cancelledAt = date('Y-m-d H:i:s');
    $statement = volleycup_database()->prepare(
        'UPDATE registrations
         SET status = :status, cancelled_at = :cancelled_at
         WHERE id = :id'
    );
    $statement->execute([
        'status' => 'cancelled',
        'cancelled_at' => $cancelledAt,
        'id' => $registrationId,
    ]);

    return volleycup_find_registration($registrationId);
}

function volleycup_find_latest_registration(): ?array
{
    $statement = volleycup_database()->query(
        'SELECT id, university_name, team_name, captain, roster_size, email, phone, category, services_json, comments, status, submitted_at, cancelled_at
         FROM registrations
         ORDER BY submitted_at DESC, id DESC
         LIMIT 1'
    );
    $row = $statement->fetch();

    if (!is_array($row)) {
        return null;
    }

    return volleycup_decode_registration($row);
}

function volleycup_update_registration(string $registrationId, array $data): ?array
{
    $registration = volleycup_find_registration($registrationId);

    if ($registration === null) {
        return null;
    }

    $statement = volleycup_database()->prepare(
        'UPDATE registrations
         SET university_name = :university_name,
             team_name = :team_name,
             captain = :captain,
             roster_size = :roster_size,
             email = :email,
             phone = :phone,
             category = :category,
             services_json = :services_json,
             comments = :comments,
             status = :status,
             submitted_at = :submitted_at,
             cancelled_at = :cancelled_at
         WHERE id = :id'
    );

    $statement->execute([
        'id' => $registrationId,
        'university_name' => $data['university_name'],
        'team_name' => $data['team_name'],
        'captain' => $data['captain'],
        'roster_size' => $data['roster_size'],
        'email' => $data['email'],
        'phone' => $data['phone'],
        'category' => $data['category'],
        'services_json' => json_encode($data['services'], JSON_UNESCAPED_SLASHES),
        'comments' => $data['comments'],
        'status' => $data['status'],
        'submitted_at' => $data['submitted_at'],
        'cancelled_at' => $data['cancelled_at'],
    ]);

    return volleycup_find_registration($registrationId);
}

function volleycup_delete_registration(string $registrationId): bool
{
    $statement = volleycup_database()->prepare(
        'DELETE FROM registrations
         WHERE id = :id'
    );
    $statement->execute(['id' => $registrationId]);

    return $statement->rowCount() > 0;
}
