<?php
$isEnrolled = !empty($enrolled);
$accessState = !empty($existingSlip) ? $existingSlip['status'] : 'none';
$materials = $materials ?? [];
?>
<div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
    <div class="flex items-center gap-4">
        <a href="<?= APP_URL ?>/student/courses" class="w-10 h-10 rounded-lg bg-white border border-gray-200 flex items-center justify-center text-gray-500 hover:text-gray-900 hover:bg-gray-50 transition-colors shadow-sm">
            <i class="fa-solid fa-arrow-left"></i>
        </a>
        <div>
            <p class="text-xs text-gray-400 font-bold uppercase tracking-widest mb-0.5">Explore / <?= htmlspecialchars($course['subject']) ?></p>
            <h1 class="text-2xl font-bold text-gray-900"><?= htmlspecialchars($course['name']) ?></h1>
        </div>
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

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8 pb-10">

    <!-- Left: Payment / Join Details -->
    <div class="flex flex-col gap-6">

        <!-- Course Access / Payment Block -->
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 relative overflow-hidden group">
            <div class="absolute top-0 right-0 w-32 h-32 bg-emerald-50 rounded-bl-full -z-10 group-hover:scale-110 transition-transform duration-500"></div>
            
            <div class="flex justify-between items-start mb-6 relative z-10">
                <div class="w-12 h-12 bg-emerald-100 text-emerald-600 rounded-lg flex items-center justify-center text-xl shadow-sm border border-emerald-200">
                    <i class="fa-solid fa-file-invoice-dollar"></i>
                </div>
                <div class="text-right">
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">Monthly Fee</p>
                    <p class="text-2xl font-bold text-emerald-600 tracking-tight">LKR <?= number_format($course['price'], 0) ?></p>
                </div>
            </div>

            <div class="space-y-4 relative z-10">
                <?php if ($accessState === 'approved'): ?>
                    <div class="bg-emerald-50 border border-emerald-200 rounded-lg p-4 flex gap-3">
                        <i class="fa-solid fa-circle-check text-emerald-500 mt-0.5"></i>
                        <div>
                            <p class="text-xs font-bold text-emerald-800 uppercase tracking-wider mb-1">Access Granted</p>
                            <p class="text-xs text-emerald-700">Your audit for this cycle is verified.</p>
                        </div>
                    </div>

                    <?php if (!empty($course['join_link'])): ?>
                        <a href="<?= htmlspecialchars($course['join_link']) ?>" target="_blank" 
                           class="w-full bg-emerald-600 text-white px-4 py-3 rounded-lg font-bold hover:bg-emerald-700 transition-colors shadow-sm flex items-center justify-center gap-2">
                            <i class="fa-solid fa-video"></i> Join Live Session
                        </a>
                    <?php else: ?>
                        <div class="w-full bg-gray-100 text-gray-500 px-4 py-3 rounded-lg font-bold text-sm text-center border border-gray-200 border-dashed">
                            Session link not broadcasted yet
                        </div>
                    <?php endif; ?>

                <?php elseif ($accessState === 'pending'): ?>
                    <div class="bg-amber-50 border border-amber-200 rounded-lg p-4 flex gap-3">
                        <i class="fa-solid fa-hourglass-half text-amber-500 mt-0.5"></i>
                        <div>
                            <p class="text-xs font-bold text-amber-800 uppercase tracking-wider mb-1">Audit in Progress</p>
                            <p class="text-xs text-amber-700">Slip uploaded. Waiting for admin verification.</p>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="bg-red-50 border border-red-200 rounded-lg p-4 flex gap-3">
                        <i class="fa-solid fa-lock text-red-500 mt-0.5"></i>
                        <div>
                            <p class="text-xs font-bold text-red-800 uppercase tracking-wider mb-1">Access Restricted</p>
                            <p class="text-xs text-red-700">
                                <?= !$isEnrolled ? "Submit your first month's slip to initiate enrollment." : "Submit this month's slip to unlock materials and live sessions." ?>
                            </p>
                        </div>
                    </div>

                    <!-- Upload Form -->
                    <form method="POST" action="<?= APP_URL ?>/student/slips/<?= $course['id'] ?>/submit" enctype="multipart/form-data" class="mt-4 pt-4 border-t border-gray-100">
                        <label class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-2 block">Upload Slip (Images only)</label>
                        <input type="file" name="slip_file" accept="image/*" required
                               class="w-full text-xs text-gray-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-bold file:uppercase file:tracking-wider file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100 file:transition-colors file:cursor-pointer mb-4">
                        <button type="submit" class="w-full bg-gray-900 text-white px-4 py-3 rounded-lg font-bold hover:bg-gray-800 transition-colors shadow-sm flex items-center justify-center gap-2">
                            <i class="fa-solid fa-arrow-up-from-bracket text-xs text-gray-400"></i> Submit Audit
                        </button>
                    </form>
                <?php endif; ?>
            </div>
        </div>

        <!-- Instructor Profile -->
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 flex items-center gap-4">
            <div class="w-14 h-14 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center text-xl font-bold border border-emerald-200">
                <?= substr($course['teacher_name'], 0, 1) ?>
            </div>
            <div>
                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-0.5">Lead Instructor</p>
                <h4 class="font-bold text-gray-900 leading-tight"><?= htmlspecialchars($course['teacher_name']) ?></h4>
            </div>
        </div>
    </div>

    <!-- Right: Material & Schedule -->
    <div class="lg:col-span-2 flex flex-col gap-6">

        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="p-6 border-b border-gray-100">
                <h2 class="font-bold text-gray-900">Course Materials</h2>
            </div>
            
            <?php if (!$isEnrolled || $accessState !== 'approved'): ?>
                <div class="p-16 text-center bg-gray-50/50">
                    <div class="w-16 h-16 bg-white border border-gray-200 rounded-full flex items-center justify-center mx-auto mb-4 text-gray-300 shadow-sm">
                        <i class="fa-solid fa-lock text-xl"></i>
                    </div>
                    <h3 class="font-bold text-gray-900 mb-1">Vault Locked</h3>
                    <p class="text-gray-500 text-sm">Valid payment audit required for the current cycle to access materials.</p>
                </div>
            <?php else: ?>
                <?php if (empty($materials)): ?>
                    <div class="p-12 text-center">
                        <p class="text-gray-400 text-sm italic font-medium">No materials have been uploaded by the instructor yet.</p>
                    </div>
                <?php else: ?>
                    <div class="divide-y divide-gray-100">
                        <?php foreach ($materials as $m): ?>
                        <div class="p-5 flex items-center justify-between hover:bg-gray-50 transition-colors group">
                            <div class="flex items-center gap-4">
                                <div class="w-10 h-10 rounded-lg bg-gray-100 text-gray-500 flex items-center justify-center group-hover:bg-emerald-100 group-hover:text-emerald-600 transition-colors border border-gray-200">
                                    <i class="fa-solid fa-file-pdf"></i>
                                </div>
                                <div>
                                    <h4 class="font-bold text-gray-900 text-sm mb-0.5"><?= htmlspecialchars($m['title']) ?></h4>
                                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Posted <?= date('M d, Y', strtotime($m['created_at'])) ?></p>
                                </div>
                            </div>
                            <a href="<?= APP_URL ?>/public/uploads/materials/<?= htmlspecialchars($m['file_path']) ?>" download
                               class="w-8 h-8 rounded-lg bg-white border border-gray-200 text-gray-500 flex items-center justify-center hover:bg-emerald-600 hover:text-white hover:border-emerald-600 transition-all shadow-sm">
                                <i class="fa-solid fa-download text-[10px]"></i>
                            </a>
                        </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
</div>
