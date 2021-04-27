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

                        }

                    } elseif (get_current_user_id() !== 0) {

                        ob_start();

?>
<p>
    <?= esc_html__('Вы авторизованы.', 'regime') ?> 
    <a href="<?= wp_logout_url($_SERVER['REQUEST_URI']) ?>"><?= esc_html__('Выйти', 'regime') ?></a>
</p>
<?php
                        
                        return ob_get_clean();

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
<form action="<?= $form['type'] === 'authorization' && $_GET['restore'] === 'true' ? '' : $form['action'] ?>" method="post" id="regimeForm_<?= $id ?>">
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
        <p><label for="regimeFormField_<?= $field_id ?>" id="regimeFormField_<?= $field_id ?>_label"><?= htmlspecialchars($fields[$field_id]['label']) ?></label></p>
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

                        $field .= ' id="regimeFormField_'.$field_id.
                            '" name="regimeFormField_'.$field_id.'"';

                        $field .= ' class="'.$fields[$field_id]['css'].'"';

                        if ($type === 'datalist') $field .= ' list="regimeFormField_'.
                            $field_id.'_datalist"';

                        if ($tag !== 'button' &&
                            $tag !== 'select') $field .= ' placeholder="'.
                            $fields[$field_id]['placeholder'].'"';

                        if ($type !== 'select' &&
                            $type !== 'textarea' &&
                            $type !== 'reset' &&
                            $type !== 'checkbox' &&
                            $type !== 'radio' &&
                            !empty($fields[$field_id]['value'])) $field .= ' value="'.
                                $fields[$field_id]['value'].'"';

                        if ($type === 'checkbox' ||
                            $type === 'radio') $field .= ' value="'.$field_id.'"';

                        if ($fields[$field_id]['checked'] === true) $field .= ' checked="true"';

                        if ($fields[$field_id]['bound'] === true ||
                            $fields[$field_id]['required'] === true) $field .= ' required="true">'.PHP_EOL;

                        if ($type ===
                            'textarea') $field .= htmlspecialchars($fields[$field_id]['value']).PHP_EOL.'</textarea>'.PHP_EOL;
                        elseif ($type ===
                            'reset') $field .= htmlspecialchars($fields[$field_id]['placeholder']).PHP_EOL.'</button>'.PHP_EOL;

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

                                $field .= '<option value="'.$value.'"'.
                                    ($value ===
                                        $fields[$field_id]['value'] &&
                                        $type === 'select' ?
                                            ' selected="true"': ''
                                    ).'>'.($type === 'select' ?
                                        htmlspecialchars($value).'</option>' :
                                        '').PHP_EOL;

                            }

                            if ($type === 'datalist') $field .= '</datalist>';
                            elseif ($type === 'select') $field .= '</select>';

                            $field .= PHP_EOL;

                        }

                        echo $field;

                        if (!empty($fields[$field_id]['label']) &&
                            ($type === 'checkbox' || $type === 'radio')) {

?>
        <label for="regimeFormField_<?= $field_id ?>" id="regimeFormField_<?= $field_id ?>_label"><?= htmlspecialchars($fields[$field_id]['label']) ?></label>
<?php

                        }

?>
    </div>
<?php

                    }

?>
    <div id="regimeForm_<?= $field_id ?>_submit">
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
</form>
<?php

                    return ob_get_clean();

                }

            }

        });

        return $this;

    }

}
