<?php
/**
 * @package Regime
 */
if (!defined('ABSPATH')) die;

?>
<div class="container-fluid mt-3">
    <h1 class="h3 text-center">
        <?= esc_html__('Настройки меню пользователей', 'regime') ?>
    </h1>
    <div class="container my-5 mx-auto text-center" style="max-width: 400px;">
        <form action="" method="post">
            <?php wp_nonce_field('regimeMenus', 'regimeMenus-wpnp') ?>
            <div class="mb-3">
                <label for="regimeMenus_user_menu" class="form-label">
                    <?= esc_html__('Меню для авторизованных пользователей', 'regime') ?>
                </label>
                <select name="regimeMenus_user_menu" id="regimeMenus_user_menu" class="form-select form-select-sm">
                    <option value="0"><?= esc_html__('По умолчанию', 'regime') ?></option>
<?php

foreach (apply_filters('regime-menus-user-menu', []) as $menu) {

?>
                    <option value="<?= $menu['term_id'] ?>"<?= isset($menu['selected'])? 'selected="true"' : '' ?>><?= htmlspecialchars($menu['name']) ?></option>
<?php

}

?>
                </select>
            </div>
            <button type="submit" class="button button-primary"><?= esc_html__('Сохранить', 'regime') ?></button>
        </form>
    </div>
</div>