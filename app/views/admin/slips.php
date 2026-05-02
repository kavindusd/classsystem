<div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Payment Slips</h1>
        <p class="text-gray-500 text-sm">Review and reconcile academic transaction slips.</p>
    </div>
</div>

<?php if ($msg = Session::flash('success')): ?>
    <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-lg mb-6 text-sm font-medium">
        <i class="fa-solid fa-check-circle mr-2"></i>
        <?= htmlspecialchars($msg) ?>
    </div>
<?php endif; ?>

<!-- Filter -->
<div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm mb-8">
    <form method="GET" class="flex flex-col md:flex-row md:items-end gap-4">
        <div class="flex-1">
            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Status</label>
            <select name="status" class="w-full px-4 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                <option value="pending" <?= $status === 'pending' ? 'selected' : '' ?>>Pending Review</option>
                <option value="approved" <?= $status === 'approved' ? 'selected' : '' ?>>Verified Only</option>
                <option value="rejected" <?= $status === 'rejected' ? 'selected' : '' ?>>Declined Only</option>
                <option value="all" <?= $status === 'all' ? 'selected' : '' ?>>All Records</option>
            </select>
        </div>

        <div class="flex-1">
            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Month</label>
            <input type="month" name="month" value="<?= $month ?>"
                   class="w-full px-4 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-emerald-500 focus:outline-none">
        </div>

        <button type="submit" class="w-full md:w-auto py-2.5 bg-emerald-600 text-white rounded-lg font-bold hover:bg-emerald-700 transition-colors px-6">
            Apply Audit Filter
        </button>

        <div class="text-xs font-bold text-gray-600 md:ml-auto bg-gray-50 border border-gray-200 rounded-lg px-4 py-2">
            Queue Size: <?= count($slips) ?>
        </div>
    </form>
</div>

<!-- Slips Table -->
<div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
    <div class="p-6 border-b border-gray-100 flex items-center justify-between gap-4">
        <h2 class="font-bold text-gray-900">Queue</h2>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead class="bg-gray-50 border-b border-gray-100">
                <tr>
                    <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Student</th>
                    <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Course</th>
                    <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Month</th>
                    <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-4 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>

            <tbody class="divide-y divide-gray-100">
                <?php if (empty($slips)): ?>
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-gray-400 text-sm italic">No slips found for this selection.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($slips as $s): ?>
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="font-bold text-gray-900"><?= htmlspecialchars($s['student_name']) ?></div>
                                <div class="text-[11px] text-gray-500 mt-1"><?= htmlspecialchars($s['student_id']) ?></div>
                            </td>

                            <td class="px-6 py-4">
                                <div class="font-medium text-gray-900"><?= htmlspecialchars($s['course_name']) ?></div>
                                <div class="text-[11px] text-gray-500 mt-1"><?= htmlspecialchars($s['subject'] ?? ($s['course_subject'] ?? '')) ?></div>
                            </td>

                            <td class="px-6 py-4">
                                <span class="px-2 py-1 bg-gray-100 text-gray-600 text-[11px] font-bold rounded uppercase">
                                    <?= htmlspecialchars($s['slip_month']) ?>
                                </span>
                            </td>

                            <td class="px-6 py-4">
                                <?php if ($s['status'] === 'pending'): ?>
                                    <span class="px-3 py-1 bg-amber-50 text-amber-700 text-[11px] font-bold rounded-full border border-amber-100">
                                        <i class="fa-solid fa-clock mr-1"></i> Pending
                                    </span>
                                <?php elseif ($s['status'] === 'approved'): ?>
                                    <span class="px-3 py-1 bg-emerald-50 text-emerald-700 text-[11px] font-bold rounded-full border border-emerald-100">
                                        <i class="fa-solid fa-check-double mr-1"></i> Approved
                                    </span>
                                <?php else: ?>
                                    <span class="px-3 py-1 bg-rose-50 text-rose-700 text-[11px] font-bold rounded-full border border-rose-100">
                                        <i class="fa-solid fa-xmark mr-1"></i> Rejected
                                    </span>
                                <?php endif; ?>

                                <?php if (!empty($s['rejection_reason'])): ?>
                                    <div class="text-[11px] text-rose-600 font-bold mt-2 max-w-[220px] truncate" title="<?= htmlspecialchars($s['rejection_reason']) ?>">
                                        Reason: <?= htmlspecialchars($s['rejection_reason']) ?>
                                    </div>
                                <?php endif; ?>

                                <?php if (!empty($s['reviewed_at'])): ?>
                                    <div class="text-[11px] text-gray-500 mt-2">
                                        Processed: <?= date('d M, Y', strtotime($s['reviewed_at'])) ?>
                                    </div>
                                <?php endif; ?>
                            </td>

                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="<?= APP_URL ?>/public/uploads/slips/<?= htmlspecialchars($s['file_path']) ?>" target="_blank"
                                       class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg font-bold hover:bg-gray-200 transition-colors text-sm border border-gray-200 flex items-center gap-2">
                                        <i class="fa-solid fa-eye"></i> View
                                    </a>
                                    <?php if ($s['status'] === 'pending'): ?>
                                        <form method="POST" action="<?= APP_URL ?>/admin/slips/approve/<?= $s['id'] ?>">
                                            <button class="px-5 py-2 bg-emerald-600 text-white rounded-lg font-bold hover:bg-emerald-700 transition-colors text-sm">
                                                Verify
                                            </button>
                                        </form>
                                        <button
                                            type="button"
                                            onclick="openRejectModal(<?= (int)$s['id'] ?>)"
                                            class="px-4 py-2 bg-rose-50 text-rose-600 rounded-lg font-bold hover:bg-rose-600 hover:text-white transition-colors text-sm border border-rose-100"
                                        >
                                            Decline
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Reject Modal -->
<div id="rejectModal" class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-xl w-full max-w-md overflow-hidden shadow-xl">
        <div class="p-5 border-b border-gray-100 flex items-center justify-between">
            <h3 class="font-bold text-gray-900 text-lg">Decline Audit</h3>
            <button type="button" onclick="closeRejectModal()" class="text-gray-400 hover:text-gray-600">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <form method="POST" id="rejectForm" class="p-5 space-y-4">
            <div class="flex flex-col gap-2">
                <label class="text-xs font-bold text-gray-700 uppercase tracking-wider">Audit Feedback</label>
                <textarea name="reason" required
                          class="w-full px-4 py-3 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-emerald-500 focus:outline-none min-h-[120px]"
                          placeholder="Incorrect amount, blurry image, etc..."></textarea>
            </div>

            <div class="flex items-center gap-3">
                <button type="button" onclick="closeRejectModal()"
                        class="flex-1 py-3 bg-gray-100 text-gray-700 rounded-lg font-bold hover:bg-gray-200 transition-colors">
                    Cancel
                </button>
                <button type="submit"
                        class="flex-[2] py-3 bg-rose-600 text-white rounded-lg font-bold hover:bg-rose-700 transition-colors">
                    Decline Submission
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function openRejectModal(id) {
    document.getElementById('rejectForm').action = '<?= APP_URL ?>/admin/slips/reject/' + id;
    document.getElementById('rejectModal').classList.remove('hidden');
}

function closeRejectModal() {
    document.getElementById('rejectModal').classList.add('hidden');
}
</script>
