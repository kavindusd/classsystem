<!-- Header Area -->
<div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Find a Class</h1>
        <p class="text-gray-500 text-sm">Find the best classes for your studies.</p>
    </div>
</div>

<!-- Search Bar -->
<div class="bg-white border border-gray-200 p-4 mb-8 rounded-xl shadow-sm">
    <form method="GET" action="<?= APP_URL ?>/student/courses/search" class="flex flex-col md:flex-row gap-4 w-full">
        <div class="flex-1 relative">
            <i class="fa-solid fa-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
            <input type="text" name="q" placeholder="What do you want to learn?" value="<?= htmlspecialchars($keyword ?? '') ?>"
                   class="form-input w-full pl-10">
        </div>
        
        <div class="flex gap-3">
            <select name="subject" class="form-select min-w-[140px]">
                <option value="">All Subjects</option>
                <?php foreach ($subjects as $sub): ?>
                    <option value="<?= htmlspecialchars($sub) ?>" <?= ($filter_subject ?? '') === $sub ? 'selected' : '' ?>><?= htmlspecialchars($sub) ?></option>
                <?php endforeach; ?>
            </select>

            <button type="submit" class="bg-emerald-600 text-white px-6 py-2.5 rounded-lg font-bold hover:bg-emerald-700 transition-colors shadow-sm text-sm">
                Search
            </button>
            <?php if (!empty($is_search)): ?>
                <a href="<?= APP_URL ?>/student/courses" class="w-10 h-10 flex items-center justify-center bg-gray-100 border border-gray-200 text-gray-500 rounded-lg hover:bg-gray-200 hover:text-gray-900 transition-colors">
                    <i class="fa-solid fa-xmark text-sm"></i>
                </a>
            <?php endif; ?>
        </div>
    </form>
</div>

<!-- Courses Grid -->
<?php if (empty($courses)): ?>
    <div class="bg-white rounded-xl border border-dashed border-gray-300 py-16 text-center shadow-sm">
        <div class="w-16 h-16 bg-gray-50 text-gray-300 rounded-full flex items-center justify-center mx-auto mb-4 text-2xl">
            <i class="fa-solid fa-book-skull"></i>
        </div>
        <p class="text-gray-500 font-bold text-sm">No classes found. Try different keywords.</p>
    </div>
<?php else: ?>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 pb-10">
        <?php foreach ($courses as $c): ?>
        <?php $isEnrolled = in_array($c['id'], $enrolledIds); ?>
        
        <div class="bg-white border border-gray-200 rounded-xl overflow-hidden hover:border-emerald-500 transition-colors flex flex-col h-full shadow-sm group">
            <!-- Cover Accent -->
            <div class="w-full h-32 bg-gray-100 relative overflow-hidden flex-shrink-0">
                <?php if (!empty($c['cover_image'])): ?>
                    <img src="<?= APP_URL ?>/public/uploads/courses/<?= htmlspecialchars($c['cover_image']) ?>" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                <?php else: ?>
                    <div class="w-full h-full flex items-center justify-center text-gray-300 text-5xl font-bold select-none">
                        <?= substr($c['subject'], 0, 1) ?>
                    </div>
                <?php endif; ?>
                
                <div class="absolute top-2 left-2 flex flex-col gap-2 z-10">
                    <span class="px-2 py-1 bg-white/90 backdrop-blur text-[10px] font-bold uppercase tracking-wider rounded shadow-sm text-gray-800">
                        <?= htmlspecialchars(is_numeric($c['grade']) ? 'Grade ' . $c['grade'] : $c['grade']) ?>
                    </span>
                    <?php if ($isEnrolled): ?>
                        <span class="px-2 py-1 bg-emerald-600 text-white text-[10px] font-bold uppercase tracking-wider rounded shadow-sm flex items-center gap-1 w-fit">
                            <i class="fa-solid fa-check-circle"></i> Active
                        </span>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="p-6 flex flex-col flex-1">
                <h3 class="text-lg font-bold text-gray-900 group-hover:text-emerald-600 transition-colors leading-tight mb-2 line-clamp-2"><?= htmlspecialchars($c['name']) ?></h3>
                
                <div class="flex items-center gap-2 mb-4">
                    <div class="w-6 h-6 rounded-md bg-emerald-50 flex items-center justify-center text-[10px] font-bold text-emerald-700">
                        <?= substr($c['teacher_name'], 0, 1) ?>
                    </div>
                    <span class="text-xs text-gray-500 font-bold tracking-tight"><?= htmlspecialchars($c['teacher_name']) ?> &bull; <span class="text-emerald-600"><?= htmlspecialchars($c['subject']) ?></span></span>
                </div>
                
                <p class="text-xs text-gray-500 leading-relaxed line-clamp-2 mb-6 font-medium">
                    <?= htmlspecialchars($c['description'] ?: 'Learn ' . $c['subject'] . ' with our expert teachers and easy-to-follow lessons.') ?>
                </p>
                
                <!-- Bottom Action -->
                <div class="mt-auto pt-4 border-t border-gray-100 flex items-center justify-between">
                    <div>
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-0.5">Monthly Fee</p>
                        <p class="text-sm font-bold text-gray-900 tracking-tight">LKR <?= number_format($c['price'], 0) ?></p>
                    </div>
                    <a href="<?= APP_URL ?>/student/courses/<?= $c['id'] ?>" 
                       class="px-4 py-2 rounded-lg font-bold text-xs transition-colors <?= $isEnrolled ? 'bg-gray-100 text-gray-600 hover:bg-emerald-600 hover:text-white' : 'bg-emerald-600 text-white hover:bg-emerald-700 shadow-sm' ?>">
                        <?= $isEnrolled ? 'Go to Class' : 'Join Class' ?>
                    </a>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
