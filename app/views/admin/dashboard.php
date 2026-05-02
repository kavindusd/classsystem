<div class="mb-8">
    <h1 class="text-2xl font-bold text-gray-900">Admin Home</h1>
    <p class="text-gray-500 text-sm">Quick overview of what's happening today.</p>
</div>

<!-- Stats Grid -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm">
        <div class="flex items-center gap-4 mb-2">
            <div class="w-10 h-10 bg-blue-100 text-blue-600 rounded-lg flex items-center justify-center">
                <i class="fa-solid fa-users"></i>
            </div>
            <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">Students</span>
        </div>
        <div class="text-2xl font-bold text-gray-900"><?= number_format($stats['total_students']) ?></div>
    </div>

    <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm">
        <div class="flex items-center gap-4 mb-2">
            <div class="w-10 h-10 bg-emerald-100 text-emerald-600 rounded-lg flex items-center justify-center">
                <i class="fa-solid fa-chalkboard-teacher"></i>
            </div>
            <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">Teachers</span>
        </div>
        <div class="text-2xl font-bold text-gray-900"><?= number_format($stats['total_teachers']) ?></div>
    </div>

    <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm">
        <div class="flex items-center gap-4 mb-2">
            <div class="w-10 h-10 bg-indigo-100 text-indigo-600 rounded-lg flex items-center justify-center">
                <i class="fa-solid fa-book"></i>
            </div>
            <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">Classes</span>
        </div>
        <div class="text-2xl font-bold text-gray-900"><?= number_format($stats['total_courses']) ?></div>
    </div>

    <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm">
        <div class="flex items-center gap-4 mb-2">
            <div class="w-10 h-10 bg-amber-100 text-amber-600 rounded-lg flex items-center justify-center">
                <i class="fa-solid fa-receipt"></i>
            </div>
            <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">Pending Slips</span>
        </div>
        <div class="text-2xl font-bold text-gray-900"><?= number_format($stats['pending_slips']) ?></div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    <!-- Recent Activity Table -->
    <div class="lg:col-span-2 bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="p-6 border-b border-gray-100 flex items-center justify-between">
            <h2 class="font-bold text-gray-900">Recent Payment Slips</h2>
            <a href="<?= APP_URL ?>/admin/slips" class="text-xs font-bold text-emerald-600 hover:underline">View All</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-xs font-bold text-gray-500 uppercase tracking-wider">Student</th>
                        <th class="px-6 py-3 text-xs font-bold text-gray-500 uppercase tracking-wider">Course</th>
                        <th class="px-6 py-3 text-xs font-bold text-gray-500 uppercase tracking-wider">Month</th>
                        <th class="px-6 py-3 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php if (empty($recentSlips)): ?>
                    <tr>
                        <td colspan="4" class="px-6 py-8 text-center text-gray-400 text-sm">No recent slips to review.</td>
                    </tr>
                    <?php else: ?>
                        <?php foreach ($recentSlips as $slip): ?>
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 text-sm font-medium text-gray-900"><?= htmlspecialchars($slip['student_name']) ?></td>
                            <td class="px-6 py-4 text-sm text-gray-500"><?= htmlspecialchars($slip['course_name']) ?></td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 bg-gray-100 text-gray-600 text-[10px] font-bold rounded uppercase"><?= htmlspecialchars($slip['slip_month']) ?></span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <a href="<?= APP_URL ?>/admin/slips" class="text-emerald-600 hover:text-emerald-700 font-bold text-xs">Review</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
        <h2 class="font-bold text-gray-900 mb-6">Quick Actions</h2>
        <div class="space-y-4">
            <a href="<?= APP_URL ?>/admin/teachers" class="flex items-center gap-4 p-4 rounded-lg bg-gray-50 hover:bg-gray-100 transition-colors">
                <div class="w-10 h-10 bg-emerald-500 text-white rounded-lg flex items-center justify-center">
                    <i class="fa-solid fa-plus"></i>
                </div>
                <div>
                    <div class="text-sm font-bold text-gray-900">Add Teacher</div>
                    <div class="text-xs text-gray-500">Set up a teacher account</div>
                </div>
            </a>
            <a href="<?= APP_URL ?>/admin/courses" class="flex items-center gap-4 p-4 rounded-lg bg-gray-50 hover:bg-gray-100 transition-colors">
                <div class="w-10 h-10 bg-blue-500 text-white rounded-lg flex items-center justify-center">
                    <i class="fa-solid fa-book"></i>
                </div>
                <div>
                    <div class="text-sm font-bold text-gray-900">New Class</div>
                    <div class="text-xs text-gray-500">Create a new class or subject</div>
                </div>
            </a>
            <a href="<?= APP_URL ?>/admin/notifications" class="flex items-center gap-4 p-4 rounded-lg bg-gray-50 hover:bg-gray-100 transition-colors">
                <div class="w-10 h-10 bg-amber-500 text-white rounded-lg flex items-center justify-center">
                    <i class="fa-solid fa-bullhorn"></i>
                </div>
                <div>
                    <div class="text-sm font-bold text-gray-900">Broadcast</div>
                    <div class="text-xs text-gray-500">Send a message to everyone</div>
                </div>
            </a>
        </div>
    </div>
</div>