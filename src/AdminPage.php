<?php
/**
 * @package Regime
 */
namespace Regime;

/**
 * @abstract
 * Admin page abstract POE class.
 * @since 0.0.3
 */
abstract class AdminPage extends PointOfEntry
{

    /**
     * @var array $notice
     * Notice container. Contains notice type and text.
     * @since 0.0.3
     */
    protected $notice_container = [
        'type' => '',
        'text' => ''
    ];

    /**
     * Output notice on admin page.
     * @since 0.0.3
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

        add_action('admin_notices', function($prev_notices) {

            ob_start();

?>
<div class="notice notice-<?= esc_attr_e($this->notice_container['type'], 'regime-ru_RU') ?> is-dismissible" style="max-width: 500px; margin-left: auto; margin-right: auto;">
    <p style="text-align: center;"><?= $this->notice_container['text'] ?></p>
</div>
<?php

            echo $prev_notices.ob_get_clean();

        });

        return $this;

    }

}
