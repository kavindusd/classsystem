<div class="auth-card rounded-[1.5rem] md:rounded-[2rem] p-6 sm:p-8 md:p-10">
    <div class="text-center mb-10">
        <h2 class="text-3xl font-black syne-font text-emerald-600 mb-2">Identity Check</h2>
        <p class="text-emerald-600/60 text-sm font-medium">Verification Required</p>
    </div>

    <?php if ($err = Session::flash('error')): ?>
        <div class="bg-red-50 border border-red-100 text-red-600 px-5 py-3 rounded-2xl mb-6 text-sm font-semibold flex items-center gap-3">
            <i class="fa-solid fa-circle-exclamation"></i>
            <?= htmlspecialchars($err) ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="<?= APP_URL ?>/verify-otp" class="space-y-6">
        <input type="hidden" name="purpose" value="<?= htmlspecialchars($purpose) ?>">
        
        <div class="space-y-2">
            <label class="text-[10px] font-bold text-emerald-600 uppercase tracking-[0.15em] ml-2 text-center block">Secure Intelligence Code</label>
            <div class="relative group">
                <i class="fa-solid fa-key absolute left-5 top-1/2 -translate-y-1/2 text-emerald-600/30 group-focus-within:text-emerald-600 transition-colors"></i>
                <input type="text" name="otp" required maxlength="6" autofocus placeholder="••••••"
                       class="w-full pl-12 pr-6 py-4 bg-appBg border border-emerald-600/10 rounded-2xl focus:border-emerald-600 focus:bg-white focus:outline-none text-xl text-center font-black tracking-[0.5em] text-emerald-600 transition-all placeholder:text-emerald-600/20 placeholder:tracking-normal">
            </div>
        </div>

        <button type="submit" class="w-full py-5 bg-emerald-600 text-white rounded-2xl font-black syne-font text-sm uppercase tracking-widest hover:bg-emerald-700 transition-all shadow-xl shadow-emerald-600/20 active:scale-[0.98]">
            Establish Identity
        </button>
    </form>

    <div class="mt-10 text-center pt-8 border-t border-emerald-600/5">
        <p class="text-xs text-emerald-600/40 font-bold">
            Missed the intel? 
            <a href="#" class="text-emerald-600 hover:underline underline-offset-4 decoration-2">Request Redispatch</a>
        </p>
    </div>
</div>