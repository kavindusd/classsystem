<div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
    <div class="flex items-center gap-4">
        <a href="<?= APP_URL ?>/<?= $gradingBase ?>" class="w-10 h-10 rounded-lg bg-white border border-gray-200 flex items-center justify-center text-gray-500 hover:bg-gray-50 hover:text-gray-900 transition-colors shadow-sm">
            <i class="fa-solid fa-arrow-left"></i>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-900"><?= htmlspecialchars($course['name']) ?></h1>
            <p class="text-gray-500 text-sm font-medium mt-0.5"><?= htmlspecialchars($course['subject']) ?> &bull; <?= htmlspecialchars(is_numeric($course['grade']) ? 'Grade ' . $course['grade'] : $course['grade']) ?></p>
        </div>
    </div>
</div>

<?php if ($msg = Session::flash('success')): ?>
    <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-lg mb-6 text-sm font-medium flex items-center gap-2">
        <i class="fa-solid fa-circle-check"></i>
        <?= htmlspecialchars($msg) ?>
    </div>
<?php endif; ?>

<?php if ($err = Session::flash('error')): ?>
    <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-6 text-sm font-medium flex items-center gap-2">
        <i class="fa-solid fa-circle-exclamation"></i>
        <?= htmlspecialchars($err) ?>
    </div>
<?php endif; ?>

<div class="grid grid-cols-1 lg:grid-cols-12 gap-8 pb-10">
    
    <!-- Left Column: Exams List & Create Exam -->
    <div class="lg:col-span-4 flex flex-col gap-6">
        
        <div class="bg-emerald-600 p-6 rounded-xl text-white shadow-sm">
            <h3 class="font-bold mb-4 flex items-center gap-2">
                <i class="fa-solid fa-wand-magic-sparkles text-emerald-200"></i> Create New Exam
            </h3>
            <form method="POST" action="<?= APP_URL ?>/<?= $gradingBase ?>/<?= $course['id'] ?>/exam/create">
                <input type="text" name="title" placeholder="e.g. Mid Term Exam 2025" required
                       class="w-full px-4 py-2 border border-emerald-500 rounded-lg focus:outline-none focus:ring-2 focus:ring-white bg-emerald-700/50 placeholder-emerald-300 text-white mb-4 text-sm font-medium">
                <button type="submit" class="w-full py-2 bg-white text-emerald-700 rounded-lg font-bold text-sm hover:bg-gray-50 transition-colors shadow-sm flex items-center justify-center gap-2">
                    <i class="fa-solid fa-paper-plane text-xs"></i> Publish Exam
                </button>
            </form>
        </div>

        <div class="bg-white border border-gray-200 p-6 rounded-xl shadow-sm flex-grow">
            <h3 class="font-bold text-gray-900 mb-4">Published Exams</h3>
            
            <?php if (empty($exams)): ?>
                <div class="py-8 text-center">
                    <div class="w-12 h-12 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-3 text-gray-300">
                        <i class="fa-regular fa-file-lines text-xl"></i>
                    </div>
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">No exams created yet.</p>
                </div>
            <?php else: ?>
                <div class="space-y-2">
                    <?php foreach ($exams as $ex): ?>
                    <?php $isSelected = ($selectedExamId == $ex['id']); ?>
                    <a href="<?= APP_URL ?>/<?= $gradingBase ?>/<?= $course['id'] ?>?exam_id=<?= $ex['id'] ?>" 
                       class="block p-4 rounded-lg border transition-all <?= $isSelected ? 'border-emerald-500 bg-emerald-50' : 'border-gray-200 bg-gray-50 hover:border-emerald-300' ?>">
                        <div class="flex justify-between items-center">
                            <div>
                                <h4 class="font-bold text-sm <?= $isSelected ? 'text-emerald-700' : 'text-gray-900' ?> line-clamp-1"><?= htmlspecialchars($ex['title']) ?></h4>
                                <p class="text-[10px] text-gray-500 font-bold mt-1 uppercase tracking-wider"><?= date('d M Y', strtotime($ex['created_at'])) ?></p>
                            </div>
                            <?php if ($isSelected): ?>
                                <i class="fa-solid fa-circle-check text-emerald-600"></i>
                            <?php else: ?>
                                <i class="fa-solid fa-chevron-right text-gray-400 text-xs"></i>
                            <?php endif; ?>
                        </div>
                    </a>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Right Column: Grade Entry Form -->
    <div class="lg:col-span-8">
        <div class="bg-white border border-gray-200 rounded-xl shadow-sm h-full min-h-[500px] flex flex-col overflow-hidden">
            <?php if (!$selectedExamId): ?>
                <div class="py-20 text-center flex-grow flex flex-col items-center justify-center">
                    <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mb-4 text-gray-300">
                        <i class="fa-solid fa-arrow-pointer text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Select an Exam</h3>
                    <p class="text-gray-500 text-sm">Choose an academic module from the left panel to begin entry.</p>
                </div>
            <?php elseif (empty($students)): ?>
                <div class="py-20 text-center flex-grow flex flex-col items-center justify-center">
                    <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mb-4 text-gray-300">
                        <i class="fa-solid fa-users-slash text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">No Students Enrolled</h3>
                    <p class="text-gray-500 text-sm">There are currently no active learners in this module.</p>
                </div>
            <?php else: ?>
                <?php
                $currentExamTitle = '';
                foreach ($exams as $ex) {
                    if ($ex['id'] == $selectedExamId) $currentExamTitle = $ex['title'];
                }
                $letterGrades = ['A', 'B', 'C', 'S', 'F'];
                ?>

                <div class="p-6 border-b border-gray-100 flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <div>
                        <h2 class="font-bold text-gray-900 text-lg">Audit Entry</h2>
                        <div class="flex items-center gap-2 mt-1">
                            <span class="text-xs text-gray-500 font-medium">Exam Path:</span>
                            <span class="text-xs font-bold text-emerald-600"><?= htmlspecialchars($currentExamTitle) ?></span>
                        </div>
                    </div>
                    <span class="px-3 py-1.5 bg-gray-100 text-gray-600 rounded-lg font-bold text-xs"><?= count($students) ?> Learners</span>
                </div>

                <!-- Legend -->
                <div class="flex items-center gap-6 px-6 py-4 bg-gray-50 border-b border-gray-100 text-xs text-gray-500 font-medium flex-wrap">
                    <div class="flex items-center gap-2">
                        <div class="flex gap-1">
                            <?php foreach ($letterGrades as $lg): ?>
                                <span class="px-1.5 py-0.5 bg-white border border-gray-200 text-gray-700 rounded text-[10px] font-bold"><?= $lg ?></span>
                            <?php endforeach; ?>
                        </div>
                        <span>Letter Grade</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="px-2 py-0.5 bg-white border border-gray-200 text-gray-700 rounded text-[10px] font-bold">85</span>
                        <span>Numerical Mark</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="px-2 py-0.5 bg-red-50 border border-red-200 text-red-600 rounded text-[10px] font-bold italic">Absent</span>
                        <span>Absence Protocol</span>
                    </div>
                </div>
                
                <form method="POST" action="<?= APP_URL ?>/teacher/grading/<?= $course['id'] ?>/exam/<?= $selectedExamId ?>/grade" id="gradeForm" class="flex-grow flex flex-col">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-gray-50 border-b border-gray-200">
                                    <th class="py-3 px-6 text-xs font-bold text-gray-500 uppercase tracking-wider w-1/3">Learner Profile</th>
                                    <th class="py-3 px-6 text-xs font-bold text-gray-500 uppercase tracking-wider">Entry Protocol</th>
                                    <th class="py-3 px-6 text-xs font-bold text-gray-500 uppercase tracking-wider w-1/4">Remarks</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                <?php foreach ($students as $s): ?>
                                <?php 
                                    $sid          = $s['student_id_pk'];
                                    $savedGrade   = $grades[$sid]['grade']   ?? '';
                                    $savedRemark  = $grades[$sid]['remarks'] ?? '';

                                    if ($savedGrade === 'Absent') {
                                        $initMode = 'absent';
                                    } elseif (in_array($savedGrade, $letterGrades)) {
                                        $initMode = 'letter';
                                    } elseif ($savedGrade !== '') {
                                        $initMode = 'mark';
                                    } else {
                                        $initMode = 'letter';
                                    }
                                    $initMark = ($initMode === 'mark') ? $savedGrade : '';
                                    $initLetter = ($initMode === 'letter') ? $savedGrade : '';
                                ?>
                                <tr class="hover:bg-gray-50 transition-colors" id="row-<?= $sid ?>">
                                    <!-- Student Info -->
                                    <td class="py-4 px-6">
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center text-sm font-bold flex-shrink-0">
                                                <?= strtoupper(substr($s['name'], 0, 1)) ?>
                                            </div>
                                            <div>
                                                <p class="font-bold text-gray-900 text-sm"><?= htmlspecialchars($s['name']) ?></p>
                                                <p class="text-[10px] font-bold text-gray-500 uppercase tracking-wider mt-0.5"><?= htmlspecialchars($s['student_id']) ?></p>
                                            </div>
                                        </div>
                                    </td>

                                    <!-- Grade Picker -->
                                    <td class="py-4 px-6">
                                        <input type="hidden" name="grades[<?= $sid ?>]" id="gradeVal-<?= $sid ?>" value="<?= htmlspecialchars($savedGrade) ?>">

                                        <!-- Mode tabs -->
                                        <div class="flex gap-1 mb-3 bg-gray-100 p-1 rounded-lg w-fit">
                                            <button type="button" onclick="setMode(<?= $sid ?>, 'letter')"
                                                    id="tab-letter-<?= $sid ?>"
                                                    class="tab-btn px-3 py-1 text-[10px] font-bold uppercase tracking-wider rounded-md transition-all <?= $initMode === 'letter' ? 'bg-white text-gray-900 shadow-sm' : 'text-gray-500 hover:text-gray-900' ?>">
                                                Letter
                                            </button>
                                            <button type="button" onclick="setMode(<?= $sid ?>, 'mark')"
                                                    id="tab-mark-<?= $sid ?>"
                                                    class="tab-btn px-3 py-1 text-[10px] font-bold uppercase tracking-wider rounded-md transition-all <?= $initMode === 'mark' ? 'bg-white text-gray-900 shadow-sm' : 'text-gray-500 hover:text-gray-900' ?>">
                                                Mark
                                            </button>
                                            <button type="button" onclick="setMode(<?= $sid ?>, 'absent')"
                                                    id="tab-absent-<?= $sid ?>"
                                                    class="tab-btn px-3 py-1 text-[10px] font-bold uppercase tracking-wider rounded-md transition-all <?= $initMode === 'absent' ? 'bg-red-50 text-red-600 shadow-sm' : 'text-gray-500 hover:text-red-600' ?>">
                                                Absent
                                            </button>
                                        </div>

                                        <!-- Letter grade panel -->
                                        <div id="panel-letter-<?= $sid ?>" class="<?= $initMode !== 'letter' ? 'hidden' : '' ?> flex gap-2 flex-wrap">
                                            <?php foreach ($letterGrades as $lg): ?>
                                            <button type="button"
                                                    onclick="selectLetter(<?= $sid ?>, '<?= $lg ?>')"
                                                    id="letter-<?= $sid ?>-<?= $lg ?>"
                                                    class="letter-btn w-8 h-8 rounded-lg border font-bold text-sm transition-all <?= ($initLetter === $lg) ? 'border-emerald-500 bg-emerald-50 text-emerald-700' : 'border-gray-200 bg-white text-gray-600 hover:border-emerald-300 hover:text-emerald-600' ?>">
                                                <?= $lg ?>
                                            </button>
                                            <?php endforeach; ?>
                                        </div>

                                        <!-- Mark panel -->
                                        <div id="panel-mark-<?= $sid ?>" class="<?= $initMode !== 'mark' ? 'hidden' : '' ?>">
                                            <div class="flex items-center gap-2">
                                                <input type="number" min="0" max="100"
                                                       id="markInput-<?= $sid ?>"
                                                       value="<?= htmlspecialchars($initMark) ?>"
                                                       onchange="setMark(<?= $sid ?>, this.value)"
                                                       oninput="setMark(<?= $sid ?>, this.value)"
                                                       placeholder="0–100"
                                                       class="w-20 px-3 py-1.5 bg-white border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent text-center font-bold text-sm text-gray-900">
                                                <span class="text-[10px] text-gray-500 font-bold uppercase tracking-wider">/ 100 Marks</span>
                                            </div>
                                        </div>

                                        <!-- Absent panel -->
                                        <div id="panel-absent-<?= $sid ?>" class="<?= $initMode !== 'absent' ? 'hidden' : '' ?>">
                                            <span class="inline-flex items-center gap-2 px-3 py-1.5 bg-red-50 border border-red-200 text-red-600 rounded-lg font-bold text-[10px] uppercase tracking-wider">
                                                <i class="fa-solid fa-user-slash"></i> Marked Absent
                                            </span>
                                        </div>
                                    </td>

                                    <!-- Remarks -->
                                    <td class="py-4 px-6">
                                        <input type="text" name="remarks[<?= $sid ?>]"
                                               value="<?= htmlspecialchars($savedRemark) ?>"
                                               placeholder="Session notes..."
                                               class="w-full px-3 py-2 bg-white border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 font-medium text-sm text-gray-900 placeholder-gray-400">
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="p-6 border-t border-gray-100 bg-gray-50">
                        <button type="submit" class="w-full py-3 bg-emerald-600 text-white rounded-lg font-bold hover:bg-emerald-700 transition-colors shadow-sm flex items-center justify-center gap-2">
                            <i class="fa-solid fa-floppy-disk"></i> Commit Audit Entry
                        </button>
                    </div>
                </form>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
// Grade picker logic
function setMode(sid, mode) {
    const panels = ['letter', 'mark', 'absent'];
    const activeTabStyle = (mode === 'absent') ? ['bg-red-50', 'text-red-600', 'shadow-sm'] : ['bg-white', 'text-gray-900', 'shadow-sm'];
    const inactiveTabStyle = ['text-gray-500', 'hover:text-gray-900', 'hover:text-red-600'];

    panels.forEach(p => {
        const panel = document.getElementById(`panel-${p}-${sid}`);
        const tab   = document.getElementById(`tab-${p}-${sid}`);
        if (panel) panel.classList.add('hidden');
        if (tab) {
            tab.classList.remove('bg-white', 'bg-red-50', 'text-gray-900', 'text-red-600', 'shadow-sm');
            tab.classList.add('text-gray-500');
        }
    });

    const activePanel = document.getElementById(`panel-${mode}-${sid}`);
    const activeTab   = document.getElementById(`tab-${mode}-${sid}`);
    if (activePanel) activePanel.classList.remove('hidden');
    if (activeTab) {
        activeTab.classList.remove('text-gray-500');
        activeTab.classList.add(...activeTabStyle);
    }

    const hidden = document.getElementById(`gradeVal-${sid}`);
    if (mode === 'absent') {
        hidden.value = 'Absent';
    } else if (mode === 'mark') {
        const markInput = document.getElementById(`markInput-${sid}`);
        hidden.value = markInput ? markInput.value : '';
    } else {
        const activeLetter = document.querySelector(`#panel-letter-${sid} .letter-btn.border-emerald-500`);
        hidden.value = activeLetter ? activeLetter.textContent.trim() : '';
    }
}

function selectLetter(sid, grade) {
    const letters = ['A', 'B', 'C', 'S', 'F'];
    letters.forEach(lg => {
        const btn = document.getElementById(`letter-${sid}-${lg}`);
        if (!btn) return;
        if (lg === grade) {
            btn.classList.add('border-emerald-500', 'bg-emerald-50', 'text-emerald-700');
            btn.classList.remove('border-gray-200', 'bg-white', 'text-gray-600');
        } else {
            btn.classList.remove('border-emerald-500', 'bg-emerald-50', 'text-emerald-700');
            btn.classList.add('border-gray-200', 'bg-white', 'text-gray-600');
        }
    });
    document.getElementById(`gradeVal-${sid}`).value = grade;
}

function setMark(sid, value) {
    const v = parseInt(value);
    if (!isNaN(v) && v >= 0 && v <= 100) {
        document.getElementById(`gradeVal-${sid}`).value = v;
    } else {
        document.getElementById(`gradeVal-${sid}`).value = '';
    }
}
</script>
