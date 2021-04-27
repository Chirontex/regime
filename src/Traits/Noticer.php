<?php
/**
 * @package Regime
 */
namespace Regime\Traits;

/**
 * Add noticing methods.
 * @since 0.6.2
 */
trait Noticer
{

    /**
     * Output a standard notice alert.
     * @since 0.6.2
     * 
     * @param string $type
     * Available types: 'success', 'warning', 'error' (or 'danger').
     * 
     * @param string $text
     * Notice text.
     * 
     * @return $this
     */
    protected function standartNoticeEcho(string $type, string $text) : self
    {

        echo $this->standartNotice($type, $text);

        return $this;

    }

    /**
     * Return standart notice alert.
     * @since 0.6.4
     * 
     * @param string $type
     * Available types: 'success', 'warning', 'error' (or 'danger').
     * 
     * @param string $text
     * Notice text.
     * 
     * @return string
     */
    protected function standartNotice(string $type, string $text) : string
    {

        ob_start();

?>
<div class="notice notice-<?= htmlspecialchars($type) ?> is-dismissible" style="max-width: 500px; margin: 1rem auto;">
    <p style="text-align: center;"><?= $text ?></p>
</div>
<?php

        return ob_get_clean();

    }

}
