<div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Active Modules</h1>
        <p class="text-gray-500 text-sm">Configure your assigned courses, manage rosters, and broadcast links.</p>
    </div>
</div>

<?php if ($msg = Session::flash('success')): ?>
    <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-lg mb-6 text-sm font-medium flex items-center gap-2">
        <i class="fa-solid fa-circle-check"></i>
        <?= htmlspecialchars($msg) ?>
    </div>
<?php endif; ?>

<!-- Course Grid -->
<div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6 pb-20">
    <?php if (empty($courses)): ?>
        <div class="col-span-full bg-white py-12 text-center rounded-xl border border-dashed border-gray-300">
            <p class="text-gray-400 text-sm italic">You haven't been assigned to any academic modules yet.</p>
        </div>
    <?php else: ?>
        <?php foreach ($courses as $c): ?>
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 hover:border-emerald-500 transition-colors group flex flex-col">
            <!-- Cover or Placeholder -->
            <div class="w-full h-32 bg-gray-100 rounded-lg mb-4 overflow-hidden relative flex-shrink-0">
                <?php if (!empty($c['cover_image'])): ?>
                    <img src="<?= APP_URL ?>/public/uploads/courses/<?= htmlspecialchars($c['cover_image']) ?>" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                <?php else: ?>
                    <div class="w-full h-full flex items-center justify-center text-gray-300 text-5xl font-bold">
                        <?= strtoupper(substr($c['subject'], 0, 1)) ?>
                    </div>
                <?php endif; ?>
                <div class="absolute top-2 right-2">
                    <span class="px-2 py-1 bg-white/90 text-gray-800 text-[10px] rounded uppercase font-bold shadow-sm">
                        <?= htmlspecialchars(is_numeric($c['grade']) ? 'Grade ' . $c['grade'] : $c['grade']) ?>
                    </span>
                </div>
                <div class="absolute bottom-2 left-2">
                    <span class="px-2 py-1 <?= $c['status'] === 'active' ? 'bg-emerald-600 text-white' : 'bg-gray-800 text-white' ?> text-[9px] rounded uppercase font-bold shadow-sm">
                        <?= htmlspecialchars($c['status']) ?>
                    </span>
                </div>
            </div>

            <div class="flex-grow flex flex-col">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs font-bold text-gray-500 uppercase tracking-wider"><?= htmlspecialchars($c['subject']) ?></span>
                </div>
                
                <h3 class="font-bold text-gray-900 group-hover:text-emerald-600 transition-colors mb-4 line-clamp-2"><?= htmlspecialchars($c['name']) ?></h3>
                
                <div class="mt-auto pt-4 border-t border-gray-100 flex items-center justify-between">
                    <div class="flex flex-col gap-1">
                        <span class="text-[10px] text-gray-400 uppercase font-bold tracking-wider">Enrollment</span>
                        <div class="flex items-center gap-2">
                            <i class="fa-solid fa-users text-emerald-600 text-xs"></i>
                            <span class="text-sm font-bold text-gray-900"><?= $c['student_count'] ?? 0 ?> Learners</span>
                        </div>
                    </div>
                    
                    <a href="<?= APP_URL ?>/teacher/courses/<?= $c['id'] ?>" class="w-10 h-10 rounded-lg bg-gray-50 text-gray-600 flex items-center justify-center hover:bg-emerald-600 hover:text-white transition-all">
                        <i class="fa-solid fa-arrow-right text-sm"></i>
                    </a>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>
