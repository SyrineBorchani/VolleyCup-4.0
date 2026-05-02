<?php
declare(strict_types=1);

final class Registration
{
    public function __construct(
        public readonly string $id,
        public readonly string $universityName,
        public readonly string $teamName,
        public readonly string $captain,
        public readonly int $rosterSize,
        public readonly string $email,
        public readonly string $phone,
        public readonly string $category,
        public readonly array $services,
        public readonly string $comments,
        public readonly ?string $teamPhoto,
        public readonly string $status,
        public readonly string $submittedAt,
        public readonly ?string $cancelledAt,
    ) {
    }

    public static function fromDatabaseRow(array $row): self
    {
        $services = json_decode((string) ($row['services_json'] ?? '[]'), true);

        return new self(
            (string) ($row['id'] ?? ''),
            (string) ($row['university_name'] ?? ''),
            (string) ($row['team_name'] ?? ''),
            (string) ($row['captain'] ?? ''),
            (int) ($row['roster_size'] ?? 0),
            (string) ($row['email'] ?? ''),
            (string) ($row['phone'] ?? ''),
            (string) ($row['category'] ?? ''),
            is_array($services) ? array_values(array_map('strval', $services)) : [],
            (string) ($row['comments'] ?? ''),
            isset($row['team_photo']) && $row['team_photo'] !== '' ? (string) $row['team_photo'] : null,
            (string) ($row['status'] ?? 'confirmed'),
            (string) ($row['submitted_at'] ?? ''),
            isset($row['cancelled_at']) ? (string) $row['cancelled_at'] : null,
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'university_name' => $this->universityName,
            'team_name' => $this->teamName,
            'captain' => $this->captain,
            'roster_size' => $this->rosterSize,
            'email' => $this->email,
            'phone' => $this->phone,
            'category' => $this->category,
            'services' => $this->services,
            'comments' => $this->comments,
            'team_photo' => $this->teamPhoto,
            'status' => $this->status,
            'submitted_at' => $this->submittedAt,
            'cancelled_at' => $this->cancelledAt,
        ];
    }

    public function teamPhotoUrl(): ?string
    {
        return $this->teamPhoto;
    }
}
