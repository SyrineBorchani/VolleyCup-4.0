<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/registration_repository.php';

function escape_team_value(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

$registeredTeams = [];
$teamsError = '';

try {
    $registeredTeams = volleycup_find_all_registrations(false);
} catch (Throwable $exception) {
    $teamsError = 'Registered teams are temporarily unavailable. Start Apache and MySQL in XAMPP, then refresh this page.';
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style_header_footer.css">
    <link rel="stylesheet" href="style.css">
    <title>VolleyCup 4.0 - Teams</title>
    <style>
        .registered-teams {
            width: min(1180px, calc(100% - 40px));
            margin: 40px auto 80px;
            padding: 28px;
            border-radius: 28px;
            border: 1px solid rgba(255, 255, 255, 0.08);
            background: linear-gradient(180deg, rgba(8, 10, 16, 0.94), rgba(16, 19, 29, 0.92));
            box-shadow: 0 24px 60px rgba(0, 0, 0, 0.24);
            color: #f7f0ea;
        }

        .registered-teams-header {
            display: flex;
            justify-content: space-between;
            gap: 16px;
            align-items: flex-end;
            flex-wrap: wrap;
            margin-bottom: 24px;
        }

        .registered-teams-header h2 {
            margin: 0 0 6px;
            font-size: clamp(1.9rem, 3.5vw, 2.6rem);
        }

        .registered-teams-header p,
        .registered-empty,
        .registered-error {
            margin: 0;
            color: rgba(247, 240, 234, 0.72);
            line-height: 1.7;
        }

        .registered-total {
            padding: 10px 16px;
            border-radius: 999px;
            background: rgba(255, 90, 0, 0.12);
            border: 1px solid rgba(255, 90, 0, 0.24);
            color: #ffbe97;
            font-weight: 700;
        }

        .registered-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 18px;
        }

        .registered-card {
            overflow: hidden;
            border-radius: 22px;
            border: 1px solid rgba(255, 255, 255, 0.08);
            background: rgba(255, 255, 255, 0.04);
        }

        .registered-photo {
            height: 190px;
            background:
                linear-gradient(135deg, rgba(255, 90, 0, 0.18), rgba(255, 90, 0, 0.02)),
                #151925;
            display: grid;
            place-items: center;
        }

        .registered-photo img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }

        .registered-photo-fallback {
            display: grid;
            place-items: center;
            gap: 10px;
            color: rgba(255, 255, 255, 0.72);
            text-align: center;
            padding: 20px;
        }

        .registered-photo-fallback strong {
            font-size: 1.05rem;
            color: #ffffff;
        }

        .registered-card-body {
            padding: 18px;
        }

        .registered-card-body h3 {
            margin: 0 0 8px;
            font-size: 1.15rem;
            color: #ffffff;
        }

        .registered-card-body p {
            margin: 0 0 8px;
            color: rgba(247, 240, 234, 0.76);
        }

        .registered-card-body p:last-child {
            margin-bottom: 0;
        }

        @media (max-width: 640px) {
            .registered-teams {
                width: min(100% - 24px, 1180px);
                padding: 22px 16px;
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
                    <li><a href="teams.php" class="active">Teams</a></li>
                    <li><a href="register.html" class="btn-register">Register</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <main>
    <div class="teams-page">

        <div class="teams-header">
        <h1>Featured Teams</h1>
        <p class="teams-subtitle">Meet the top-ranked university teams competing the last year 24-25</p>
        <hr class="teams-divider">
        <div class="spotlight-toggle-wrap">
            <button id="spotlightToggle">ðŸŽ¨ Unified Mode</button>
        </div>
        </div>

        <ul class="teams-container">

        <li class="team-card">
            <div class="badge-row">
            <span class="rank rank-red">#1</span>
            <span class="uni-name">Alumni ENSI</span>
            </div>
            <h3 class="team-name">Falcons Without Wings</h3>
            <p class="conference-rank">Conference Ranking: <strong>#1</strong></p>
            <div class="key-players">
            <p class="players-label">Key Players</p>
            <p>Bechir</p>
            <p>Mehdi</p>
            </div>
            <div class="sets-row">
            <span class="sets-label">Sets Wins</span>
            <span class="sets-number">5</span>
            </div>
        </li>

        <li class="team-card">
            <div class="badge-row">
            <span class="rank rank-blue">#2</span>
            <span class="uni-name">ENSI</span>
            </div>
            <h3 class="team-name">Athletic Planet</h3>
            <p class="conference-rank">Conference Ranking: <strong>#2</strong></p>
            <div class="key-players">
            <p class="players-label">Key Players</p>
            <p>Hayder</p>
            <p>Hamza</p>
            </div>
            <div class="sets-row">
            <span class="sets-label">Sets Wins</span>
            <span class="sets-number">4</span>
            </div>
        </li>

        <li class="team-card">
            <div class="badge-row">
            <span class="rank rank-darkred">#3</span>
            <span class="uni-name">INSAT</span>
            </div>
            <h3 class="team-name">Sky Setters</h3>
            <p class="conference-rank">Conference Ranking: <strong>#3</strong></p>
            <div class="key-players">
            <p class="players-label">Key Players</p>
            <p>Karima</p>
            <p>Aziz</p>
            </div>
            <div class="sets-row">
            <span class="sets-label">Sets Wins</span>
            <span class="sets-number">3</span>
            </div>
        </li>

        <li class="team-card">
            <div class="badge-row">
            <span class="rank rank-orange">#4</span>
            <span class="uni-name">ESPRIT</span>
            </div>
            <h3 class="team-name">Shakshouka</h3>
            <p class="conference-rank">Conference Ranking: <strong>#4</strong></p>
            <div class="key-players">
            <p class="players-label">Key Players</p>
            <p>Chiheb</p>
            <p>Brahim</p>
            </div>
            <div class="sets-row">
            <span class="sets-label">Sets Wins</span>
            <span class="sets-number">2</span>
            </div>
        </li>

        <li class="team-card">
            <div class="badge-row">
            <span class="rank rank-red">#5</span>
            <span class="uni-name">ENSI</span>
            </div>
            <h3 class="team-name">Ensi Flowers</h3>
            <p class="conference-rank">Conference Ranking: <strong>#5</strong></p>
            <div class="key-players">
            <p class="players-label">Key Players</p>
            <p>Hamza</p>
            <p>Firas</p>
            </div>
            <div class="sets-row">
            <span class="sets-label">Sets Wins</span>
            <span class="sets-number">2</span>
            </div>
        </li>

        <li class="team-card">
            <div class="badge-row">
            <span class="rank rank-gold">#6</span>
            <span class="uni-name">ENSI</span>
            </div>
            <h3 class="team-name">Blue Grana</h3>
            <p class="conference-rank">Conference Ranking: <strong>#6</strong></p>
            <div class="key-players">
            <p class="players-label">Key Players</p>
            <p>Anas</p>
            <p>Rami</p>
            </div>
            <div class="sets-row">
            <span class="sets-label">Sets Wins</span>
            <span class="sets-number">1</span>
            </div>
        </li>

        </ul>
    </div>

    <section class="registered-teams">
        <div class="registered-teams-header">
            <div>
                <h2>Registered Teams from the Database</h2>
                <p>This section is loaded with PDO and displays the saved team photo, captain, category, and roster size for each confirmed registration.</p>
            </div>
            <div class="registered-total"><?= count($registeredTeams) ?> saved team<?= count($registeredTeams) === 1 ? '' : 's' ?></div>
        </div>

        <?php if ($teamsError !== ''): ?>
            <p class="registered-error"><?= escape_team_value($teamsError) ?></p>
        <?php elseif ($registeredTeams === []): ?>
            <p class="registered-empty">No confirmed registrations have been saved yet. Submit a team from the registration page to see it appear here.</p>
        <?php else: ?>
            <div class="registered-grid">
                <?php foreach ($registeredTeams as $team): ?>
                    <article class="registered-card">
                        <div class="registered-photo">
                            <?php if (!empty($team['team_photo'])): ?>
                                <img src="<?= escape_team_value((string) $team['team_photo']) ?>" alt="Team photo for <?= escape_team_value((string) ($team['team_name'] ?? $team['university_name'])) ?>">
                            <?php else: ?>
                                <div class="registered-photo-fallback">
                                    <strong><?= escape_team_value((string) ($team['team_name'] ?? $team['university_name'])) ?></strong>
                                    <span>No photo uploaded</span>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="registered-card-body">
                            <h3><?= escape_team_value((string) ($team['team_name'] ?? 'Registered Team')) ?></h3>
                            <p><strong>University:</strong> <?= escape_team_value((string) $team['university_name']) ?></p>
                            <p><strong>Captain:</strong> <?= escape_team_value((string) $team['captain']) ?></p>
                            <p><strong>Category:</strong> <?= escape_team_value(ucfirst((string) $team['category'])) ?></p>
                            <p><strong>Roster Size:</strong> <?= escape_team_value((string) $team['roster_size']) ?></p>
                            <p><strong>Submitted:</strong> <?= escape_team_value((string) $team['submitted_at']) ?></p>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </section>

    <button id="scrollTopBtn" title="Back to top">â†‘</button>
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
                    <li> 📧 <a href="https://mail.google.com/mail/u/6/#inbox">
                            volleycup.x@gmail.com
                            </a>
                    </li>
                    <li>📞  <a href="tel:+216123456789">
                             +216 123 456 789
                            </a>
                    </li>
                    <li>📍<a href="https://maps.app.goo.gl/LzZDCpgUu3KXt3e27">
                           ENSI - National School of Computer Science
                           </a>
                    </li>
                </ul>
            </div>
            <hr class="footer-divider">

            <div class="footer-bottom">
                <p>&copy; 2026 <strong>VolleyCup</strong>. All rights reserved.</p>
                <p>Designed for the University Tournament.</p>
            </div>
        </div>
    </footer>
    <script src="src/js/teams.js"></script>
</body>
</html>
