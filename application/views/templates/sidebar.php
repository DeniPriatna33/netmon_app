<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <a href="<?= base_url() ?>" class="brand-link">
        <span class="brand-text font-weight-light pl-3">NetMon</span>
    </a>

    <div class="sidebar">
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
            <div class="image">
                <img src="https://ui-avatars.com/api/?name=<?= urlencode($current_user->full_name ?? 'User') ?>&background=random" class="img-circle elevation-2" alt="User Image">
            </div>
            <div class="info">
                <a href="#" class="d-block"><?= htmlspecialchars($current_user->full_name ?? $current_user->username ?? 'User') ?></a>
            </div>
        </div>

        <?php $seg1 = $this->uri->segment(1); ?>
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu">

                <!-- Dashboard -->
                <li class="nav-item">
                    <a href="<?= base_url('dashboard') ?>" class="nav-link <?= ($seg1 == 'dashboard' || $seg1 == '') ? 'active' : '' ?>">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>Dashboard</p>
                    </a>
                </li>

                <!-- Master Data -->
                <li class="nav-header">MASTER DATA</li>
                <li class="nav-item">
                    <a href="<?= base_url('devices') ?>" class="nav-link <?= ($seg1 == 'devices') ? 'active' : '' ?>">
                        <i class="nav-icon fas fa-server"></i>
                        <p>Perangkat</p>
                    </a>
                </li>

                <!-- Monitoring -->
                <li class="nav-header">MONITORING</li>
                <li class="nav-item">
                    <a href="<?= base_url('alerts') ?>" class="nav-link <?= ($seg1 == 'alerts') ? 'active' : '' ?>">
                        <i class="nav-icon fas fa-bell"></i>
                        <p>Alerts</p>
                    </a>
                </li>

                <!-- Settings (Admin Only) -->
                <?php if (($current_user->role ?? '') === 'admin'): ?>
                <li class="nav-header">SETTINGS</li>
                <li class="nav-item">
                    <a href="<?= base_url('users') ?>" class="nav-link <?= ($seg1 == 'users') ? 'active' : '' ?>">
                        <i class="nav-icon fas fa-users"></i>
                        <p>Manajemen User</p>
                    </a>
                </li>
                <?php endif; ?>
            </ul>
        </nav>
    </div>
</aside>
