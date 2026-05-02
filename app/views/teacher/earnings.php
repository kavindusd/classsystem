<div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Earnings</h1>
        <p class="text-sm text-gray-500 mt-0.5">Detailed transparency into your monthly payouts and module performance.</p>
    </div>
    <form method="GET" class="flex items-center gap-3 bg-white border border-gray-200 rounded-xl px-4 py-2 shadow-sm">
        <label class="text-xs font-bold text-gray-500 uppercase tracking-wider whitespace-nowrap">Cycle</label>
        <input type="month" name="month" value="<?= $selectedMonth ?>" onchange="this.form.submit()"
               class="bg-gray-50 border border-gray-200 rounded-lg px-3 py-1.5 text-sm font-bold outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all text-gray-900">
    </form>
</div>

<!-- Stats Cards -->
<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">

    <!-- Total Earnings -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
        <div class="flex items-center justify-between mb-4">
            <div class="w-10 h-10 bg-emerald-100 text-emerald-600 rounded-lg flex items-center justify-center">
                <i class="fa-solid fa-vault"></i>
            </div>
            <span class="px-2.5 py-1 bg-emerald-100 text-emerald-700 text-xs font-bold rounded-lg uppercase tracking-wider">
                <?= date('M Y', strtotime($selectedMonth . '-01')) ?>
            </span>
        </div>
        <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Net Faculty Payout</p>
        <p class="text-3xl font-bold text-gray-900">
            <span class="text-lg font-bold text-gray-400">LKR</span>
            <?= number_format($summary['total_earnings'] ?? 0, 0) ?>
        </p>
        <p class="text-xs text-gray-500 mt-3 flex items-center gap-1.5">
            <i class="fa-solid fa-circle-check text-emerald-500"></i>
            Synced with <?= $summary['approved_slips'] ?? 0 ?> verified audits
        </p>
    </div>

    <!-- Student Reach -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
        <div class="flex items-center justify-between mb-4">
            <div class="w-10 h-10 bg-blue-100 text-blue-600 rounded-lg flex items-center justify-center">
                <i class="fa-solid fa-users"></i>
            </div>
            <span class="px-2.5 py-1 bg-gray-100 text-gray-600 text-xs font-bold rounded-lg uppercase tracking-wider">
                Active Learners
            </span>
        </div>
        <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Student Reach</p>
        <p class="text-3xl font-bold text-gray-900"><?= $summary['approved_slips'] ?? 0 ?></p>
        <p class="text-xs text-gray-400 mt-3 italic">Total approved enrollments for this cycle</p>
    </div>
</div>

<!-- Detailed Ledger -->
<div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden mb-6">
    <div class="p-5 border-b border-gray-100 flex items-center justify-between">
        <h2 class="font-bold text-gray-900">Module Breakdown</h2>
        <i class="fa-solid fa-file-invoice text-gray-400 text-lg"></i>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Academic Module</th>
                    <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider text-center">Verified Audits</th>
                    <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider text-right">Yield / Student</th>
                    <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider text-right">Net Earning</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                <?php if (empty($byCourse)): ?>
                    <tr>
                        <td colspan="4" class="px-6 py-12 text-center text-gray-400 italic font-medium text-sm">
                            No financial records for this cycle.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($byCourse as $c): ?>
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4">
                            <div class="font-bold text-gray-900 text-sm"><?= htmlspecialchars($c['name']) ?></div>
                            <div class="text-xs text-gray-500 mt-1 uppercase tracking-wider font-bold">
                                <?= htmlspecialchars($c['subject']) ?> &bull; <?= htmlspecialchars($c['grade']) ?>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="inline-flex items-center justify-center w-8 h-8 bg-emerald-100 text-emerald-700 rounded-lg font-bold text-sm">
                                <?= $c['students_paid'] ?>
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right text-sm text-gray-600 font-medium">
                            LKR <?= number_format($c['earnings'] / ($c['students_paid'] ?: 1), 0) ?>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <span class="font-bold text-emerald-600 text-sm">LKR <?= number_format($c['earnings'], 0) ?></span>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Security Notice -->
<div class="bg-gray-50 border border-dashed border-gray-200 rounded-xl p-5 flex items-start gap-3">
    <i class="fa-solid fa-shield-halved text-gray-400 mt-0.5 flex-shrink-0"></i>
    <p class="text-xs text-gray-500 leading-relaxed">
        Earnings are synchronized in real-time with administrative audit approvals. Discrepancies in student counts may be due to pending slip verification. For support, please contact the institutional registrar.
    </p>
</div>
