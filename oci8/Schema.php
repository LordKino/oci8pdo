<?php
/**
 * Created by PhpStorm.
 * User: isere_000
 * Date: 15.03.2015
 * Time: 16:26
 */

namespace iseredov\oci8pdo\oci8;

use yii\base\InvalidCallException;
use yii\db\Connection;

class Schema extends \yii\db\oci\Schema {

    /**
     * Finds constraints and fills them into TableSchema object passed
     * @param TableSchema $table
     */
    protected function findConstraints($table)
    {
        $sql = <<<SQL
SELECT D.CONSTRAINT_NAME, C.COLUMN_NAME, C.POSITION, D.R_CONSTRAINT_NAME,
        E.TABLE_NAME AS TABLE_REF, F.COLUMN_NAME AS COLUMN_REF,
        C.TABLE_NAME
FROM ALL_CONS_COLUMNS C
INNER JOIN ALL_CONSTRAINTS D ON D.OWNER = C.OWNER AND D.CONSTRAINT_NAME = C.CONSTRAINT_NAME
INNER JOIN ALL_CONSTRAINTS E ON E.OWNER = D.R_OWNER AND E.CONSTRAINT_NAME = D.R_CONSTRAINT_NAME
INNER JOIN ALL_CONS_COLUMNS F ON F.OWNER = E.OWNER AND F.CONSTRAINT_NAME = E.CONSTRAINT_NAME AND F.POSITION = C.POSITION
WHERE C.OWNER = :schemaName
   AND C.TABLE_NAME = :tableName
   AND D.CONSTRAINT_TYPE = 'R'
ORDER BY D.CONSTRAINT_NAME, C.POSITION
SQL;
        $command = $this->db->createCommand($sql, [
            ':tableName' => $table->name,
            ':schemaName' => $table->schemaName,
        ]);
        $constraints = [];
        foreach ($command->queryAll() as $row) {
            if ($this->db->slavePdo->getAttribute(\PDO::ATTR_CASE) === \PDO::CASE_LOWER) {
                $row = array_change_key_case($row, CASE_UPPER);
            }
            $name = $row['CONSTRAINT_NAME'];
            if (!isset($constraints[$name])) {
                $constraints[$name] = [
                    'tableName' => $row["TABLE_REF"],
                    'columns' => [],
                ];
            }
            $constraints[$name]['columns'][$row["COLUMN_NAME"]] = $row["COLUMN_REF"];
        }
        foreach ($constraints as $constraint) {
            $table->foreignKeys[] = array_merge([$constraint['tableName']], $constraint['columns']);
        }
    }
}
