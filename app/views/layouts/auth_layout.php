<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= APP_NAME ?> — Authentication</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        emerald: {
                            50: '#EAF3DE',
                            600: '#2C5F2E',
                            700: '#245026',
                        },
                        appBg: '#F5F4F0',
                    },
                    fontFamily: {
                        sans: ['Plus Jakarta Sans', 'sans-serif'],
                    }
                }
            }
        }
    </script>

    <style>
        :root {
            --bg: #F5F4F0;
            --accent: #2C5F2E;
            --accent-hover: #245026;
            --text: #1a1a1a;
            --muted: #666;
        }
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--bg);
            color: var(--text);
        }
        .auth-card {
            background: #ffffff;
            border: 1px solid #e8e6e0;
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
        }
        .syne-font { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-4 relative overflow-hidden">
    
    <!-- Ambient Gradients -->
    <div class="absolute top-[-10%] left-[-5%] w-[40rem] h-[40rem] bg-emerald-600/5 rounded-full blur-[100px] pointer-events-none"></div>
    <div class="absolute bottom-[-10%] right-[-5%] w-[30rem] h-[30rem] bg-emerald-700/5 rounded-full blur-[100px] pointer-events-none"></div>

    <div class="relative z-10 w-full flex flex-col items-center gap-8">
        <!-- Logo -->
        <a href="<?= APP_URL ?>" class="flex flex-col items-center gap-3 group">
            <div class="w-20 h-20 transition-transform group-hover:scale-105 overflow-hidden flex items-center justify-center">
                <?php if (!empty($site_settings['site_logo'])): ?>
                    <img src="<?= APP_URL ?>/public/assets/images/<?= htmlspecialchars($site_settings['site_logo']) ?>" class="w-full h-full object-contain" />
                <?php else: ?>
                    <div class="w-full h-full bg-emerald-600 text-white rounded-[1.5rem] flex items-center justify-center text-4xl shadow-xl">
                        <i class="fa-solid fa-graduation-cap"></i>
                    </div>
                <?php endif; ?>
            </div>
            <h1 class="text-2xl font-extrabold syne-font text-emerald-600 tracking-tight uppercase"><?= htmlspecialchars($site_settings['site_name'] ?? APP_NAME) ?></h1>
        </a>

        <!-- Dynamic Content -->
        <div class="w-full max-w-md">
            <?php require_once $content; ?>
        </div>

        <!-- Footer -->
        <p class="text-[11px] font-bold text-emerald-600/40 uppercase tracking-widest syne-font">
            &copy; <?= date('Y') ?> <?= APP_NAME ?> Systems • Premium Portal
        </p>
    </div>

    <script src="<?= APP_URL ?>/public/assets/js/app.js"></script>
</body>
</html>

