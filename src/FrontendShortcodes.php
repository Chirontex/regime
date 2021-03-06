<?php
/**
 * @package Regime
 */
namespace Regime;

use Regime\Models\Tables\FormsTable;

/**
 * @final
 * Frontend shordcodes handler POE.
 * @since 0.6.3
 */
final class FrontendShortcodes extends GlobalHandler
{

    /**
     * @since 0.6.3
     */
    protected function init() : self
    {

        $this->regimeForm();

        return $this;

    }

    /**
     * Initialize the 'regime-form' shortcode.
     * @since 0.6.3
     * 
     * @return $this
     */
    protected function regimeForm() : self
    {

        add_shortcode('regime-form', function($atts) {

            $atts = shortcode_atts([
                'id' => ''
            ], $atts);

            if (!empty($atts['id'])) {

                $id = (int)$atts['id'];

                $forms_table = new FormsTable(
                    $this->wpdb,
                    $this->tables_props['forms']
                );

                $form = $forms_table->getForm($id);

                if (isset($form['action']) &&
                    !empty($form['fields'])) {

                    if ($form['type'] === 'profile') {

                        if (get_current_user_id() === 0) {

                            ob_start();

?>
<p><?= esc_html__('Авторизуйтесь, чтобы просматривать контент данной страницы.', 'regime') ?></p>
<?php

                            return ob_get_clean();

                        } else $user = wp_get_current_user();

                    } elseif (get_current_user_id() !== 0) {

                        ob_start();

?>
<p>
    <?= esc_html__('Вы авторизованы.', 'regime') ?> 
    <a href="<?= wp_logout_url('/') ?>"><?= esc_html__('Выйти', 'regime') ?></a>
</p>
<?php
                        
                        return ob_get_clean();

                    } elseif ($form['type'] === 'authorization') {

                        if (isset($_GET['regime'])) {

                            if ($_GET['regime'] === 'restore') {

                                ob_start();

?>
<?= apply_filters('regime-front-notice', '') ?>
<form action="" method="post" id="regimeForm_restore">
    <?php wp_nonce_field('regimeForm-restore', 'regimeForm-restore-wpnp') ?>
    <div id="regimeFormField_user_email_container">
        <div><label id="regimeFormField_user_email_label" for="regimeFormField_user_email">
            <?= esc_html__('Пожалуйста, введите ваш e-mail:', 'regime') ?>
        </label></div>
        <input type="email" name="regimeFormField_user_email" id="regimeFormField_user_email" placeholder="<?= esc_attr__('ваш e-mail', 'regime') ?>" required="true">
    </div>
    <div id="regimeForm_restore_submit" style="margin-top: 1rem;">
        <button type="submit"><?= esc_html__('Отправить', 'regime') ?></button>
    </div>
</form>
<?php

                                return ob_get_clean();

                            } elseif ($_GET['regime'] === 'newpass' &&
                                isset($_GET['user']) &&
                                isset($_GET['token'])) {

                                ob_start();
                            

?>
<?= apply_filters('regime-front-notice', '') ?>
<form action="" method="post" id="regimeForm_newpass">
    <?php wp_nonce_field('regimeForm-newpass', 'regimeForm-newpass-wpnp') ?>
    <input type="hidden" name="regimeFormField_user" value="<?= htmlspecialchars(urldecode($_GET['user'])) ?>" required="true">
    <input type="hidden" name="regimeFormField_user_token" value="<?= htmlspecialchars(urldecode($_GET['token'])) ?>" required="true">
    <div id="regimeFormField_user_password_container">
        <div><label for="regimeFormField_user_password_label">
            <?= esc_html__('Введите новый пароль:', 'regime') ?>
        </label></div>
        <input type="password" name="regimeFormField_user_password" id="regimeFormField_user_password" placeholder="<?= esc_attr__('новый пароль', 'regime') ?>" required="true">
    </div>
    <div id="regimeForm_newpass_submit" style="margin-top: 1rem;">
        <button type="submit"><?= esc_html__('Сохранить', 'regime') ?></button>
    </div>
</form>
<?php

                                return ob_get_clean();

                            }

                        }

                    }

                    $fields = json_decode($form['fields'], true);

                    $fields_enqueue = [];

                    foreach ($fields as $field_id => $props) {

                        $fields_enqueue[$field_id] = (int)$props['position'];

                    }

                    asort($fields_enqueue, SORT_NUMERIC);

                    $fields_enqueue = array_flip($fields_enqueue);

                    ob_start();

?>
<?= apply_filters('regime-front-notice', '') ?>
<form action="" method="post" id="regimeForm_<?= $id ?>">
    <input type="hidden" name="regimeFormField_formId" value="<?= $id ?>">
<?php

                    wp_nonce_field('regimeForm-'.$form['type'], 'regimeForm-'.$form['type'].'-wpnp');

                    foreach ($fields_enqueue as $field_id) {

?>
    <div id="regimeFormField_<?= $field_id ?>_container">
<?php

                        $type = explode('_', $field_id);
                        $type = $type[0];

                        if (!empty($fields[$field_id]['label']) &&
                            $type !== 'reset' &&
                            $type !== 'checkbox' &&
                            $type !== 'radio') {

?>
        <div>
            <label for="regimeFormField_<?= $field_id ?>" id="regimeFormField_<?= $field_id ?>_label"><?= $fields[$field_id]['label'] ?></label>
        </div>
<?php

                        }

                        $field = '<';

                        if ($type === 'textarea' ||
                            $type === 'select') $tag = $type;
                        elseif ($type === 'reset') $tag = 'button';
                        else $tag = 'input';

                        $field .= $tag;

                        if ($type !==
                            'textarea') $field .= ' type="'.$type.'"';

                        $field .= ' id="regimeFormField_'.$field_id.'"';

                        if ($type === 'radio') $field .= ' name="regimeFormField_'.
                            $type.'_'.$fields[$field_id]['key'].'"';
                        elseif ($type === 'select' && $fields[$field_id]['multiple']) {

                            $field .= ' name="regimeFormField_'.$field_id.'[]"';
                            $field .= ' multiple="true"';
                            $field .= ' size="5"';

                        } else $field .= ' name="regimeFormField_'.$field_id.'"';

                        $field .= ' class="'.$fields[$field_id]['css'].'"';

                        if ($type === 'datalist') $field .= ' list="regimeFormField_'.
                            $field_id.'_datalist"';

                        if ($tag !== 'button' &&
                            $tag !== 'select' &&
                            $type !== 'radio' &&
                            $type !== 'checkbox') $field .= ' placeholder="'.
                            htmlspecialchars($fields[$field_id]['placeholder']).'"';

                        if ($type !== 'select' &&
                            $type !== 'textarea' &&
                            $type !== 'reset') {
                                
                            if ($form['type'] ===
                                'profile' &&
                                $type !== 'checkbox' &&
                                $type !== 'radio' &&
                                $fields[$field_id]['key'] !==
                                'user_pass') $field .= ' value="'.htmlspecialchars($user->get(
                                    $fields[$field_id]['key']
                            )).'"';
                            elseif (isset($_POST['regimeFormField_'.$field_id]) &&
                                $type !== 'checkbox' &&
                                $type !== 'radio') $field .= ' value="'.htmlspecialchars($_POST['regimeFormField_'.$field_id]).'"';
                            elseif (isset(
                                $fields[$field_id]['value']
                            )) $field .= ' value="'.htmlspecialchars($fields[$field_id]['value']).'"';
                        
                        }

                        if ($type === 'date' ||
                            $type === 'number') {

                            if (isset($fields[$field_id]['min'])) $field .= ' min="'.
                                    htmlspecialchars($fields[$field_id]['min']).'"';

                            if (isset($fields[$field_id]['max'])) $field .= ' max="'.
                                    htmlspecialchars($fields[$field_id]['max']).'"';

                        }

                        if ($type === 'checkbox' ||
                            $type === 'radio') {

                            if ($form['type'] === 'profile') {

                                if ($user->get($fields[$field_id]['key']) ===
                                    $fields[$field_id]['value']) $field .= ' checked="true"';

                            } else {

                                if (isset(
                                    $_POST['regimeFormField_radio_'.$fields[$field_id]['key']]
                                )) {

                                    if ($_POST['regimeFormField_radio_'.$fields[$field_id]['key']] ===
                                        $fields[$field_id]['value']) $field .= ' checked="true"';

                                } elseif (isset($_POST['regimeFormField_'.$field_id])) {

                                    if ($_POST['regimeFormField_'.$field_id] ===
                                        $fields[$field_id]['value']) $field .= ' checked="true"';

                                } elseif ($fields[$field_id]['checked'] === true) $field .= ' checked="true"';

                            }

                        }

                        if ($fields[$field_id]['bound'] ||
                            $fields[$field_id]['required']) {
                                
                            if ($fields[$field_id]['key'] ===
                                'user_pass') {

                                if ($form['type'] ===
                                    'profile') $field .= '>'.PHP_EOL;
                                else $field .= ' required="true">'.PHP_EOL;

                            } else $field .= ' required="true">'.PHP_EOL;
                        
                        } else $field .= '>'.PHP_EOL;

                        if ($type ===
                            'textarea') {

                            if ($form['type'] ===
                                'profile') $field .= htmlspecialchars($user->get($fields[$field_id]['key'])).PHP_EOL;
                            elseif (isset(
                                $_POST['regimeFormField_'.$field_id]
                            )) $field .= htmlspecialchars($_POST['regimeFormField_'.$field_id]).PHP_EOL;
                            elseif (isset(
                                $fields[$field_id]['value']
                            )) $field .= htmlspecialchars($fields[$field_id]['value']).PHP_EOL;
                                
                            $field .= '</textarea>'.PHP_EOL;
                            
                        } elseif ($type === 'reset') $field .=
                            htmlspecialchars($fields[$field_id]['placeholder']).PHP_EOL.'</button>'.PHP_EOL;

                        if ($type === 'select' ||
                            $type === 'datalist') {

                            if ($type === 'datalist') $field .= '<datalist id="regimeFormField_'.
                                $field_id.'_datalist">'.PHP_EOL;

                            if ($type === 'select' &&
                                !empty($fields[$field_id]['placeholder'])) {

                                $field .= '<option value="">'.
                                    htmlspecialchars(
                                        $fields[$field_id]['placeholder']
                                    ).'</option>';

                            }

                            foreach ($fields[$field_id]['options'] as $value) {

                                if ($form['type'] ===
                                    'profile') $selected = $user->get($fields[$field_id]['key']);
                                elseif (isset(
                                    $_POST['regimeFormField_'.$field_id]
                                )) $selected = $_POST['regimeFormField_'.$field_id];
                                else $selected = $fields[$field_id]['value'];

                                if ($type === 'select' &&
                                    $fields[$field_id]['multiple']) {

                                    if (!is_array($selected)) $selected = unserialize($selected);

                                    if (!is_array($selected)) $selected = [$selected];

                                    $field .= '<option value="'.$value.'"'.
                                        (array_search($value, $selected) === false ?
                                            '' : ' selected="true"').'>'.$value.
                                            '</option>'.PHP_EOL;

                                } else {

                                    $field .= '<option value="'.$value.'"'.
                                        ($value === $selected &&
                                            $type === 'select' ?
                                                ' selected="true"': ''
                                        ).'>'.($type === 'select' ?
                                                $value.'</option>' :
                                                '').PHP_EOL;

                                }


                            }

                            if ($type === 'datalist') $field .= '</datalist>';
                            elseif ($type === 'select') $field .= '</select>';

                            $field .= PHP_EOL;

                        }

                        echo $field;

                        if (!empty($fields[$field_id]['label']) &&
                            ($type === 'checkbox' || $type === 'radio')) {

?>
        <label for="regimeFormField_<?= $field_id ?>" id="regimeFormField_<?= $field_id ?>_label"><?= $fields[$field_id]['label'] ?></label>
<?php

                        }

?>
    </div>
<?php

                    }

?>
    <div id="regimeForm_<?= htmlspecialchars($form['type'].'_'.$id) ?>_submit" style="margin-top: 1rem;">
        <button type="submit">
<?php

        switch ($form['type']) {

            case 'registration':
                esc_html_e('Зарегистрироваться', 'regime');
                break;

            case 'authorization':
                esc_html_e('Войти', 'regime');
                break;

            case 'profile':
                esc_html_e('Сохранить', 'regime');
                break;

        }

?>
        </button>
    </div>
<?php

        if ($form['type'] === 'profile') {

?>
    <div id="regimeForm_profile_<?= $id ?>_logout" style="margin-top: 1rem;">
        <button type="button" onclick="window.location.replace('<?= wp_logout_url('/') ?>');">
            <?= esc_html__('Выход', 'regime') ?>
        </button>
    </div>
<?php

        } elseif ($form['type'] === 'authorization' &&
            !isset($_GET['regime'])) {

        $link = $_SERVER['REQUEST_URI'];

        $link .= strpos($link, '?') ? '&' : '?';
        $link .= 'regime=restore';

?>
<div id="regimeForm_authorization_<?= $id ?>_forgot" style="margin-top: 1rem;">
    <p><a href="<?= $link ?>"><?= esc_html__('Забыли пароль?', 'regime') ?></a></p>
</div>
<?php            

        }

?>
</form>
<?php

                    return ob_get_clean();

                }

            }

        });

        return $this;

    }

}
