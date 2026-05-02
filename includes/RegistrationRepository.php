<?php
declare(strict_types=1);

require_once __DIR__ . '/Registration.php';

final class RegistrationRepository
{
    public function __construct(private readonly PDO $pdo)
    {
    }

    public function find(string $registrationId): ?Registration
    {
        $statement = $this->pdo->prepare(
            'SELECT id, university_name, team_name, captain, roster_size, email, phone, category, services_json, comments, team_photo, status, submitted_at, cancelled_at
             FROM registrations
             WHERE id = :id
             LIMIT 1'
        );
        $statement->execute(['id' => $registrationId]);
        $row = $statement->fetch();

        return is_array($row) ? Registration::fromDatabaseRow($row) : null;
    }

    public function create(array $data): string
    {
        $registrationId = bin2hex(random_bytes(8));
        $submittedAt = isset($data['submitted_at']) && $data['submitted_at'] !== ''
            ? (string) $data['submitted_at']
            : date('Y-m-d H:i:s');

        $statement = $this->pdo->prepare(
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
                team_photo,
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
                :team_photo,
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
            'team_photo' => $data['team_photo'] ?? null,
            'status' => $data['status'] ?? 'confirmed',
            'submitted_at' => $submittedAt,
            'cancelled_at' => $data['cancelled_at'] ?? null,
        ]);

        return $registrationId;
    }

    public function cancel(string $registrationId): ?Registration
    {
        $registration = $this->find($registrationId);

        if ($registration === null || $registration->status === 'cancelled') {
            return $registration;
        }

        $statement = $this->pdo->prepare(
            'UPDATE registrations
             SET status = :status, cancelled_at = :cancelled_at
             WHERE id = :id'
        );
        $statement->execute([
            'status' => 'cancelled',
            'cancelled_at' => date('Y-m-d H:i:s'),
            'id' => $registrationId,
        ]);

        return $this->find($registrationId);
    }

    public function findLatest(): ?Registration
    {
        $statement = $this->pdo->query(
            'SELECT id, university_name, team_name, captain, roster_size, email, phone, category, services_json, comments, team_photo, status, submitted_at, cancelled_at
             FROM registrations
             ORDER BY submitted_at DESC, id DESC
             LIMIT 1'
        );
        $row = $statement->fetch();

        return is_array($row) ? Registration::fromDatabaseRow($row) : null;
    }

    public function findAll(bool $includeCancelled = true): array
    {
        $sql = 'SELECT id, university_name, team_name, captain, roster_size, email, phone, category, services_json, comments, team_photo, status, submitted_at, cancelled_at
                FROM registrations';

        if (!$includeCancelled) {
            $sql .= " WHERE status <> 'cancelled'";
        }

        $sql .= ' ORDER BY submitted_at DESC, id DESC';

        $statement = $this->pdo->query($sql);
        $rows = $statement->fetchAll();

        return array_values(array_map(
            static fn(array $row): Registration => Registration::fromDatabaseRow($row),
            is_array($rows) ? $rows : []
        ));
    }

    public function update(string $registrationId, array $data): ?Registration
    {
        $registration = $this->find($registrationId);

        if ($registration === null) {
            return null;
        }

        $statement = $this->pdo->prepare(
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
                 team_photo = :team_photo,
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
            'team_photo' => array_key_exists('team_photo', $data) ? $data['team_photo'] : $registration->teamPhotoUrl(),
            'status' => $data['status'],
            'submitted_at' => $data['submitted_at'],
            'cancelled_at' => $data['cancelled_at'],
        ]);

        return $this->find($registrationId);
    }

    public function delete(string $registrationId): bool
    {
        $statement = $this->pdo->prepare(
            'DELETE FROM registrations
             WHERE id = :id'
        );
        $statement->execute(['id' => $registrationId]);

        return $statement->rowCount() > 0;
    }
}
