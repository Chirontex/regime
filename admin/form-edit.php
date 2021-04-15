<?php
/**
 * @package Regime
 */
if (!defined('ABSPATH')) die;

?>
<div class="container-fluid">
    <h1 class="h3 text-center mb-5">
<?php

if (empty($_GET['fid'])) esc_html_e('Создание формы', 'regime-ru_RU');
else esc_html_e('Редактирование формы', 'regime-ru_RU');

?>
    </h1>
</div>