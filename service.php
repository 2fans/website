<?php
// same as index.php - creds from env vars, nothing hardcoded
$host = getenv('DB_HOST') ?: 'localhost';
$user = getenv('DB_USER') ?: 'root';
$pass = getenv('DB_PASS') ?: '';
$db   = getenv('DB_NAME') ?: 'it_db';

// icon whitelist - same path traversal protection as the main page
$allowed_icons = ['cloud.png', 'repair.png', 'shield.png'];
$service  = null;
$db_error = false;

// validate the id before doing anything with it
// has to be a positive integer, anything else just redirects back home
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);

if ($id === false || $id === null) {
    header('Location: index.php');
    exit;
}

try {
    // real prepared statements - the id never touches the sql string directly
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ]);
    $stmt = $pdo->prepare("SELECT id, name, details, price, icon FROM services WHERE id = ?");
    $stmt->execute([$id]);
    $service = $stmt->fetch();
} catch (PDOException $e) {
    // log it, don't expose it
    error_log('DB error: ' . $e->getMessage());
    $db_error = true;
}

// if nothing came back just send them home, no weird blank pages
if (!$db_error && $service === false) {
    header('Location: index.php');
    exit;
}

// xss protection - same as index.php
function h(string $s): string {
    return htmlspecialchars($s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

// check icon against whitelist before using it
$icon_file = (!$db_error && in_array($service['icon'], $allowed_icons, true))
    ? $service['icon']
    : 'placeholder.png';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $service ? h($service['name']) : 'Service' ?> — Tech Solutions</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;0,9..40,600;1,9..40,400&family=DM+Serif+Display&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --navy:   #0f1f3d;
            --navy2:  #162850;
            --white:  #ffffff;
            --off:    #f4f6fb;
            --muted:  #6b7a99;
            --border: #dde3ef;
            --accent: #2f6bff;
            --text:   #1a2540;
        }

        body {
            font-family: 'DM Sans', sans-serif;
            background: var(--off);
            color: var(--text);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* NAV */
        .nav {
            background: var(--navy);
            padding: 0 52px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            height: 66px;
            position: sticky;
            top: 0;
            z-index: 100;
            box-shadow: 0 2px 16px rgba(0,0,0,.3);
        }

        .nav-logo {
            font-family: 'DM Serif Display', serif;
            font-size: 1.4rem;
            color: var(--white);
            text-decoration: none;
        }

        .nav-logo span { color: #7aaeff; }

        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            font-size: .78rem;
            font-weight: 500;
            color: #6a88b8;
            text-decoration: none;
            transition: color .15s;
        }

        .back-link:hover { color: #7aaeff; }

        .back-link svg {
            width: 14px;
            height: 14px;
        }

        /* MAIN */
        .page {
            max-width: 900px;
            margin: 0 auto;
            padding: 52px 40px 80px;
            flex: 1;
        }

        /* SERVICE CARD */
        .service-card {
            background: var(--white);
            border: 1px solid var(--border);
            border-radius: 16px;
            overflow: hidden;
            margin-bottom: 24px;
            box-shadow: 0 4px 24px rgba(15,31,61,.07);
        }

        .service-hero {
            background: linear-gradient(140deg, var(--navy) 0%, #1c3464 100%);
            padding: 40px 44px;
            display: flex;
            align-items: center;
            gap: 24px;
            position: relative;
            overflow: hidden;
        }

        .service-hero::after {
            content: '';
            position: absolute;
            right: -40px;
            top: -40px;
            width: 220px;
            height: 220px;
            border-radius: 50%;
            background: rgba(47,107,255,.1);
            pointer-events: none;
        }

        .hero-icon {
            width: 72px;
            height: 72px;
            background: rgba(255,255,255,.1);
            border: 1px solid rgba(255,255,255,.18);
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .hero-icon img {
            width: 36px;
            height: 36px;
            object-fit: contain;
        }

        .hero-text {}

        .hero-tag {
            display: inline-block;
            background: rgba(255,255,255,.1);
            border: 1px solid rgba(255,255,255,.15);
            color: #a8c4ff;
            font-size: .68rem;
            font-weight: 500;
            letter-spacing: .15em;
            text-transform: uppercase;
            padding: 4px 12px;
            border-radius: 20px;
            margin-bottom: 12px;
        }

        .hero-name {
            font-family: 'DM Serif Display', serif;
            font-size: clamp(1.7rem, 3.5vw, 2.6rem);
            color: var(--white);
            font-weight: 400;
            line-height: 1.15;
        }

        /* BODY */
        .service-body {
            padding: 32px 44px 36px;
        }

        .service-desc {
            font-size: .92rem;
            color: var(--muted);
            line-height: 1.8;
            max-width: 600px;
            margin-bottom: 32px;
        }

        /* STATS */
        .stats {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1px;
            background: var(--border);
            border: 1px solid var(--border);
            border-radius: 12px;
            overflow: hidden;
            margin-bottom: 32px;
        }

        .stat {
            background: var(--white);
            padding: 22px 24px;
        }

        .stat-label {
            font-size: .65rem;
            text-transform: uppercase;
            letter-spacing: .14em;
            color: #a0aec0;
            margin-bottom: 6px;
        }

        .stat-value {
            font-size: 1.45rem;
            font-weight: 600;
            color: var(--text);
            letter-spacing: -.02em;
        }

        .stat-value.accent { color: var(--accent); }

        /* CTA */
        .cta-block {
            background: linear-gradient(135deg, var(--navy) 0%, #1c3464 100%);
            border-radius: 14px;
            padding: 32px 40px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 24px;
            flex-wrap: wrap;
        }

        .cta-title {
            font-family: 'DM Serif Display', serif;
            font-size: 1.3rem;
            color: var(--white);
            font-weight: 400;
            margin-bottom: 6px;
        }

        .cta-sub {
            font-size: .82rem;
            color: #6a88b8;
        }

        .cta-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: var(--accent);
            color: var(--white);
            font-family: 'DM Sans', sans-serif;
            font-size: .85rem;
            font-weight: 600;
            padding: 13px 28px;
            border-radius: 8px;
            text-decoration: none;
            transition: background .15s, transform .15s;
            white-space: nowrap;
        }

        .cta-btn:hover {
            background: #1a4fd6;
            transform: translateY(-1px);
        }

        .cta-btn svg {
            width: 14px;
            height: 14px;
        }

        /* ERROR */
        .error-box {
            background: var(--white);
            border: 1px solid var(--border);
            border-radius: 14px;
            padding: 48px 40px;
            text-align: center;
        }

        .error-box strong {
            display: block;
            font-family: 'DM Serif Display', serif;
            font-size: 1.4rem;
            font-weight: 400;
            margin-bottom: 10px;
        }

        .error-box p { color: var(--muted); font-size: .9rem; }

        /* FOOTER */
        .footer {
            background: var(--navy);
            padding: 22px 52px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .footer-logo {
            font-family: 'DM Serif Display', serif;
            color: #5a7aaa;
            font-size: .9rem;
        }

        .footer-copy {
            font-size: .72rem;
            color: #3d5070;
        }

        @media (max-width: 640px) {
            .nav { padding: 0 20px; }
            .page { padding: 28px 20px 60px; }
            .service-hero { padding: 28px 24px; flex-direction: column; align-items: flex-start; }
            .service-body { padding: 24px; }
            .stats { grid-template-columns: 1fr; }
            .cta-block { padding: 24px; flex-direction: column; align-items: flex-start; }
            .footer { padding: 18px 20px; flex-direction: column; gap: 6px; text-align: center; }
        }
    </style>
</head>
<body>

<nav class="nav">
    <a class="nav-logo" href="index.php">Tech<span>Solutions</span></a>
    <a class="back-link" href="index.php">
        <svg viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M13 8H3M7 4L3 8l4 4"/>
        </svg>
        Back to catalog
    </a>
</nav>

<main class="page">

    <?php if ($db_error): ?>
        <div class="error-box">
            <strong>Service Unavailable</strong>
            <p>Could not load service data. Please try again shortly.</p>
        </div>

    <?php else: ?>

        <div class="service-card">
            <div class="service-hero">
                <div class="hero-icon">
                    <img src="assets/<?= h($icon_file) ?>" alt="<?= h($service['name']) ?>">
                </div>
                <div class="hero-text">
                    <div class="hero-tag">Service Detail</div>
                    <div class="hero-name"><?= h($service['name']) ?></div>
                </div>
            </div>

            <div class="service-body">
                <p class="service-desc"><?= h($service['details']) ?></p>

                <div class="stats">
                    <div class="stat">
                        <div class="stat-label">Pricing</div>
                        <div class="stat-value accent"><?= h($service['price']) ?></div>
                    </div>
                    <div class="stat">
                        <div class="stat-label">Availability</div>
                        <div class="stat-value">24 / 7</div>
                    </div>
                    <div class="stat">
                        <div class="stat-label">Response Time</div>
                        <div class="stat-value">&lt; 2h</div>
                    </div>
                </div>

                <div class="cta-block">
                    <div>
                        <div class="cta-title">Ready to get started?</div>
                    </div>
                    <a class="cta-btn" href="mailto:hello@techsolutions.com?subject=Enquiry: <?= urlencode($service['name']) ?>">
                        Request this service
                        <svg viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M3 8h10M9 4l4 4-4 4"/>
                        </svg>
                    </a>
                </div>
            </div>
        </div>

    <?php endif; ?>

</main>

<footer class="footer">
    <div class="footer-logo">TechSolutions</div>
</footer>

</body>
</html>
