<?php
/**
 * @package Regime
 */
namespace Regime;

use Regime\Traits\Noticer;
use Regime\Models\Relocator;
use Regime\Models\Tables\FormsTable;
use Regime\Models\Tables\MailsTable;
use Regime\Models\Tables\SettingsTable;
use Regime\Exceptions\FormsHandlerException;
use Regime\Exceptions\ErrorsList;
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
                            'radio') $value =
                                (string)$_POST['regimeFormField_'.$type.'_'.$props['key']];
                        else $value = (string)$_POST['regimeFormField_'.$field_id];

                        if ($type === 'datalist' &&
                            $props['strict'] &&
                            array_search($value, $props['options']) ===
                                false) {

                            $this->notice(
                                'danger',
                                esc_html__('Поле', 'regime').
                                    ' "'.$props['label'].'" '.
                                    esc_html__('заполнено некорректно.', 'regime')
                            );

                            return;

                        } elseif ($type === 'date' ||
                            $type === 'number') {

                            $min = $props['min'];
                            $max = $props['max'];

                            if ($type === 'date') {

                                $value = strtotime($value);

                                if (!empty($min)) {
                                    
                                    $min = strtotime($min);

                                    if ($value < $min) {

                                        $this->notice(
                                            'danger',
                                            esc_html__('Значение поля', 'regime').
                                                ' "'.$props['label'].'" '.
                                                esc_html__('раньше допустимого диапазона дат.', 'regime')
                                        );

                                        return;

                                    }
                                
                                }

                                if (!empty($max)) {
                                    
                                    $max = strtotime($max);

                                    if ($value > $max) {

                                        $this->notice(
                                            'danger',
                                            esc_html__('Значение поля', 'regime').
                                                ' "'.$props['label'].'" '.
                                                esc_html__('позже допустимого диапазона дат.', 'regime')
                                        );

                                        return;

                                    }
                                
                                }

                                $value = date("Y-m-d", $value);

                            } elseif ($type === 'number') {

                                $value = (int)$value;

                                if (!empty($min)) {
                                    
                                    $min = (int)$min;

                                    if ($value < $min) {

                                        $this->notice(
                                            'danger',
                                            esc_html__('Значение поля', 'regime').
                                                ' "'.$props['label'].'" '.
                                                esc_html__('меньше допустимого минимального значения.', 'regime')
                                        );

                                        return;

                                    }
                                
                                }

                                if (!empty($max)) {
                                    
                                    $max = (int)$max;

                                    if ($value > $max) {

                                        $this->notice(
                                            'danger',
                                            esc_html__('Значение поля', 'regime').
                                                ' "'.$props['label'].'" '.
                                                esc_html__('больше допустимого максимального значения.', 'regime')
                                        );

                                        return;

                                    }
                                
                                }

                            }

                        }
                        
                        $userdata[$props['key']] = $value;

                    }

                }

                $userdata['show_admin_bar_front'] = 'false';

                if (!isset($userdata['user_login']) &&
                    isset($userdata['user_email'])) {

                   $userdata['user_login'] = explode('@', $userdata['user_email']);
                   $userdata['user_login'] = $userdata['user_login'][0].time();

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
                
                    if (isset($userdata['user_email'])) $this
                        ->sendMail($userdata['user_email'], 'registration');

                    $credentials = [
                        'user_login' => $userdata['user_login'],
                        'user_password' => $userdata['user_pass']
                    ];

                    wp_signon($credentials);
                    
                    if (!empty($form['action'])) new Relocator($form['action']);

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
                        elseif ($type ===
                            'radio') $userdata[$props['key']] =
                                (string)$_POST['regimeFormField_'.$type.'_'.$props['key']];
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

                    if (!empty($form['action'])) new Relocator($form['action']);
                
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

                    $this->sendMail($email, 'password', new WP_User($user_id));

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

                        $action = explode('?', $_SERVER['REQUEST_URI']);
                        $action = $action[0];

                        new Relocator($action);
                
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

                            if ($type === 'radio') $value =
                                (string)$_POST['regimeFormField_'.$type.'_'.$props['key']];
                            else $value = (string)$_POST['regimeFormField_'.$field_id];

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

                            } elseif ($type === 'date' ||
                                $type === 'number') {

                                $min = $props['min'];
                                $max = $props['max'];

                                if ($type === 'date') {

                                    $value = strtotime($value);

                                    if (!empty($min)) {
                                        
                                        $min = strtotime($min);

                                        if ($value < $min) {

                                            $this->notice(
                                                'danger',
                                                esc_html__('Значение поля', 'regime').
                                                    ' "'.$props['label'].'" '.
                                                    esc_html__('раньше допустимого диапазона дат.', 'regime')
                                            );

                                            return;

                                        }
                                    
                                    }

                                    if (!empty($max)) {
                                        
                                        $max = strtotime($max);

                                        if ($value > $max) {

                                            $this->notice(
                                                'danger',
                                                esc_html__('Значение поля', 'regime').
                                                    ' "'.$props['label'].'" '.
                                                    esc_html__('позже допустимого диапазона дат.', 'regime')
                                            );

                                            return;

                                        }
                                    
                                    }

                                    $value = date("Y-m-d", $value);

                                } elseif ($type === 'number') {

                                    $value = (int)$value;

                                    if (!empty($min)) {
                                        
                                        $min = (int)$min;

                                        if ($value < $min) {

                                            $this->notice(
                                                'danger',
                                                esc_html__('Значение поля', 'regime').
                                                    ' "'.$props['label'].'" '.
                                                    esc_html__('меньше допустимого минимального значения.', 'regime')
                                            );

                                            return;

                                        }
                                    
                                    }

                                    if (!empty($max)) {
                                        
                                        $max = (int)$max;

                                        if ($value > $max) {

                                            $this->notice(
                                                'danger',
                                                esc_html__('Значение поля', 'regime').
                                                    ' "'.$props['label'].'" '.
                                                    esc_html__('больше допустимого максимального значения.', 'regime')
                                            );

                                            return;

                                        }
                                    
                                    }

                                }

                            }
                            
                            $meta[$props['key']] = $value;

                        }

                    }

                }

                if (!empty($userdata)) {

                    if (isset(
                        $userdata['user_pass']
                    )) $userdata['user_pass'] = wp_hash_password($userdata['user_pass']);

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

                if (!empty($form['action']) &&
                    isset($userdata['user_pass'])) new Relocator($form['action']);
                else $this->notice(
                        'success',
                        esc_html__('Изменения сохранены!', 'regime')
                );

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
     * Send a mail by template.
     * @since 0.8.7
     * 
     * @param string $to
     * Recipient email.
     * 
     * @param string $template_name
     * Mail template name.
     * 
     * @param WP_User|null $user
     * Recipient WP_User instance.
     * Optional as usual. Must be defined if $template_name is 'password'.
     * 
     * @return $this
     * 
     * @throws Regime\Exceptions\FormsHandlerException
     */
    protected function sendMail(string $to, string $template_name, $user = null) : self
    {

        if (empty($to)) throw new FormsHandlerException(
            sprintf(ErrorsList::COMMON['-1']['message'], 'Recipient e-mail'),
            ErrorsList::COMMON['-1']['code']
        );

        if (filter_var($to, FILTER_VALIDATE_EMAIL) ===
            false) throw new FormsHandlerException(
                sprintf(
                    ErrorsList::COMMON['-4']['message'],
                    'Argument "to"',
                    'e-mail'
                ),
                ErrorsList::COMMON['-4']['code']
        );

        if (empty($template_name)) throw new FormsHandlerException(
            sprintf(ErrorsList::COMMON['-1']['message'], 'Template name'),
            ErrorsList::COMMON['-1']['code']
        );

        if (!($user instanceof WP_User) &&
            $user !== null) throw new FormsHandlerException(
                sprintf(
                    ErrorsList::COMMON['-4']['message'],
                    'Argument "user"',
                    'WP_User instance'
                ),
                ErrorsList::COMMON['-4']['code']
            );

        $template = $this->getMailTemplate($template_name);

        if (empty($template)) throw new FormsHandlerException(
            sprintf(ErrorsList::COMMON['-5']['message'], 'Template'),
            ErrorsList::COMMON['-5']['code']
        );

        if ($template_name === 'password' &&
            $user instanceof WP_User) {

            $uri = explode('?', $_SERVER['REQUEST_URI']);
            $uri = $uri[0];

            $uri .= '?regime=newpass&user='.urlencode($user->get('user_login')).
                '&token='.urlencode(
                    get_password_reset_key($user)
            );

            $template['message'] = str_replace(
                '!%password_restorage_link%!',
                site_url($uri),
                $template['message']
            );

        }

        $settings_table = new SettingsTable(
            $this->wpdb,
            $this->tables_props['settings']
        );

        $sender_email = $settings_table->get('sender_email');
        $sender_email = empty($sender_email) ?
            'wordpress@'.$_SERVER['HTTP_HOST'] : $sender_email;

        $sender_name = $settings_table->get('sender_name');
        $sender_name = empty($sender_name) ?
            'Admin' : $sender_name;

        wp_mail(
            $to,
            $template['header'],
            $template['message'],
            [
                'Content-type: text/html; charset=utf-8',
                'From: '.$sender_name.' <'.$sender_email.'>'
            ]
        );

        return $this;

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

        add_filter('regime-front-notice', function() {

            return $this->standartNotice(
                $this->notice_container['type'],
                $this->notice_container['text']
            );

        }, 100);

        return $this;

    }

}
