<?php
/**
 * @package Regime
 */
if (!defined('ABSPATH')) die;

?>
<div class="container-fluid">
    <h1 class="h3 text-center mb-5">
        <?= esc_html__('Шаблоны писем', 'regime') ?>
    </h1>
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
            <form action="" method="post">
                <?php wp_nonce_field('regimeMailsEdit', 'regimeMailsEdit-wpnp') ?>
                <h5 class="text-center mb-5"><?= esc_html__('Приветственное письмо', 'regime') ?></h5>
                <div class="mb-3">
                    <label class="form-label" for="regimeMailRegistrationHeader"><?= esc_html__('Заголовок письма:', 'regime') ?></label>
                    <input type="text" name="regimeMailRegistrationHeader" id="regimeMailRegistrationHeader" class="form-control form-control-sm" value="<?= apply_filters('regime-mail-registration-header', '') ?>">
                </div>
                <div class="mb-5">
                    <label for="regimeMailRegistrationMessage" class="form-label"><?= esc_html__('Тело письма:', 'regime') ?></label>
                    <textarea name="regimeMailRegistrationMessage" id="regimeMailRegistrationMessage" cols="30" rows="10" class="form-control form-control-sm"><?= apply_filters('regime-mail-registration-message', '') ?></textarea>
                </div>
                <h5 class="text-center mb-5"><?= esc_html__('Письмо восстановления пароля', 'regime') ?></h5>
                <div class="mb-3">
                    <label class="form-label" for="regimeMailPasswordHeader"><?= esc_html__('Заголовок письма:', 'regime') ?></label>
                    <input type="text" name="regimeMailPasswordHeader" id="regimeMailPasswordHeader" class="form-control form-control-sm" value="<?= apply_filters('regime-mail-password-header', '') ?>">
                </div>
                <div class="mb-5">
                    <label for="regimeMailPasswordMessage" class="form-label"><?= esc_html__('Тело письма:', 'regime') ?></label>
                    <textarea name="regimeMailPasswordMessage" id="regimeMailPasswordMessage" cols="30" rows="10" class="form-control form-control-sm"><?= apply_filters('regime-mail-password-message', '') ?></textarea>
                </div>
                <div class="text-center">
                    <button type="submit" class="button button-primary"><?= esc_html__('Сохранить', 'regime') ?></button>
                </div>
            </form>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
            <h5 class="text-center mb-5"><?= esc_html__('Доступные шорткоды', 'regime') ?></h5>
            <ul class="list-group list-group-flush">
                <li class="list-group-item"><span class="fst-italic">!%site_url%!</span> — <?= esc_html__('адрес сайта', 'regime') ?></li>
                <li class="list-group-item"><span class="fst-italic">!%site_name%!</span> — <?= esc_html__('название сайта', 'regime') ?></li>
                <li class="list-group-item"><span class="fst-italic">!%password_restorage_link%!</span> — <?= esc_html__('Ссылка для восстановления пароля (работает только в соответствующем письме)', 'regime') ?></li>
            </ul>
        </div>
    </div>
</div>