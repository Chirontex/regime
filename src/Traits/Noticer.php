<?php
/**
 * @package Regime
 */
namespace Regime\Traits;

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

        ob_start();

?>
<div class="notice notice-<?= htmlspecialchars($type) ?> is-dismissible" style="max-width: 500px; margin: 1rem auto;">
    <p style="text-align: center;"><?= $text ?></p>
</div>
<?php

        echo ob_get_clean();

        return $this;

    }

}
