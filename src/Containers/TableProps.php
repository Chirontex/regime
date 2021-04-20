<?php
/**
 * @package Regime
 */
namespace Regime\Containers;

use Regime\Interfaces\ITableProps;
use Regime\Exceptions\TablePropsException;
use Regime\Exceptions\ErrorsList;

/**
 * Contains database table properties.
 * @since 0.3.4
 */
class TableProps implements ITableProps
{

    /**
     * @var array $fields
     * Table fields.
     * @since 0.3.4
     */
    protected $fields = [];

    /**
     * @var array $indexes
     * Table indexes.
     * @since 0.3.4
     */
    protected $indexes = [];

    /**
     * @var string $table_name
     * Table name.
     * @since 0.3.6
     */
    protected $table_name = '';

    const FIELD = 'field';

    const INDEX = 'index';

    /**
     * @since 0.3.6
     */
    public function __construct(string $table_name)
    {
        
        if (empty($table_name)) throw new TablePropsException(
            ErrorsList::TABLE_PROPS['-24']['message'],
            ErrorsList::TABLE_PROPS['-24']['code']
        );

        $this->table_name = $table_name;

    }

    /**
     * @since 0.3.4
     * 
     * @throws Regime\Exceptions\TablePropsException
     */
    public function setField(string $key, string $params) : self
    {

        $this->setEntity(self::FIELD, ['key' => $key, 'params' => $params]);

        return $this;

    }

    /**
     * @since 0.3.4
     * 
     * @throws Regime\Exceptions\TablePropsException
     */
    public function setIndex(string $key, string $params) : self
    {

        $this->setEntity(self::INDEX, ['key' => $key, 'params' => $params]);

        return $this;

    }

    /**
     * @since 0.3.4
     */
    public function getFields() : array
    {

        return $this->fields;

    }

    /**
     * @since 0.3.4
     */
    public function getIndexes() : array
    {

        return $this->indexes;

    }

    /**
     * @since 0.3.6
     */
    public function getTableName(): string
    {
        
        return $this->table_name;

    }

    /**
     * Save fields and indexes in the obj.
     * @since 0.3.4
     * 
     * @param string $entity_type
     * Might be only 'field' or 'index'.
     * 
     * @param array $entity
     * Must have 'key' and 'params'.
     * 
     * @return $this
     * 
     * @throws Regime\Exceptions\TablePropsException
     */
    protected function setEntity(string $entity_type, array $entity) : self
    {

        if ($entity_type !== 'field' &&
            $entity_type !== 'index') throw new TablePropsException(
                ErrorsList::TABLE_PROPS['-22']['message'],
                ErrorsList::TABLE_PROPS['-22']['code']
            );

        if (!isset($entity['key']) &&
            !isset($entity['params'])) throw new TablePropsException(
                ErrorsList::TABLE_PROPS['-23']['message'],
                ErrorsList::TABLE_PROPS['-23']['code']
            );

        switch ($entity_type) {

            case 'field':
                $this->fields[$entity['key']] = $entity['params'];
                break;

            case 'index':
                $this->indexes[$entity['key']] = $entity['params'];
                break;

        }

        return $this;

    }

}
