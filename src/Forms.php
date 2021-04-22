<?php
/**
 * @package Regime
 */
namespace Regime;

use Regime\Containers\TableProps;
use Regime\Models\Tables\FormsTable;

/**
 * @final
 * Forms admin page POE.
 * @since 0.0.6
 */
final class Forms extends AdminPage
{

    /**
     * @since 0.1.4
     */
    protected function init() : self
    {

        if ($_GET['page'] ===
            $this->container->slugGet()) {
                
            $this->enqueueInit();

            if (isset(
                $_POST['regimeFormSave-wpnp']
            )) $this->formSave();
        
        }

        return $this;

    }

    /**
     * Initialize styles and scripts enqueue.
     * @since 0.1.4
     * 
     * @return $this
     */
    protected function enqueueInit() : self
    {

        add_action('admin_enqueue_scripts', function() {

            if ($_GET['faction'] === 'edit') {

                wp_enqueue_style(
                    'form-edit',
                    $this->url.'css/form-edit.css',
                    [],
                    '0.0.2'
                );

                wp_enqueue_script(
                    'form-edit',
                    $this->url.'js/form-edit.js',
                    [],
                    '0.6.9',
                );

            }

        });

        return $this;

    }

    /**
     * Save the form.
     * @since 0.4.1
     * 
     * @return $this
     */
    protected function formSave() : self
    {

        add_action('plugins_loaded', function() {

            if (wp_verify_nonce(
                $_POST['regimeFormSave-wpnp'],
                'regimeFormSave'
            ) === false) $this->notice(
                'danger',
                $this->nonce_fail_message
            );
            else {

                $fields = json_decode($_POST['regimeFormFields'], true);

                $fields_enqueue = [];

                foreach ($fields as $field_id => $props) {

                    $fields_enqueue[$field_id] = (int)$props['position'];

                }

                asort($fields_enqueue, SORT_NUMERIC);

                $fields_enqueue = array_keys($fields_enqueue);

                for ($i = 1; $i <= count($fields_enqueue); $i++) {

                    $fields[$fields_enqueue[$i]]['position'] = $i;

                }

                $fields = json_encode($fields);

                $table_props = new TableProps('forms');

                $table_props
                    ->setField('form_id', 'BIGINT(20) UNSIGNED NOT NULL')
                    ->setField('key', 'VARCHAR(255) NOT NULL')
                    ->setField('value', 'LONGTEXT NOT NULL');

                $forms_table = new FormsTable($this->wpdb, $table_props);

                if (isset($_POST['regimeFormId'])) {

                    $id = (int)$_POST['regimeFormId'];

                    $forms_table->formUpdate($id, $fields);

                    $notice_text = esc_html__(
                        'Форма успешно обновлена!',
                        'regime'
                    );

                } else {

                    $forms_table->formAdd(
                        $_POST['regimeFormType'],
                        $fields
                    );

                    $notice_text = esc_html__(
                        'Форма успешно сохранена!',
                        'regime'
                    );

                }

                $this->notice('success', $notice_text);

            }

        });

        return $this;

    }

}
