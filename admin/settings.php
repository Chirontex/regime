<?php
/**
 * @package Regime
 */
if (!defined('ABSPATH')) die;

?>
<div class="container-fluid mt-3">
    <h1 class="h3 text-center mb-5">
        <?= esc_html__('Общие настройки', 'regime') ?>
    </h1>
    <div class="container" style="max-width: 500px;">
        <form action="" method="post">
            <?php wp_nonce_field('regimeSettings', 'regimeSettings-wpnp') ?>
            <div class="mb-3">
                <label for="regimeSettings_sender_email" class="form-label">
                    <?= esc_html__('E-mail отправителя писем', 'regime') ?>
                </label>
                <input type="email" name="regimeSettings_sender_email" id="regimeSettings_sender_email" class="form-control form-control-sm" placeholder="e-mail" value="<?= htmlspecialchars(apply_filters('regime-settings-sender-email', '')) ?>" required="true">
            </div>
            <div class="mb-3">
                <label for="regimeSettings_sender_name" class="form-label">
                    <?= esc_html__('Имя отправителя писем', 'regime') ?>
                </label>
                <input type="text" name="regimeSettings_sender_name" id="regimeSettings_sender_name" class="form-control form-control-sm" placeholder="<?= esc_attr__('имя', 'regime') ?>" value="<?= htmlspecialchars(apply_filters('regime-settings-sender-name', '')) ?>" required="true">
            </div>
            <div class="mb-3 text-center">
                <button type="submit" class="button button-primary"><?= esc_html__('Сохранить', 'regime') ?></button>
            </div>
        </form>
    </div>
</div>