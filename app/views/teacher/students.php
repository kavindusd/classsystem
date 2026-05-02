<div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Student Directory</h1>
        <p class="text-gray-500 text-sm">Manage and communicate with all learners across your academic modules.</p>
    </div>
    <div class="px-4 py-2 bg-gray-100 rounded-lg text-xs font-bold text-gray-600 flex items-center">
        Total Learners: <?= count($students) ?>
    </div>
</div>

<!-- Filters -->
<div class="mb-6">
    <form method="GET" action="<?= APP_URL ?>/teacher/students" class="flex items-center gap-3">
        <select name="course_id" onchange="this.form.submit()" class="form-select">
            <option value="">All Classes</option>
            <?php foreach ($courses as $c): ?>
                <option value="<?= $c['id'] ?>" <?= $selectedCourse == $c['id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($c['name']) ?>
                </option>
            <?php endforeach; ?>
        </select>
        
        <?php if (!empty($selectedCourse)): ?>
            <a href="<?= APP_URL ?>/teacher/students" class="text-xs font-bold text-rose-500 hover:text-rose-600 transition-colors flex items-center gap-1 ml-1">
                <i class="fa-solid fa-xmark"></i> Clear Filter
            </a>
        <?php endif; ?>
    </form>
</div>

<!-- Student List -->
<div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden mb-10">
    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Student Profile</th>
                    <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Contact Info</th>
                    <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-4 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                <?php if (empty($students)): ?>
                    <tr>
                        <td colspan="4" class="px-6 py-12 text-center text-gray-400 text-sm italic">Your academic modules currently have no active enrollments.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($students as $s): ?>
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-4">
                                <div class="w-10 h-10 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center font-bold text-sm overflow-hidden">
                                    <?php if (!empty($s['profile_image'])): ?>
                                        <img src="<?= APP_URL ?>/public/uploads/profiles/<?= htmlspecialchars($s['profile_image']) ?>" class="w-full h-full object-cover">
                                    <?php else: ?>
                                        <?= strtoupper(substr($s['name'], 0, 1)) ?>
                                    <?php endif; ?>
                                </div>
                                <div>
                                    <h3 class="text-sm font-bold text-gray-900"><?= htmlspecialchars($s['name']) ?></h3>
                                    <p class="text-xs text-gray-500 uppercase tracking-wider mt-0.5"><?= htmlspecialchars($s['student_id']) ?></p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-600 flex items-center gap-2 mb-1">
                                <i class="fa-regular fa-envelope text-gray-400 w-4"></i>
                                <a href="mailto:<?= htmlspecialchars($s['email']) ?>" class="hover:text-emerald-600 transition-colors"><?= htmlspecialchars($s['email'] ?: 'No email') ?></a>
                            </div>
                            <div class="text-sm text-gray-600 flex items-center gap-2">
                                <i class="fa-solid fa-phone text-gray-400 w-4"></i>
                                <span><?= htmlspecialchars($s['phone'] ?: 'No phone') ?></span>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-xs text-gray-500">Joined <br> <strong class="text-gray-900"><?= date('M d, Y', strtotime($s['first_enrolled'])) ?></strong></span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <?php 
                                $waRaw = ($s['whatsapp_number'] ?? $s['phone']) ?? '';
                                $waClean = preg_replace('/[^0-9]/', '', $waRaw);
                            ?>
                            <a href="https://wa.me/<?= $waClean ?>" target="_blank" 
                               class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 hover:bg-emerald-600 hover:text-white transition-all">
                                <i class="fa-brands fa-whatsapp text-sm"></i>
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
