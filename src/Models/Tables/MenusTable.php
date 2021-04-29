<?php
/**
 * @package Regime
 */
namespace Regime\Models\Tables;

use Regime\Exceptions\MenusTableException;
use Regime\Exceptions\ErrorsList;

/**
 * Menus table class.
 * @since 0.7.8
 */
class MenusTable extends Table
{

    /**
     * @since 0.7.8
     */
    protected function init(): self
    {

        if (empty($this->selectAll())) $this->entryAdd([
            'role' => 'user',
            'menu_id' => 0
        ]);
        
        return $this;

    }

    /**
     * Update menu for users by role.
     * @since 0.7.8
     * 
     * @param string $role
     * Role name. Cannot be empty.
     * 
     * @param int $menu_id
     * Menu ID. If 0, it means that role users has no unique menu.
     * 
     * @return $this
     * 
     * @throws Regime\Exceptions\MenusTableException
     */
    public function updateRoleMenu(string $role, int $menu_id) : self
    {

        if (empty($role)) throw new MenusTableException(
            sprintf(ErrorsList::COMMON['-1']['message'], 'Role'),
            ErrorsList::COMMON['-1']['code']
        );

        if ($menu_id < 0) throw new MenusTableException(
            sprintf(ErrorsList::COMMON['-3']['message'], 'Menu ID', 0),
            ErrorsList::COMMON['-3']['code']
        );

        if (empty($this->wpdb->get_results(
            $this->wpdb->prepare(
                "SELECT *
                    FROM `".$this->wpdb->prefix.
                        $this->table_props->getTableName()."` AS t
                    WHERE t.role = %s",
                $role
            ),
            ARRAY_A
        ))) throw new MenusTableException(
                ErrorsList::TABLE['-36']['message'],
                ErrorsList::TABLE['-36']['code']
        );

        return $this->entryUpdate(
            ['menu_id' => $menu_id],
            ['role' => $role]
        );

    }

    /**
     * Get menu ID by role.
     * @since 0.7.8
     * 
     * @param string $role
     * Users role. Cannot be empty.
     * 
     * @return int
     * 
     * @throws throw Regime\Exceptions\MenusTableException
     */
    public function getRoleMenu(string $role) : int
    {

        if (empty($role)) throw new MenusTableException(
            sprintf(ErrorsList::COMMON['-1']['message'], 'Role'),
            ErrorsList::COMMON['-1']['code']
        );

        $select = $this->wpdb->get_results(
            $this->wpdb->prepare(
                "SELECT *
                    FROM `".$this->wpdb->prefix.
                        $this->table_props->getTableName()."` AS t
                    WHERE t.role = %s",
                $role
            ),
            ARRAY_A
        );

        if (empty($select)) throw new MenusTableException(
            ErrorsList::TABLE['-36']['message'],
            ErrorsList::TABLE['-36']['code']
        );

        return (int)$select[0]['menu_id'];

    }

}
