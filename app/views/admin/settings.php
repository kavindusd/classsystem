<div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Site Configuration</h1>
        <p class="text-gray-500 text-sm">Manage system preferences and infrastructure.</p>
    </div>
</div>

<?php if ($msg = Session::flash('success')): ?>
    <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-lg mb-6 text-sm font-medium">
        <i class="fa-solid fa-check-circle mr-2"></i>
        <?= htmlspecialchars($msg) ?>
    </div>
<?php endif; ?>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 pb-10">
    <!-- General Identity -->
    <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm flex flex-col">
        <h2 class="font-bold text-gray-900 mb-6 text-lg flex items-center gap-2">
            <i class="fa-solid fa-globe text-emerald-600"></i>
            General Identity
        </h2>

        <form method="POST" action="<?= APP_URL ?>/admin/settings/site" enctype="multipart/form-data" class="flex flex-col gap-5 flex-1">
            <div class="flex flex-col gap-2">
                <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider">Site Name / Brand</label>
                <input type="text" name="site_name" value="<?= htmlspecialchars($settings['site_name'] ?? 'ClassSystem') ?>" required class="form-input w-full">
            </div>

            <div class="flex flex-col gap-2">
                <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider">Site Logo</label>
                <div class="p-4 bg-gray-50 border border-gray-200 rounded-lg">
                    <div class="flex items-center gap-3">
                        <i class="fa-solid fa-image text-gray-400"></i>
                        <div class="flex-1">
                            <input type="file" name="site_logo" accept=".png,.jpg,.jpeg,.svg" class="text-xs text-gray-600" />
                        </div>
                        <?php if (!empty($settings['site_logo'])): ?>
                            <img src="<?= APP_URL ?>/public/assets/images/<?= htmlspecialchars($settings['site_logo']) ?>" class="h-6 w-auto" />
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="flex flex-col gap-2">
                <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider">Favicon</label>
                <div class="p-4 bg-gray-50 border border-gray-200 rounded-lg">
                    <div class="flex items-center gap-3">
                        <i class="fa-solid fa-circle-dot text-gray-400"></i>
                        <div class="flex-1">
                            <input type="file" name="favicon" accept=".ico,.png,.jpg" class="text-xs text-gray-600" />
                        </div>
                    </div>
                </div>
            </div>

            <button type="submit" class="w-full py-3 bg-emerald-600 text-white rounded-lg font-bold hover:bg-emerald-700 transition-colors mt-auto">
                Save Identity
            </button>
        </form>
    </div>

    <!-- WhatsApp & Logic -->
    <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm">
        <h2 class="font-bold text-gray-900 mb-4 text-lg flex items-center gap-2">
            <i class="fa-brands fa-whatsapp text-emerald-600"></i>
            WhatsApp Intelligence & Auth
        </h2>

        <form method="POST" action="<?= APP_URL ?>/admin/settings/whatsapp" class="flex flex-col gap-5">
            <div class="p-4 bg-emerald-50 border border-emerald-100 rounded-lg">
                <label class="flex items-center gap-3 cursor-pointer">
                    <input type="checkbox" name="phone_login_enabled" value="1" <?= ($settings['phone_login_enabled'] ?? '0') === '1' ? 'checked' : '' ?> class="w-4 h-4 text-emerald-600 border-emerald-300 rounded focus:ring-emerald-500">
                    <span class="text-sm font-bold text-emerald-800">Enable Phone Number Login</span>
                </label>
                <p class="text-[10px] text-emerald-600 mt-2 uppercase tracking-wider font-bold">When active, students can authenticate using their registered mobile identifiers.</p>
            </div>

            <div class="flex flex-col gap-2">
                <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider">WhatsApp Gateway Provider</label>
                <select name="whatsapp_provider" class="form-select w-full">
                    <option value="none" <?= ($settings['whatsapp_provider'] ?? '') === 'none' ? 'selected' : '' ?>>Simulation Mode (Log Only)</option>
                    <option value="twilio" <?= ($settings['whatsapp_provider'] ?? '') === 'twilio' ? 'selected' : '' ?>>Twilio Business API</option>
                    <option value="ultramsg" <?= ($settings['whatsapp_provider'] ?? '') === 'ultramsg' ? 'selected' : '' ?>>UltraMsg Gateway</option>
                    <option value="custom" <?= ($settings['whatsapp_provider'] ?? '') === 'custom' ? 'selected' : '' ?>>Custom Webhook</option>
                </select>
            </div>

            <div class="flex flex-col gap-2">
                <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider">API Endpoint URL</label>
                <input type="text" name="whatsapp_api_url" value="<?= htmlspecialchars($settings['whatsapp_api_url'] ?? '') ?>" placeholder="https://api.ultramsg.com/instance123/messages/chat" class="form-input w-full">
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="flex flex-col gap-2">
                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider">API Token / Key</label>
                    <input type="password" name="whatsapp_api_key" placeholder="••••••••" class="form-input w-full">
                </div>
                <div class="flex flex-col gap-2">
                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider">Instance ID (Optional)</label>
                    <input type="text" name="whatsapp_instance_id" value="<?= htmlspecialchars($settings['whatsapp_instance_id'] ?? '') ?>" placeholder="instance12345" class="form-input w-full">
                </div>
            </div>

            <button type="submit" class="w-full py-3 bg-emerald-600 text-white rounded-lg font-bold hover:bg-emerald-700 transition-colors shadow-sm">
                Deploy WhatsApp Connectivity
            </button>
        </form>
    </div>

    <!-- SMTP Settings -->
    <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm">
        <h2 class="font-bold text-gray-900 mb-6 text-lg flex items-center gap-2">
            <i class="fa-solid fa-paper-plane text-emerald-600"></i>
            Mailing Engine (SMTP)
        </h2>

        <form method="POST" action="<?= APP_URL ?>/admin/settings/smtp" class="flex flex-col gap-5">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="flex flex-col gap-2">
                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider">SMTP Host</label>
                    <input type="text" name="smtp_host" value="<?= htmlspecialchars($settings['smtp_host'] ?? '') ?>" placeholder="smtp.gmail.com" class="form-input w-full">
                </div>

                <div class="flex flex-col gap-2">
                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider">SMTP Port</label>
                    <input type="number" name="smtp_port" value="<?= htmlspecialchars($settings['smtp_port'] ?? '587') ?>" class="form-input w-full">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="flex flex-col gap-2">
                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider">Auth Username</label>
                    <input type="text" name="smtp_username" value="<?= htmlspecialchars($settings['smtp_username'] ?? '') ?>" class="form-input w-full">
                </div>

                <div class="flex flex-col gap-2">
                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider">Auth Password</label>
                    <input type="password" name="smtp_password" placeholder="••••••••" class="form-input w-full">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="flex flex-col gap-2">
                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider">Sender Email</label>
                    <input type="email" name="smtp_from_email" value="<?= htmlspecialchars($settings['smtp_from_email'] ?? '') ?>" class="form-input w-full">
                </div>

                <div class="flex flex-col gap-2">
                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider">Encryption</label>
                    <select name="smtp_encryption" class="form-select w-full">
                        <option value="tls" <?= ($settings['smtp_encryption'] ?? '') === 'tls' ? 'selected' : '' ?>>TLS (Recommended)</option>
                        <option value="ssl" <?= ($settings['smtp_encryption'] ?? '') === 'ssl' ? 'selected' : '' ?>>SSL</option>
                        <option value="" <?= ($settings['smtp_encryption'] ?? '') === '' ? 'selected' : '' ?>>None</option>
                    </select>
                </div>
            </div>

            <div class="flex flex-col gap-2">
                <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider">Sender Display Name</label>
                <input type="text" name="smtp_from_name" value="<?= htmlspecialchars($settings['smtp_from_name'] ?? '') ?>" class="form-input w-full">
            </div>

            <button type="submit" class="w-full py-3 bg-emerald-600 text-white rounded-lg font-bold hover:bg-emerald-700 transition-colors mt-2">
                Update SMTP Infrastructure
            </button>
        </form>
    </div>
</div>
