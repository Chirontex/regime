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
                'header' => 'Вы успешно зарегистрированы!',
                'message' => ''
            ],
            'password' => [
                'header' => 'Запрос на восстановление пароля',
                'message' => ''
            ]
        ];

        ob_start();

?>
<h3>Поздравляем! Вы зарегистрированы на нашем сайте!</h3>
<p>Для входа используйте логин и пароль, которые вы указали при регистрации.</p>
<p>С уважением, администрация сайта !%site_name%!.</p>
<?php

        $defaults['registration']['message'] = ob_get_clean();

        ob_start();

?>
<p>Получен запрос на восстановление пароля.</p>
<p>Пожалуйста, перейдите по этой ссылке для восстановления: !%password_restorage_link%!</p>
<p>С уважением, администрация сайта !%site_name%!.</p>
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
                'template_id' => 'registration',
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

        foreach ([
            'Template ID' => $template_id,
            'Header' => $header,
            'Message' => $message] as $key => $value) {

            if (empty($value)) throw new MailsTableException(
                sprintf(ErrorsList::COMMON['-1']['message'], $key),
                ErrorsList::COMMON['-1']['code']
            );

        }

        $this->entryUpdate(
            [
                'header' => $header,
                'message' => $message
            ],
            ['template_id' => $template_id]
        );

        return $this;

    }

}
