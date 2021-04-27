<?php
/**
 * @package Regime
 */
namespace Regime\Models\Tables;

use Regime\Exceptions\FormsTableException;
use Regime\Exceptions\TableException;
use Regime\Exceptions\ErrorsList;

/**
 * Forms table class.
 * @since 0.3.7
 */
class FormsTable extends Table
{

    /**
     * Save new form in the table.
     * @since 0.3.7
     * 
     * @param string $type
     * Form type.
     * 
     * @param string $fields
     * Form fields in JSON format.
     * 
     * @param string $action
     * Form action URI.
     * 
     * @return $this
     * 
     * @throws Regime\Exceptions\FormsTableException
     */
    public function addForm(string $type, string $fields, string $action) : self
    {

        $invalid_param = '';

        if (empty($type)) $invalid_param = 'Type';

        if (empty($fields)) $invalid_param = 'Fields';

        if (!empty($invalid_param)) throw new FormsTableException(
            sprintf(ErrorsList::COMMON['-1']['message'], $invalid_param),
            ErrorsList::COMMON['-1']['code']
        );

        $id = $this->getNewFormId();

        try {

            $this
                ->entryAdd([
                    'form_id' => $id,
                    'key' => 'type',
                    'value' => $type
                ])
                ->entryAdd([
                    'form_id' => $id,
                    'key' => 'fields',
                    'value' => $fields
                ])
                ->entryAdd([
                    'form_id' => $id,
                    'key' => 'action',
                    'value' => $action
                ])
                ->entryAdd([
                    'form_id' => $id,
                    'key' => 'status',
                    'value' => 'active'
                ]);

        } catch (TableException $e) {

            throw new FormsTableException(
                $e->getMessage(),
                $e->getCode()
            );

        }

        return $this;

    }

    /**
     * Update form fields.
     * @since 0.4.0
     * 
     * @param int $form_id
     * Form ID.
     * 
     * @param string $fields
     * Form fields in JSON format.
     * 
     * @param string $action
     * Form action URI.
     * 
     * @return $this
     * 
     * @throws Regime\Exceptions\FormsTableException
     */
    public function updateForm(int $form_id, string $fields, string $action) : self
    {

        if ($form_id < 1) throw new FormsTableException(
            ErrorsList::COMMON['-2']['message'],
            ErrorsList::COMMON['-2']['code']
        );

        if (empty($fields)) throw new FormsTableException(
            sprintf(ErrorsList::COMMON['-1']['message'], 'Fields'),
            ErrorsList::COMMON['-1']['code']
        );

        try {

            $this
                ->entryUpdate(
                    ['value' => $fields],
                    [
                        'form_id' => $form_id,
                        'key' => 'fields'
                    ]
                )
                ->entryUpdate(
                    ['value' => $action],
                    [
                        'form_id' => $form_id,
                        'key' => 'action'
                    ]
                );

        } catch (TableException $e) {

            throw new FormsTableException(
                $e->getMessage(),
                $e->getCode()
            );

        }

        return $this;

    }

    /**
     * Delete the form.
     * @since 0.5.2
     * 
     * @param int $form_id
     * Form ID. Cannot be less than 1.
     * 
     * @param bool $allow_exception
     * Determines is exception throwing allowed where deleting fails.
     * 
     * @return $this
     * 
     * @throws Regime\Exceptions\FormsTableException
     */
    public function deleteForm(int $form_id, bool $allow_exception = true) : self
    {

        if ($form_id < 1) throw new FormsTableException(
            ErrorsList::COMMON['-2']['message'],
            ErrorsList::COMMON['-2']['code']
        );

        $delete = $this->wpdb->delete(
            $this->wpdb->prefix.$this->table_props->getTableName(),
            ['form_id' => $form_id],
            ['%d']
        );

        if (($delete === false ||
            $delete === 0) &&
            $allow_exception) throw new FormsTableException(
                ErrorsList::FORMS_TABLE['-40']['message'],
                ErrorsList::FORMS_TABLE['-40']['code']
            );

        return $this;

    }

    /**
     * Get the form by ID.
     * @since 0.4.2
     * 
     * @param int $form_id
     * Form ID. Cannot be less than 1.
     * 
     * @return array
     * 
     * @throws Regime\Exceptions\FormsTableException
     */
    public function getForm(int $form_id) : array
    {

        if ($form_id < 1) throw new FormsTableException(
            ErrorsList::COMMON['-2']['message'],
            ErrorsList::COMMON['-2']['code']
        );

        $result = [];

        $select = $this->wpdb->get_results(
            $this->wpdb->prepare(
                "SELECT t.key, t.value
                    FROM `".$this->wpdb->prefix.
                        $this->table_props->getTableName()."` AS t
                    WHERE t.form_id = %d",
                    $form_id
            ),
            ARRAY_A
        );

        foreach ($select as $row) {

            $result[$row['key']] = $row['value'];

        }

        return $result;

    }

    /**
     * Get form fields.
     * @since 0.4.8
     * 
     * @param int $form_id
     * Form ID. Cannot be less than 1.
     * 
     * @return string
     * Fields in JSON format.
     * 
     * @throws Regime\Exceptions\FormsTableException
     */
    public function getFormFields(int $form_id) : string
    {

        if ($form_id < 1) throw new FormsTableException(
            ErrorsList::COMMON['-2']['message'],
            ErrorsList::COMMON['-2']['code']
        );

        $result = '{}';

        $select = $this->wpdb->get_results(
            $this->wpdb->prepare(
                "SELECT t.value
                    FROM `".$this->wpdb->prefix.
                        $this->table_props->getTableName()."` AS t
                    WHERE t.form_id = %d
                    AND t.key = 'fields'",
                $form_id
                ),
            ARRAY_A
        );

        if (!empty($select)) $result = $select[0]['value'];

        return $result;

    }

    /**
     * Get form action URI.
     * @since 0.5.9
     * 
     * @param int $form_id
     * 
     * @return string
     * 
     * @throws Regime\Exceptions\FormsTableException
     */
    public function getFormAction(int $form_id) : string
    {

        if ($form_id < 1) throw new FormsTableException(
            ErrorsList::COMMON['-2']['message'],
            ErrorsList::COMMON['-2']['code']
        );

        $result = '';

        $select = $this->wpdb->get_results(
            $this->wpdb->prepare(
                "SELECT t.value
                    FROM `".$this->wpdb->prefix.
                        $this->table_props->getTableName()."` AS t
                    WHERE t.form_id = %d
                    AND t.key = 'action'",
                $form_id
                ),
            ARRAY_A
        );

        if (!empty($select)) $result = $select[0]['value'];

        return $result;

    }

    /**
     * Get all forms.
     * @since 0.4.3
     * 
     * @return array
     */
    public function getAllForms() : array
    {

        $result = [];

        try {

            $select = $this->selectAll();

        } catch (TableException $e) {

            throw new FormsTableException(
                $e->getMessage(),
                $e->getCode()
            );

        }

        foreach ($select as $row) {

            $result[$row['form_id']][$row['key']] = $row['value'];

        }

        return $result;

    }

    /**
     * Update form status.
     * @since 0.4.0
     * 
     * @param int $form_id
     * Form ID.
     * 
     * @param string $status
     * Form status.
     * 
     * @return $this
     * 
     * @throws Regime\Exceptions\FormsTableException
     */
    public function updateStatus(int $form_id, string $status) : self
    {

        if ($form_id < 1) throw new FormsTableException(
            ErrorsList::COMMON['-2']['message'],
            ErrorsList::COMMON['-2']['code']
        );

        if (empty($status)) throw new FormsTableException(
            sprintf(ErrorsList::COMMON['-1']['message'], 'Status'),
            ErrorsList::COMMON['-1']['code']
        );

        try {

            $this->entryUpdate(
                ['value' => $status],
                [
                    'form_id' => $form_id,
                    'key' => 'status'
                ]
            );

        } catch (TableException $e) {

            throw new FormsTableException(
                $e->getMessage(),
                $e->getCode()
            );

        }

        return $this;

    }

    /**
     * Return ID for the new form.
     * @since 0.3.7
     * 
     * @return int
     */
    protected function getNewFormId() : int
    {

        $id = 0;

        $select = $this->wpdb->get_results(
            "SELECT t.form_id
                FROM `".$this->wpdb->prefix.
                    $this->table_props->getTableName()."` AS t
                WHERE t.key = 'type'
                ORDER BY t.form_id DESC
                LIMIT 1",
            ARRAY_A
        );

        if (!empty($select)) $id = (int)$select[0]['form_id'];

        $id += 1;

        return $id;

    }

}
