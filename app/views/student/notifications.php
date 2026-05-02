<div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Notification Center</h1>
        <p class="text-gray-500 text-sm">Stay updated with academic alerts and broadcasts.</p>
    </div>
</div>

<!-- Inbox List -->
<div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden mb-10">
    <div class="p-5 border-b border-gray-100 flex items-center justify-between">
        <h2 class="font-bold text-gray-900 flex items-center gap-2">
            <i class="fa-solid fa-inbox text-gray-400"></i>
            Inbox
        </h2>
    </div>
    
    <div class="divide-y divide-gray-100">
        <?php if (empty($notifications)): ?>
            <div class="py-16 text-center text-gray-400">
                <i class="fa-solid fa-bell-slash text-4xl mb-4 text-gray-300"></i>
                <p class="text-sm font-medium">Your communication log is empty.</p>
            </div>
        <?php else: ?>
            <?php foreach ($notifications as $n): ?>
            <div class="p-6 hover:bg-gray-50 transition-colors <?= $n['is_read'] ? 'opacity-70' : '' ?>">
                <div class="flex gap-4">
                    <div class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0 <?= $n['sender_role'] === 'admin' ? 'bg-amber-100 text-amber-600' : 'bg-emerald-100 text-emerald-600' ?>">
                        <i class="fa-solid <?= $n['sender_role'] === 'admin' ? 'fa-shield-halved' : 'fa-chalkboard-user' ?> text-xl"></i>
                    </div>
                    <div class="flex-1">
                        <div class="flex justify-between items-start mb-2">
                            <div class="flex items-center gap-3">
                                <span class="text-[10px] font-bold uppercase tracking-wider px-2 py-0.5 rounded <?= $n['sender_role'] === 'admin' ? 'bg-amber-100 text-amber-700' : 'bg-emerald-100 text-emerald-700' ?>">
                                    <?= $n['sender_role'] === 'admin' ? 'System Admin' : 'Faculty Broadcast' ?>
                                </span>
                                <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">
                                    <?= date('d M, h:i A', strtotime($n['created_at'])) ?>
                                </span>
                            </div>
                            <?php if (!$n['is_read']): ?>
                                <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 shadow-[0_0_8px_rgba(16,185,129,0.5)]"></span>
                            <?php endif; ?>
                        </div>
                        <p class="text-gray-700 text-sm leading-relaxed mt-2 font-medium"><?= nl2br(htmlspecialchars($n['message'])) ?></p>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>
