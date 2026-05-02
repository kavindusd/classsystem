<div class="auth-card rounded-[1.5rem] md:rounded-[2rem] p-6 sm:p-8 md:p-10 lg:p-14">
    <div class="text-center mb-10">
        <h2 class="text-3xl font-black syne-font text-emerald-600 mb-2">Recover Account</h2>
        <p class="text-emerald-600/60 text-sm font-medium uppercase tracking-widest text-[10px]">Recovery Protocol</p>
    </div>

    <?php if ($err = Session::flash('error')): ?>
        <div class="bg-red-50 border border-red-100 text-red-600 px-5 py-3 rounded-2xl mb-8 text-sm font-semibold flex items-center gap-3">
            <i class="fa-solid fa-circle-exclamation"></i>
            <?= htmlspecialchars($err) ?>
        </div>
    <?php endif; ?>

    <?php if ($msg = Session::flash('success')): ?>
        <div class="bg-emerald-50 border border-emerald-100 text-emerald-600 px-5 py-3 rounded-2xl mb-8 text-sm font-semibold flex items-center gap-3">
            <i class="fa-solid fa-circle-check"></i>
            <?= htmlspecialchars($msg) ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="<?= APP_URL ?>/forgot-password" class="space-y-6">
        <div class="space-y-2">
            <label class="text-[10px] font-bold text-emerald-600 uppercase tracking-[0.15em] ml-2">Recovery Identifier</label>
            <div class="relative group">
                <i class="fa-solid fa-envelope absolute left-5 top-1/2 -translate-y-1/2 text-emerald-600/30 group-focus-within:text-emerald-600 transition-colors"></i>
                <input type="text" name="identifier" required placeholder="Email or Phone Number"
                       class="w-full pl-12 pr-6 py-4 bg-appBg border border-emerald-600/10 rounded-2xl focus:border-emerald-600 focus:bg-white focus:outline-none text-sm transition-all font-medium">
            </div>
        </div>

        <button type="submit" class="w-full py-5 bg-emerald-600 text-white rounded-2xl font-black syne-font text-sm uppercase tracking-widest hover:bg-emerald-700 transition-all shadow-xl shadow-emerald-600/20 active:scale-[0.98]">
            Initialize Recovery
        </button>
    </form>

    <div class="mt-12 text-center pt-8 border-t border-emerald-600/5">
        <p class="text-xs text-emerald-600/40 font-bold">
            Identity Found? 
            <a href="<?= APP_URL ?>/login" class="text-emerald-600 hover:underline underline-offset-4 decoration-2 ml-1">Secure Login</a>
        </p>
    </div>
</div>