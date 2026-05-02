<div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">System Administrators</h1>
        <p class="text-gray-500 text-sm">Manage admin accounts and access permissions.</p>
    </div>
    <button onclick="toggleModal('createAdminModal')" class="bg-emerald-600 text-white px-6 py-2.5 rounded-lg font-bold hover:bg-emerald-700 transition-colors shadow-sm flex items-center gap-2">
        <i class="fa-solid fa-user-plus text-xs"></i>
        Add Administrator
    </button>
</div>

<?php if ($msg = Session::flash('success')): ?>
    <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-lg mb-6 text-sm font-medium">
        <i class="fa-solid fa-check-circle mr-2"></i>
        <?= htmlspecialchars($msg) ?>
    </div>
<?php endif; ?>

<!-- Admin Table -->
<div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden pb-20">
    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead class="bg-gray-50 border-b border-gray-100">
                <tr>
                    <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Administrator</th>
                    <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Email</th>
                    <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Date Created</th>
                    <th class="px-6 py-4 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                <?php foreach ($admins as $a): ?>
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-indigo-100 text-indigo-700 rounded-lg flex items-center justify-center font-bold text-sm">
                                <?= strtoupper(substr($a['name'], 0, 1)) ?>
                            </div>
                            <div>
                                <div class="font-bold text-gray-900"><?= htmlspecialchars($a['name']) ?></div>
                                <div class="text-xs text-gray-500"><?= htmlspecialchars($a['admin_id']) ?></div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-gray-600"><?= htmlspecialchars($a['email']) ?></td>
                    <td class="px-6 py-4 text-gray-600"><?= date('d M Y', strtotime($a['created_at'])) ?></td>
                    <td class="px-6 py-4 text-right">
                        <form method="POST" action="<?= APP_URL ?>/admin/admins/delete/<?= $a['id'] ?>" 
                              onsubmit="return confirm('Remove admin access?')" class="inline">
                            <button class="px-4 py-2 bg-red-100 text-red-600 rounded-lg text-xs font-bold hover:bg-red-600 hover:text-white transition-all">
                                <i class="fa-solid fa-trash mr-1"></i>
                                Remove
                            </button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Create Modal -->
<div id="createAdminModal" class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-lg overflow-hidden">
        <div class="p-6 border-b border-gray-100 flex justify-between items-center">
            <h3 class="font-bold text-gray-900">Create New Administrator</h3>
            <button onclick="toggleModal('createAdminModal')" class="text-gray-400 hover:text-gray-600"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <form method="POST" action="<?= APP_URL ?>/admin/admins/create" class="p-6 space-y-4">
            <div>
                <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Full Name</label>
                <input type="text" name="name" required class="w-full px-4 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-emerald-500 focus:outline-none">
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Email</label>
                <input type="email" name="email" required class="w-full px-4 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-emerald-500 focus:outline-none">
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Password</label>
                <input type="password" name="password" required class="w-full px-4 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-emerald-500 focus:outline-none">
            </div>
            <button type="submit" class="w-full py-3 bg-emerald-600 text-white rounded-lg font-bold hover:bg-emerald-700 transition-colors mt-4">Create Administrator</button>
        </form>
    </div>
</div>

<script>
function toggleModal(id) {
    document.getElementById(id).classList.toggle('hidden');
}
</script>