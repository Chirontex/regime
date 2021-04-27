<?php
/**
 * @package Regime
 */
namespace Regime;

use Regime\Traits\Noticer;
use Regime\Models\Tables\FormsTable;
use Regime\Models\Tables\MailsTable;

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

                $user_data = [];

                foreach ($fields as $field_id => $props) {

                    // do later

                }

                // do later

            }

        });

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

        add_filter('the_content', function($content) {

            return $this->standartNotice(
                $this->notice_container['type'],
                $this->notice_container['text']
            ).$content;

        }, 100);

        return $this;

    }

}
