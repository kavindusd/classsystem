<div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Academic Schedule</h1>
        <p class="text-gray-500 text-sm">Your synchronized learning schedule for the current cycle.</p>
    </div>
</div>

<!-- Week Navigation Bar -->
<div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm mb-8 flex flex-col md:flex-row items-center justify-between gap-4">
    <a href="<?= APP_URL ?>/student/schedule?week=<?= $weekOffset - 1 ?>" class="px-4 py-2 bg-gray-50 text-gray-600 rounded-lg font-bold text-xs hover:bg-gray-100 transition-colors flex items-center gap-2">
        <i class="fa-solid fa-chevron-left text-[10px]"></i>
        Previous Week
    </a>
    
    <div class="flex flex-col items-center text-center">
        <span class="text-sm font-bold text-gray-900">
            <?= date('d M', strtotime($weekStart)) ?> — <?= date('d M Y', strtotime($weekEnd)) ?>
        </span>
        <?php if ($weekOffset === 0): ?>
            <span class="px-2 py-0.5 bg-emerald-100 text-emerald-700 text-[10px] font-bold rounded uppercase mt-1">Active Cycle</span>
        <?php else: ?>
            <a href="<?= APP_URL ?>/student/schedule" class="text-[10px] font-bold text-emerald-600 hover:underline mt-1 uppercase tracking-wider">Return to Today</a>
        <?php endif; ?>
    </div>
    
    <a href="<?= APP_URL ?>/student/schedule?week=<?= $weekOffset + 1 ?>" class="px-4 py-2 bg-gray-50 text-gray-600 rounded-lg font-bold text-xs hover:bg-gray-100 transition-colors flex items-center gap-2">
        Next Week
        <i class="fa-solid fa-chevron-right text-[10px]"></i>
    </a>
</div>

<?php if (empty($byDate)): ?>
    <div class="bg-white py-16 text-center rounded-xl border border-dashed border-gray-300 shadow-sm">
        <div class="w-16 h-16 bg-gray-50 text-gray-300 rounded-full flex items-center justify-center mx-auto mb-4 text-2xl">
            <i class="fa-solid fa-calendar-day"></i>
        </div>
        <h3 class="font-bold text-gray-900 mb-1">Empty Timetable</h3>
        <p class="text-gray-400 text-sm">No learning sessions scheduled for this period.</p>
    </div>
<?php else: ?>
    <div class="overflow-x-auto pb-10">
        <div class="min-w-[1000px] grid grid-cols-7 gap-4">
            <?php
            $currentDate = $weekStart;
            for ($i = 0; $i < 7; $i++):
                $dateStr = date('Y-m-d', strtotime("$currentDate + $i days"));
                $dayName = date('D', strtotime($dateStr));
                $dayNum = date('j', strtotime($dateStr));
                $isToday = ($dateStr === date('Y-m-d'));
                $classesForDay = $byDate[$dateStr] ?? [];
            ?>
            <div class="flex flex-col gap-4">
                <!-- Day Column Header -->
                <div class="text-center pb-3 border-b-2 <?= $isToday ? 'border-emerald-500' : 'border-gray-200' ?>">
                    <div class="text-[10px] font-bold uppercase tracking-wider mb-1 <?= $isToday ? 'text-emerald-600' : 'text-gray-500' ?>"><?= $dayName ?></div>
                    <div class="text-xl font-bold <?= $isToday ? 'text-emerald-600' : 'text-gray-900' ?>">
                        <?= $dayNum ?>
                    </div>
                </div>
                
                <!-- Time Blocks -->
                <div class="flex flex-col gap-3 h-full">
                    <?php if (empty($classesForDay)): ?>
                        <div class="h-24 rounded-xl border border-dashed border-gray-200 bg-gray-50 flex flex-col items-center justify-center text-gray-400">
                            <span class="text-[10px] font-bold uppercase tracking-wider">Rest Period</span>
                        </div>
                    <?php else: ?>
                        <?php foreach ($classesForDay as $c): ?>
                            <a href="<?= APP_URL ?>/student/courses/<?= $c['course_id'] ?>" class="block group h-full">
                                <div class="bg-white border border-gray-200 p-4 rounded-xl shadow-sm hover:border-emerald-500 transition-colors h-full flex flex-col relative overflow-hidden">
                                    <div class="text-[10px] font-bold text-emerald-700 mb-2 flex items-center gap-1.5 bg-emerald-50 px-2 py-1 rounded w-fit">
                                        <i class="fa-regular fa-clock"></i> 
                                        <?= date('h:i A', strtotime($c['class_start_time'])) ?>
                                    </div>
                                    
                                    <h4 class="font-bold text-gray-900 text-sm leading-tight group-hover:text-emerald-600 transition-colors mb-3">
                                        <?= htmlspecialchars($c['course_name']) ?>
                                    </h4>
                                    
                                    <div class="mt-auto flex flex-col gap-1.5">
                                        <div class="text-[9px] uppercase font-bold px-1.5 py-0.5 bg-gray-100 text-gray-600 rounded w-fit tracking-wider">
                                            <?= htmlspecialchars($c['subject']) ?>
                                        </div>
                                        <div class="text-[10px] font-bold text-gray-400">
                                            <?= htmlspecialchars($c['teacher_name']) ?>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
            <?php endfor; ?>
        </div>
    </div>
<?php endif; ?>
