<div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
    <div class="flex items-center gap-4">
        <a href="<?= APP_URL ?>/teacher/courses" class="w-9 h-9 rounded-xl bg-white border border-gray-200 flex items-center justify-center text-gray-500 hover:text-gray-900 hover:border-gray-300 transition-all shadow-sm">
            <i class="fa-solid fa-arrow-left text-xs"></i>
        </a>
        <div>
            <p class="text-xs text-gray-400 font-bold uppercase tracking-widest mb-0.5">Courses / <?= htmlspecialchars($course['name']) ?></p>
            <h1 class="text-2xl font-bold text-gray-900"><?= htmlspecialchars($course['name']) ?></h1>
        </div>
    </div>
    <span class="px-3 py-1 rounded-full text-xs font-bold uppercase tracking-widest <?= $course['status'] === 'active' ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-700' ?>">
        <i class="fa-solid fa-circle text-[6px] mr-1"></i>
        <?= htmlspecialchars($course['status']) ?>
    </span>
</div>

<?php if ($msg = Session::flash('success')): ?>
    <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-lg mb-6 text-sm font-medium flex items-center gap-2">
        <i class="fa-solid fa-circle-check"></i>
        <?= htmlspecialchars($msg) ?>
    </div>
<?php endif; ?>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 pb-10">

    <!-- Left Column: Settings -->
    <div class="flex flex-col gap-6">

        <!-- Live Session Link -->
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-9 h-9 bg-emerald-100 text-emerald-600 rounded-lg flex items-center justify-center">
                    <i class="fa-solid fa-tower-broadcast"></i>
                </div>
                <h3 class="font-bold text-gray-900">Live Session Link</h3>
            </div>
            <p class="text-xs text-gray-500 mb-4 leading-relaxed">Broadcast your Zoom or Meet link to all students with active payments for this month.</p>
            <form method="POST" action="<?= APP_URL ?>/teacher/courses/<?= $course['id'] ?>/join-link" class="flex flex-col gap-3">
                <div class="relative">
                    <i class="fa-solid fa-link absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                    <input type="url" name="join_link" placeholder="https://zoom.us/j/..." required
                           class="form-input w-full pl-9">
                </div>
                <button type="submit" class="w-full bg-emerald-600 text-white px-4 py-2.5 rounded-lg font-bold hover:bg-emerald-700 transition-colors shadow-sm flex items-center justify-center gap-2">
                    <i class="fa-solid fa-paper-plane text-xs"></i> Broadcast Link
                </button>
            </form>
        </div>

        <!-- Schedule Configuration -->
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
            <h3 class="font-bold text-gray-900 mb-5">Course Settings</h3>
            <form method="POST" action="<?= APP_URL ?>/teacher/courses/<?= $course['id'] ?>/update" enctype="multipart/form-data" class="flex flex-col gap-5">

                <div>
                    <label class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-2 block">Scheduled Days</label>
                    <div class="flex flex-wrap gap-2">
                        <?php
                        $savedDays = array_filter(explode(',', $course['class_days'] ?? ''));
                        $allDays   = ['Mon','Tue','Wed','Thu','Fri','Sat','Sun'];
                        ?>
                        <?php foreach ($allDays as $d): ?>
                        <label class="cursor-pointer">
                            <input type="checkbox" name="class_days[]" value="<?= $d ?>" <?= in_array($d, $savedDays) ? 'checked' : '' ?> class="hidden peer">
                            <span class="px-3 py-1.5 rounded-lg border border-gray-200 text-[10px] font-bold uppercase tracking-wider transition-all peer-checked:bg-emerald-600 peer-checked:text-white peer-checked:border-emerald-600 hover:border-emerald-400 text-gray-600">
                                <?= $d ?>
                            </span>
                        </label>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="border-t border-gray-100 pt-5">
                    <label class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-3 block">Session Duration</label>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <span class="text-[10px] font-bold text-gray-400 uppercase mb-1 block">Start Time</span>
                            <div class="flex gap-1">
                                <select name="start_hour" class="form-select flex-1 !px-2 !py-2 !text-xs !bg-gray-50">
                                    <?php for($h=1;$h<=12;$h++): ?>
                                    <option value="<?= str_pad($h,2,'0',STR_PAD_LEFT) ?>" <?= str_pad($h,2,'0',STR_PAD_LEFT) === ($startHour ?? '') ? 'selected' : '' ?>><?= str_pad($h,2,'0',STR_PAD_LEFT) ?></option>
                                    <?php endfor; ?>
                                </select>
                                <select name="start_ampm" class="form-select !px-2 !py-2 !text-[10px] !bg-gray-50">
                                    <option value="AM" <?= ('AM' === ($startAmPm ?? '')) ? 'selected' : '' ?>>AM</option>
                                    <option value="PM" <?= ('PM' === ($startAmPm ?? '')) ? 'selected' : '' ?>>PM</option>
                                </select>
                            </div>
                        </div>
                        <div>
                            <span class="text-[10px] font-bold text-gray-400 uppercase mb-1 block">End Time</span>
                            <div class="flex gap-1">
                                <select name="end_hour" class="form-select flex-1 !px-2 !py-2 !text-xs !bg-gray-50">
                                    <?php for($h=1;$h<=12;$h++): ?>
                                    <option value="<?= str_pad($h,2,'0',STR_PAD_LEFT) ?>" <?= str_pad($h,2,'0',STR_PAD_LEFT) === ($endHour ?? '') ? 'selected' : '' ?>><?= str_pad($h,2,'0',STR_PAD_LEFT) ?></option>
                                    <?php endfor; ?>
                                </select>
                                <select name="end_ampm" class="form-select !px-2 !py-2 !text-[10px] !bg-gray-50">
                                    <option value="AM" <?= ('AM' === ($endAmPm ?? '')) ? 'selected' : '' ?>>AM</option>
                                    <option value="PM" <?= ('PM' === ($endAmPm ?? '')) ? 'selected' : '' ?>>PM</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <button type="submit" class="w-full bg-emerald-600 text-white px-4 py-2.5 rounded-lg font-bold hover:bg-emerald-700 transition-colors shadow-sm flex items-center justify-center gap-2">
                    Sync Course Settings
                </button>
            </form>
        </div>
    </div>

    <!-- Right Column: Student Roster -->
    <div class="lg:col-span-2">
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="p-5 border-b border-gray-100 flex items-center justify-between">
                <h2 class="font-bold text-gray-900">Enrolled Students</h2>
                <span class="px-2 py-1 bg-emerald-100 text-emerald-700 text-xs font-bold rounded-lg"><?= count($students) ?> Learners</span>
            </div>

            <?php if (empty($students)): ?>
                <div class="py-16 text-center">
                    <div class="w-16 h-16 bg-gray-50 text-gray-300 rounded-full flex items-center justify-center mx-auto mb-4 text-2xl">
                        <i class="fa-solid fa-users-slash"></i>
                    </div>
                    <p class="text-gray-400 font-bold text-sm">No students have enrolled in this module yet.</p>
                </div>
            <?php else: ?>
                <div class="divide-y divide-gray-100">
                    <?php foreach ($students as $s): ?>
                    <div class="flex items-center gap-4 px-5 py-4 hover:bg-gray-50 transition-colors">
                        <div class="w-10 h-10 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center text-sm font-bold flex-shrink-0 overflow-hidden">
                            <?php if (!empty($s['profile_image'])): ?>
                                <img src="<?= APP_URL ?>/public/uploads/profiles/<?= htmlspecialchars($s['profile_image']) ?>" class="w-full h-full object-cover">
                            <?php else: ?>
                                <?= strtoupper(substr($s['name'], 0, 1)) ?>
                            <?php endif; ?>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="font-bold text-gray-900 text-sm truncate"><?= htmlspecialchars($s['name']) ?></p>
                            <div class="flex items-center gap-2 mt-0.5">
                                <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider"><?= htmlspecialchars($s['student_id']) ?></span>
                                <?php if ($s['email']): ?>
                                <span class="text-gray-300">·</span>
                                <span class="text-[11px] text-gray-400 truncate"><?= htmlspecialchars($s['email']) ?></span>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="text-right flex-shrink-0">
                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Enrolled</p>
                            <p class="text-xs font-bold text-gray-600"><?= date('M d, Y', strtotime($s['enrolled_at'])) ?></p>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
