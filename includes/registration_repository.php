<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/Registration.php';
require_once __DIR__ . '/RegistrationRepository.php';

function volleycup_repository(): RegistrationRepository
{
    static $repository = null;

    if (!$repository instanceof RegistrationRepository) {
        $repository = new RegistrationRepository(volleycup_database());
    }

    return $repository;
}

function volleycup_find_registration(string $registrationId): ?array
{
    $registration = volleycup_repository()->find($registrationId);

    return $registration instanceof Registration ? $registration->toArray() : null;
}

function volleycup_create_registration(array $data): string
{
    return volleycup_repository()->create($data);
}

function volleycup_cancel_registration(string $registrationId): ?array
{
    $registration = volleycup_repository()->cancel($registrationId);

    return $registration instanceof Registration ? $registration->toArray() : null;
}

function volleycup_find_latest_registration(): ?array
{
    $registration = volleycup_repository()->findLatest();

    return $registration instanceof Registration ? $registration->toArray() : null;
}

function volleycup_find_all_registrations(bool $includeCancelled = true): array
{
    return array_map(
        static fn(Registration $registration): array => $registration->toArray(),
        volleycup_repository()->findAll($includeCancelled)
    );
}

function volleycup_update_registration(string $registrationId, array $data): ?array
{
    $registration = volleycup_repository()->update($registrationId, $data);

    return $registration instanceof Registration ? $registration->toArray() : null;
}

function volleycup_delete_registration(string $registrationId): bool
{
    return volleycup_repository()->delete($registrationId);
}
