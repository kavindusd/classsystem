<!-- Header Area -->
<div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Academic Payouts</h1>
        <p class="text-gray-500 text-sm">Audit trail for your course enrollments and monthly slips.</p>
    </div>
    <a href="<?= APP_URL ?>/student/courses" class="bg-emerald-600 text-white px-6 py-2.5 rounded-lg font-bold hover:bg-emerald-700 transition-colors shadow-sm flex items-center gap-2">
        <i class="fa-solid fa-plus-circle text-xs"></i>
        New Enrollment
    </a>
</div>

<?php if ($msg = Session::flash('success')): ?>
    <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-lg mb-6 text-sm font-medium flex items-center gap-2">
        <i class="fa-solid fa-circle-check"></i>
        <?= htmlspecialchars($msg) ?>
    </div>
<?php endif; ?>

<!-- Financial Ledger List -->
<div class="flex flex-col gap-4 pb-10">
    <?php if (empty($slips)): ?>
        <div class="bg-white rounded-xl border border-dashed border-gray-300 py-16 text-center shadow-sm">
            <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4 text-gray-300">
                <i class="fa-solid fa-file-invoice-dollar text-2xl"></i>
            </div>
            <h3 class="font-bold text-gray-900 mb-1">No Payout Records</h3>
            <p class="text-gray-500 text-sm">Initialize enrollment to see your audit trail.</p>
        </div>
    <?php else: ?>
        <?php foreach ($slips as $s): ?>
        <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm hover:border-emerald-500 transition-colors group">
            <div class="flex flex-col md:flex-row md:items-center gap-6">
                <!-- Status Icon -->
                <div class="w-12 h-12 rounded-lg bg-gray-50 flex items-center justify-center text-gray-400 flex-shrink-0 border border-gray-200 group-hover:bg-emerald-50 group-hover:text-emerald-600 group-hover:border-emerald-200 transition-colors">
                    <i class="fa-solid fa-file-invoice-dollar text-lg"></i>
                </div>

                <!-- Record Details -->
                <div class="flex-1">
                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-2 mb-2">
                        <h3 class="font-bold text-gray-900 group-hover:text-emerald-600 transition-colors"><?= htmlspecialchars($s['course_name']) ?></h3>
                        <div class="flex items-center gap-2">
                            <?php if ($s['status'] === 'approved'): ?>
                                <span class="px-2.5 py-1 bg-emerald-100 text-emerald-700 text-[10px] font-bold uppercase tracking-wider rounded">
                                    <i class="fa-solid fa-check-circle mr-1"></i> Audit Verified
                                </span>
                            <?php elseif ($s['status'] === 'pending'): ?>
                                <span class="px-2.5 py-1 bg-amber-100 text-amber-700 text-[10px] font-bold uppercase tracking-wider rounded">
                                    <i class="fa-solid fa-hourglass-half mr-1"></i> Under Review
                                </span>
                            <?php else: ?>
                                <span class="px-2.5 py-1 bg-red-100 text-red-700 text-[10px] font-bold uppercase tracking-wider rounded">
                                    <i class="fa-solid fa-circle-xmark mr-1"></i> Audit Declined
                                </span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="flex flex-wrap items-center gap-y-2 gap-x-6">
                        <div class="flex items-center gap-1.5 text-gray-500">
                            <i class="fa-regular fa-calendar-alt text-[10px] text-gray-400"></i>
                            <span class="text-xs font-bold uppercase tracking-wider"><?= date('F Y', strtotime($s['slip_month'] . '-01')) ?> SESSION</span>
                        </div>
                        <div class="flex items-center gap-1.5 text-gray-500">
                            <i class="fa-regular fa-clock text-[10px] text-gray-400"></i>
                            <span class="text-xs font-medium">Logged <?= date('d M, Y', strtotime($s['submitted_at'])) ?></span>
                        </div>
                        <?php if ($s['status'] === 'rejected' && $s['rejection_reason']): ?>
                        <div class="flex items-center gap-1.5 text-red-600">
                            <i class="fa-solid fa-info-circle text-[10px]"></i>
                            <span class="text-xs font-bold italic"><?= htmlspecialchars($s['rejection_reason']) ?></span>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Navigation Action -->
                <div class="md:pl-6 md:border-l border-gray-100">
                    <a href="<?= APP_URL ?>/student/courses/<?= $s['course_id'] ?>" class="w-10 h-10 rounded-lg bg-gray-50 border border-gray-200 flex items-center justify-center text-gray-500 hover:bg-emerald-600 hover:text-white hover:border-emerald-600 transition-colors shadow-sm">
                        <i class="fa-solid fa-arrow-right text-[10px]"></i>
                    </a>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>
