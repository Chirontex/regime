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
    <div class="mb-5 mx-auto" style="max-width: 500px;">
        <div class="text-center mb-3">
            <?= esc_html__('Тип формы:', 'regime') ?>
        </div>
        <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
                <input type="radio" name="regimeFormType" id="regimeFormTypeRegistration" value="registration"<?= $_GET['ftype'] === 'registration' ? 'checked' : '' ?>>
                <label for="regimeFormTypeRegistration" class="form-label"><?= esc_html__('Регистрация', 'regime') ?></label>
            </div>
            <div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
                <input type="radio" name="regimeFormType" id="regimeFormTypeAuthorization" value="authorization"<?= $_GET['ftype'] === 'authorization' ? 'checked' : '' ?>>
                <label for="regimeFormTypeAuthorization" class="form-label"><?= esc_html__('Авторизация', 'regime') ?></label>
            </div>
            <div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
                <input type="radio" name="regimeFormType" id="regimeFormTypeProfile" value="profile"<?= $_GET['ftype'] === 'profile' ? 'checked' : '' ?>>
                <label for="regimeFormTypeProfile" class="form-label"><?= esc_html__('Профиль', 'regime') ?></label>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
            <h4 class="text-center mb-5">
                <?= esc_html__('Добавление полей', 'regime') ?>
            </h4>
            <div class="mb-3">
                <div class="text-center mb-3">
                    <?= esc_html__('Ввод:', 'regime') ?>
                </div>
                <div class="text-center">
                    <button class="btn btn-sm btn-outline-primary mb-1" onclick="document.regimeFormEdit.methods.fieldAdd('text', 'primary', '<?= esc_html__('Ввод текста', 'regime') ?>');"><?= esc_html__('Текст', 'regime') ?></button>
                    <button class="btn btn-sm btn-outline-primary mb-1" onclick="document.regimeFormEdit.methods.fieldAdd('number', 'primary', '<?= esc_html__('Ввод числа', 'regime') ?>');"><?= esc_html__('Число', 'regime') ?></button>
                    <button class="btn btn-sm btn-outline-primary mb-1" onclick="document.regimeFormEdit.methods.fieldAdd('email', 'primary', '<?= esc_html__('Ввод e-mail', 'regime') ?>');"><?= esc_html__('E-mail', 'regime') ?></button>
                    <button class="btn btn-sm btn-outline-primary mb-1" onclick="document.regimeFormEdit.methods.fieldAdd('password', 'primary', '<?= esc_html__('Ввод пароля', 'regime') ?>');"><?= esc_html__('Пароль', 'regime') ?></button>
                    <button class="btn btn-sm btn-outline-primary mb-1" onclick="document.regimeFormEdit.methods.fieldAdd('tel', 'primary', '<?= esc_html__('Ввод телефона', 'regime') ?>');"><?= esc_html__('Телефон', 'regime') ?></button>
                    <button class="btn btn-sm btn-outline-primary mb-1" onclick="document.regimeFormEdit.methods.fieldAdd('url', 'primary', '<?= esc_html__('Ввод URL', 'regime') ?>');"><?= esc_html__('URL', 'regime') ?></button>
                </div>
            </div>
            <div class="mb-3">
                <div class="text-center mb-3">
                    <?= esc_html__('Чекер:', 'regime') ?>
                </div>
                <div class="text-center">
                    <button class="btn btn-sm btn-outline-success mb-1" onclick="document.regimeFormEdit.methods.fieldAdd('checkbox', 'success', '<?= esc_html__('Чекбокс', 'regime') ?>');"><?= esc_html__('Чекбокс', 'regime') ?></button>
                    <button class="btn btn-sm btn-outline-success mb-1" onclick="document.regimeFormEdit.methods.fieldAdd('radio', 'success', '<?= esc_html__('Радиокнопка', 'regime') ?>');"><?= esc_html__('Радио', 'regime') ?></button>
                </div>
            </div>
            <div class="mb-3">
                <div class="text-center mb-3">
                    <?= esc_html__('Выбор:', 'regime') ?>
                </div>
                <div class="text-center">
                    <button class="btn btn-sm btn-outline-dark mb-1" onclick="document.regimeFormEdit.methods.fieldAdd('select', 'light', '<?= esc_html__('Список', 'regime') ?>');"><?= esc_html__('Список', 'regime') ?></button>
                    <button class="btn btn-sm btn-outline-dark mb-1" onclick="document.regimeFormEdit.methods.fieldAdd('datalist', 'light', '<?= esc_html__('Список с предиктивным вводом', 'regime') ?>');"><?= esc_html__('Предиктивный', 'regime') ?></button>
                </div>
            </div>
            <div class="mb-3">
                <div class="text-center mb-3">
                    <?= esc_html__('Прочее:', 'regime') ?>
                </div>
                <div class="text-center">
                    <button class="btn btn-sm btn-outline-secondary mb-1" onclick="document.regimeFormEdit.methods.fieldAdd('textarea', 'secondary', '<?= esc_html__('Текстовое поле', 'regime') ?>');"><?= esc_html__('Текстовое поле', 'regime') ?></button>
                    <button class="btn btn-sm btn-outline-secondary mb-1" onclick="document.regimeFormEdit.methods.fieldAdd('reset', 'secondary', '<?= esc_html__('Кнопка сброса', 'regime') ?>');"><?= esc_html__('Кнопка сброса', 'regime') ?></button>
                </div>
            </div>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
            <h4 class="text-center mb-5">
                <?= esc_html__('Поля формы', 'regime') ?>
            </h4>
            <table class="table table-sm table-hover mb-3">
                <tbody id="regimeFormFields">

                </tbody>
            </table>
        </div>
    </div>
</div>