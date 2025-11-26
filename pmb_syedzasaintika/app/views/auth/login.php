<?php $this->view('templates/header'); ?>

<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h3 class="text-center">Login</h3>
            </div>
            <div class="card-body">
                <?php if (isset($data['login_error'])): ?>
                    <div class="alert alert-danger"><?php echo $data['login_error']; ?></div>
                <?php endif; ?>
                
                <?php if (isset($data['captcha_error'])): ?>
                    <div class="alert alert-danger"><?php echo $data['captcha_error']; ?></div>
                <?php endif; ?>
                
                <form action="<?php echo BASE_URL; ?>/auth/login" method="post">
                    <div class="mb-3">
                        <label for="username" class="form-label">Username atau Email</label>
                        <input type="text" class="form-control" id="username" name="username" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    <div class="mb-3">
                        <label for="captcha" class="form-label">Captcha</label>
                        <div class="input-group">
                            <input type="text" class="form-control" id="captcha" name="captcha" maxlength="6" required>
                            <span class="input-group-text">
                                <img src="<?php echo BASE_URL; ?>/captcha" alt="Captcha" id="captcha-img">
                                <a href="#" onclick="refreshCaptcha()" class="ms-2">
                                    <i class="fas fa-sync-alt"></i>
                                </a>
                            </span>
                        </div>
                        <div class="form-text">Masukkan 6 digit angka captcha di atas</div>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Login</button>
                </form>
                
                <div class="text-center mt-3">
                    <p>Belum punya akun? <a href="<?php echo BASE_URL; ?>/auth/register">Daftar di sini</a></p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function refreshCaptcha() {
    document.getElementById('captcha-img').src = '<?php echo BASE_URL; ?>/captcha?' + new Date().getTime();
}
</script>

<?php $this->view('templates/footer'); ?>