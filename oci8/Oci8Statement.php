<?php
/**
 * Created by PhpStorm.
 * User: isere_000
 * Date: 15.03.2015
 * Time: 16:02
 */

namespace iseredov\oci8pdo\oci8;


class Oci8Statement extends \yajra\Pdo\Oci8\Statement {

    /**
     * Closes the cursor, enabling the statement to be executed again.
     *
     * @return bool
     */
    public function closeCursor()
    {
        //Because we use OCI8 functions, we don't need this.
        return oci_free_statement($this->_sth);
    }
}