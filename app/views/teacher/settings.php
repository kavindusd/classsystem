<div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Account Preferences</h1>
        <p class="text-gray-500 text-sm">Manage your professional profile and security protocols.</p>
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
                <div class="relative group">
                    <?php if (!empty($user['profile_image'])): ?>
                        <div class="w-20 h-20 rounded-full overflow-hidden shadow-sm border border-gray-200">
                            <img src="<?= APP_URL ?>/public/uploads/profiles/<?= htmlspecialchars($user['profile_image']) ?>" class="w-full h-full object-cover">
                        </div>
                    <?php else: ?>
                        <div class="w-20 h-20 bg-emerald-100 text-emerald-700 rounded-full flex items-center justify-center text-2xl font-bold shadow-sm border border-emerald-200">
                            <?= strtoupper(substr($user['name'] ?? '?', 0, 1)) ?>
                        </div>
                    <?php endif; ?>
                </div>
                <div>
                    <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider block mb-1">Faculty Identifier</span>
                    <h3 class="text-xl font-bold text-gray-900"><?= htmlspecialchars($teacher['teacher_id']) ?></h3>
                </div>
            </div>
            
            <form method="POST" action="<?= APP_URL ?>/teacher/settings/update" enctype="multipart/form-data" class="space-y-5">
                <div>
                    <label class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-2 block">Update Avatar</label>
                    <input type="file" name="profile_image" accept="image/*" class="w-full text-xs text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-bold file:uppercase file:tracking-wider file:bg-gray-100 file:text-gray-700 hover:file:bg-gray-200 file:transition-colors file:cursor-pointer">
                </div>
                
                <div>
                    <label class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-2 block">Full Legal Name</label>
                    <input type="text" name="name" value="<?= htmlspecialchars($user['name']) ?>" required
                           class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:outline-none text-sm font-medium">
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-2 block">Email Address</label>
                        <input type="email" name="email" value="<?= htmlspecialchars($user['email'] ?? '') ?>"
                               class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:outline-none text-sm font-medium">
                    </div>
                    <div>
                        <label class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-2 block">Contact Number</label>
                        <input type="text" name="phone" value="<?= htmlspecialchars($user['phone'] ?? '') ?>"
                               class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:outline-none text-sm font-medium">
                    </div>
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
            <?php if ($teacher['is_first_login']): ?>
                <div class="bg-amber-50 border border-amber-200 p-4 rounded-lg mb-6 flex gap-3">
                    <i class="fa-solid fa-triangle-exclamation text-amber-500 mt-0.5"></i>
                    <div>
                        <p class="text-xs font-bold text-amber-800 uppercase tracking-wider mb-1">Mandatory Update</p>
                        <p class="text-xs text-amber-700">Please establish a personalized security credential to protect your portal.</p>
                    </div>
                </div>
            <?php endif; ?>

            <form method="POST" action="<?= APP_URL ?>/teacher/settings/change-password" class="space-y-5">
                <div>
                    <label class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-2 block">Current Credentials</label>
                    <input type="password" name="current_password" required placeholder="••••••••"
                           class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:outline-none text-sm font-medium">
                </div>
                
                <hr class="border-gray-100 my-2">
                
                <div>
                    <label class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-2 block">New Security Key</label>
                    <input type="password" name="password" required placeholder="••••••••"
                           class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:outline-none text-sm font-medium">
                    <p class="text-[10px] text-gray-400 mt-1.5 uppercase tracking-wider font-bold">Minimum 8 characters recommended</p>
                </div>
                
                <div>
                    <label class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-2 block">Verify New Key</label>
                    <input type="password" name="confirm_password" required placeholder="••••••••"
                           class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:outline-none text-sm font-medium">
                </div>
                
                <button type="submit" class="w-full bg-emerald-600 text-white px-4 py-2.5 rounded-lg font-bold hover:bg-emerald-700 transition-colors shadow-sm flex items-center justify-center gap-2 mt-2">
                    Update Security
                </button>
            </form>
        </div>
    </div>
</div>
