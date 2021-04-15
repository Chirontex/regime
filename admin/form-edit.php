<?php
/**
 * @package Regime
 */
if (!defined('ABSPATH')) die;

?>
<div class="container-fluid">
    <h1 class="h3 text-center mb-5">
<?php

if (empty($_GET['fid'])) esc_html_e('Создание формы', 'regime');
else esc_html_e('Редактирование формы', 'regime');

?>
    </h1>
    <div>
        <label for="regimeFormType" class="form-label"><?= esc_html__('Тип формы:', 'regime') ?></label>
        <select name="regimeFormType" id="regimeFormType" class="form-select form-select-sm" style="max-width: 200px;">
            <option value="registration"<?= $_GET['ftype'] === 'registration' ? 'selected' : '' ?>><?= esc_html__('Регистрация', 'regime') ?></option>
            <option value="authorization"<?= $_GET['ftype'] === 'authorization' ? 'selected' : '' ?>><?= esc_html__('Авторизация', 'regime') ?></option>
            <option value="profile"<?= $_GET['ftype'] === 'profile' ? 'selected' : '' ?>><?= esc_html__('Профиль', 'regime') ?></option>
        </select>
    </div>
</div>