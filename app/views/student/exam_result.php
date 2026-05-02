<div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
    <div class="flex items-center gap-4">
        <a href="<?= APP_URL ?>/student/grading/<?= $course['id'] ?>" class="w-10 h-10 rounded-lg bg-white border border-gray-200 flex items-center justify-center text-gray-500 hover:text-gray-900 hover:bg-gray-50 transition-colors shadow-sm">
            <i class="fa-solid fa-arrow-left"></i>
        </a>
        <div>
            <p class="text-xs text-gray-400 font-bold uppercase tracking-widest mb-0.5"><?= htmlspecialchars($course['subject']) ?> / Result Details</p>
            <h1 class="text-2xl font-bold text-gray-900"><?= htmlspecialchars($exam['title']) ?></h1>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-8 pb-10">
    
    <!-- Left: Score Card -->
    <div class="md:col-span-1">
        <div class="bg-white rounded-xl border border-gray-200 p-8 shadow-sm text-center">
            <div class="w-16 h-16 bg-gray-50 text-gray-400 rounded-2xl flex items-center justify-center mx-auto mb-6 text-2xl border border-gray-200">
                <i class="fa-solid fa-award"></i>
            </div>
            
            <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Final Grade</p>
            
            <?php if ($grade['grade'] === 'Absent'): ?>
                <div class="text-3xl font-bold text-red-600 mb-2">Absent</div>
                <div class="inline-block px-3 py-1 bg-red-50 border border-red-200 text-red-700 text-[10px] font-bold uppercase tracking-wider rounded">Missed Session</div>
            <?php else: ?>
                <div class="text-5xl font-bold text-gray-900 mb-4"><?= htmlspecialchars($grade['grade']) ?></div>
                <div class="inline-block px-4 py-1.5 bg-emerald-100 border border-emerald-200 text-emerald-700 text-[10px] font-bold uppercase tracking-wider rounded">Officially Verified</div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Right: Context Details -->
    <div class="md:col-span-2 flex flex-col gap-6">
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="p-6 border-b border-gray-100">
                <h2 class="font-bold text-gray-900">Class Information</h2>
            </div>
            <div class="p-6 grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div>
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">Class Name</p>
                    <p class="text-sm font-bold text-gray-900"><?= htmlspecialchars($course['name']) ?></p>
                </div>
                <div>
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">Teacher</p>
                    <p class="text-sm font-bold text-gray-900"><?= htmlspecialchars($course['teacher_name']) ?></p>
                </div>
                <div>
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">Exam Date</p>
                    <p class="text-sm font-bold text-gray-900"><?= date('M d, Y', strtotime($exam['created_at'])) ?></p>
                </div>
                <div>
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">Graded On</p>
                    <p class="text-sm font-bold text-gray-900"><?= date('M d, Y', strtotime($grade['created_at'])) ?></p>
                </div>
            </div>
        </div>

        <?php if (!empty($grade['remarks'])): ?>
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 relative overflow-hidden group">
            <div class="absolute left-0 top-0 bottom-0 w-1 bg-emerald-500"></div>
            <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-3">Teacher's Remarks</h3>
            <p class="text-gray-900 font-medium text-sm leading-relaxed italic border-l-2 border-emerald-200 pl-4">
                "<?= htmlspecialchars($grade['remarks']) ?>"
            </p>
        </div>
        <?php endif; ?>
    </div>
</div>
