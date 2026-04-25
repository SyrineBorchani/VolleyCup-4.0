<?php
declare(strict_types=1);

$dataFile = __DIR__ . '/data/registrations.json';
$registrationId = isset($_GET['id']) ? trim((string) $_GET['id']) : '';
$registration = null;
$statusMessage = '';

function escape(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
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

$registrations = load_registrations($dataFile);

foreach ($registrations as $item) {
    if (($item['id'] ?? '') === $registrationId) {
        $registration = $item;
        break;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $registration !== null && ($registration['status'] ?? '') !== 'cancelled') {
    foreach ($registrations as &$item) {
        if (($item['id'] ?? '') === $registrationId) {
            $item['status'] = 'cancelled';
            $item['cancelled_at'] = date(DATE_ATOM);
            $registration = $item;
            $statusMessage = 'Registration canceled successfully.';
            break;
        }
    }
    unset($item);

    save_registrations($dataFile, $registrations);
}

$isMissing = $registration === null;
$isCancelled = !$isMissing && (($registration['status'] ?? '') === 'cancelled');
$services = !$isMissing && isset($registration['services']) && is_array($registration['services']) ? $registration['services'] : [];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Registration Status</title>
  <link rel="stylesheet" href="style.css" />
  <link rel="stylesheet" href="style_header_footer.css" />
  <style>
    body {
      background: #0f1118;
    }

    .success-page {
      min-height: calc(100vh - 96px);
      display: flex;
      align-items: flex-start;
      justify-content: center;
      box-sizing: border-box;
      padding: 132px 20px 72px;
      background:
        radial-gradient(circle at top left, rgba(255, 90, 0, 0.16), transparent 32%),
        linear-gradient(180deg, #06070b 0%, #0f1118 100%);
    }

    .success-card {
      width: min(620px, 100%);
      padding: 30px 26px;
      margin: 0 auto;
      border: 1px solid rgba(255, 90, 0, 0.18);
      background:
        linear-gradient(145deg, rgba(255, 90, 0, 0.12), rgba(255, 90, 0, 0.03) 42%),
        linear-gradient(180deg, #10131a 0%, #090b10 100%);
      border-radius: 28px;
      text-align: center;
      box-shadow: 0 24px 60px rgba(0, 0, 0, 0.32);
      color: #f8efe8;
    }

    .success-kicker {
      display: inline-flex;
      align-items: center;
      gap: 10px;
      margin-bottom: 18px;
      padding: 8px 16px;
      border-radius: 999px;
      background: rgba(255, 90, 0, 0.12);
      border: 1px solid rgba(255, 90, 0, 0.18);
      color: #ff9a66;
      font-size: 0.9rem;
      font-weight: 700;
      letter-spacing: 0.08em;
      text-transform: uppercase;
    }

    .success-dot {
      width: 9px;
      height: 9px;
      border-radius: 50%;
      background: #ff5a00;
      box-shadow: 0 0 16px rgba(255, 90, 0, 0.65);
    }

    .success-title {
      margin: 0 0 14px;
      font-size: clamp(2rem, 4vw, 2.8rem);
      line-height: 1.08;
      color: #ffffff;
    }

    .success-title span {
      color: #ff5a00;
    }

    .success-text {
      max-width: 460px;
      margin: 0 auto 24px;
      color: rgba(255, 239, 228, 0.78);
      font-size: 1rem;
      line-height: 1.7;
    }

    .success-actions {
      display: flex;
      gap: 14px;
      justify-content: center;
      flex-wrap: wrap;
    }

    .success-btn {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      min-width: 190px;
      padding: 14px 22px;
      border-radius: 999px;
      text-decoration: none;
      font-weight: 700;
      transition: transform 0.2s ease, box-shadow 0.2s ease, background 0.2s ease;
      border: 0;
    }

    .success-btn.primary {
      background: #ff5a00;
      color: #fff;
      box-shadow: 0 14px 28px rgba(255, 90, 0, 0.28);
    }

    .success-btn.secondary {
      border: 1px solid rgba(255, 90, 0, 0.24);
      color: #ffe8d6;
      background: rgba(255, 90, 0, 0.08);
    }

    .success-btn.danger {
      border: 1px solid rgba(255, 107, 107, 0.34);
      color: #ffd7d7;
      background: rgba(255, 107, 107, 0.12);
      cursor: pointer;
      font: inherit;
    }

    .success-btn:hover {
      transform: translateY(-2px);
    }

    .success-btn.primary:hover {
      background: #e64a00;
    }

    .success-btn.danger:hover {
      background: rgba(255, 107, 107, 0.2);
    }

    .success-btn:disabled {
      opacity: 0.72;
      cursor: not-allowed;
      transform: none;
    }

    .success-card.is-cancelled,
    .success-card.is-missing {
      border-color: rgba(255, 107, 107, 0.28);
      background:
        linear-gradient(145deg, rgba(255, 107, 107, 0.12), rgba(255, 107, 107, 0.03) 42%),
        linear-gradient(180deg, #10131a 0%, #090b10 100%);
    }

    .success-card.is-cancelled .success-kicker,
    .success-card.is-missing .success-kicker {
      background: rgba(255, 107, 107, 0.12);
      border-color: rgba(255, 107, 107, 0.24);
      color: #ffb4b4;
    }

    .success-card.is-cancelled .success-dot,
    .success-card.is-missing .success-dot {
      background: #ff6b6b;
      box-shadow: 0 0 16px rgba(255, 107, 107, 0.65);
    }

    .success-card.is-cancelled .success-title span,
    .success-card.is-missing .success-title span {
      color: #ff6b6b;
    }

    .success-card.is-cancelled .success-check,
    .success-card.is-missing .success-check {
      background: #ff6b6b;
      box-shadow: 0 18px 36px rgba(255, 107, 107, 0.24);
    }

    .success-check {
      width: 90px;
      height: 90px;
      margin: 0 auto 18px;
      border-radius: 24px;
      display: grid;
      place-items: center;
      background: #ff5a00;
      font-size: 2.7rem;
      font-weight: 900;
      box-shadow: 0 18px 36px rgba(255, 90, 0, 0.24);
      animation: pop-in 0.55s ease;
      color: #180d04;
    }

    .success-meta {
      margin: 24px 0;
      padding: 18px;
      border-radius: 20px;
      text-align: left;
      background: rgba(255, 255, 255, 0.04);
      border: 1px solid rgba(255, 255, 255, 0.08);
    }

    .success-meta p {
      margin: 0 0 12px;
      color: rgba(255, 239, 228, 0.84);
    }

    .success-meta p:last-child {
      margin-bottom: 0;
    }

    .status-message {
      margin: 0 auto 18px;
      padding: 12px 14px;
      border-radius: 14px;
      max-width: 460px;
      font-weight: 600;
      background: rgba(255, 107, 107, 0.12);
      border: 1px solid rgba(255, 107, 107, 0.24);
      color: #ffd7d7;
    }

    @keyframes pop-in {
      0% {
        transform: scale(0.65) rotate(-8deg);
        opacity: 0;
      }

      100% {
        transform: scale(1) rotate(0deg);
        opacity: 1;
      }
    }

    @media (max-width: 640px) {
      .success-page {
        min-height: calc(100vh - 88px);
        padding: 108px 16px 48px;
      }

      .success-card {
        padding: 26px 18px;
        border-radius: 24px;
      }

      .success-btn {
        width: 100%;
      }
    }
  </style>
</head>
<body>
  <header class="main-header">
    <div class="container">
      <div class="header-logo">
        Volley<span>Cup</span> 4.0
      </div>
      <nav>
        <ul>
          <li><a href="home.html">Home</a></li>
          <li><a href="schedule.html">Schedule</a></li>
          <li><a href="teams.html">Teams</a></li>
          <li><a href="register.html" class="btn-register">Register</a></li>
        </ul>
      </nav>
    </div>
  </header>

  <main class="success-page">
    <section class="success-card<?= $isCancelled ? ' is-cancelled' : '' ?><?= $isMissing ? ' is-missing' : '' ?>">
      <div class="success-check" aria-hidden="true"><?= $isMissing || $isCancelled ? '&#10005;' : '&#10003;' ?></div>

      <div class="success-kicker">
        <span class="success-dot"></span>
        <span>
          <?php if ($isMissing): ?>
            Registration Not Found
          <?php elseif ($isCancelled): ?>
            Registration Canceled
          <?php else: ?>
            Team Registered
          <?php endif; ?>
        </span>
      </div>

      <h1 class="success-title">
        <?php if ($isMissing): ?>
          Registration <span>Unavailable</span>
        <?php elseif ($isCancelled): ?>
          Registration <span>Canceled</span>
        <?php else: ?>
          Registration <span>Confirmed</span>
        <?php endif; ?>
      </h1>

      <p class="success-text">
        <?php if ($isMissing): ?>
          We could not find a saved registration for this link. Try submitting the form again from the registration page.
        <?php elseif ($isCancelled): ?>
          This team registration has been canceled. You can go back and submit a new one whenever you are ready.
        <?php else: ?>
          Your team has been saved successfully. We will contact you soon with the next steps.
        <?php endif; ?>
      </p>

      <?php if ($statusMessage !== ''): ?>
        <p class="status-message"><?= escape($statusMessage) ?></p>
      <?php endif; ?>

      <?php if (!$isMissing): ?>
        <div class="success-meta">
          <p><strong>University:</strong> <?= escape((string) ($registration['university_name'] ?? '')) ?></p>
          <p><strong>Captain:</strong> <?= escape((string) ($registration['captain'] ?? '')) ?></p>
          <p><strong>Category:</strong> <?= escape(ucfirst((string) ($registration['category'] ?? ''))) ?></p>
          <p><strong>Roster Size:</strong> <?= escape((string) ($registration['roster_size'] ?? '')) ?></p>
          <p><strong>Contact:</strong> <?= escape((string) ($registration['email'] ?? '')) ?> | <?= escape((string) ($registration['phone'] ?? '')) ?></p>
          <p><strong>Optional Services:</strong> <?= escape($services === [] ? 'None requested' : implode(', ', $services)) ?></p>
          <p><strong>Submitted:</strong> <?= escape((string) ($registration['submitted_at'] ?? '')) ?></p>
        </div>
      <?php endif; ?>

      <div class="success-actions">
        <a class="success-btn primary" href="home.html">Back to Home</a>
        <a class="success-btn secondary" href="register.html">Register Again</a>
        <?php if (!$isMissing && !$isCancelled): ?>
          <form method="post" action="success.php?id=<?= escape($registrationId) ?>" style="margin: 0;">
            <button class="success-btn danger" type="submit">Cancel Registration</button>
          </form>
        <?php endif; ?>
      </div>
    </section>
  </main>

  <footer class="main-footer">
    <div class="footer-container">
      <div class="footer-column">
        <div class="footer-logo">Volley<span>Cup</span> 4.0</div>
        <p class="footer-description">
          The premier intercollegiate volleyball
          tournament bringing universities
          together since 2023.
        </p>
        <div class="social-icons">
          <a href="#"><i class="fab fa-facebook"></i></a>
          <a href="#"><i class="fab fa-instagram"></i></a>
        </div>
      </div>
      <div class="footer-column">
        <h3>Contact Us</h3>
        <ul class="contact-info">
          <li>Email <a href="https://mail.google.com/mail/u/6/#inbox">volleycup.x@gmail.com</a></li>
          <li>Phone <a href="tel:+216123456789">+216 123 456 789</a></li>
          <li>Location <a href="https://maps.app.goo.gl/LzZDCpgUu3KXt3e27">ENSI - National School of Computer Science</a></li>
        </ul>
      </div>
      <hr class="footer-divider">

      <div class="footer-bottom">
        <p>&copy; 2026 <strong>VolleyCup</strong>. All rights reserved.</p>
        <p>Designed for the University Tournament.</p>
      </div>
    </div>
  </footer>
</body>
</html>
