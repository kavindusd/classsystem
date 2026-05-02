<div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Student Classes</h1>
        <p class="text-gray-500 text-sm">See which students are in which classes.</p>
    </div>
</div>

<?php if ($msg = Session::flash('success')): ?>
    <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-lg mb-6 text-sm font-medium">
        <i class="fa-solid fa-check-circle mr-2"></i>
        <?= htmlspecialchars($msg) ?>
    </div>
<?php endif; ?>

<!-- Courses Summary -->
<div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden mb-8">
    <div class="p-6 border-b border-gray-100">
        <h2 class="font-bold text-gray-900">Class Summary</h2>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead class="bg-gray-50 border-b border-gray-100">
                <tr>
                    <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Class Name</th>
                    <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider text-center">Year/Grade</th>
                    <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Teacher</th>
                    <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider text-center">Enrolled</th>
                    <th class="px-6 py-4 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Details</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                <?php if (empty($courses)): ?>
                <tr>
                    <td colspan="5" class="px-6 py-12 text-center text-gray-400 text-sm italic">No courses found.</td>
                </tr>
                <?php else: ?>
                    <?php foreach ($courses as $c): ?>
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4">
                            <div class="font-bold text-gray-900"><?= htmlspecialchars($c['name']) ?></div>
                            <div class="text-xs text-gray-500"><?= htmlspecialchars($c['subject']) ?></div>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="px-2 py-1 bg-amber-100 text-amber-700 text-xs font-bold rounded uppercase">
                                <?= htmlspecialchars($c['grade']) ?>
                            </span>
                        </td>
                        <td class="px-6 py-4 font-medium text-gray-900"><?= htmlspecialchars($c['teacher_name']) ?></td>
                        <td class="px-6 py-4 text-center">
                            <span class="px-3 py-1 bg-blue-100 text-blue-700 font-bold rounded-lg text-sm">
                                <?= $c['enrolled_count'] ?>
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <a href="<?= APP_URL ?>/admin/enrollments?course_id=<?= $c['id'] ?>"
                               class="px-4 py-2 bg-gray-100 text-gray-900 rounded-lg text-xs font-bold hover:bg-emerald-500 hover:text-white transition-all">
                                View Students
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Detailed Student Enrollments -->
<div class="mt-16">
    <?php if (!empty($enrollments)): ?>
        <div class="space-y-6">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-xl font-bold text-textMain">Student List</h2>
                    <p class="text-sm text-textMuted">A full list of students joined in classes.</p>
                </div>
                <?php if ($filter_course_id): ?>
                    <a href="<?= APP_URL ?>/admin/enrollments" class="text-xs font-black text-rose-500 hover:underline uppercase tracking-widest">
                        <i class="fa-solid fa-xmark mr-1"></i> Clear Selection
                    </a>
                <?php endif; ?>
            </div>

            <div class="bento-card p-0 overflow-hidden ring-2 ring-indigo-50 bg-indigo-50/10">
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="bg-indigo-50/50 border-b border-indigo-100">
                                <th class="px-6 py-4 text-xs font-bold text-textMuted uppercase tracking-wider">Student ID</th>
                                <th class="px-6 py-4 text-xs font-bold text-textMuted uppercase tracking-wider">Full Name</th>
                                <th class="px-6 py-4 text-xs font-bold text-textMuted uppercase tracking-wider">Class / Subject</th>
                                <th class="px-6 py-4 text-xs font-bold text-textMuted uppercase tracking-wider">Enrolled Date</th>
                                <th class="px-6 py-4 text-xs font-bold text-textMuted uppercase tracking-wider text-right">Settings</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-indigo-50">
                            <?php foreach ($enrollments as $e): ?>
                                <tr class="hover:bg-white transition-colors">
                                    <td class="px-6 py-4">
                                        <span class="px-3 py-1 bg-white text-indigo-600 rounded-lg text-[10px] font-black uppercase tracking-widest border border-indigo-100">
                                            <?= htmlspecialchars($e['student_id']) ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 font-bold text-textMain">
                                        <?= htmlspecialchars($e['student_name']) ?>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm font-bold text-textMain"><?= htmlspecialchars($e['course_name']) ?></div>
                                        <div class="text-[10px] font-medium text-textMuted"><?= htmlspecialchars($e['subject']) ?></div>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-textMuted font-medium">
                                        <?= date('d M Y', strtotime($e['enrolled_at'])) ?>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <form method="POST" action="<?= APP_URL ?>/admin/enrollments/remove/<?= $e['id'] ?>"
                                              onsubmit="return confirm('Revoke this student\'s access to the course? This action cannot be undone.')"
                                              class="inline">
                                            <button class="px-4 py-2 bg-white text-rose-500 rounded-xl text-[10px] font-black hover:bg-rose-500 hover:text-white transition-all shadow-sm border border-rose-100 uppercase tracking-widest">
                                                Remove from Class
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>
