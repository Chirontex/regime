<?php
/**
 * @package Regime
 */
namespace Regime;

use Regime\Containers\AdminMenuPage;
use Regime\Containers\TableProps;
use Regime\Models\Tables\FormsTable;

/**
 * @final
 * Main POE.
 * @since 0.0.2
 */
final class Main extends PointOfEntry
{

    /**
     * @var string $admin_pages_dir
     * Admin pages directory.
     * @since 0.0.4
     */
    protected $admin_pages_dir;

    /**
     * @var string $icons_path
     * Icon universal dir/root relative path.
     * @since 0.0.6
     */
    protected $icons_path = 'misc/icons/';

    /**
     * @var TableProps $forms_table_props
     * Forms table properties container.
     * @since 0.4.4
     */
    protected $forms_table_props;

    /**
     * @var TableProps $mails_table_props
     * Letters table properties container.
     * @since 0.5.3
     */
    protected $mails_table_props;

    /**
     * @since 0.0.3
     */
    protected function init() : self
    {

        $this->admin_pages_dir = $this->path.'admin/';

        $this->forms_table_props = new TableProps('regime_forms');

        $this->forms_table_props
            ->setField('form_id', 'BIGINT(20) UNSIGNED NOT NULL')
            ->setField('key', 'VARCHAR(255) NOT NULL')
            ->setField('value', 'LONGTEXT');

        $this->mails_table_props = new TableProps('regime_mails');

        $this->mails_table_props
            ->setField('template_id', 'CHAR(50) NOT NULL')
            ->setField('header', 'VARCHAR(255)')
            ->setField('message', 'LONGTEXT');

        if (strpos($_SERVER['REQUEST_URI'], 'wp-admin') !== false) {

            $this
                ->menuAdd()
                ->formsInit()
                ->mailsInit()
                ->submenuRemove();

        }

        $this->shortcodeInit();
        
        return $this;

    }

    /**
     * Add plugin pages to admin menu.
     * @since 0.0.4
     * 
     * @return $this
     */
    protected function menuAdd() : self
    {

        add_action('admin_menu', function() {

            add_menu_page(
                esc_html__('Regime', 'regime'),
                esc_html__('Regime', 'regime'),
                100,
                'regime',
                function() {},
                $this->url.$this->icons_path.'airplay.svg'
            );

        });

        return $this;

    }

    /**
     * Initialize forms page.
     * @since 0.1.0
     * 
     * @return $this
     */
    protected function formsInit() : self
    {

        $view = 'forms.php';

        $page_title = esc_html__('Формы', 'regime');

        if (isset($_GET['faction'])) {

            if ($_GET['faction'] === 'edit' ||
                $_GET['faction'] === 'copy') {

                $view = 'form-edit.php';

                $page_title = esc_html__('Новая форма | Regime', 'regime');

                if (isset($_GET['fid'])) $page_title = empty($_GET['fid']) ?
                    $page_title : esc_html__('Редактирование формы | Regime', 'regime');
                elseif (!isset($_GET['ftype'])) $_GET['ftype'] = 'registration';

            }

        }

        $container = new AdminMenuPage(
            $this->admin_pages_dir.$view,
            file_get_contents($this->path.$this->icons_path.'grid.svg').
                ' '.esc_html__('Формы', 'regime'),
            $page_title,
            'regime-forms'
        );

        new Forms(
            $this->path,
            $this->url,
            $container,
            $this->forms_table_props
        );

        return $this;

    }

    /**
     * Initialize mail templates page.
     * @since 0.5.3
     * 
     * @return $this
     */
    protected function mailsInit() : self
    {

        $container = new AdminMenuPage(
            $this->admin_pages_dir.'mails.php',
            file_get_contents($this->path.$this->icons_path.'mail.svg').
            ' '.esc_html__('Шаблоны писем', 'regime'),
            'Шаблоны писем',
            'regime-letters'
        );

        new Mails(
            $this->path,
            $this->url,
            $container,
            $this->mails_table_props
        );

        return $this;

    }

    /**
     * Remove main page from its own submenu.
     * @since 0.0.7
     * 
     * @return $this
     */
    protected function submenuRemove() : self
    {

        add_action('admin_menu', function() {

            remove_submenu_page('regime', 'regime');

        });

        return $this;

    }

    /**
     * Initialize the shorcode.
     * @since 0.6.0
     * 
     * @return $this
     */
    protected function shortcodeInit() : self
    {

        add_shortcode('regime-form', function($atts) {

            $atts = shortcode_atts([
                'id' => ''
            ], $atts);

            $result = '';

            if (!empty($atts['id'])) {

                $id = (int)$atts['id'];

                $forms_table = new FormsTable(
                    $this->wpdb,
                    $this->forms_table_props
                );

                $form = $forms_table->getForm($id);

                if (!empty($form['action']) &&
                    !empty($form['fields'])) {

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
<?php

                    wp_nonce_field('regimeForm-'.$form['type'], 'regimeForm-'.$form['type'].'-wpnp');

                    foreach ($fields_enqueue as $field_id) {

?>
    <div id="regimeFormField_<?= $field_id ?>_container">
<?php

                        $type = explode('_', $field_id);
                        $type = $type[0];

                        if (!empty($fields[$field_id]['label']) &&
                            $type !== 'reset') {

?>
        <label for="regimeFormField_<?= $field_id ?>"><?= htmlspecialchars($fields[$field_id]['label']) ?></label>
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

?>
    </div>
<?php

                    }

?>
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
</form>
<?php

                    $result = ob_get_clean();

                }

            }

            return $result;

        });

        return $this;

    }

}
