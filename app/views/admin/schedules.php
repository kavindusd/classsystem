<div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Class Schedule</h1>
        <p class="text-gray-500 text-sm">Manage and plan your classes.</p>
    </div>
    <button onclick="toggleModal('createScheduleModal')" class="bg-emerald-600 text-white px-6 py-2.5 rounded-lg font-bold hover:bg-emerald-700 transition-colors shadow-sm flex items-center gap-2">
        <i class="fa-solid fa-calendar-plus text-xs"></i>
        New Schedule
    </button>
</div>

<?php if ($msg = Session::flash('success')): ?>
    <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-lg mb-6 text-sm font-medium">
        <i class="fa-solid fa-check-circle mr-2"></i>
        <?= htmlspecialchars($msg) ?>
    </div>
<?php endif; ?>

<!-- Filter -->
<div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm mb-8">
    <form method="GET" class="flex items-center gap-4">
        <div class="flex-1 relative">
            <i class="fa-solid fa-book-open absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
            <select name="course_id" class="w-full pl-11 pr-4 py-2 bg-gray-50 border border-gray-200 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:outline-none text-sm font-medium appearance-none cursor-pointer">
                <option value="">All Classes</option>
                <?php foreach ($courses as $c): ?>
                    <option value="<?= $c['id'] ?>" <?= $filter_course_id == $c['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($c['name']) ?> (<?= htmlspecialchars($c['subject']) ?>)
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <button type="submit" class="px-6 py-2 bg-gray-900 text-white rounded-lg font-bold hover:bg-gray-800 transition-colors text-sm">
            Filter
        </button>
    </form>
</div>

<!-- Schedule Table -->
<div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden pb-20">
    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead class="bg-gray-50 border-b border-gray-100">
                <tr>
                    <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Class</th>
                    <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider text-center">Date</th>
                    <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider text-center">Time</th>
                    <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Teacher</th>
                    <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Notes</th>
                    <th class="px-6 py-4 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                <?php if (empty($schedules)): ?>
                <tr>
                    <td colspan="6" class="px-6 py-12 text-center text-gray-400 text-sm italic">No schedules found.</td>
                </tr>
                <?php else: ?>
                    <?php foreach ($schedules as $s): ?>
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4">
                            <div class="font-bold text-gray-900"><?= htmlspecialchars($s['course_name']) ?></div>
                            <div class="text-xs text-gray-500"><?= htmlspecialchars($s['subject']) ?></div>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="px-2 py-1 bg-blue-100 text-blue-700 text-xs font-bold rounded uppercase">
                                <?= date('d M Y', strtotime($s['class_date'])) ?>
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center text-sm font-bold text-gray-900">
                            <?= date('H:i', strtotime($s['start_time'])) ?> - <?= date('H:i', strtotime($s['end_time'])) ?>
                        </td>
                        <td class="px-6 py-4 text-sm font-medium text-gray-900"><?= htmlspecialchars($s['teacher_name']) ?></td>
                        <td class="px-6 py-4 text-sm text-gray-600"><?= htmlspecialchars($s['notes'] ?? '—') ?></td>
                        <td class="px-6 py-4 text-right">
                            <form method="POST" action="<?= APP_URL ?>/admin/schedules/delete/<?= $s['id'] ?>" onsubmit="return confirm('Delete this schedule?')">
                                <button class="w-8 h-8 rounded-lg bg-gray-100 text-red-500 hover:bg-red-500 hover:text-white transition-all flex items-center justify-center">
                                    <i class="fa-solid fa-trash text-xs"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<div id="createScheduleModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-[2.5rem] w-full max-w-lg shadow-2xl overflow-hidden animate-in zoom-in-95 duration-200">
        <div class="bg-primary px-8 py-6 text-white flex justify-between items-center">
            <div>
                <h3 class="text-xl font-bold italic tracking-tight">Add Class Time</h3>
                <p class="text-white/70 text-xs font-medium">Add a new session to the schedule</p>
            </div>
            <button onclick="toggleModal('createScheduleModal')" class="w-10 h-10 rounded-full bg-white/20 flex items-center justify-center hover:bg-white/30 transition-colors">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
        <form method="POST" action="<?= APP_URL ?>/admin/schedules/create" class="p-8 flex flex-col gap-5">
            <div class="flex flex-col gap-2">
                <label class="text-xs font-black text-textMuted uppercase tracking-widest ml-1">Select Class *</label>
                <div class="relative">
                    <select name="course_id" required class="w-full pl-6 pr-12 py-4 bg-gray-50 border border-gray-100 rounded-2xl focus:outline-none focus:ring-2 focus:ring-primary focus:bg-white transition-all font-bold text-sm appearance-none cursor-pointer">
                        <option value="">Select Course</option>
                        <?php foreach ($courses as $c): ?>
                            <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['name']) ?> — <?= htmlspecialchars($c['teacher_name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <i class="fa-solid fa-chevron-down absolute right-6 top-1/2 -translate-y-1/2 text-textMuted pointer-events-none"></i>
                </div>
            </div>
            
            <div class="flex flex-col gap-2">
                <label class="text-xs font-black text-textMuted uppercase tracking-widest ml-1">Class Date *</label>
                <input type="date" name="class_date" required
                       class="w-full px-6 py-4 bg-gray-50 border border-gray-100 rounded-2xl focus:outline-none focus:ring-2 focus:ring-primary focus:bg-white transition-all font-black text-sm">
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div class="flex flex-col gap-2">
                    <label class="text-xs font-black text-textMuted uppercase tracking-widest ml-1">Start Time *</label>
                    <input type="time" name="start_time" required
                           class="w-full px-6 py-4 bg-gray-50 border border-gray-100 rounded-2xl focus:outline-none focus:ring-2 focus:ring-primary focus:bg-white transition-all font-black text-sm">
                </div>
                <div class="flex flex-col gap-2">
                    <label class="text-xs font-black text-textMuted uppercase tracking-widest ml-1">End Time *</label>
                    <input type="time" name="end_time" required
                           class="w-full px-6 py-4 bg-gray-50 border border-gray-100 rounded-2xl focus:outline-none focus:ring-2 focus:ring-primary focus:bg-white transition-all font-black text-sm">
                </div>
            </div>

            <div class="flex flex-col gap-2">
                <label class="text-xs font-black text-textMuted uppercase tracking-widest ml-1">Session Notes</label>
                <input type="text" name="notes" placeholder="e.g. Unit 4 Exam or Introduction..."
                       class="w-full px-6 py-4 bg-gray-50 border border-gray-100 rounded-2xl focus:outline-none focus:ring-2 focus:ring-primary focus:bg-white transition-all font-medium text-sm">
            </div>

            <div class="flex items-center gap-3 mt-4">
                <button type="button" onclick="toggleModal('createScheduleModal')" 
                        class="flex-1 py-4 bg-gray-100 text-textMuted rounded-2xl font-bold hover:bg-gray-200 transition-colors">
                    Cancel
                </button>
                <button type="submit" 
                        class="flex-[2] py-4 bg-primary text-white rounded-2xl font-bold shadow-lg shadow-primary/20 hover:bg-teal-700 transition-all">
                    Save Class Time
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function toggleModal(id) {
    const m = document.getElementById(id);
    m.classList.toggle('hidden');
}
</script>
