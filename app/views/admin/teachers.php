<div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Teacher Management</h1>
        <p class="text-gray-500 text-sm">Manage teacher profiles and account settings.</p>
    </div>
    <button onclick="toggleModal('createTeacherModal')" class="bg-emerald-600 text-white px-6 py-2.5 rounded-lg font-bold hover:bg-emerald-700 transition-colors shadow-sm flex items-center gap-2">
        <i class="fa-solid fa-plus text-xs"></i>
        Add New Teacher
    </button>
</div>

<!-- Filter Bar -->
<div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm mb-8">
    <form method="GET" class="relative group">
        <i class="fa-solid fa-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
        <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="Search teachers by name or ID..."
               class="w-full pl-11 pr-4 py-2 bg-gray-50 border border-gray-200 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:outline-none text-sm font-medium">
    </form>
</div>

<?php if ($msg = Session::flash('success')): ?>
    <div class="bg-emerald-50 border border-emerald-200 text-emerald-600 px-4 py-3 rounded-lg mb-6 text-sm font-medium">
        <?= htmlspecialchars($msg) ?>
    </div>
<?php endif; ?>

<!-- Teacher Table -->
<div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Teacher</th>
                    <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">ID Number</th>
                    <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Contact</th>
                    <th class="px-6 py-4 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                <?php if (empty($teachers)): ?>
                <tr>
                    <td colspan="4" class="px-6 py-12 text-center text-gray-400 text-sm italic">No teachers found.</td>
                </tr>
                <?php else: ?>
                    <?php foreach ($teachers as $t): ?>
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-blue-100 text-blue-700 rounded-full flex items-center justify-center font-bold text-sm overflow-hidden border border-blue-200">
                                    <?php if (!empty($t['profile_image'])): ?>
                                        <img src="<?= APP_URL ?>/public/uploads/profiles/<?= htmlspecialchars($t['profile_image']) ?>" class="w-full h-full object-cover">
                                    <?php else: ?>
                                        <?= strtoupper(substr($t['name'], 0, 1)) ?>
                                    <?php endif; ?>
                                </div>
                                <div class="text-sm font-bold text-gray-900"><?= htmlspecialchars($t['name']) ?></div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-2.5 py-1 bg-gray-100 text-gray-600 text-[11px] font-bold rounded uppercase"><?= htmlspecialchars($t['teacher_id']) ?></span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-xs text-gray-600"><?= htmlspecialchars($t['email'] ?: 'No Email') ?></div>
                            <div class="text-[10px] text-gray-400"><?= htmlspecialchars($t['phone'] ?: '—') ?></div>
                        </td>
                        <td class="px-6 py-4 text-right flex items-center justify-end gap-2">
                            <button onclick="openEditModal(<?= htmlspecialchars(json_encode($t)) ?>)" class="w-8 h-8 rounded-lg bg-gray-100 text-gray-600 hover:bg-emerald-500 hover:text-white transition-all flex items-center justify-center">
                                <i class="fa-solid fa-pen text-xs"></i>
                            </button>
                            <form method="POST" action="<?= APP_URL ?>/admin/teachers/delete/<?= $t['id'] ?>" onsubmit="return confirm('Archive this account?')">
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

<!-- Create Modal -->
<div id="createTeacherModal" class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-lg overflow-hidden">
        <div class="p-6 border-b border-gray-100 flex justify-between items-center">
            <h3 class="font-bold text-gray-900">Add New Teacher</h3>
            <button onclick="toggleModal('createTeacherModal')" class="text-gray-400 hover:text-gray-600"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <form method="POST" action="<?= APP_URL ?>/admin/teachers/create" enctype="multipart/form-data" class="p-6 space-y-4">
            <div>
                <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">Profile Photo</label>
                <input type="file" name="profile_image" accept="image/*" class="text-xs text-gray-500">
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">Full Name</label>
                <input type="text" name="name" required class="w-full px-4 py-2 border border-gray-200 rounded-lg text-sm">
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">Email</label>
                    <input type="email" name="email" class="w-full px-4 py-2 border border-gray-200 rounded-lg text-sm" placeholder="Optional if phone provided">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">Phone</label>
                    <input type="text" name="phone" class="w-full px-4 py-2 border border-gray-200 rounded-lg text-sm" placeholder="Optional if email provided">
                </div>
            </div>
            <button type="submit" class="w-full py-3 bg-emerald-600 text-white rounded-lg font-bold hover:bg-emerald-700 transition-colors mt-4">Create Account</button>
        </form>
    </div>
</div>

<!-- Edit Modal -->
<div id="editTeacherModal" class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-lg overflow-hidden">
        <div class="p-6 border-b border-gray-100 flex justify-between items-center">
            <h3 class="font-bold text-gray-900">Update Teacher</h3>
            <button onclick="toggleModal('editTeacherModal')" class="text-gray-400 hover:text-gray-600"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <form method="POST" id="editTeacherForm" enctype="multipart/form-data" class="p-6 space-y-4">
            <div>
                <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">Profile Photo</label>
                <input type="file" name="profile_image" accept="image/*" class="text-xs text-gray-500">
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">Full Name</label>
                <input type="text" name="name" id="editName" required class="w-full px-4 py-2 border border-gray-200 rounded-lg text-sm">
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">Email</label>
                    <input type="email" name="email" id="editEmail" class="w-full px-4 py-2 border border-gray-200 rounded-lg text-sm">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">Phone</label>
                    <input type="text" name="phone" id="editPhone" class="w-full px-4 py-2 border border-gray-200 rounded-lg text-sm">
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
function openEditModal(t) {
    document.getElementById('editName').value = t.name;
    document.getElementById('editEmail').value = t.email || '';
    document.getElementById('editPhone').value = t.phone || '';
    document.getElementById('editTeacherForm').action = '<?= APP_URL ?>/admin/teachers/update/' + t.id;
    toggleModal('editTeacherModal');
}
</script>