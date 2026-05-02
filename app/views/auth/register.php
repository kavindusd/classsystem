<div class="auth-card rounded-[1.5rem] md:rounded-[2rem] p-6 sm:p-8 md:p-10 lg:p-14">
    <div class="text-center mb-12">
        <h2 class="text-3xl font-black syne-font text-emerald-600 mb-2">Join Us</h2>
        <p class="text-emerald-600/60 text-sm font-medium uppercase tracking-widest text-[10px]">Create Your Account</p>
    </div>

    <?php if ($err = Session::flash('error')): ?>
        <div class="bg-red-50 border border-red-100 text-red-600 px-5 py-3 rounded-2xl mb-8 text-sm font-semibold flex items-center gap-3">
            <i class="fa-solid fa-circle-exclamation"></i>
            <?= htmlspecialchars($err) ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="<?= APP_URL ?>/register" class="space-y-8">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="space-y-2">
                <label class="text-[10px] font-bold text-emerald-600 uppercase tracking-[0.15em] ml-2">Your Full Name</label>
                <div class="relative group">
                    <i class="fa-solid fa-user absolute left-5 top-1/2 -translate-y-1/2 text-emerald-600/30 group-focus-within:text-emerald-600 transition-colors"></i>
                    <input type="text" name="name" required placeholder="Enter full name"
                           class="w-full pl-12 pr-6 py-4 bg-appBg border border-emerald-600/10 rounded-2xl focus:border-emerald-600 focus:bg-white focus:outline-none text-sm transition-all font-medium">
                </div>
            </div>

            <div class="space-y-2">
                <label class="text-[10px] font-bold text-emerald-600 uppercase tracking-[0.15em] ml-2">Email or Phone</label>
                <div class="relative group">
                    <i class="fa-solid fa-envelope absolute left-5 top-1/2 -translate-y-1/2 text-emerald-600/30 group-focus-within:text-emerald-600 transition-colors"></i>
                    <input type="text" name="contact" required placeholder="contact@example.com"
                           class="w-full pl-12 pr-6 py-4 bg-appBg border border-emerald-600/10 rounded-2xl focus:border-emerald-600 focus:bg-white focus:outline-none text-sm transition-all font-medium">
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="space-y-2">
                <label class="text-[10px] font-bold text-emerald-600 uppercase tracking-[0.15em] ml-2">Password</label>
                <div class="relative group">
                    <i class="fa-solid fa-lock absolute left-5 top-1/2 -translate-y-1/2 text-emerald-600/30 group-focus-within:text-emerald-600 transition-colors"></i>
                    <input type="password" name="password" required placeholder="••••••••"
                           class="w-full pl-12 pr-6 py-4 bg-appBg border border-emerald-600/10 rounded-2xl focus:border-emerald-600 focus:bg-white focus:outline-none text-sm transition-all font-medium">
                </div>
            </div>

            <div class="space-y-2">
                <label class="text-[10px] font-bold text-emerald-600 uppercase tracking-[0.15em] ml-2">Confirm Password</label>
                <div class="relative group">
                    <i class="fa-solid fa-shield-check absolute left-5 top-1/2 -translate-y-1/2 text-emerald-600/30 group-focus-within:text-emerald-600 transition-colors"></i>
                    <input type="password" name="confirm_password" required placeholder="••••••••"
                           class="w-full pl-12 pr-6 py-4 bg-appBg border border-emerald-600/10 rounded-2xl focus:border-emerald-600 focus:bg-white focus:outline-none text-sm transition-all font-medium">
                </div>
            </div>
        </div>

        <button type="submit" class="w-full py-5 bg-emerald-600 text-white rounded-2xl font-black syne-font text-sm uppercase tracking-widest hover:bg-emerald-700 transition-all shadow-xl shadow-emerald-600/20 active:scale-[0.98]">
            Create My Account
        </button>
    </form>

    <div class="mt-12 text-center pt-8 border-t border-emerald-600/5">
        <p class="text-xs text-emerald-600/40 font-bold">
            Already have an account? 
            <a href="<?= APP_URL ?>/login" class="text-emerald-600 hover:underline underline-offset-4 decoration-2 ml-1">Sign In Securely</a>
        </p>
    </div>
</div>