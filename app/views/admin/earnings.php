<div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Financial Overview</h1>
        <p class="text-gray-500 text-sm">Monitor institutional revenue, teacher payouts, and earnings.</p>
    </div>
    <form method="GET" class="flex items-center gap-3 bg-white px-4 py-2 rounded-lg border border-gray-200 shadow-sm">
        <label class="text-xs font-bold text-gray-600 uppercase">Month:</label>
        <input type="month" name="month" value="<?= $selectedMonth ?>" onchange="this.form.submit()"
               class="px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:outline-none text-sm font-medium">
    </form>
</div>

<!-- Summary Stats Grid -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    <!-- Total Revenue -->
    <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm">
        <div class="flex items-center gap-4 mb-3">
            <div class="w-10 h-10 bg-emerald-100 text-emerald-600 rounded-lg flex items-center justify-center">
                <i class="fa-solid fa-coins"></i>
            </div>
            <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">Gross Revenue</span>
        </div>
        <div class="text-3xl font-bold text-gray-900">LKR <?= number_format($summary['total_collected'] ?? 0, 0) ?></div>
        <div class="text-xs text-gray-500 mt-2"><?= date('M Y', strtotime($selectedMonth . '-01')) ?> Cycle</div>
    </div>

    <!-- Institute Profit -->
    <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm">
        <div class="flex items-center gap-4 mb-3">
            <div class="w-10 h-10 bg-blue-100 text-blue-600 rounded-lg flex items-center justify-center">
                <i class="fa-solid fa-chart-line"></i>
            </div>
            <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">Institute Margin</span>
        </div>
        <div class="text-3xl font-bold text-gray-900">LKR <?= number_format($summary['total_institute'] ?? 0, 0) ?></div>
        <div class="text-xs text-gray-500 mt-2">Net Profit</div>
    </div>

    <!-- Teacher Payouts -->
    <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm">
        <div class="flex items-center gap-4 mb-3">
            <div class="w-10 h-10 bg-indigo-100 text-indigo-600 rounded-lg flex items-center justify-center">
                <i class="fa-solid fa-wallet"></i>
            </div>
            <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">Teacher Payouts</span>
        </div>
        <div class="text-3xl font-bold text-gray-900">LKR <?= number_format($summary['total_teacher_cut'] ?? 0, 0) ?></div>
        <div class="text-xs text-gray-500 mt-2">Pending Distribution</div>
    </div>
</div>

<!-- Faculty & Course Performance -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-8 pb-20">
    
    <!-- Faculty Payouts -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="p-6 border-b border-gray-100">
            <h2 class="font-bold text-gray-900 flex items-center gap-2">
                <i class="fa-solid fa-users text-indigo-600"></i>
                Teacher Earnings
            </h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="px-6 py-3 text-xs font-bold text-gray-500 uppercase tracking-wider">Instructor</th>
                        <th class="px-6 py-3 text-xs font-bold text-gray-500 uppercase tracking-wider">Sessions</th>
                        <th class="px-6 py-3 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Earnings</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php if (empty($byTeacher)): ?>
                    <tr>
                        <td colspan="3" class="px-6 py-8 text-center text-gray-400 text-sm italic">No payout data available.</td>
                    </tr>
                    <?php else: ?>
                        <?php foreach ($byTeacher as $t): ?>
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 font-bold text-gray-900"><?= htmlspecialchars($t['teacher_name']) ?></td>
                            <td class="px-6 py-4 text-gray-600"><?= $t['total_students'] ?> Active</td>
                            <td class="px-6 py-4 text-right font-bold text-indigo-600">LKR <?= number_format($t['total_teacher_due'], 0) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Course Performance -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="p-6 border-b border-gray-100">
            <h2 class="font-bold text-gray-900 flex items-center gap-2">
                <i class="fa-solid fa-book text-emerald-600"></i>
                Course Performance
            </h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="px-6 py-3 text-xs font-bold text-gray-500 uppercase tracking-wider">Module</th>
                        <th class="px-6 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Slips</th>
                        <th class="px-6 py-3 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Revenue</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php if (empty($byCourse)): ?>
                    <tr>
                        <td colspan="3" class="px-6 py-8 text-center text-gray-400 text-sm italic">No performance data detected.</td>
                    </tr>
                    <?php else: ?>
                        <?php foreach ($byCourse as $c): ?>
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 font-bold text-gray-900"><?= htmlspecialchars($c['name']) ?></td>
                            <td class="px-6 py-4 text-center text-gray-600"><?= $c['approved_slips'] ?></td>
                            <td class="px-6 py-4 text-right font-bold text-emerald-600">LKR <?= number_format($c['institute_share'], 0) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
