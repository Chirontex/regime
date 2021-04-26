<?php
/**
 * @package Regime
 */
namespace Regime\Models\Tables;

use Regime\Exceptions\MailsTableException;
use Regime\Exceptions\ErrorsList;

/**
 * Mails table class.
 * @since 0.5.4
 */
class MailsTable extends Table
{

    /**
     * @since 0.5.4
     */
    protected function init() : self
    {

        $defaults = [
            'registration' => [
                'header' => esc_html__('Вы успешно зарегистрированы!', 'regime'),
                'message' => ''
            ],
            'password' => [
                'header' => esc_html__('Запрос на восстановление пароля', 'regime'),
                'message' => ''
            ]
        ];

        ob_start();

?>
<h3><?= esc_html('Поздравляем! Вы зарегистрированы на нашем сайте!', 'regime') ?></h3>
<p><?= esc_html__('Для входа используйте логин и пароль, которые вы указали при регистрации.', 'regime') ?></p>
<p><?= esc_html__('С уважением, администрация сайта', 'regime') ?> !%site_name%!.</p>
<?php

        $defaults['registration']['message'] = ob_get_clean();

        ob_start();

?>
<p><?= esc_html__('Получен запрос на восстановление пароля.', 'regime') ?></p>
<p><?= esc_html__('Пожалуйста, перейдите по этой ссылке для восстановления:', 'regime') ?> !%password_restorage_link%!</p>
<p><?= esc_html__('С уважением, администрация сайта', 'regime') ?> !%site_name%!.</p>
<?php

        $defaults['password']['message'] = ob_get_clean();

        if (empty($this->selectAll())) $this
            ->entryAdd(
            [
                'template_id' => 'registration',
                'header' => $defaults['registration']['header'],
                'message' => $defaults['registration']['message']
            ]
        )
            ->entryAdd([
                'template_id' => 'password',
                'header' => $defaults['password']['header'],
                'message' => $defaults['password']['message']
            ]);

        return $this;

    }

    /**
     * Update mail template.
     * @since 0.5.4
     * 
     * @param string $template_id
     * Template ID. Cannot be empty.
     * 
     * @param string $header
     * Mail header. Cannot be empty.
     * 
     * @param string $message
     * Mail message. Cannot be empty.
     * 
     * @return $this
     * 
     * @throws Regime\Exceptions\MailsTableException
     */
    public function mailUpdate(string $template_id, string $header, string $message) : self
    {

        if (empty($template_id)) throw new MailsTableException(
            sprintf(ErrorsList::COMMON['-1']['message'], 'Template ID'),
            ErrorsList::COMMON['-1']['code']
        );

        $this->entryUpdate(
            [
                'header' => $header,
                'message' => $message
            ],
            ['template_id' => $template_id]
        );

        return $this;

    }

    /**
     * Get all mail templates.
     * @since 0.5.7
     * 
     * @return array
     */
    public function mailsGetAll() : array
    {

        $result = [];

        $select = $this->selectAll();

        if (!empty($select)) {

            foreach ($select as $row) {

                $result[$row['template_id']] = [
                    'header' => $row['header'],
                    'message' => $row['message']
                ];

            }

        }

        return $result;

    }

}
