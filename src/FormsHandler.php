<?php
/**
 * @package Regime
 */
namespace Regime;

use Regime\Traits\Noticer;
use Regime\Models\Tables\FormsTable;
use Regime\Models\Tables\MailsTable;
use WP_Error;

/**
 * @final
 * Forms handler.
 * @since 0.6.4
 */
final class FormsHandler extends GlobalHandler
{

    use Noticer;

    /**
     * @var array $notice_container
     * Contains last notice.
     * Associative array with 'type' and 'text' keys.
     * @since 0.6.4
     */
    protected $notice_container;

    /**
     * @var string $nonce_fail_notice
     * Nonce vreifying fail notice text.
     * @since 0.6.4
     */
    protected $nonce_fail_notice;

    /**
     * @since 0.6.4
     */
    protected function init() : self
    {

        $this->nonce_fail_notice = esc_html__(
            'Произошла ошибка при отправке формы. Пожалуйста, попробуйте позже.',
            'regime'
        );

        if (isset($_POST['regimeForm-registration-wpnp'])) $this->registration();

        return $this;

    }

    /**
     * Handle registration forms.
     * @since 0.6.6
     * 
     * @return $this
     */
    protected function registration() : self
    {

        add_action('plugins_loaded', function() {

            if (wp_verify_nonce(
                $_POST['regimeForm-registration-wpnp'],
                'regimeForm-registration'
            ) === false) $this->notice(
                'danger',
                $this->nonce_fail_notice
            );
            else {

                $form = $this->formGet();

                if (empty($form)) return;

                $userdata = [];

                foreach ($form['fields'] as $field_id => $props) {

                    $type = explode('_', $field_id);
                    $type = $type[0];

                    if ($type !== 'reset') {

                        if ($type ===
                            'checkbox') $userdata[$props['key']] = isset(
                                $_POST['regimeFormField_'.$field_id]
                            ) ? 'true' : 'false';
                        else $userdata[$props['key']] = (string)$_POST['regimeFormField_'.$field_id];

                    }

                }

                $userdata['show_admin_bar_front'] = 'false';

                if (!isset($userdata['user_login']) &&
                    isset($userdata['user_email'])) {

                   $userdata['user_login'] = explode('@', $userdata['user_email']);
                   $userdata['user_login'] = $userdata['user_login'][0];

                }

                $insert = wp_insert_user($userdata);

                if ($insert instanceof WP_Error) $this->notice(
                    'danger',
                    $insert->get_error_message()
                );
                else {

                    foreach ($userdata as $metaname => $metadata) {

                        if ($metaname !== 'user_login' &&
                            $metaname !== 'user_pass' &&
                            $metaname !== 'user_nicename' &&
                            $metaname !== 'user_email' &&
                            $metaname !== 'user_url' &&
                            $metaname !== 'user_registered' &&
                            $metaname !== 'user_activation_key' &&
                            $metaname !== 'user_status' &&
                            $metaname !== 'display_name') {

                            add_user_meta(
                                $insert,
                                $metaname,
                                $metadata,
                                true
                            );

                        }

                    }

                    $this->notice(
                        'success',
                        esc_html__('Регистрация завершена!', 'regime')
                    );
                
                    if (isset($userdata['user_email'])) {

                        $template = $this->getMailTemplate('registration');

                        wp_mail(
                            $userdata['user_email'],
                            $template['header'],
                            $template['message'],
                            ['Content-type: text/html; charset=utf-8']
                        );

                    }

                }

            }

        });

        return $this;

    }

    protected function authorization() : self
    {

        add_action('plugins_loaded', function() {

            if (wp_verify_nonce(
                $_POST['regimeForm-authorization-wpnp'],
                'regimeForm-authorization'
            ) === false) $this->notice(
                'danger',
                $this->nonce_fail_notice
            );
            else {

                $form = $this->formGet();

                if (empty($form)) return;

                if ($_GET['regime'] === 'restorage') {

                    //

                } else {

                    $userdata = [];

                    foreach ($form['fields'] as $field_id => $props) {

                        $type = explode('_', $field_id);
                        $type = $type[0];

                        if ($type !== 'reset') {

                            if ($type ===
                                'checkbox') $userdata[$props['key']] = isset(
                                    $_POST['regimeFormField_'.$field_id]
                                ) ? 'true' : 'false';
                            else $userdata[$props['key']] = (string)$_POST['regimeFormField_'.$field_id];
    
                        }

                    }

                    $keys = array_keys($userdata);

                    $credentials = [];

                    if (array_search('user_login', $keys) !==
                        false) $credentials['user_login'] = $userdata['user_login'];

                    if (array_search('user_pass', $keys) !==
                        false) $credentials['user_password'] = $userdata['user_pass'];
                    elseif (array_search('user_password', $keys) !==
                        false) $credentials['user_password'] = $userdata['user_password'];

                    if (array_search('remember', $keys) !==
                        false) $credentials['remember'] = true;

                    $sign = wp_signon($credentials);

                    if ($sign instanceof WP_Error) $this->notice(
                        'danger',
                        $sign->get_error_message()
                    );
                    elseif (!empty($form['action'])) header(
                        'Location: '.site_url($form['action'])
                    );

                }

            }

        });

        return $this;

    }

    /**
     * Get form data.
     * @since 0.6.9
     * 
     * @param bool $fail_notice
     * Determines notice outputs.
     * 
     * @return array
     */
    protected function formGet(bool $fail_notice = true) : array
    {

        if (!isset($_POST['regimeFormField_formId'])) {

            if ($fail_notice) $this->notice(
                'danger',
                esc_html__(
                    'Ошибка при отправке формы: целостность данных нарушена.',
                    'regime'
                )
            );

            return [];

        }

        $form_id = (int)$_POST['regimeFormField_formId'];

        $forms_table = new FormsTable(
            $this->wpdb,
            $this->tables_props['forms']
        );

        $form = $forms_table->getForm($form_id);

        if (empty($form) &&
            $fail_notice) $this->notice(
                'danger',
                esc_html__(
                    'Ошибка обработки формы: форма не найдена.',
                    'regime'
                )
            );

        if (isset($form['fields'])) $form['fields'] = json_decode(
            $form['fields'],
            true
        );

        return $form;

    }

    /**
     * Return mail template data by template ID.
     * @since 0.6.5
     * 
     * @param string $template_id
     * Template ID. Cannot be empty.
     * 
     * @return array
     * Empty if template was not found.
     */
    protected function getMailTemplate(string $template_id) : array
    {

        $mails_table = new MailsTable(
            $this->wpdb,
            $this->tables_props['mails']
        );

        $template = $mails_table->mailGetByTemplate($template_id);

        if (!empty($template)) {

            foreach (['header', 'message'] as $entity) {

                $template[$entity] = str_replace(
                    ['!%site_url%!', '!%site_name%!'],
                    [site_url(), get_bloginfo('name')],
                    $template[$entity]
                );

            }

        }

        return $template;

    }

    /**
     * Output user notice.
     * @since 0.6.4
     * 
     * @param string $type
     * Available types: 'success', 'warning', 'error' (or 'danger').
     * 
     * @param string $text
     * Notice text.
     * 
     * @return $this
     */
    protected function notice(string $type, string $text) : self
    {

        if ($type === 'danger') $type = 'error';

        $this->notice_container = [
            'type' => $type,
            'text' => $text
        ];

        add_filter('the_content', function($content) {

            return $this->standartNotice(
                $this->notice_container['type'],
                $this->notice_container['text']
            ).$content;

        }, 100);

        return $this;

    }

}
