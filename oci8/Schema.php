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
     * @inheritdoc
     */
    public function getLastInsertID($sequenceName = '')
    {
        $sequenceName = $this->quoteSimpleTableName($sequenceName);

        if ($this->db->isActive) {
            // get the last insert id from the master connection
            return $this->db->useMaster(function (Connection $db) use ($sequenceName) {
                return $db->createCommand("SELECT {$sequenceName}.CURRVAL FROM DUAL")->queryScalar();
            });
        } else {
            throw new InvalidCallException('DB Connection is not active.');
        }
    }

}