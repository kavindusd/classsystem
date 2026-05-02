<div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
    <div class="flex items-center gap-4">
        <a href="<?= APP_URL ?>/student/grading" class="w-10 h-10 rounded-lg bg-white border border-gray-200 flex items-center justify-center text-gray-500 hover:text-gray-900 hover:bg-gray-50 transition-colors shadow-sm">
            <i class="fa-solid fa-arrow-left"></i>
        </a>
        <div>
            <p class="text-xs text-gray-400 font-bold uppercase tracking-widest mb-0.5">Performance Log / <?= htmlspecialchars($course['subject']) ?></p>
            <h1 class="text-2xl font-bold text-gray-900"><?= htmlspecialchars($course['name']) ?></h1>
        </div>
    </div>
</div>

<div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden mb-10">
    <div class="p-6 border-b border-gray-100 flex items-center justify-between">
        <h2 class="font-bold text-gray-900">Grading Ledger</h2>
        <i class="fa-solid fa-chart-line text-gray-400 text-lg"></i>
    </div>
    
    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Examination Protocol</th>
                    <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider text-center">Score</th>
                    <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider text-right">Verification</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                <?php if (empty($exams)): ?>
                    <tr>
                        <td colspan="3" class="px-6 py-12 text-center text-gray-400 italic text-sm">
                            No performance records initialized for this module.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($exams as $exam): ?>
                    <?php $g = $grades[$exam['id']] ?? null; ?>
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4">
                            <div class="font-bold text-gray-900 text-sm"><?= htmlspecialchars($exam['title']) ?></div>
                            <div class="text-[10px] font-bold text-gray-500 uppercase tracking-wider mt-1">
                                Conducted <?= date('M d, Y', strtotime($exam['created_at'])) ?>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <?php if (!$g): ?>
                                <span class="px-3 py-1 bg-gray-100 text-gray-500 text-xs font-bold uppercase tracking-wider rounded-lg border border-gray-200">Pending Grading</span>
                            <?php elseif ($g['grade'] === 'Absent'): ?>
                                <span class="px-3 py-1 bg-red-50 text-red-600 text-xs font-bold uppercase tracking-wider rounded-lg border border-red-200">Absent</span>
                            <?php else: ?>
                                <span class="inline-flex items-center justify-center min-w-[3rem] px-3 py-1.5 bg-emerald-100 text-emerald-700 text-sm font-bold rounded-lg border border-emerald-200">
                                    <?= htmlspecialchars($g['grade']) ?>
                                </span>
                            <?php endif; ?>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <?php if ($g): ?>
                            <a href="<?= APP_URL ?>/student/grading/<?= $course['id'] ?>/exam/<?= $exam['id'] ?>" 
                               class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-200 text-gray-600 rounded-lg text-[10px] font-bold uppercase tracking-wider hover:bg-emerald-600 hover:text-white hover:border-emerald-600 transition-all shadow-sm">
                                Inspect Audit <i class="fa-solid fa-arrow-right"></i>
                            </a>
                            <?php else: ?>
                            <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">—</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
