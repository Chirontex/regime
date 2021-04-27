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

        return $this;

    }

    protected function registration() : self
    {

        add_action('plugins_loadded', function() {

            if (wp_verify_nonce(
                $_POST['regimeForm-registration'],
                'regimeForm-registration-wpnp'
            ) === false) $this->notice(
                'danger',
                $this->nonce_fail_notice
            );
            else {

                if (!isset($_POST['regimeFormField_formId'])) {

                    $this->notice(
                        'danger',
                        esc_html__(
                            'Ошибка при отправке формы: целостность данных нарушена',
                            'regime'
                        )
                    );

                    return $this;

                }

                $form_id = (int)$_POST['regimeFormField_formId'];

                $forms_table = new FormsTable(
                    $this->wpdb,
                    $this->tables_props['forms']
                );

                $form = $forms_table->getForm($form_id);

                if (empty($form)) {

                    $this->notice(
                        'danger',
                        esc_html__(
                            'Ошибка обработки формы: форма не найдена.',
                            'regime'
                        )
                    );

                    return $this;

                }

                $fields = json_decode($form['fields'], true);

                $userdata = [];

                foreach ($fields as $field_id => $props) {

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

                if (!isset($userdata['user_login']) &&
                    isset($userdata['user_email'])) {

                   $userdata['user_login'] = explode('@', $userdata['user_email']);
                   $userdata['user_login'] =$userdata['user_login'][0];

                }

                $insert = wp_insert_user($userdata);

                if ($insert instanceof WP_Error) $this->notice(
                    'danger',
                    $insert->get_error_message()
                );
                elseif (isset($userdata['user_email'])) {

                    //

                }

            }

        });

        return $this;

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
