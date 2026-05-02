<div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Course Management</h1>
        <p class="text-gray-500 text-sm">Design and organize academic modules for your students.</p>
    </div>
    <button onclick="toggleModal('createCourseModal')" class="bg-emerald-600 text-white px-6 py-2.5 rounded-lg font-bold hover:bg-emerald-700 transition-colors shadow-sm flex items-center gap-2">
        <i class="fa-solid fa-plus text-xs"></i>
        New Course
    </button>
</div>

<!-- Search & Filter -->
<div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm mb-8 flex flex-col md:flex-row gap-4">
    <form method="GET" class="flex-1 relative">
        <i class="fa-solid fa-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
        <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="Search modules..."
               class="w-full pl-11 pr-4 py-2 bg-gray-50 border border-gray-200 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:outline-none text-sm font-medium">
    </form>
    <div class="px-4 py-2 bg-gray-100 rounded-lg text-xs font-bold text-gray-600 flex items-center">
        Total Modules: <?= count($courses) ?>
    </div>
</div>

<!-- Course Grid -->
<div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6 pb-20">
    <?php if (empty($courses)): ?>
        <div class="col-span-full bg-white py-12 text-center rounded-xl border border-dashed border-gray-300">
            <p class="text-gray-400 text-sm italic">No courses detected.</p>
        </div>
    <?php else: ?>
        <?php foreach ($courses as $c): ?>
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 hover:border-emerald-500 transition-colors group">
            <div class="flex items-start justify-between mb-4">
                <div class="w-12 h-12 bg-emerald-100 text-emerald-600 rounded-lg flex items-center justify-center text-xl">
                    <i class="fa-solid fa-book"></i>
                </div>
                <span class="px-2 py-1 <?= $c['status'] === 'active' ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-100 text-gray-600' ?> text-[10px] font-bold rounded uppercase">
                    <?= ucfirst($c['status']) ?>
                </span>
            </div>

            <h3 class="font-bold text-gray-900 group-hover:text-emerald-600 transition-colors mb-2"><?= htmlspecialchars($c['name']) ?></h3>
            <p class="text-xs text-gray-400 mb-4"><?= htmlspecialchars($c['grade']) ?> &bull; <?= htmlspecialchars($c['subject'] ?? 'Academic') ?></p>
            
            <div class="space-y-3 pt-4 border-t border-gray-100">
                <div class="flex items-center justify-between text-xs">
                    <span class="text-gray-500">Instructor:</span>
                    <span class="font-bold text-gray-900"><?= htmlspecialchars($c['teacher_name']) ?></span>
                </div>
                <div class="flex items-center justify-between text-xs">
                    <span class="text-gray-500">Fee (Monthly):</span>
                    <span class="font-bold text-emerald-600">LKR <?= number_format($c['price'], 0) ?></span>
                </div>
            </div>

            <div class="mt-6 flex items-center gap-2">
                <button onclick="openEditModal(<?= htmlspecialchars(json_encode($c)) ?>)" class="flex-1 py-2 bg-gray-50 text-gray-600 rounded-lg font-bold text-xs hover:bg-emerald-500 hover:text-white transition-all">
                    Configure
                </button>
                <form method="POST" action="<?= APP_URL ?>/admin/courses/delete/<?= $c['id'] ?>" onsubmit="return confirm('Archive course?')">
                    <button class="w-10 h-10 bg-gray-50 text-red-500 rounded-lg flex items-center justify-center hover:bg-red-500 hover:text-white transition-all">
                        <i class="fa-solid fa-trash text-xs"></i>
                    </button>
                </form>
            </div>
        </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<!-- Modals -->
<div id="createCourseModal" class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-xl overflow-hidden">
        <div class="p-6 border-b border-gray-100 flex justify-between items-center">
            <h3 class="font-bold text-gray-900">New Course Module</h3>
            <button onclick="toggleModal('createCourseModal')" class="text-gray-400 hover:text-gray-600"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <form method="POST" action="<?= APP_URL ?>/admin/courses/create" enctype="multipart/form-data" class="p-6 space-y-4 max-h-[80vh] overflow-y-auto">
            <div class="grid grid-cols-2 gap-4">
                <div class="col-span-2">
                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">Module Name</label>
                    <input type="text" name="name" required class="form-input w-full">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">Subject</label>
                    <input type="text" name="subject" required class="form-input w-full">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">Grade</label>
                    <input type="text" name="grade" required class="form-input w-full">
                </div>
                <div class="col-span-2">
                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">Instructor</label>
                    <select name="teacher_id" required class="form-select w-full">
                        <?php foreach ($teachers as $t): ?>
                            <option value="<?= $t['id'] ?>"><?= htmlspecialchars($t['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">Monthly Fee</label>
                    <input type="number" name="price" step="0.01" class="form-input w-full">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">Commission</label>
                    <input type="number" name="teacher_commission" step="0.01" class="form-input w-full">
                </div>
                <div class="col-span-2">
                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">Cover Image</label>
                    <input type="file" name="cover_image" accept="image/*" class="form-input w-full">
                </div>
                <div class="col-span-2">
                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">Description</label>
                    <textarea name="description" class="form-input w-full" rows="3"></textarea>
                </div>
            </div>
            <button type="submit" class="w-full py-3 bg-emerald-600 text-white rounded-lg font-bold hover:bg-emerald-700 transition-colors mt-4">Create Module</button>
        </form>
    </div>
</div>

<div id="editCourseModal" class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-xl overflow-hidden">
        <div class="p-6 border-b border-gray-100 flex justify-between items-center">
            <h3 class="font-bold text-gray-900">Update Course</h3>
            <button onclick="toggleModal('editCourseModal')" class="text-gray-400 hover:text-gray-600"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <form method="POST" id="editCourseForm" enctype="multipart/form-data" class="p-6 space-y-4 max-h-[80vh] overflow-y-auto">
            <div>
                <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">Module Name</label>
                <input type="text" name="name" id="editName" required class="form-input w-full">
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">Subject</label>
                    <input type="text" name="subject" id="editSubject" class="form-input w-full">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">Grade</label>
                    <input type="text" name="grade" id="editGrade" class="form-input w-full">
                </div>
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">Instructor</label>
                <select name="teacher_id" id="editTeacherId" class="form-select w-full">
                    <?php foreach ($teachers as $t): ?>
                        <option value="<?= $t['id'] ?>"><?= htmlspecialchars($t['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">Monthly Fee</label>
                    <input type="number" name="price" id="editPrice" step="0.01" class="form-input w-full">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">Commission</label>
                    <input type="number" name="teacher_commission" id="editCommission" step="0.01" class="form-input w-full">
                </div>
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">Cover Image</label>
                <input type="file" name="cover_image" accept="image/*" class="form-input w-full">
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">Description</label>
                <textarea name="description" id="editDescription" class="form-input w-full" rows="3"></textarea>
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">Status</label>
                <select name="status" id="editStatus" class="form-select w-full">
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                </select>
            </div>
            <button type="submit" class="w-full py-3 bg-emerald-600 text-white rounded-lg font-bold hover:bg-emerald-700 transition-colors mt-4">Save Changes</button>
        </form>
    </div>
</div>

<script>
function toggleModal(id) {
    document.getElementById(id).classList.toggle('hidden');
}
function openEditModal(c) {
    document.getElementById('editName').value = c.name;
    document.getElementById('editSubject').value = c.subject;
    document.getElementById('editGrade').value = c.grade;
    document.getElementById('editTeacherId').value = c.teacher_id;
    document.getElementById('editPrice').value = c.price;
    document.getElementById('editCommission').value = c.teacher_commission;
    document.getElementById('editDescription').value = c.description;
    document.getElementById('editStatus').value = c.status;
    document.getElementById('editCourseForm').action = '<?= APP_URL ?>/admin/courses/update/' + c.id;
    toggleModal('editCourseModal');
}
</script>