<div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">System Notifications</h1>
        <p class="text-gray-500 text-sm">Send announcements to students, teachers, and administrators.</p>
    </div>
    <button onclick="toggleModal('sendNotificationModal')" class="bg-emerald-600 text-white px-6 py-2.5 rounded-lg font-bold hover:bg-emerald-700 transition-colors shadow-sm flex items-center gap-2">
        <i class="fa-solid fa-bell text-xs"></i>
        Send Notification
    </button>
</div>

<?php if ($msg = Session::flash('success')): ?>
    <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-lg mb-6 text-sm font-medium">
        <i class="fa-solid fa-check-circle mr-2"></i>
        <?= htmlspecialchars($msg) ?>
    </div>
<?php endif; ?>

<!-- Notification History -->
<div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
    <div class="p-6 border-b border-gray-100">
        <h2 class="font-bold text-gray-900">Notification History</h2>
    </div>
    <div class="overflow-x-auto max-h-[600px] overflow-y-auto">
        <?php if (empty($sent)): ?>
        <div class="px-6 py-12 text-center text-gray-400 text-sm italic">No notifications sent yet.</div>
        <?php else: ?>
        <div class="divide-y divide-gray-100">
            <?php foreach ($sent as $n): ?>
            <div class="px-6 py-4 hover:bg-gray-50 transition-colors border-l-4 border-emerald-500">
                <div class="flex items-start justify-between gap-4">
                    <div class="flex-1">
                        <div class="flex items-center gap-2 mb-2">
                            <span class="px-2 py-1 bg-emerald-100 text-emerald-700 text-[10px] font-bold rounded uppercase">
                                <?= htmlspecialchars($n['recipient_name']) ?>
                            </span>
                            <span class="text-xs text-gray-500">(<?= strtoupper($n['recipient_role']) ?>)</span>
                        </div>
                        <p class="text-sm text-gray-900 leading-relaxed"><?= nl2br(htmlspecialchars($n['message'])) ?></p>
                        <div class="text-xs text-gray-500 mt-2">
                            <i class="fa-solid fa-clock mr-1"></i>
                            <?= date('d M Y, H:i', strtotime($n['created_at'])) ?>
                        </div>
                    </div>
                    <i class="fa-solid fa-check-double text-emerald-500 text-lg"></i>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- Send Notification Modal -->
<div id="sendNotificationModal" class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-xl overflow-hidden">
        <div class="p-6 border-b border-gray-100 flex justify-between items-center">
            <h3 class="font-bold text-gray-900">Send Notification</h3>
            <button onclick="toggleModal('sendNotificationModal')" class="text-gray-400 hover:text-gray-600"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <form method="POST" action="<?= APP_URL ?>/admin/notifications/send" class="p-6 space-y-4">
            <div>
                <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Recipient Group</label>
                <select name="target" id="recipientTarget" required class="w-full px-4 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                    <option value="">Select Recipients...</option>
                    <optgroup label="Global Distribution">
                        <option value="all">Everyone (System Wide)</option>
                        <option value="all_students">All Students</option>
                        <option value="all_teachers">All Teachers</option>
                    </optgroup>

                    <optgroup label="Individual Students">
                        <option value="student_direct">Student (email or ID)</option>
                    </optgroup>

                    <optgroup label="Individual Teachers">
                        <?php foreach ($teachers as $t): ?>
                            <option value="teacher_<?= $t['id'] ?>"><?= htmlspecialchars($t['name']) ?> (<?= htmlspecialchars($t['teacher_id']) ?>)</option>
                        <?php endforeach; ?>
                    </optgroup>
                </select>

                <div id="studentDirectField" class="mt-3 hidden">
                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Student Email or ID</label>
                    <input
                        type="text"
                        name="student_identifier"
                        class="w-full px-4 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-emerald-500 focus:outline-none"
                        placeholder="e.g. student@email.com or student_id (or student row id)"
                    />
                </div>
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Message</label>
                <textarea name="message" rows="4" required placeholder="Type your notification message here..."
                          class="w-full px-4 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-emerald-500 focus:outline-none resize-none"></textarea>
            </div>
            <button type="submit" class="w-full py-3 bg-emerald-600 text-white rounded-lg font-bold hover:bg-emerald-700 transition-colors">Send Notification</button>
        </form>
    </div>
</div>

<script>
function toggleModal(id) {
    document.getElementById(id).classList.toggle('hidden');
}

(function initRecipientFields() {
    const recipientTarget = document.getElementById('recipientTarget');
    const studentDirectField = document.getElementById('studentDirectField');
    if (!recipientTarget || !studentDirectField) return;

    function sync() {
        const isStudentDirect = recipientTarget.value === 'student_direct';
        studentDirectField.classList.toggle('hidden', !isStudentDirect);
    }

    recipientTarget.addEventListener('change', sync);
    document.addEventListener('DOMContentLoaded', sync);
    sync();
})();
</script>
