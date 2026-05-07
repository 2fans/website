<?php
// db creds from env vars so nothing sensitive ends up in the source code
$host = getenv('DB_HOST') ?: 'localhost';
$user = getenv('DB_USER') ?: 'root';
$pass = getenv('DB_PASS') ?: '';
$db   = getenv('DB_NAME') ?: 'it_db';

// only these filenames are valid icons - stops anything dodgy getting through from the db
$allowed_icons = ['cloud.png', 'repair.png', 'shield.png'];
$services  = [];
$db_error  = false;

try {
    // utf8mb4 + real prepared statements = no sql injection
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ]);
    // only grab what we actually need, no SELECT *
    $stmt     = $pdo->query("SELECT id, name, details, price, icon FROM services");
    $services = $stmt->fetchAll();
} catch (PDOException $e) {
    // log it server side, don't show the error to the user
    error_log('DB error: ' . $e->getMessage());
    $db_error = true;
}

// run everything through this before printing it - stops XSS attacks
function h(string $s): string {
    return htmlspecialchars($s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tech Solutions — IT Services</title>
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
        }

        .nav-logo span { color: #7aaeff; }

        .nav-right {
            display: flex;
            align-items: center;
            gap: 24px;
        }

        .nav-badge {
            background: rgba(47,107,255,.2);
            border: 1px solid rgba(47,107,255,.35);
            color: #7aaeff;
            font-size: .7rem;
            font-weight: 500;
            letter-spacing: .1em;
            text-transform: uppercase;
            padding: 4px 12px;
            border-radius: 20px;
        }

        .nav-date {
            font-size: .75rem;
            color: #6a88b8;
        }

        /* HERO */
        .hero {
            background: linear-gradient(135deg, var(--navy) 0%, #1a3560 55%, #1e3f78 100%);
            color: var(--white);
            padding: 68px 52px 60px;
            position: relative;
            overflow: hidden;
        }

        .hero::before {
            content: '';
            position: absolute;
            right: 5%;
            top: -60px;
            width: 340px;
            height: 340px;
            border-radius: 50%;
            background: rgba(47,107,255,.1);
            pointer-events: none;
        }

        .hero::after {
            content: '';
            position: absolute;
            right: 18%;
            bottom: -100px;
            width: 220px;
            height: 220px;
            border-radius: 50%;
            background: rgba(122,174,255,.07);
            pointer-events: none;
        }

        .hero-eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: #7aaeff;
            font-size: .72rem;
            font-weight: 500;
            letter-spacing: .18em;
            text-transform: uppercase;
            margin-bottom: 18px;
        }

        .hero-eyebrow::before {
            content: '';
            display: block;
            width: 20px;
            height: 2px;
            background: #7aaeff;
            border-radius: 2px;
        }

        .hero h1 {
            font-family: 'DM Serif Display', serif;
            font-size: clamp(1.9rem, 3.8vw, 3rem);
            font-weight: 400;
            line-height: 1.18;
            max-width: 520px;
            margin-bottom: 16px;
        }

        .hero-sub {
            font-size: .92rem;
            color: #8fa8d0;
            max-width: 400px;
            line-height: 1.75;
            font-weight: 300;
        }

        /* MAIN */
        .page {
            max-width: 1120px;
            margin: 0 auto;
            padding: 52px 40px 80px;
            flex: 1;
        }

        .section-header {
            display: flex;
            align-items: baseline;
            justify-content: space-between;
            margin-bottom: 28px;
        }

        .section-title {
            font-family: 'DM Serif Display', serif;
            font-size: 1.55rem;
            font-weight: 400;
        }

        .section-count {
            font-size: .78rem;
            color: var(--muted);
            font-weight: 500;
            background: var(--white);
            border: 1px solid var(--border);
            padding: 3px 10px;
            border-radius: 12px;
        }

        /* CARDS */
        .card-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(310px, 1fr));
            gap: 22px;
        }

        .card {
            background: var(--white);
            border: 1px solid var(--border);
            border-radius: 14px;
            text-decoration: none;
            color: inherit;
            display: flex;
            flex-direction: column;
            transition: box-shadow .2s ease, transform .2s ease, border-color .2s ease;
            overflow: hidden;
        }

        .card:hover {
            box-shadow: 0 10px 36px rgba(15,31,61,.13);
            transform: translateY(-4px);
            border-color: #b3c6ff;
        }

        .card-header {
            background: linear-gradient(140deg, var(--navy) 0%, #1c3464 100%);
            padding: 26px 26px 20px;
            display: flex;
            align-items: flex-start;
            gap: 14px;
        }

        .card-icon {
            width: 46px;
            height: 46px;
            background: rgba(255,255,255,.1);
            border: 1px solid rgba(255,255,255,.15);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .card-icon img {
            width: 24px;
            height: 24px;
            object-fit: contain;
        }

        .card-name {
            font-family: 'DM Serif Display', serif;
            font-size: 1.2rem;
            color: var(--white);
            font-weight: 400;
            line-height: 1.25;
            padding-top: 4px;
        }

        .card-body {
            padding: 20px 26px 22px;
            flex: 1;
            display: flex;
            flex-direction: column;
            gap: 18px;
        }

        .card-desc {
            font-size: .86rem;
            color: var(--muted);
            line-height: 1.7;
            flex: 1;
        }

        .card-footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding-top: 16px;
            border-top: 1px solid var(--border);
        }

        .price-label {
            font-size: .65rem;
            text-transform: uppercase;
            letter-spacing: .12em;
            color: #a0aec0;
            margin-bottom: 3px;
        }

        .price-value {
            font-size: 1.35rem;
            font-weight: 600;
            color: var(--accent);
            letter-spacing: -.02em;
        }

        .card-link {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: .78rem;
            font-weight: 500;
            color: var(--accent);
            transition: gap .15s;
        }

        .card:hover .card-link { gap: 10px; }

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
            .hero { padding: 44px 20px 36px; }
            .page { padding: 32px 20px 60px; }
            .footer { padding: 18px 20px; flex-direction: column; gap: 6px; text-align: center; }
            .nav-date { display: none; }
        }
    </style>
</head>
<body>

<nav class="nav">
    <div class="nav-logo">Tech<span>Solutions</span></div>
    <div class="nav-right">
        <span class="nav-badge"><?= count($services) ?> Services</span>
        <span class="nav-date"><?= date('d M Y') ?></span>
    </div>
</nav>

<div class="hero">
    <div class="hero-eyebrow">IT Services Catalog</div>
    <h1>Professional IT Solutions for Your Business</h1>
    <p class="hero-sub">Cloud infrastructure, cybersecurity, and technical support — delivered by experts, built for reliability.</p>
</div>

<main class="page">

    <?php if ($db_error): ?>
        <div class="error-box">
            <strong>Service Unavailable</strong>
            <p>We couldn't load the services catalog right now. Please try again shortly.</p>
        </div>

    <?php elseif (empty($services)): ?>
        <div class="error-box">
            <strong>No Services Listed</strong>
            <p>The catalog is currently empty.</p>
        </div>

    <?php else: ?>
        <div class="section-header">
            <h2 class="section-title">Available Services</h2>
            <span class="section-count"><?= count($services) ?> listed</span>
        </div>

        <div class="card-grid">
            <?php foreach ($services as $row):
                $icon_file = in_array($row['icon'], $allowed_icons, true)
                    ? $row['icon']
                    : 'placeholder.png';
            ?>
                <a class="card" href="service.php?id=<?= (int)$row['id'] ?>">
                    <div class="card-header">
                        <div class="card-icon">
                            <img src="assets/<?= h($icon_file) ?>" alt="">
                        </div>
                        <div class="card-name"><?= h($row['name']) ?></div>
                    </div>
                    <div class="card-body">
                        <p class="card-desc"><?= h($row['details']) ?></p>
                        <div class="card-footer">
                            <div>
                                <div class="price-label">Starting from</div>
                                <div class="price-value"><?= h($row['price']) ?></div>
                            </div>
                            <span class="card-link">
                                View details
                                <svg width="14" height="14" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M3 8h10M9 4l4 4-4 4"/>
                                </svg>
                            </span>
                        </div>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

</main>

<footer class="footer">
    <div class="footer-logo">TechSolutions</div>
</footer>

</body>
</html>
