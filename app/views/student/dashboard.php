<!-- Header Area -->
<div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Mastering Your Path</h1>
        <p class="text-gray-500 text-sm">Real-time learning insights and academic milestones.</p>
    </div>
    <div class="flex items-center gap-3">
        <a href="<?= APP_URL ?>/student/courses" class="bg-emerald-600 text-white px-6 py-2.5 rounded-lg font-bold hover:bg-emerald-700 transition-colors shadow-sm flex items-center gap-2">
            <i class="fa-solid fa-plus-circle text-xs"></i>
            Join Class
        </a>
        <a href="<?= APP_URL ?>/student/settings" class="w-10 h-10 bg-white border border-gray-200 rounded-lg flex items-center justify-center text-gray-500 hover:text-gray-900 hover:bg-gray-50 transition-colors shadow-sm">
            <i class="fa-solid fa-gear text-sm"></i>
        </a>
    </div>
</div>

<!-- Stats Grid -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
        <div class="flex items-center justify-between mb-4">
            <div class="w-10 h-10 bg-emerald-100 text-emerald-600 rounded-lg flex items-center justify-center">
                <i class="fa-solid fa-book-bookmark text-lg"></i>
            </div>
            <span class="text-[10px] font-bold text-emerald-700 uppercase tracking-wider bg-emerald-100 px-2.5 py-1 rounded">Academic</span>
        </div>
        <h3 class="text-3xl font-bold text-gray-900"><?= $enrolledCount ?></h3>
        <p class="text-gray-500 text-xs font-bold uppercase tracking-wider mt-1">Active Enrollments</p>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
        <div class="flex items-center justify-between mb-4">
            <div class="w-10 h-10 bg-amber-100 text-amber-600 rounded-lg flex items-center justify-center">
                <i class="fa-solid fa-hourglass-half text-lg"></i>
            </div>
            <span class="text-[10px] font-bold text-amber-700 uppercase tracking-wider bg-amber-100 px-2.5 py-1 rounded">Finance</span>
        </div>
        <h3 class="text-3xl font-bold text-gray-900"><?= $pendingSlips ?></h3>
        <p class="text-gray-500 text-xs font-bold uppercase tracking-wider mt-1">Awaiting Audit</p>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
        <div class="flex items-center justify-between mb-4">
            <div class="w-10 h-10 bg-blue-100 text-blue-600 rounded-lg flex items-center justify-center">
                <i class="fa-solid fa-bell text-lg"></i>
            </div>
            <span class="text-[10px] font-bold text-blue-700 uppercase tracking-wider bg-blue-100 px-2.5 py-1 rounded">Alerts</span>
        </div>
        <h3 class="text-3xl font-bold text-gray-900"><?= $unreadCount ?></h3>
        <p class="text-gray-500 text-xs font-bold uppercase tracking-wider mt-1">Unread Updates</p>
    </div>
</div>

<!-- Navigation Tabs -->
<div class="flex items-center gap-2 mb-8 bg-white border border-gray-200 p-1.5 rounded-lg w-fit shadow-sm">
    <a href="<?= APP_URL ?>/student" class="bg-emerald-600 text-white px-6 py-2 rounded-md font-bold text-sm shadow-sm">Pathview</a>
    <a href="<?= APP_URL ?>/student/grading" class="text-gray-500 hover:text-gray-900 px-4 font-bold text-sm transition-colors">Performance</a>
    <a href="<?= APP_URL ?>/student/schedule" class="text-gray-500 hover:text-gray-900 px-4 font-bold text-sm transition-colors">Calendar</a>
</div>

<!-- Main Content Grid -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-8 pb-10">
    
    <!-- Left: Learning Path -->
    <div class="lg:col-span-2 flex flex-col gap-6">
        <h2 class="text-lg font-bold text-gray-900">Active Learning Modules</h2>
        
        <?php if (empty($enrolledCourses)): ?>
            <div class="bg-white rounded-xl border border-dashed border-gray-300 py-16 text-center shadow-sm">
                <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4 text-gray-300">
                    <i class="fa-solid fa-graduation-cap text-2xl"></i>
                </div>
                <h3 class="font-bold text-gray-900 mb-1">Initialize Your Journey</h3>
                <p class="text-gray-500 text-sm mb-6">You haven't enrolled in any modules yet.</p>
                <a href="<?= APP_URL ?>/student/courses" class="bg-emerald-600 text-white px-6 py-2.5 rounded-lg font-bold hover:bg-emerald-700 transition-colors shadow-sm inline-block">Browse Catalog</a>
            </div>
        <?php else: ?>
            <div class="grid grid-cols-1 gap-4">
                <?php foreach ($enrolledCourses as $c): ?>
                <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm hover:border-emerald-500 transition-colors group flex flex-col md:flex-row md:items-center justify-between gap-6">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-lg bg-emerald-100 text-emerald-600 flex items-center justify-center text-lg font-bold">
                            <?= strtoupper(substr($c['name'], 0, 1)) ?>
                        </div>
                        <div>
                            <h3 class="font-bold text-gray-900 group-hover:text-emerald-600 transition-colors"><?= htmlspecialchars($c['name']) ?></h3>
                            <div class="flex items-center gap-2 mt-1">
                                <span class="text-xs font-bold text-gray-500 flex items-center gap-1">
                                    <i class="fa-solid fa-user-tie text-gray-300 text-[10px]"></i>
                                    <?= htmlspecialchars($c['teacher_name']) ?>
                                </span>
                                <span class="text-gray-300">&bull;</span>
                                <span class="text-[10px] font-bold text-gray-600 uppercase tracking-wider bg-gray-100 px-2 py-0.5 rounded"><?= htmlspecialchars($c['subject']) ?></span>
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center gap-6">
                        <div class="text-right">
                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">Fee Status</p>
                            <?php if ($c['this_month_slip_status'] === 'approved'): ?>
                                <span class="px-2.5 py-1 bg-emerald-100 text-emerald-700 text-[10px] font-bold uppercase tracking-wider rounded">Paid</span>
                            <?php elseif ($c['this_month_slip_status'] === 'pending'): ?>
                                <span class="px-2.5 py-1 bg-amber-100 text-amber-700 text-[10px] font-bold uppercase tracking-wider rounded">Review</span>
                            <?php else: ?>
                                <span class="px-2.5 py-1 bg-red-100 text-red-700 text-[10px] font-bold uppercase tracking-wider rounded">Pending</span>
                            <?php endif; ?>
                        </div>
                        <a href="<?= APP_URL ?>/student/courses/<?= $c['id'] ?>" class="w-10 h-10 rounded-lg bg-gray-50 border border-gray-200 flex items-center justify-center text-gray-600 group-hover:bg-emerald-600 group-hover:text-white group-hover:border-emerald-600 transition-colors">
                            <i class="fa-solid fa-arrow-right text-sm"></i>
                        </a>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- Right: Recent Feed -->
    <div class="flex flex-col gap-6">
        <h2 class="text-lg font-bold text-gray-900">Academic Intel</h2>
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 flex flex-col gap-4">
            <?php if (empty($recentNotifs)): ?>
                <div class="text-center py-8 text-gray-400 text-sm italic">No recent updates found.</div>
            <?php else: ?>
                <div class="space-y-4">
                    <?php foreach ($recentNotifs as $n): ?>
                    <div class="flex gap-3 group">
                        <div class="w-1 h-auto bg-gray-200 group-hover:bg-emerald-500 rounded-full transition-colors"></div>
                        <div class="flex-1 py-1">
                            <p class="text-sm font-medium text-gray-900 leading-relaxed"><?= htmlspecialchars($n['message']) ?></p>
                            <p class="text-[10px] text-gray-400 mt-1.5 font-bold uppercase tracking-wider"><?= date('h:i A, M d', strtotime($n['created_at'])) ?></p>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
            <a href="<?= APP_URL ?>/student/notifications" class="text-center text-xs font-bold text-emerald-600 uppercase tracking-wider mt-2 hover:underline">View All Intelligence</a>
        </div>
    </div>
</div>
