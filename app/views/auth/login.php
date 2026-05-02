<div class="auth-card rounded-[1.5rem] md:rounded-[2rem] p-6 sm:p-8 md:p-10">
    <div class="text-center mb-10">
        <h2 class="text-3xl font-black syne-font text-emerald-600 mb-2">Welcome back</h2>
        <p class="text-emerald-600/60 text-sm font-medium">Please enter your details to continue</p>
    </div>

    <?php if ($err = Session::flash('error')): ?>
        <div class="bg-red-50 border border-red-100 text-red-600 px-5 py-3 rounded-2xl mb-6 text-sm font-semibold flex items-center gap-3">
            <i class="fa-solid fa-circle-exclamation"></i>
            <?= htmlspecialchars($err) ?>
        </div>
    <?php endif; ?>

    <?php if ($success = Session::flash('success')): ?>
        <div class="bg-emerald-50 border border-emerald-100 text-emerald-600 px-5 py-3 rounded-2xl mb-6 text-sm font-semibold flex items-center gap-3">
            <i class="fa-solid fa-circle-check"></i>
            <?= htmlspecialchars($success) ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="<?= APP_URL ?>/login" class="space-y-6">
        <div class="space-y-2">
            <label class="text-[10px] font-bold text-emerald-600 uppercase tracking-[0.15em] ml-2">Email, Phone or ID</label>
            <div class="relative group">
                <i class="fa-solid fa-user absolute left-5 top-1/2 -translate-y-1/2 text-emerald-600/30 group-focus-within:text-emerald-600 transition-colors"></i>
                <input type="text" name="identifier" required placeholder="Email or Username"
                       class="w-full pl-12 pr-6 py-4 bg-appBg border border-emerald-600/10 rounded-2xl focus:border-emerald-600 focus:bg-white focus:outline-none text-sm transition-all font-medium placeholder:text-emerald-600/20">
            </div>
        </div>

        <div class="space-y-2">
            <div class="flex items-center justify-between px-2">
                <label class="text-[10px] font-bold text-emerald-600 uppercase tracking-[0.15em]">Password</label>
                <a href="<?= APP_URL ?>/forgot-password" class="text-[10px] font-bold text-emerald-600/40 hover:text-emerald-600 transition-colors uppercase tracking-[0.15em]">Forgot?</a>
            </div>
            <div class="relative group">
                <i class="fa-solid fa-lock absolute left-5 top-1/2 -translate-y-1/2 text-emerald-600/30 group-focus-within:text-emerald-600 transition-colors"></i>
                <input type="password" name="password" required placeholder="••••••••"
                       class="w-full pl-12 pr-6 py-4 bg-appBg border border-emerald-600/10 rounded-2xl focus:border-emerald-600 focus:bg-white focus:outline-none text-sm transition-all font-medium placeholder:text-emerald-600/20">
            </div>
        </div>

        <div class="flex items-center px-2">
            <label class="flex items-center gap-3 cursor-pointer group">
                <div class="relative w-5 h-5 flex items-center justify-center">
                    <input type="checkbox" name="remember" class="peer appearance-none w-5 h-5 border-2 border-emerald-600/10 rounded-lg checked:bg-emerald-600 checked:border-emerald-600 transition-all cursor-pointer">
                    <i class="fa-solid fa-check absolute text-white text-[10px] opacity-0 peer-checked:opacity-100 transition-opacity pointer-events-none"></i>
                </div>
                <span class="text-xs text-emerald-600/60 font-bold group-hover:text-emerald-600 transition-colors">Remember me</span>
            </label>
        </div>

        <button type="submit" class="w-full py-5 bg-emerald-600 text-white rounded-2xl font-black syne-font text-sm uppercase tracking-widest hover:bg-emerald-700 transition-all shadow-xl shadow-emerald-600/20 active:scale-[0.98]">
            Login
        </button>
    </form>

    <div class="mt-10 text-center pt-8 border-t border-emerald-600/5">
        <p class="text-xs text-emerald-600/40 font-bold">
            New to our system? 
            <a href="<?= APP_URL ?>/register" class="text-emerald-600 hover:underline underline-offset-4 decoration-2">Create Student Account</a>
        </p>
    </div>
</div>