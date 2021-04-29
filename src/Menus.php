<?php
/**
 * @package Regime
 */
namespace Regime;

use Regime\Models\Tables\MenusTable;

/**
 * @final
 * Menus admin page class.
 * @since 0.7.9
 */
final class Menus extends AdminPage
{

    /**
     * @var Regime\Models\Tables\MenusTable $table
     * Table object.
     * @since 0.7.9
     */
    protected $table;

    /**
     * @since 0.7.9
     */
    protected function init() : self
    {

        if ($_GET['page'] ===
            $this->container->slugGet()) {

            $this->table = new MenusTable(
                $this->wpdb,
                $this->table_props
            );

            if (isset($_POST['regimeMenus-wpnp'])) $this->menusUpdate();

            $this->menusOutput();

        }

        return $this;

    }

    /**
     * Output menus.
     * @since 0.7.9
     * 
     * @return $this
     */
    protected function menusOutput() : self
    {

        add_filter('regime-menus-user-menu', function() {

            $menus = $this->wpdb->get_results(
                "SELECT t.term_id, t1.name
                    FROM `".$this->wpdb->prefix."term_taxonomy` AS t
                    LEFT JOIN `".$this->wpdb->prefix."terms` AS t1
                    ON t.term_id = t1.term_id
                    WHERE t.taxonomy = 'nav_menu'",
                ARRAY_A
            );

            $role_menu = $this->table->getRoleMenu('user');

            if ($role_menu !== 0) {

                foreach ($menus as $i => $menu) {

                    if ((int)$menu['term_id'] === $role_menu) {
                            
                        $menus[$i]['selected'] = true;

                        break;
                    
                    }

                }

            }

            return $menus;

        });

        return $this;

    }

    /**
     * Update menus setting.
     * @since 0.8.0
     * 
     * @return $this
     */
    protected function menusUpdate() : self
    {

        add_action('plugins_loaded', function() {

            if (wp_verify_nonce(
                $_POST['regimeMenus-wpnp'],
                'regimeMenus'
            ) === false) $this->notice(
                'danger',
                $this->nonce_fail_message
            );
            else {

                if (isset($_POST['regimeMenus_user_menu'])) {

                    $this->table->updateRoleMenu(
                        'user',
                        (int)$_POST['regimeMenus_user_menu']
                    );

                    $this->notice(
                        'success',
                        esc_html__('Настройки меню сохранены!', 'regime')
                    );

                } else $this->notice(
                    'danger',
                    esc_html__('Не удалось обновить настройки меню: недостаточно данных.', 'regime')
                );

            }

        });

        return $this;

    }

}
