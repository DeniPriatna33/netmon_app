<div class="login-box">
    <div class="card card-outline card-primary">
        <div class="card-header text-center">
            <a href="<?= base_url() ?>" class="h1"><b>Net</b>Mon</a>
        </div>
        <div class="card-body">
            <p class="login-box-msg">Masuk untuk memulai sesi monitoring</p>

            <?php if (validation_errors()): ?>
                <div class="alert alert-danger alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                    <?= validation_errors() ?>
                </div>
            <?php endif; ?>

            <?= form_open('auth/process_login') ?>
                <div class="input-group mb-3">
                    <input type="text" name="username" class="form-control" placeholder="Username"
                           value="<?= set_value('username') ?>" autofocus>
                    <div class="input-group-append">
                        <div class="input-group-text"><span class="fas fa-user"></span></div>
                    </div>
                </div>
                <div class="input-group mb-3">
                    <input type="password" name="password" class="form-control" placeholder="Password">
                    <div class="input-group-append">
                        <div class="input-group-text"><span class="fas fa-lock"></span></div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary btn-block">Masuk</button>
                    </div>
                </div>
            <?= form_close() ?>
        </div>
    </div>
</div>
