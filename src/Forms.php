<?php
/**
 * @package Regime
 */
namespace Regime;

use Regime\Interfaces\IAdminMenuPage;
use Regime\Interfaces\ITableProps;
use Regime\Models\Tables\FormsTable;

/**
 * @final
 * Forms admin page POE.
 * @since 0.0.6
 */
final class Forms extends AdminPage
{

    /**
     * @var FormsTable $forms_table
     * Class to work with BD table.
     * @since 0.4.3
     */
    protected $forms_table;

    /**
     * @var ITableProps $table_props
     * Table properties container.
     * @since 0.4.4
     */
    protected $table_props;

    /**
     * @since 0.4.4
     * 
     * @param ITableProps $table_props
     * Table properties container.
     */
    public function __construct(string $path, string $url, IAdminMenuPage $container, ITableProps $table_props)
    {
        
        $this->table_props = $table_props;

        parent::__construct($path, $url, $container);

    }

    /**
     * @since 0.1.4
     */
    protected function init() : self
    {

        if ($_GET['page'] ===
            $this->container->slugGet()) {
                
            $this->enqueueInit();

            $this->forms_table = new FormsTable(
                $this->wpdb,
                $this->table_props
            );

            $view_script = explode('/', $this->container->viewGet());
            $view_script = $view_script[count($view_script) - 1];

            switch ($view_script) {

                case 'forms.php':

                    if (isset(
                        $_POST['regimeFormSave-wpnp']
                    )) $this->formSave();

                    break;

                case 'form-edit.php':

                    if (isset($_GET['fid'])) $this->formGet();

                    break;

            }
        
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

                $type = (string)$_POST['regimeFormType'];

                if (empty($fields)) $this->notice(
                    'danger',
                    esc_html__('Пустую форму сохранить нельзя.', 'regime')
                );
                elseif ($type !== 'registration' &&
                    $type !== 'authorization' &&
                    $type !== 'profile') $this->notice(
                        'danger',
                        esc_html__('Что-то пошло не так: тип формы отсутствует или указан некорректно.', 'regime')
                    );
                else {

                    $fields_enqueue = [];

                    $email_check = false;

                    $password_check = false;

                    foreach ($fields as $field_id => $props) {

                        $fields_enqueue[$field_id] = (int)$props['position'];

                        $type = explode('_', $field_id);
                        $type = $type[0];

                        if ($type === 'email' &&
                            $props['bound'] === true) $email_check = true;

                        if ($type === 'password' &&
                            $props['bound'] === true) $password_check = true;

                    }

                    if ($email_check && $password_check) {

                        asort($fields_enqueue, SORT_NUMERIC);

                        $fields_enqueue = array_keys($fields_enqueue);

                        for ($i = 0; $i < count($fields_enqueue); $i++) {

                            $fields[$fields_enqueue[$i]]['position'] = $i + 1;

                        }

                        $fields = json_encode($fields);

                        if (isset($_POST['regimeFormId'])) {

                            $id = (int)$_POST['regimeFormId'];

                            $this->forms_table->updateForm($id, $fields);

                            $notice_text = esc_html__(
                                'Форма успешно обновлена!',
                                'regime'
                            );

                        } else {

                            $this->forms_table->addForm(
                                (string)$_POST['regimeFormType'],
                                $fields
                            );

                            $notice_text = esc_html__(
                                'Форма успешно сохранена!',
                                'regime'
                            );

                        }

                        $this->notice('success', $notice_text);

                    } else $this->notice(
                        'danger',
                        esc_html__('Форма не была сохранена: в ней отсутствуют обязательные поля.', 'regime')
                    );

                }

            }

        });

        return $this;

    }

    /**
     * Get form to the view.
     * @since 0.4.8
     * 
     * @return $this
     */
    protected function formGet() : self
    {

        add_filter('regime-exist-form-fields', function() {

            $id = (int)$_GET['fid'];

            return $this->forms_table->getFormFields($id);

        });

        return $this;

    }

}
