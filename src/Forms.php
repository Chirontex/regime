<?php
/**
 * @package Regime
 */
namespace Regime;

use Regime\Models\Tables\FormsTable;

/**
 * @final
 * Forms admin page POE.
 * @since 0.0.6
 */
final class Forms extends AdminPage
{

    /**
     * @var Regime\Models\Tables\FormsTable $table
     * Table object.
     * @since 0.5.6
     */
    protected $table;

    /**
     * @since 0.1.4
     */
    protected function init() : self
    {

        if ($_GET['page'] ===
            $this->container->slugGet()) {
            
            $this->table = new FormsTable(
                $this->wpdb,
                $this->table_props
            );
                
            $view_script = explode('/', $this->container->viewGet());
            $view_script = $view_script[count($view_script) - 1];
                
            switch ($view_script) {

                case 'forms.php':

                    $this->enqueueFormsDirectly();

                    if (isset(
                        $_POST['regimeFormSave-wpnp']
                    )) $this->formSave();
                    elseif (isset(
                        $_POST['regimeFormDelete-wpnp']
                    )) $this->formDelete();

                    $this->formsOutput();

                    break;

                case 'form-edit.php':

                    $this->enqueueEdit();

                    if (isset($_GET['fid'])) $this->formGet();

                    break;

            }
        
        }

        return $this;

    }

    /**
     * Enqueue for main forms page only.
     * @since 0.5.2
     * 
     * @return $this
     */
    protected function enqueueFormsDirectly() : self
    {

        add_action('admin_enqueue_scripts', function() {

            wp_enqueue_script(
                'regime-forms',
                $this->url.'js/forms.js',
                [],
                '0.0.1'
            );

        });

        return $this;

    }

    /**
     * Scripts & styles enqueue for forms edit page.
     * @since 0.5.1
     * 
     * @return $this
     */
    protected function enqueueEdit() : self
    {

        add_action('admin_enqueue_scripts', function() {

            wp_enqueue_style(
                'regime-form-edit',
                $this->url.'css/form-edit.css',
                [],
                '0.0.2'
            );

            wp_enqueue_script(
                'regime-form-edit',
                $this->url.'js/form-edit.js',
                [],
                '0.8.3',
            );

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

                        $fields = json_encode(
                            $fields,
                            JSON_UNESCAPED_UNICODE |
                            JSON_UNESCAPED_SLASHES |
                            JSON_HEX_QUOT |
                            JSON_HEX_APOS
                        );

                        if (isset($_POST['regimeFormId'])) {

                            $id = (int)$_POST['regimeFormId'];

                            $this->table->updateForm(
                                $id,
                                $fields,
                                (string)$_POST['regimeFormAction']
                            );

                            $notice_text = esc_html__(
                                'Форма успешно обновлена!',
                                'regime'
                            );

                        } else {

                            $this->table->addForm(
                                (string)$_POST['regimeFormType'],
                                $fields,
                                (string)$_POST['regimeFormAction']
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
     * Initiates form deleting.
     * 
     * @return $this
     */
    protected function formDelete() : self
    {

        add_action('plugins_loaded', function() {

            if (wp_verify_nonce(
                $_POST['regimeFormDelete-wpnp'],
                'regimeFormDelete'
            ) === false) $this->notice(
                'danger',
                $this->nonce_fail_message
            );
            else {

                $this->table->deleteForm((int)$_POST['regimeFormId']);

                $this->notice(
                    'success',
                    esc_html__('Форма успешно удалена!', 'regime')
                );

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

            return $this->table->getFormFields($id);

        });

        add_filter('regime-exist-form-action', function() {

            $id = (int)$_GET['fid'];

            return $this->table->getFormAction($id);

        });

        return $this;

    }

    /**
     * Output forms to forms view.
     * @since 0.4.9
     * 
     * @return $this
     */
    protected function formsOutput() : self
    {

        add_filter('regime-forms', function() {

            return $this->table->getAllForms();

        });

        return $this;

    }

}
