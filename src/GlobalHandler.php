<?php
/**
 * @package Regime
 */
namespace Regime;

use Regime\Interfaces\ITableProps;
use Regime\Exceptions\GlobalHandlerException;
use Regime\Exceptions\ErrorsList;

abstract class GlobalHandler extends PointOfEntry
{

    /**
     * @var ITableProps[] $tables_props
     * List of all tables props from Main POE.
     * @since 0.6.3
     */
    protected $tables_props;

    /**
     * @since 0.6.3
     * 
     * @param ITableProps[] $tables_props
     * List of all tables props from Main POE.
     */
    public function __construct(string $path, string $url, array $tables_props)
    {

        foreach ($tables_props as $table_props) {

            if (!($table_props instanceof ITableProps)) throw new GlobalHandlerException(
                ErrorsList::GLOBAL_HANDLER['-50']['message'],
                ErrorsList::GLOBAL_HANDLER['-50']['code']
            );

        }

        $this->tables_props = $tables_props;
        
        parent::__construct($path, $url);

    }

}
