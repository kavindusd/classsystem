<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>403 — Restricted</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-[#f5f2eb] flex items-center justify-center min-h-screen p-6">
    <div class="max-w-md w-full bg-white rounded-[3rem] shadow-2xl p-12 text-center flex flex-col items-center gap-6 border-b-8 border-rose-100">
        <div class="w-24 h-24 bg-rose-50 rounded-full flex items-center justify-center text-5xl text-rose-200">
            <i class="fa-solid fa-user-lock"></i>
        </div>
        <div>
            <h1 class="text-8xl font-black text-rose-50 leading-none">403</h1>
            <h2 class="text-2xl font-black text-textMain mt-2 italic tracking-tight">Access Denied</h2>
            <p class="text-textMuted mt-4 font-medium leading-relaxed">
                You do not have the required permissions to access this restricted academic area.
            </p>
        </div>
        <a href="<?= APP_URL ?>" class="mt-4 px-10 py-4 bg-gray-100 text-textMain rounded-2xl font-black hover:bg-gray-200 hover:-translate-y-1 transition-all">
            Go Back Home
        </a>
    </div>
</body>
</html>
