<div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Add Student Marks</h1>
        <p class="text-gray-500 text-sm">Choose a class to add marks and exam results for your students.</p>
    </div>
</div>

<div class="pb-10">
    <?php if (empty($courses)): ?>
        <div class="bg-white py-16 text-center rounded-xl border border-dashed border-gray-300 shadow-sm">
            <div class="w-16 h-16 bg-gray-50 text-gray-300 rounded-full flex items-center justify-center mx-auto mb-4 text-2xl">
                <i class="fa-solid fa-graduation-cap"></i>
            </div>
            <h3 class="font-bold text-gray-900 mb-1">No Classes Assigned</h3>
            <p class="text-gray-400 text-sm">You have not been assigned to any classes yet.</p>
        </div>
    <?php else: ?>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php foreach ($courses as $c): ?>
            <a href="<?= APP_URL ?>/<?= $gradingBase ?>/<?= $c['id'] ?>" class="block group h-full">
                <div class="bg-white border border-gray-200 p-6 rounded-xl hover:border-emerald-500 shadow-sm hover:shadow-md transition-all h-full flex flex-col">
                    <div class="flex items-center justify-between mb-6">
                        <div class="w-12 h-12 bg-emerald-100 text-emerald-600 rounded-lg flex items-center justify-center font-bold text-xl group-hover:bg-emerald-600 group-hover:text-white transition-colors">
                            <?= substr($c['subject'], 0, 1) ?>
                        </div>
                        <span class="px-2 py-1 bg-gray-100 text-gray-600 text-[10px] uppercase tracking-wider rounded font-bold"><?= htmlspecialchars(is_numeric($c['grade']) ? 'Grade ' . $c['grade'] : $c['grade']) ?></span>
                    </div>
                    
                    <h3 class="font-bold text-gray-900 mb-1 group-hover:text-emerald-600 transition-colors leading-tight"><?= htmlspecialchars($c['name']) ?></h3>
                    <p class="text-xs text-gray-500 font-bold uppercase tracking-wider mb-6"><?= htmlspecialchars($c['subject']) ?></p>
                    
                    <div class="mt-auto pt-4 border-t border-gray-100 flex items-center justify-between">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-emerald-600">Add Marks</span>
                        <div class="w-8 h-8 rounded-lg bg-gray-50 text-gray-500 flex items-center justify-center group-hover:bg-emerald-600 group-hover:text-white transition-colors">
                            <i class="fa-solid fa-arrow-right text-xs"></i>
                        </div>
                    </div>
                </div>
            </a>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
