<div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Notification Center</h1>
        <p class="text-gray-500 text-sm">Manage broadcasts and view administrative announcements.</p>
    </div>
</div>

<?php if ($msg = Session::flash('success')): ?>
    <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-lg mb-6 text-sm font-medium flex items-center gap-2">
        <i class="fa-solid fa-circle-check"></i>
        <?= htmlspecialchars($msg) ?>
    </div>
<?php endif; ?>

<div class="grid grid-cols-1 lg:grid-cols-12 gap-8 pb-10">
    
    <!-- Inbox Section -->
    <div class="lg:col-span-7 space-y-6">
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="p-5 border-b border-gray-100 flex items-center justify-between">
                <h2 class="font-bold text-gray-900 flex items-center gap-2">
                    <i class="fa-solid fa-inbox text-gray-400"></i>
                    Inbox
                </h2>
            </div>
            
            <div class="divide-y divide-gray-100">
                <?php if (empty($notifications)): ?>
                    <div class="py-12 text-center text-gray-400">
                        <i class="fa-solid fa-bell-slash text-3xl mb-3 text-gray-300"></i>
                        <p class="text-sm">Your communication log is empty.</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($notifications as $n): ?>
                    <div class="p-6 hover:bg-gray-50 transition-colors <?= $n['is_read'] ? 'opacity-80' : '' ?>">
                        <div class="flex gap-4">
                            <div class="w-10 h-10 rounded-lg flex items-center justify-center flex-shrink-0 <?= $n['sender_role'] === 'admin' ? 'bg-amber-100 text-amber-600' : 'bg-emerald-100 text-emerald-600' ?>">
                                <i class="fa-solid <?= $n['sender_role'] === 'admin' ? 'fa-shield-halved' : 'fa-user-graduate' ?>"></i>
                            </div>
                            <div class="flex-1">
                                <div class="flex justify-between items-start mb-2">
                                    <div class="flex items-center gap-3">
                                        <span class="text-[10px] font-bold uppercase tracking-wider px-2 py-0.5 rounded bg-gray-100 text-gray-600">
                                            <?= $n['sender_role'] === 'admin' ? 'System Admin' : 'Learner' ?>
                                        </span>
                                        <span class="text-[11px] font-bold text-gray-400 uppercase tracking-wider">
                                            <?= date('d M, h:i A', strtotime($n['created_at'])) ?>
                                        </span>
                                    </div>
                                    <?php if (!$n['is_read']): ?>
                                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                                    <?php endif; ?>
                                </div>
                                <p class="text-gray-700 text-sm leading-relaxed"><?= nl2br(htmlspecialchars($n['message'])) ?></p>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Compose Section -->
    <div class="lg:col-span-5">
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 sticky top-6">
            <div class="w-10 h-10 bg-emerald-100 text-emerald-600 rounded-lg flex items-center justify-center mb-4">
                <i class="fa-solid fa-paper-plane"></i>
            </div>
            
            <h2 class="font-bold text-gray-900 mb-1">Broadcast</h2>
            <p class="text-xs text-gray-500 mb-6">Send an announcement to your learners.</p>
            
            <form method="POST" action="<?= APP_URL ?>/teacher/notifications/send" class="space-y-4">
                <div>
                    <label class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-2 block">Target Audience</label>
                    <select name="recipient_type" id="recipientType" onchange="toggleRecipientFields()" required
                            class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:outline-none text-sm font-medium">
                        <option value="all">Global (All Students)</option>
                        <option value="course">Selective Module</option>
                        <option value="student">Direct Individual</option>
                    </select>
                </div>
                
                <div id="courseField" style="display:none;">
                    <label class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-2 block">Choose Course</label>
                    <select name="course_id" class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:outline-none text-sm font-medium">
                        <option value="">-- Select Module --</option>
                        <?php foreach ($myCourses as $c): ?>
                            <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div id="studentField" style="display:none;">
                    <label class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-2 block">Select Learner</label>
                    <select name="student_user_id" class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:outline-none text-sm font-medium">
                        <option value="">-- Choose Individual --</option>
                        <?php foreach ($myStudents as $s): ?>
                            <option value="<?= $s['user_id'] ?>"><?= htmlspecialchars($s['name']) ?> (<?= htmlspecialchars($s['student_id']) ?>)</option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-2 block">Message</label>
                    <textarea name="message" rows="5" required placeholder="Type your broadcast message..."
                              class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:outline-none text-sm font-medium resize-none"></textarea>
                </div>
                
                <button type="submit" class="w-full bg-emerald-600 text-white px-4 py-2.5 rounded-lg font-bold hover:bg-emerald-700 transition-colors shadow-sm flex items-center justify-center gap-2 mt-4">
                    Dispatch Alert
                </button>
            </form>
        </div>
    </div>
</div>

<script>
function toggleRecipientFields() {
    const type = document.getElementById('recipientType').value;
    document.getElementById('courseField').style.display = (type === 'course') ? 'block' : 'none';
    document.getElementById('studentField').style.display = (type === 'student') ? 'block' : 'none';
}
document.addEventListener('DOMContentLoaded', toggleRecipientFields);
</script>
