<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Student' ?> — <?= APP_NAME ?></title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        :root {
            --bg: #F5F4F0;
            --white: #ffffff;
            --text: #1a1a1a;
            --muted: #888;
            --border: #e8e6e0;
            --accent: #2C5F2E;
            --accent-hover: #245026;
            --accent-light: #EAF3DE;
            --tag-bg: #f0eee8;
        }
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: var(--bg);
            color: var(--text);
            display: flex;
            min-height: 100vh;
            font-size: 13px;
        }

        .sidebar {
            width: 228px;
            background: var(--white);
            border-right: 1px solid var(--border);
            display: flex;
            flex-direction: column;
            padding: 20px 12px;
            gap: 2px;
            flex-shrink: 0;
            position: fixed;
            left: 0;
            top: 0;
            height: 100vh;
            overflow-y: auto;
            z-index: 40;
            transform: translateX(-100%);
            transition: transform 0.3s ease;
        }
        .sidebar.open { transform: translateX(0); }
        @media (min-width: 768px) {
            .sidebar { position: sticky; transform: none; }
        }
        .mobile-overlay {
            position: fixed; inset: 0; background: rgba(0,0,0,0.4); z-index: 30;
            display: none;
        }
        .mobile-overlay.open { display: block; }
        @media (min-width: 768px) { .mobile-overlay { display: none !important; } }
        .sidebar-logo {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 4px 10px 18px;
            border-bottom: 1px solid var(--border);
            margin-bottom: 8px;
        }
        .logo-mark {
            width: 32px; height: 32px;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
        }
        .logo-mark i {
            width: 100%; height: 100%;
            background: var(--accent);
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            color: #fff; font-size: 15px;
        }
        .logo-name {
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-weight: 800; font-size: 15px;
            color: var(--text); letter-spacing: -0.3px;
        }
        .nav-item {
            display: flex; align-items: center; gap: 10px;
            padding: 9px 12px; border-radius: 10px;
            color: #666; font-size: 13px; font-weight: 500;
            text-decoration: none; transition: background .12s, color .12s;
        }
        .nav-item:hover { background: #f5f3ee; color: var(--text); }
        .nav-item.active { background: var(--accent); color: #fff; }
        .nav-item i { width: 16px; text-align: center; font-size: 13px; flex-shrink: 0; }
        .nav-spacer { flex: 1; min-height: 12px; }
        .nav-divider { border: none; border-top: 1px solid var(--border); margin: 8px 0; }

        /* Main */
        .main { flex: 1; display: flex; flex-direction: column; min-width: 0; width: 100%; }

        /* Topbar */
        .topbar {
            background: var(--white);
            border-bottom: 1px solid var(--border);
            padding: 14px 16px;
            display: flex; align-items: center; justify-content: space-between;
            flex-shrink: 0; position: sticky; top: 0; z-index: 10;
        }
        @media (min-width: 768px) { .topbar { padding: 14px 28px; } }
        .topbar-title {
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-weight: 800; font-size: 19px; letter-spacing: -0.4px;
        }
        .topbar-right { display: flex; align-items: center; gap: 10px; }
        .topbar-welcome { font-size: 12px; color: var(--muted); }
        .topbar-welcome strong { color: var(--text); }
        .topbar-avatar {
            width: 32px; height: 32px; border-radius: 50%;
            background: #D3D1C7; display: flex; align-items: center; justify-content: center;
            font-size: 11px; font-weight: 700; color: #555; cursor: pointer;
        }

        /* Content */
        .page-content { padding: 16px; flex: 1; }
        @media (min-width: 768px) { .page-content { padding: 28px; } }

        /* Flash */
        .flash-success {
            background: var(--accent-light); border: 1px solid #c3dfc4;
            color: #2C5F2E; border-radius: 10px; padding: 10px 16px;
            margin-bottom: 20px; font-size: 13px; font-weight: 500;
        }
        .flash-error {
            background: #FCEBEB; border: 1px solid #f7c1c1;
            color: #A32D2D; border-radius: 10px; padding: 10px 16px;
            margin-bottom: 20px; font-size: 13px; font-weight: 500;
        }

        /* Forms & Inputs */
        .form-input {
            padding: 10px 14px; border: 1px solid var(--border);
            border-radius: 10px; font-size: 13px;
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: var(--white); color: var(--text); outline: none; transition: all .15s ease;
            box-shadow: 0 1px 2px rgba(0,0,0,0.04);
        }
        .form-input:focus { border-color: var(--accent); box-shadow: 0 0 0 3px var(--accent-light); }

        .form-select {
            appearance: none;
            padding: 10px 38px 10px 14px;
            border: 1px solid var(--border);
            border-radius: 10px;
            font-size: 13px;
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: var(--white);
            color: var(--text);
            outline: none;
            transition: all 0.15s ease;
            cursor: pointer;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%23888888'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'%3E%3C/path%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 12px center;
            background-size: 15px;
            box-shadow: 0 1px 2px rgba(0,0,0,0.04);
        }
        .form-select:hover { border-color: var(--muted); }
        .form-select:focus {
            border-color: var(--accent);
            box-shadow: 0 0 0 3px var(--accent-light);
        }

        /* Premium Custom Dropdowns */
        .premium-select-container { z-index: 50; }
        .premium-select-menu {
            box-shadow: 0 20px 40px -15px rgba(0,0,0,0.15);
            border: 1px solid var(--border);
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
        }
        .premium-trigger {
            min-width: 180px;
            background-color: var(--white) !important;
            user-select: none;
        }
        .premium-select-item.active {
            background: var(--accent-light) !important;
            color: var(--accent) !important;
        }
        .premium-select-item:hover:not(.active) {
            background: var(--bg);
        }
    </style>
</head>
<body>
    <div class="mobile-overlay" onclick="closeSidebar()"></div>

    <aside class="sidebar" id="appSidebar">
        <div class="sidebar-logo flex items-center justify-between">
            <a href="<?= APP_URL ?>/student" class="flex items-center gap-2 group">
                <div class="logo-mark transition-transform group-hover:scale-110">
                    <?php if (!empty($site_settings['site_logo'])): ?>
                        <img src="<?= APP_URL ?>/public/assets/images/<?= htmlspecialchars($site_settings['site_logo']) ?>" class="w-full h-full object-contain" />
                    <?php else: ?>
                        <i class="fa-solid fa-graduation-cap"></i>
                    <?php endif; ?>
                </div>
                <span class="logo-name"><?= htmlspecialchars($site_settings['site_name'] ?? APP_NAME) ?></span>
            </a>
            <button onclick="closeSidebar()" class="md:hidden text-gray-500 hover:text-gray-900 w-8 h-8 flex items-center justify-center rounded-lg bg-gray-50 border border-gray-200">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <?php
        $curr = $_SERVER['REQUEST_URI'];
        $navItems = [
            ['icon' => 'fa-house',         'url' => '/student',               'label' => 'Dashboard',     'match' => '#/student$#'],
            ['icon' => 'fa-calendar-days', 'url' => '/student/schedule',      'label' => 'Class Times',   'match' => '#/student/schedule#'],
            ['icon' => 'fa-book-open',     'url' => '/student/courses',       'label' => 'My Classes',    'match' => '#/student/courses#'],
            ['icon' => 'fa-receipt',       'url' => '/student/slips',         'label' => 'Payments',      'match' => '#/student/slips#'],
            ['icon' => 'fa-bell',          'url' => '/student/notifications', 'label' => 'Notifications', 'match' => '#/student/notifications#'],
            ['icon' => 'fa-graduation-cap','url' => '/student/grading',       'label' => 'My Grades',     'match' => '#/student/grading#'],
        ];
        foreach ($navItems as $item):
            $active = preg_match($item['match'], $curr);
        ?>
        <a href="<?= APP_URL . $item['url'] ?>" class="nav-item <?= $active ? 'active' : '' ?>">
            <i class="fa-solid <?= $item['icon'] ?>"></i>
            <?= $item['label'] ?>
        </a>
        <?php endforeach; ?>

        <div class="nav-spacer"></div>
        <hr class="nav-divider">
        <a href="<?= APP_URL ?>/student/settings" class="nav-item <?= strpos($curr, '/student/settings') !== false ? 'active' : '' ?>">
            <i class="fa-solid fa-user-gear"></i> Settings
        </a>
        <a href="<?= APP_URL ?>/logout" class="nav-item" style="color:#c0392b">
            <i class="fa-solid fa-power-off"></i> Logout
        </a>
    </aside>

    <div class="main">
        <header class="topbar">
            <div class="flex items-center gap-3">
                <button onclick="openSidebar()" class="md:hidden text-gray-500 hover:text-gray-900 focus:outline-none w-8 h-8 flex items-center justify-center rounded-lg bg-gray-50 border border-gray-200">
                    <i class="fa-solid fa-bars"></i>
                </button>
                <span class="topbar-title hidden sm:block"><?= htmlspecialchars($title ?? 'Student Account') ?></span>
                <span class="topbar-title sm:hidden text-lg"><?= htmlspecialchars($title ?? 'Student') ?></span>
            </div>
            <div class="topbar-right">
                <span class="topbar-welcome hidden md:block">Student: <strong><?= htmlspecialchars($user['name'] ?? 'Student') ?></strong></span>
                <div class="topbar-avatar"><?= strtoupper(substr($user['name'] ?? 'S', 0, 2)) ?></div>
            </div>
        </header>

        <div class="page-content">
            <?php if (!empty($_SESSION['flash_success'])): ?>
                <div class="flash-success"><i class="fa-solid fa-circle-check"></i> <?= htmlspecialchars($_SESSION['flash_success']) ?></div>
                <?php unset($_SESSION['flash_success']); ?>
            <?php endif; ?>
            <?php if (!empty($_SESSION['flash_error'])): ?>
                <div class="flash-error"><i class="fa-solid fa-circle-exclamation"></i> <?= htmlspecialchars($_SESSION['flash_error']) ?></div>
                <?php unset($_SESSION['flash_error']); ?>
            <?php endif; ?>

            <?php require_once $content; ?>
        </div>
    </div>

    <script src="<?= APP_URL ?>/public/assets/js/app.js"></script>
    <script>
        function openSidebar() {
            document.getElementById('appSidebar').classList.add('open');
            document.querySelector('.mobile-overlay').classList.add('open');
        }
        function closeSidebar() {
            document.getElementById('appSidebar').classList.remove('open');
            document.querySelector('.mobile-overlay').classList.remove('open');
        }
    </script>
</body>
</html>

