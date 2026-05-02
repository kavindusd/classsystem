<div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Student Directory</h1>
        <p class="text-gray-500 text-sm">Manage student profiles and enrollment records.</p>
    </div>
    <button onclick="toggleModal('createStudentModal')" class="bg-emerald-600 text-white px-6 py-2.5 rounded-lg font-bold hover:bg-emerald-700 transition-colors shadow-sm flex items-center gap-2">
        <i class="fa-solid fa-user-plus text-xs"></i>
        Onboard Student
    </button>
</div>

<!-- Search & Filter -->
<div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm mb-8">
    <form method="GET" class="flex flex-col md:flex-row gap-4">
        <div class="flex-1 relative">
            <i class="fa-solid fa-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
            <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="Search by name, ID or email..."
                   class="w-full pl-11 pr-4 py-2 bg-gray-50 border border-gray-200 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:outline-none text-sm font-medium">
        </div>
        <button type="submit" class="px-6 py-2 bg-gray-900 text-white rounded-lg font-bold hover:bg-gray-800 transition-colors text-sm">
            Search
        </button>
    </form>
</div>

<!-- Student Table -->
<div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Student Details</th>
                    <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">ID Number</th>
                    <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Contact Intel</th>
                    <th class="px-6 py-4 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                <?php if (empty($students)): ?>
                <tr>
                    <td colspan="4" class="px-6 py-12 text-center text-gray-400 text-sm italic">No student records found matching your search.</td>
                </tr>
                <?php else: ?>
                    <?php foreach ($students as $s): ?>
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-emerald-100 text-emerald-700 rounded-full flex items-center justify-center font-bold text-sm">
                                    <?= strtoupper(substr($s['name'], 0, 1)) ?>
                                </div>
                                <div class="text-sm font-bold text-gray-900"><?= htmlspecialchars($s['name']) ?></div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-2.5 py-1 bg-gray-100 text-gray-600 text-[11px] font-bold rounded uppercase"><?= htmlspecialchars($s['student_id']) ?></span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-xs text-gray-600"><?= htmlspecialchars($s['email'] ?: 'No Email') ?></div>
                            <div class="text-[10px] text-emerald-600 font-bold"><?= htmlspecialchars($s['whatsapp_number'] ?: 'No WhatsApp') ?></div>
                        </td>
                        <td class="px-6 py-4 text-right flex items-center justify-end gap-2">
                            <button onclick="openEditModal(<?= htmlspecialchars(json_encode($s)) ?>)" class="w-8 h-8 rounded-lg bg-gray-100 text-gray-600 hover:bg-emerald-500 hover:text-white transition-all flex items-center justify-center">
                                <i class="fa-solid fa-pen text-xs"></i>
                            </button>
                            <form method="POST" action="<?= APP_URL ?>/admin/students/delete/<?= $s['id'] ?>" onsubmit="return confirm('Delete this record?')">
                                <button class="w-8 h-8 rounded-lg bg-gray-100 text-red-500 hover:bg-red-500 hover:text-white transition-all flex items-center justify-center">
                                    <i class="fa-solid fa-trash text-xs"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Simple Modal (Hidden by Default) -->
<div id="createStudentModal" class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-lg overflow-hidden">
        <div class="p-6 border-b border-gray-100 flex justify-between items-center">
            <h3 class="font-bold text-gray-900">Onboard New Student</h3>
            <button onclick="toggleModal('createStudentModal')" class="text-gray-400 hover:text-gray-600"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <form method="POST" action="<?= APP_URL ?>/admin/students/create" class="p-6 space-y-4">
            <div>
                <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">Full Name</label>
                <input type="text" name="name" required class="form-input w-full">
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">Email</label>
                    <input type="email" name="email" class="form-input w-full">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">WhatsApp</label>
                    <input type="text" name="whatsapp_number" class="form-input w-full">
                </div>
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">Password</label>
                <input type="password" name="password" required class="form-input w-full">
            </div>
            <button type="submit" class="w-full py-3 bg-emerald-600 text-white rounded-lg font-bold hover:bg-emerald-700 transition-colors mt-4">Initialize Account</button>
        </form>
    </div>
</div>

<div id="editStudentModal" class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-lg overflow-hidden">
        <div class="p-6 border-b border-gray-100 flex justify-between items-center">
            <h3 class="font-bold text-gray-900">Update Student</h3>
            <button onclick="toggleModal('editStudentModal')" class="text-gray-400 hover:text-gray-600"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <form method="POST" id="editStudentForm" class="p-6 space-y-4">
            <div>
                <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">Full Name</label>
                <input type="text" name="name" id="editName" required class="form-input w-full">
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">Email</label>
                    <input type="email" name="email" id="editEmail" class="form-input w-full">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">WhatsApp</label>
                    <input type="text" name="whatsapp_number" id="editWhatsapp" class="form-input w-full">
                </div>
            </div>
            <button type="submit" class="w-full py-3 bg-emerald-600 text-white rounded-lg font-bold hover:bg-emerald-700 transition-colors mt-4">Save Changes</button>
        </form>
    </div>
</div>

<script>
function toggleModal(id) {
    document.getElementById(id).classList.toggle('hidden');
}
function openEditModal(s) {
    document.getElementById('editName').value = s.name;
    document.getElementById('editEmail').value = s.email || '';
    document.getElementById('editWhatsapp').value = s.whatsapp_number || '';
    document.getElementById('editStudentForm').action = '<?= APP_URL ?>/admin/students/update/' + s.id;
    toggleModal('editStudentModal');
}
</script>