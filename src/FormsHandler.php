<?php
/**
 * @package Regime
 */
namespace Regime;

use Regime\Traits\Noticer;
use Regime\Models\Tables\FormsTable;
use Regime\Models\Tables\MailsTable;
use WP_Error;
use WP_User;

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
     * @var array $action_container
     * Contains action parameters: uri,
     * timeout in ms and content hiding flag.
     * @since 0.7.2
     */
    protected $action_container;

    /**
     * @since 0.6.4
     */
    protected function init() : self
    {

        $this->nonce_fail_notice = esc_html__(
            'Произошла ошибка при отправке формы. Пожалуйста, попробуйте позже.',
            'regime'
        );

        if (isset(
            $_POST['regimeForm-registration-wpnp']
        )) $this->registration();
        elseif (isset(
            $_POST['regimeForm-authorization-wpnp']
        )) $this->authorization();
        elseif (isset(
            $_POST['regimeForm-restore-wpnp']
        )) $this->restore();
        elseif (isset(
            $_POST['regimeForm-newpass-wpnp']
        )) $this->newpass();
        elseif (isset(
            $_POST['regimeForm-profile-wpnp']
        )) $this->profile();

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
                        else {

                            $value = (string)$_POST['regimeFormField_'.$field_id];

                            if ($type === 'datalist' &&
                                $props['strict'] &&
                                array_search($value, $props['options']) ===
                                    false) {

                                $this->notice(
                                    'danger',
                                    esc_html__('Поле', 'regime').
                                        ' "'.$props['label'].'" '.
                                        esc_html__('заполнено некорректно.')
                                );

                                return;

                            }
                            
                            $userdata[$props['key']] = $value;
                        
                        }

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

                        if (!empty($form['action'])) $this->setAction($form['action']);

                    }

                }

            }

        });

        return $this;

    }

    /**
     * Authorization handler.
     * @since 0.7.0
     * 
     * @return $this
     */
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
                elseif (array_search('user_email', $keys) !==
                    false) {
                        
                        $credentials['user_email'] = $userdata['user_email'];

                        $credentials['user_login'] = $this->wpdb->get_var(
                            $this->wpdb->prepare(
                                "SELECT t.user_login
                                FROM `".$this->wpdb->prefix."users` AS t
                                WHERE t.user_email = %s",
                            $credentials['user_email']
                            )
                        );
                    
                    }

                if (array_search('user_pass', $keys) !==
                    false) $credentials['user_password'] = $userdata['user_pass'];
                elseif (array_search('user_password', $keys) !==
                    false) $credentials['user_password'] = $userdata['user_password'];

                if (array_search('remember', $keys) !==
                    false) $credentials['remember'] = true;

                $sign = wp_signon($credentials);

                if ($sign instanceof WP_Error) $this->notice(
                    'danger',
                    esc_html__('Данные для входа введены неверно.', 'regime')
                );
                else {
                    
                    wp_set_current_user($sign->ID);

                    if (!empty($form['action'])) $this->setAction($form['action']);
                
                }

            }

        });

        return $this;

    }

    /**
     * Reset password form handler.
     * @since 0.7.1
     * 
     * @return $this
     */
    protected function restore() : self
    {

        add_action('plugins_loaded', function() {

            if (wp_verify_nonce(
                $_POST['regimeForm-restore-wpnp'],
                'regimeForm-restore'
            ) === false) $this->notice(
                'danger',
                $this->nonce_fail_notice
            );
            else {

                $email = (string)$_POST['regimeFormField_user_email'];

                $user_id = (int)$this->wpdb->get_var(
                    $this->wpdb->prepare(
                        "SELECT t.ID
                            FROM `".$this->wpdb->prefix."users` AS t
                            WHERE t.user_email = %s",
                        $email
                    )
                );

                if (empty($user_id)) $this->notice(
                    'danger',
                    esc_html__(
                        'Пользователь с данным e-mail не найден.',
                        'regime'
                    )
                );
                else {

                    $mail = $this->getMailTemplate('password');

                    $uri = explode('?', $_SERVER['REQUEST_URI']);
                    $uri = $uri[0];

                    $user = new WP_User($user_id);

                    $uri .= '?regime=newpass&user='.urlencode($user->get('user_login')).
                        '&token='.urlencode(
                            get_password_reset_key($user)
                    );

                    $mail['message'] = str_replace(
                        '!%password_restorage_link%!',
                        site_url($uri),
                        $mail['message']
                    );

                    wp_mail(
                        $email,
                        $mail['header'],
                        $mail['message'],
                        ['Content-type: text/html; charset=utf-8']
                    );

                    $this->notice(
                        'success',
                        esc_html__(
                            'Инструкция по восстановлению пароля отправлена.',
                            'regime'
                        )
                    );

                }

            }

        });

        return $this;

    }

    /**
     * New password form handler.
     * @since 0.7.2
     * 
     * @return $this
     */
    protected function newpass() : self
    {

        add_action('plugins_loaded', function() {

            if (wp_verify_nonce(
                $_POST['regimeForm-newpass-wpnp'],
                'regimeForm-newpass'
            ) === false) $this->notice(
                'danger',
                $this->nonce_fail_notice
            );
            else {

                $login = (string)$_POST['regimeFormField_user'];
                $token = (string)$_POST['regimeFormField_user_token'];

                $user = check_password_reset_key($token, $login);

                if ($user instanceof WP_Error) $this->notice(
                    'danger',
                    esc_html__('Токен невалиден.', 'regime')
                );
                else {

                    $password = (string)$_POST['regimeFormField_user_password'];

                    $password = str_replace(
                        [" ", "\n", "\v", "\t", "\r", "\0"],
                        '',
                        $password
                    );

                    if (empty($password)) {

                        $this->notice(
                            'danger',
                            esc_html__('Пароль не может быть пустым.', 'regime')
                        );

                        return;

                    }

                    if ($this->wpdb->update(
                        $this->wpdb->prefix.'users',
                        [
                            'user_pass' => wp_hash_password($password),
                            'user_activation_key' => ''
                        ],
                        ['ID' => $user->ID],
                        ['%s', '%s'],
                        ['%d']
                    ) === false) $this->notice(
                        'danger',
                        esc_html__('Не удалось обновить пароль. Попробуйте ещё раз.', 'regime')
                    );
                    else {
                        
                        $this->notice(
                            'success',
                            esc_html__('Пароль успешно обновлён! Сейчас вы будете перенаправлены на', 'regime').' '.
                            '<a href="'.site_url($_POST['regimeFormField_action']).'">'.
                            esc_html__('страницу авторизации.', 'regime').'</a>'
                        );

                        $this->setAction((string)$_POST['regimeFormField_action'], 2000, false);
                
                    }

                }

            }

        });

        return $this;

    }

    /**
     * Profile form handler.
     * @since 0.7.6
     * 
     * @return $this
     */
    protected function profile() : self
    {

        add_action('plugins_loaded', function() {

            if (wp_verify_nonce(
                $_POST['regimeForm-profile-wpnp'],
                'regimeForm-profile'
            ) === false) $this->notice(
                'danger',
                $this->nonce_fail_notice
            );
            else {

                $user_id = get_current_user_id();

                if ($user_id === 0) return;

                $form = $this->formGet();

                if (empty($form)) return;

                $userdata = [];
                $meta = [];

                foreach ($form['fields'] as $field_id => $props) {

                    if ($props['key'] === 'user_login' ||
                        $props['key'] === 'user_activation_key' ||
                        $props['key'] === 'user_registered' ||
                        $props['key'] === 'user_status') continue;

                    if ($props['key'] === 'user_pass' ||
                        $props['key'] === 'user_nicename' ||
                        $props['key'] === 'user_email' ||
                        $props['key'] === 'user_url' ||
                        $props['key'] ===
                            'display_name') {

                        $value = (string)$_POST['regimeFormField_'.$field_id];
                                
                        if (!empty($value)) $userdata[$props['key']] = $value;
                    
                    } else {

                        $type = explode('_', $field_id);
                        $type = $type[0];

                        if ($type !== 'reset') {

                            if ($type ===
                                'checkbox') $meta[$props['key']] = isset(
                                    $_POST['regimeFormField_'.$field_id]
                                ) ? 'true' : 'false';
                            else {

                                $value = (string)$_POST['regimeFormField_'.$field_id];

                                if ($type === 'datalist' &&
                                    $props['strict'] &&
                                    array_search($value, $props['options']) ===
                                        false) {

                                    $this->notice(
                                        'danger',
                                        esc_html__('Поле', 'regime').
                                            ' "'.$props['label'].'" '.
                                            esc_html__('заполнено некорректно.')
                                    );

                                    return;

                                }
                                
                                $meta[$props['key']] = $value;
                            
                            }

                        }

                    }

                }

                if (!empty($userdata)) {

                    if ($this->wpdb->update(
                        $this->wpdb->prefix.'users',
                        $userdata,
                        ['ID' => $user_id]
                    ) === false) {

                        $this->notice(
                            'danger',
                            esc_html__('Не удалось обновить профиль. Попробуйте ещё раз позже.', 'regime')
                        );

                        return;

                    }

                }

                if (!empty($meta)) {

                    foreach ($meta as $key => $value) {

                        update_user_meta(
                            $user_id,
                            $key,
                            $value
                        );

                    }

                }

                $this->notice(
                    'success',
                    esc_html__('Изменения сохранены!', 'regime')
                );

                if (!empty($form['action'])) $this->setAction($form['action']);

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
     * @param bool $restore_password
     * Determines whether need to generate token to
     * password restorage or not.
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
                    [
                        '!%site_url%!',
                        '!%site_name%!',
                    ],
                    [
                        site_url(),
                        get_bloginfo('name')
                    ],
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

    /**
     * Set redirect action.
     * @since 0.7.2
     * 
     * @param string $uri
     * Target URI.
     * 
     * @return $this
     */
    protected function setAction(string $uri, int $timeout = 0, bool $content_hide = true) : self
    {

        if ($timeout < 0) $timeout = 0;

        $this->action_timeout = $timeout;

        $this->action_uri = $uri;

        $this->action_container = [
            'uri' => $uri,
            'timeout' => $timeout,
            'hide' => $content_hide
        ];

        add_filter('the_content', function($content) {

            ob_start();

?>
<script>
setTimeout(
    function() 
    {
        window.location.replace('<?= site_url($this->action_container['uri']) ?>')
    },
    <?= $this->action_container['timeout'] ?>
);
</script>
<?php

            $output = ob_get_clean();

            if (!$this->action_container['hide']) $output .= $content;

            return $output;

        }, 100);

        return $this;

    }

}
