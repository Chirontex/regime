<?php
/**
 * @package Regime
 */
namespace Regime\Interfaces;

/**
 * TableProps class interface.
 * @since 0.3.4
 */
interface ITableProps
{

    const FIELD = 'field';

    const INDEX = 'index';

    /**
     * TableProps constructor.
     * @since 0.3.6
     * 
     * @param string $table_name
     * Table name.
     */
    public function __construct(string $table_name);

    /**
     * Set table field.
     * @since 0.3.4
     * 
     * @param string $key
     * Column name.
     * 
     * @param string $params
     * Column (field) parameters.
     * 
     * @return $this
     */
    public function setField(string $key, string $params) : self;

    /**
     * Set table index.
     * @since 0.3.4
     * 
     * @param string $key
     * Index key.
     * 
     * @param string $params
     * Index parametes.
     * 
     * @return $this
     */
    public function setIndex(string $key, string $params) : self;

    /**
     * Return all fields.
     * @since 0.3.4
     * 
     * @return array
     */
    public function getFields() : array;

    /**
     * Return all indexes.
     * @since 0.3.4
     * 
     * @return array
     */
    public function getIndexes() : array;

    /**
     * Return table name.
     * @since 0.3.6
     * 
     * @return string
     */
    public function getTableName() : string;

}
