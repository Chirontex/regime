<?php
/**
 * @package Regime
 */
if (!defined('ABSPATH')) die;

?>
<div class="container-fluid">
    <h1 class="h3 text-center mb-5">
        <?= esc_html__('Управление формами', 'regime-ru_RU') ?>
    </h1>
    <div>
        <button class="button button-primary dropdown-toggle" id="regime-form-create" data-bs-toggle="dropdown" aria-expanded="false">
            <?= file_get_contents($path.'misc/icons/plus.svg').
                ' '.esc_html__('Создать форму', 'regime-ru_RU')
            ?>
        </button>
        <ul class="dropdown-menu" aria-labelledby="regime-form-create">
            <li><a href="<?= site_url('/wp-admin/admin.php?page=regime-forms&faction=edit&ftype=registration') ?>" class="dropdown-item"><?= esc_html__('Регистрация', 'regime-ru_RU') ?></a></li>
            <li><a href="<?= site_url('/wp-admin/admin.php?page=regime-forms&faction=edit&ftype=authorization') ?>" class="dropdown-item"><?= esc_html__('Авторизация', 'regime-ru_RU') ?></a></li>
            <li><a href="<?= site_url('/wp-admin/admin.php?page=regime-forms&faction=edit&ftype=profile') ?>" class="dropdown-item"><?= esc_html__('Профиль', 'regime-ru_RU') ?></a></li>
        </ul>
    </div>
</div>