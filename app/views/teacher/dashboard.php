<div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Teaching Summary</h1>
        <p class="text-gray-500 text-sm">Quick look at your earnings and student count.</p>
    </div>
    <div class="flex items-center gap-3">
        <a href="<?= APP_URL ?>/teacher/schedule" class="bg-emerald-600 text-white px-6 py-2.5 rounded-lg font-bold hover:bg-emerald-700 transition-colors shadow-sm flex items-center gap-2">
            <i class="fa-solid fa-calendar-plus text-xs"></i>
            Manage Schedule
        </a>
    </div>
</div>

<!-- Performance Stats -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <!-- Total Earnings -->
    <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm">
        <div class="flex items-center gap-4 mb-2">
            <div class="w-10 h-10 bg-emerald-100 text-emerald-600 rounded-lg flex items-center justify-center">
                <i class="fa-solid fa-wallet"></i>
            </div>
            <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">Total Earnings</span>
        </div>
        <div class="text-2xl font-bold text-gray-900">LKR <?= number_format($totalEarnings ?? 0, 0) ?></div>
    </div>

    <!-- Active Students -->
    <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm">
        <div class="flex items-center gap-4 mb-2">
            <div class="w-10 h-10 bg-blue-100 text-blue-600 rounded-lg flex items-center justify-center">
                <i class="fa-solid fa-user-graduate"></i>
            </div>
            <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">Students</span>
        </div>
        <div class="text-2xl font-bold text-gray-900"><?= $studentCount ?? 0 ?></div>
    </div>

    <!-- Active Courses -->
    <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm">
        <div class="flex items-center gap-4 mb-2">
            <div class="w-10 h-10 bg-indigo-100 text-indigo-600 rounded-lg flex items-center justify-center">
                <i class="fa-solid fa-book-open"></i>
            </div>
            <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">Classes</span>
        </div>
        <div class="text-2xl font-bold text-gray-900"><?= $courseCount ?? 0 ?></div>
    </div>

    <!-- Average Per Student -->
    <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm">
        <div class="flex items-center gap-4 mb-2">
            <div class="w-10 h-10 bg-amber-100 text-amber-600 rounded-lg flex items-center justify-center">
                <i class="fa-solid fa-chart-line"></i>
            </div>
            <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">Avg / Student</span>
        </div>
        <div class="text-2xl font-bold text-gray-900">LKR <?= ($studentCount ?? 0) > 0 ? number_format(($totalEarnings ?? 0) / $studentCount, 0) : '0' ?></div>
    </div>
</div>

<!-- Main Area -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-8 pb-10">
    <!-- Course Breakdown -->
    <div class="lg:col-span-2">
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="p-6 border-b border-gray-100">
                <h2 class="font-bold text-gray-900">Earnings by Class</h2>
            </div>
            <div class="p-6 space-y-4">
                <?php if (empty($courseEarnings)): ?>
                    <div class="text-center py-6 text-gray-400 text-sm">No revenue data available.</div>
                <?php else: ?>
                    <?php foreach ($courseEarnings as $row): ?>
                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 p-4 rounded-lg border border-gray-100 bg-gray-50 hover:border-gray-200 transition-colors">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 rounded-lg bg-emerald-100 flex items-center justify-center text-emerald-600">
                                <i class="fa-solid fa-layer-group"></i>
                            </div>
                            <div>
                                <h3 class="font-bold text-gray-900"><?= htmlspecialchars($row['name']) ?></h3>
                                <div class="flex items-center gap-2 mt-1">
                                    <span class="text-xs text-gray-500"><?= htmlspecialchars($row['subject']) ?> &bull; <?= htmlspecialchars($row['grade']) ?></span>
                                    <span class="px-2 py-0.5 bg-gray-200 text-gray-600 text-[10px] font-bold rounded uppercase">
                                        <?= $row['approved_students'] ?> Students
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">Your Payout</p>
                            <p class="text-lg font-bold text-emerald-600">LKR <?= number_format($row['course_total'] ?? 0, 0) ?></p>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Right: Schedule Overview -->
    <div class="flex flex-col gap-6">
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="p-6 border-b border-gray-100 flex items-center justify-between">
                <h2 class="font-bold text-gray-900">Today's Classes</h2>
                <a href="<?= APP_URL ?>/teacher/schedule" class="text-xs font-bold text-emerald-600 hover:underline">Full Schedule</a>
            </div>
            <div class="p-6 space-y-4">
                <?php if (empty($todayClasses)): ?>
                    <div class="text-center py-8 text-gray-400 text-sm italic">No sessions scheduled for today.</div>
                <?php else: ?>
                    <?php foreach ($todayClasses as $c): ?>
                    <div class="p-4 bg-gray-50 rounded-lg border border-gray-100 hover:border-emerald-200 transition-colors">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-xs font-bold text-emerald-600 uppercase tracking-wider"><?= date('h:i A', strtotime($c['start_time'])) ?></span>
                            <i class="fa-solid fa-clock-rotate-left text-xs text-gray-300"></i>
                        </div>
                        <h4 class="font-bold text-gray-900"><?= htmlspecialchars($c['course_name']) ?></h4>
                        <p class="text-xs text-gray-500 mt-1"><?= htmlspecialchars($c['subject']) ?></p>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
