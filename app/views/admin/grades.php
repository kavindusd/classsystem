<div class="flex flex-col gap-6">
    <!-- Header Area -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-3xl font-bold text-textMain tracking-tight">Academic Performance</h1>
            <p class="text-textMuted mt-1 text-sm font-medium">System-wide overview of examination results</p>
        </div>
        <div class="flex items-center gap-3">
            <div class="px-4 py-2 bg-amber-50 rounded-2xl border border-amber-100 flex items-center gap-2">
                <i class="fa-solid fa-graduation-cap text-amber-600"></i>
                <span class="text-xs font-black text-amber-600 uppercase tracking-widest">Grading Central</span>
            </div>
        </div>
    </div>

    <!-- Course Selection -->
    <div class="bento-card">
        <form method="GET" class="flex flex-col md:flex-row items-center gap-4">
            <div class="relative flex-1 w-full">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-textMuted">
                    <i class="fa-solid fa-layer-group"></i>
                </div>
                <select name="course_id" class="w-full pl-11 pr-12 py-3 bg-gray-50 border border-gray-100 rounded-2xl focus:outline-none focus:ring-2 focus:ring-primary transition-all font-bold text-sm appearance-none cursor-pointer">
                    <option value="">Select a Course to Inspect Grades</option>
                    <?php foreach ($courses as $c): ?>
                        <option value="<?= $c['id'] ?>" <?= $filter_course_id == $c['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($c['name']) ?> (<?= htmlspecialchars($c['subject']) ?>) — <?= htmlspecialchars($c['teacher_name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <i class="fa-solid fa-chevron-down absolute right-4 top-1/2 -translate-y-1/2 text-textMuted text-xs pointer-events-none"></i>
            </div>
            <button type="submit" class="w-full md:w-auto px-8 py-3 bg-primary text-white rounded-2xl font-black shadow-lg shadow-primary/20 hover:bg-teal-700 transition-all text-sm uppercase tracking-widest">
                Fetch Records
            </button>
        </form>
    </div>

    <?php if (!$filter_course_id): ?>
        <div class="py-20 text-center flex flex-col items-center gap-4">
            <div class="w-20 h-20 bg-gray-50 text-textMuted/20 rounded-full flex items-center justify-center text-4xl">
                <i class="fa-solid fa-magnifying-glass-chart"></i>
            </div>
            <p class="text-textMuted font-bold italic">Please select a course above to visualize examination data.</p>
        </div>
    <?php elseif (empty($exams)): ?>
        <div class="py-20 text-center flex flex-col items-center gap-4">
            <div class="w-20 h-20 bg-gray-50 text-textMuted/20 rounded-full flex items-center justify-center text-4xl">
                <i class="fa-solid fa-ghost"></i>
            </div>
            <p class="text-textMuted font-bold italic">No examination records found for the selected course.</p>
        </div>
    <?php else: ?>
        <?php
        $gradesByExam = [];
        foreach ($grades as $g) { $gradesByExam[$g['exam_id']][] = $g; }
        ?>
        
        <div class="flex flex-col gap-10">
            <?php foreach ($exams as $exam): ?>
            <div class="flex flex-col gap-4 animate-in slide-in-from-bottom-4 duration-500">
                <div class="flex items-end justify-between px-2">
                    <div>
                        <h2 class="text-xl font-bold text-textMain leading-tight"><?= htmlspecialchars($exam['title']) ?></h2>
                        <div class="flex items-center gap-3 mt-1 text-[10px] font-black text-textMuted uppercase tracking-widest">
                            <span class="flex items-center gap-1"><i class="fa-solid fa-user-pen"></i> BY <?= htmlspecialchars($exam['created_by_name']) ?></span>
                            <span class="opacity-30">•</span>
                            <span class="flex items-center gap-1"><i class="fa-solid fa-calendar-day"></i> <?= date('d M Y', strtotime($exam['created_at'])) ?></span>
                        </div>
                    </div>
                    <div class="px-3 py-1 bg-gray-100 text-textMuted rounded-lg text-[10px] font-black uppercase tracking-tight">EXAM ID: #<?= str_pad($exam['id'], 4, '0', STR_PAD_LEFT) ?></div>
                </div>

                <div class="bento-card p-0 overflow-hidden ring-2 ring-gray-50">
                    <?php $examGrades = $gradesByExam[$exam['id']] ?? []; ?>
                    <?php if (empty($examGrades)): ?>
                        <div class="px-6 py-12 text-center text-textMuted italic font-medium text-sm">
                            <i class="fa-solid fa-pen-clip mr-2 opacity-30"></i> Teacher has not yet submitted marks for this exam.
                        </div>
                    <?php else: ?>
                        <div class="overflow-x-auto">
                            <table class="w-full text-left">
                                <thead>
                                    <tr class="bg-gray-50/50 border-b border-gray-100">
                                        <th class="px-6 py-4 text-[10px] font-black text-textMuted uppercase tracking-widest">Student Credentials</th>
                                        <th class="px-6 py-4 text-[10px] font-black text-textMuted uppercase tracking-widest text-center">Score / Grade</th>
                                        <th class="px-6 py-4 text-[10px] font-black text-textMuted uppercase tracking-widest">Instructor Remarks</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-50">
                                    <?php foreach ($examGrades as $g): ?>
                                    <tr class="hover:bg-gray-50/30 transition-colors">
                                        <td class="px-6 py-4">
                                            <div class="font-bold text-textMain"><?= htmlspecialchars($g['student_name']) ?></div>
                                            <div class="text-[10px] font-black text-indigo-500 uppercase tracking-widest"><?= htmlspecialchars($g['student_id']) ?></div>
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            <div class="inline-flex items-center justify-center min-w-[3rem] h-10 px-3 bg-white border-2 border-primary/20 text-primary rounded-xl font-black text-lg shadow-sm">
                                                <?= htmlspecialchars($g['grade']) ?>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-textMuted font-medium italic">
                                            <?= htmlspecialchars($g['remarks'] ?: '—') ?>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
