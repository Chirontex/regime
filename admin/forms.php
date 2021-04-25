<?php
/**
 * @package Regime
 */
if (!defined('ABSPATH')) die;

?>
<div class="container-fluid">
    <h1 class="h3 text-center mb-5">
        <?= esc_html__('Управление формами', 'regime') ?>
    </h1>
    <div class="text-center">
        <button class="button button-primary dropdown-toggle" id="regime-form-create" data-bs-toggle="dropdown" aria-expanded="false" title="<?= esc_attr__('Создать форму', 'regime') ?>">
            <?= file_get_contents($path.'misc/icons/plus.svg').
                ' '.esc_html__('Создать форму', 'regime')
            ?>
        </button>
        <ul class="dropdown-menu" aria-labelledby="regime-form-create">
            <li><a href="<?= site_url('/wp-admin/admin.php?page=regime-forms&faction=edit&ftype=registration') ?>" class="dropdown-item"><?= esc_html__('Регистрация', 'regime') ?></a></li>
            <li><a href="<?= site_url('/wp-admin/admin.php?page=regime-forms&faction=edit&ftype=authorization') ?>" class="dropdown-item"><?= esc_html__('Авторизация', 'regime') ?></a></li>
            <li><a href="<?= site_url('/wp-admin/admin.php?page=regime-forms&faction=edit&ftype=profile') ?>" class="dropdown-item"><?= esc_html__('Профиль', 'regime') ?></a></li>
        </ul>
    </div>
    <form action="" method="post" id="regimeFormDelete">
        <?php wp_nonce_field('regimeFormDelete', 'regimeFormDelete-wpnp') ?>
    </form>
<?php

$forms = apply_filters('regime-forms', []);

if (empty($forms)) {

?>
    <div class="text-lead text-muted text-center fst-italic my-5">
        <?= esc_html__('Вы пока не создали ни одной формы.', 'regime') ?>
    </div>
<?php

} else {

    $i = 0;

    foreach ($forms as $form_id => $form) {

        $i += 1;

        if (($i + 2) % 3 === 0) {

?>
    <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 my-5">
<?php

        }

?>
        <div class="col">
            <div class="card shadow-sm">
                <div class="card-body">
                    <p class="card-text">
                        <?= esc_html__('Форма #', 'regime').$form_id ?>
                    <br />
                        <span class="text-muted">
<?php

        switch ($form['type']) {

            case 'registration':
                esc_html_e('Регистрационная форма', 'regime');
                break;

            case 'authorization':
                esc_html_e('Авторизационная форма', 'regime');
                break;

            case 'profile':
                esc_html_e('Форма профиля', 'regime');
                break;

        }

?>
                        </span>
                    </p>
                    <p class="card-text">
                        Шорткод для вставки на страницу: [regime-form id=<?= $form_id ?>]
                    </p>
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="btn-group">
                            <button type="button" class="btn btn-sm btn-outline-secondary dropdown-toggle" id="regime-form-copy" data-bs-toggle="dropdown" aria-expanded="false" title="<?= esc_attr__('Скопировать форму', 'regime') ?>"><?= file_get_contents($path.'misc/icons/copy.svg') ?></button>
                            <ul class="dropdown-menu" aria-labelledby="regime-form-copy">
                                <li><a href="<?= site_url('/wp-admin/admin.php?page=regime-forms&faction=copy&ftype=registration&fid='.$form_id) ?>" class="dropdown-item"><?= esc_html__('Скопировать в новую форму регистрации', 'regime') ?></a></li>
                                <li><a href="<?= site_url('/wp-admin/admin.php?page=regime-forms&faction=copy&ftype=authorization&fid='.$form_id) ?>" class="dropdown-item"><?= esc_html__('Скопировать в новую форму авторизации', 'regime') ?></a></li>
                                <li><a href="<?= site_url('/wp-admin/admin.php?page=regime-forms&faction=copy&ftype=profile&fid='.$form_id) ?>" class="dropdown-item"><?= esc_html__('Скопировать в новую форму профиля', 'regime') ?></a></li>
                            </ul>
                            <button type="button" class="btn btn-sm btn-outline-secondary" title="<?= esc_attr__('Редактировать форму', 'regime') ?>" onclick="window.location.replace('<?= site_url('/wp-admin/admin.php?page=regime-forms&faction=edit&ftype='.$form['type'].'&fid='.$form_id) ?>');"><?= file_get_contents($path.'misc/icons/edit.svg') ?></button>
                        </div>
                        <button class="btn btn-sm btn-outline-danger" onclick="document.regimeForms.methods.formDelete(<?= $form_id ?>);" title="<?= esc_attr__('Удалить форму', 'regime') ?>"><?= file_get_contents($path.'misc/icons/trash-2.svg') ?></button>
                    </div>
                </div>
            </div>
        </div>
<?php

        if ($i % 3 === 0 ||
            $i === count($forms)) {

?>
    </div>
<?php

        }

    }

}

?>
</div>
<script>
document.regimeForms.formDelete = document.getElementById('regimeFormDelete');
</script>