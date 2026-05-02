<div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Account Preferences</h1>
        <p class="text-gray-500 text-sm">Manage your profile and security protocols.</p>
    </div>
</div>

<?php if ($msg = Session::flash('success')): ?>
    <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-lg mb-6 text-sm font-medium flex items-center gap-2">
        <i class="fa-solid fa-circle-check"></i>
        <?= htmlspecialchars($msg) ?>
    </div>
<?php endif; ?>

<?php if ($err = Session::flash('error')): ?>
    <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-6 text-sm font-medium flex items-center gap-2">
        <i class="fa-solid fa-circle-exclamation"></i>
        <?= htmlspecialchars($err) ?>
    </div>
<?php endif; ?>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-8 pb-10">
    
    <!-- Profile Module -->
    <div class="space-y-4">
        <h2 class="font-bold text-gray-900 flex items-center gap-2">
            <i class="fa-solid fa-id-card text-gray-400"></i>
            Personal Identity
        </h2>
        
        <div class="bg-white border border-gray-200 p-6 rounded-xl shadow-sm">
            <div class="flex items-center gap-6 mb-8">
                <div class="w-20 h-20 bg-emerald-100 text-emerald-700 rounded-full flex items-center justify-center text-2xl font-bold shadow-sm border border-emerald-200">
                    <?= strtoupper(substr($user['name'] ?? '?', 0, 1)) ?>
                </div>
                <div>
                    <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider block mb-1">Student Identifier</span>
                    <h3 class="text-xl font-bold text-gray-900"><?= htmlspecialchars($student['student_id']) ?></h3>
                </div>
            </div>
            
            <form method="POST" action="<?= APP_URL ?>/student/settings/update" class="space-y-5">
                
                <div>
                    <label class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-2 block">Full Legal Name</label>
                    <input type="text" name="name" value="<?= htmlspecialchars($user['name']) ?>" required
                           class="form-input w-full">
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-2 block">Email Address</label>
                        <input type="email" name="email" value="<?= htmlspecialchars($user['email'] ?? '') ?>"
                               class="form-input w-full">
                    </div>
                    <div>
                        <label class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-2 block">Personal Phone</label>
                        <input type="text" name="phone" value="<?= htmlspecialchars($user['phone'] ?? '') ?>"
                               class="form-input w-full">
                    </div>
                </div>

                <div>
                    <label class="text-xs font-bold text-emerald-600 uppercase tracking-wider mb-2 flex items-center gap-2">
                        <i class="fa-brands fa-whatsapp"></i>
                        WhatsApp Intelligence Link
                    </label>
                    <input type="text" name="whatsapp_number" value="<?= htmlspecialchars($student['whatsapp_number'] ?? '') ?>" 
                           placeholder="e.g. 94771234567"
                           class="form-input w-full border-emerald-100 bg-emerald-50/10 focus:border-emerald-500">
                    <p class="text-[10px] text-gray-400 mt-1.5 leading-relaxed font-medium">Connect your WhatsApp number with country code (e.g., 94 for Sri Lanka) to receive academic alerts and direct communication from teachers.</p>
                </div>
                
                <button type="submit" class="w-full bg-emerald-600 text-white px-4 py-2.5 rounded-lg font-bold hover:bg-emerald-700 transition-colors shadow-sm flex items-center justify-center gap-2 mt-2">
                    Synchronize Profile
                </button>
            </form>
        </div>
    </div>

    <!-- Security Module -->
    <div class="space-y-4">
        <h2 class="font-bold text-gray-900 flex items-center gap-2">
            <i class="fa-solid fa-shield-halved text-gray-400"></i>
            Security Protocols
        </h2>
        
        <div class="bg-white border border-gray-200 p-6 rounded-xl shadow-sm">
            <?php if (!empty($student['is_first_login'])): ?>
                <div class="bg-amber-50 border border-amber-200 p-4 rounded-lg mb-6 flex gap-3">
                    <i class="fa-solid fa-triangle-exclamation text-amber-500 mt-0.5"></i>
                    <div>
                        <p class="text-xs font-bold text-amber-800 uppercase tracking-wider mb-1">Mandatory Update</p>
                        <p class="text-xs text-amber-700">Please establish a personalized security credential to protect your portal.</p>
                    </div>
                </div>
            <?php endif; ?>

            <form method="POST" action="<?= APP_URL ?>/student/settings/change-password" class="space-y-5">
                <div>
                    <label class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-2 block">Current Credentials</label>
                    <input type="password" name="current_password" required placeholder="••••••••"
                           class="form-input w-full">
                </div>
                
                <hr class="border-gray-100 my-2">
                
                <div>
                    <label class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-2 block">New Security Key</label>
                    <input type="password" name="password" required placeholder="••••••••"
                           class="form-input w-full">
                    <p class="text-[10px] text-gray-400 mt-1.5 uppercase tracking-wider font-bold">Minimum 8 characters recommended</p>
                </div>
                
                <div>
                    <label class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-2 block">Verify New Key</label>
                    <input type="password" name="confirm_password" required placeholder="••••••••"
                           class="form-input w-full">
                </div>
                
                <button type="submit" class="w-full bg-emerald-600 text-white px-4 py-2.5 rounded-lg font-bold hover:bg-emerald-700 transition-colors shadow-sm flex items-center justify-center gap-2 mt-2">
                    Update Security
                </button>
            </form>
        </div>
    </div>
</div>
