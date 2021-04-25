<?php
/**
 * @package Regime
 */
if (!defined('ABSPATH')) die;

?>
<div class="container-fluid">
    <h1 class="h3 text-center mb-5">
<?php

$page_header = isset($_GET['fid']) ?
    esc_html__('Редактирование', 'regime') :
    esc_html__('Создание', 'regime');

switch ($_GET['ftype']) {

    case 'registration':
        $page_header .= ' '.esc_html__('формы регистрации', 'regime');
        break;

    case 'authorization':
        $page_header .= ' '.esc_html__('формы авторизации', 'regime');
        break;

    case 'profile':
        $page_header .= ' '.esc_html__('формы профиля', 'regime');
        break;

}

echo $page_header;

?>
    </h1>
    <div class="mb-5 text-center">
        <form id="regimeFormSave" action="<?= site_url('/wp-admin/admin.php?page=regime-forms') ?>" method="post">
            <?php wp_nonce_field('regimeFormSave', 'regimeFormSave-wpnp') ?>
<?php

if (isset($_GET['fid']) &&
    $_GET['faction'] === 'edit') {

?>
            <input type="hidden" name="regimeFormId" value="<?= htmlspecialchars(urldecode($_GET['fid'])) ?>">
<?php

}

?>
            <input type="hidden" name="regimeFormType" value="<?= htmlspecialchars(urldecode($_GET['ftype'])) ?>">
        </form>
        <button class="button button-primary" onclick="document.regimeFormEdit.methods.allFormSave();"><?= esc_html__('Сохранить форму', 'regime') ?></button>
    </div>
    <div class="row mb-5">
        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-5">
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
        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-7">
            <h4 class="text-center mb-5">
                <?= esc_html__('Поля формы', 'regime') ?>
            </h4>
            <table class="table table-sm table-hover mb-3">
                <thead>
                    <tr>
                        <th></th>
                        <th></th>
                        <th><?= esc_html__('Плейсхолдер', 'regime') ?></th>
                        <th><?= esc_html__('Лейбл', 'regime') ?></th>
                        <th><?= esc_html__('Ключ', 'regime') ?></th>
                        <th><?= esc_html__('Дефолтное значение', 'regime') ?></th>
                        <th><?= esc_html__('Отмечено', 'regime') ?></th>
                        <th><?= esc_html__('Обязательное', 'regime') ?></th>
                        <th></th>
                    </tr>
                </thead>
                <tbody id="regimeFormFields"></tbody>
            </table>
        </div>
    </div>
    <div id="regimeFieldEditingModalTrigger" style="display: none;" data-bs-toggle="modal" data-bs-target="#regimeFieldEditingModal"></div>
    <div class="modal fade" id="regimeFieldEditingModal" tabindex="-1" aria-labelledby="regimeFieldEditingModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="regimeFieldEditingModalLabel">
                        <?= esc_html__('Редактирование поля', 'regime') ?>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="<?= esc_attr__('Закрыть', 'regime') ?>"></button>
                </div>
                <div class="modal-body">
                    <div id="regimeFieldEdit_fieldId_block" hidden="true"></div>
                    <div id="regimeFieldEdit_placeholder_block" class="mb-3" hidden="true">
                        <label for="regimeFieldEdit_placeholder" class="form-label">
                            <?= esc_html__('Плейсхолдер:', 'regime') ?>
                        </label>
                    </div>
                    <div id="regimeFieldEdit_label_block" class="mb-3" hidden="true">
                        <label for="regimeFieldEdit_label" class="form-label">
                            <?= esc_html__('Лейбл:', 'regime') ?>
                        </label>
                    </div>
                    <div id="regimeFieldEdit_key_block" class="mb-3" hidden="true">
                        <label for="regimeFieldEdit_key" class="form-label">
                            <?= esc_html__('Ключ:', 'regime') ?>
                        </label>
                    </div>
                    <div id="regimeFieldEdit_value_block" class="mb-3" hidden="true">
                        <label for="regimeFieldEdit_value" class="form-label">
                            <?= esc_html__('Значение по умолчанию:', 'regime') ?>
                        </label>
                    </div>
                    <div id="regimeFieldEdit_options_block" class="mb-3" hidden="true">
                        <label for="regimeFieldEdit_options" class="form-label">
                            <?= esc_html__('Возможные значения:', 'regime') ?>
                        </label>
                    </div>
                    <div id="regimeFieldEdit_multiple_block" class="mb-3" hidden="true">
                        <label for="regimeFieldEdit_multiple">
                            <?= esc_html__('Мультивыбор', 'regime') ?>
                        </label>
                    </div>
                    <div id="regimeFieldEdit_strict_block" class="mb-3" hidden="true">
                        <label for="regimeFieldEdit_strict">
                            <?= esc_html__('Строгое', 'regime') ?>
                        </label>
                    </div>
                    <div id="regimeFieldEdit_checked_block" class="mb-3" hidden="true">
                        <label for="regimeFieldEdit_checked">
                            <?= esc_html__('Отмечено', 'regime') ?>
                        </label>
                    </div>
                    <div id="regimeFieldEdit_required_block" class="mb-3" hidden="true">
                        <label for="regimeFieldEdit_required">
                            <?= esc_html__('Обязательное', 'regime') ?>
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="button button-secondary mx-1" data-bs-dismiss="modal"><?= esc_html__('Закрыть', 'regime') ?></button>
                    <button type="button" class="button button-primary mx-1" data-bs-dismiss="modal" onclick="document.regimeFormEdit.methods.fieldSave();"><?= esc_html__('Сохранить изменения', 'regime') ?></button>
                </div>
            </div>
        </div>
    </div>
    <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 5">
        <div id="regimeToast" class="toast hide" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="toast-body">
                <span id="regimeToastText" style="color: white;"></span>
            </div>
        </div>
    </div>
</div>
<script>
document.regimeFormEdit.form = document.getElementById('regimeFormFields');
document.regimeFormEdit.emptyModal = document
    .getElementById('regimeFieldEditingModal').innerHTML;

document.regimeFormEdit.texts = {
    deleteButton: '<?= file_get_contents($path.'misc/icons/trash-2.svg') ?>',
    upButton: '<?= file_get_contents($path.'misc/icons/arrow-up.svg') ?>',
    downButton: '<?= file_get_contents($path.'misc/icons/arrow-down.svg') ?>',
    fieldDeleteSuccess: '<?= esc_html__('Поле удалено!', 'regime') ?>',
    fieldDeleteError: '<?= esc_html__('Это поле удалить нельзя!', 'regime') ?>',
    fieldSaveSuccess: '<?= esc_html__('Изменения поля сохранены!', 'regime') ?>',
    formSaveError: '<?= esc_html__('Атрибуты некоторых полей формы указаны некорректно!', 'regime') ?>'
};

<?php

if (isset($_GET['fid'])) {

?>
document.regimeFormEdit.fields = JSON.parse('<?= apply_filters('regime-exist-form-fields', '{}') ?>');
<?php

} else {

?>
document.regimeFormEdit.methods.fieldAdd(
    'email',
    'primary',
    '<?= esc_html__('Ваш e-mail', 'regime') ?>',
    true
);
document.regimeFormEdit.fields.email_1.key = 'user_email';
document.regimeFormEdit.fields.email_1.label = '<?= esc_html__('E-mail:', 'regime') ?>';

document.regimeFormEdit.methods.fieldAdd(
    'password',
    'primary',
    '<?= esc_html__('Ваш пароль', 'regime') ?>',
    true
);
document.regimeFormEdit.fields.password_1.key = 'user_pass';
document.regimeFormEdit.fields.password_1.label = '<?= esc_html__('Пароль:', 'regime') ?>';

<?php

}

?>
document.regimeFormEdit.methods.formRenderReload();
</script>