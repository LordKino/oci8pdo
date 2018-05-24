<?php
/**
 * Created by PhpStorm.
 * User: isere_000
 * Date: 15.03.2015
 * Time: 16:02
 */

namespace lordkino\oci8pdo\oci8;

use ReflectionClass;


class Oci8Statement extends \Yajra\Pdo\Oci8\Statement {

    /**
     * Closes the cursor, enabling the statement to be executed again.
     *
     * @return bool
     */
    private $_sth;
    
    private $args;
    
    public function __construct(...$args) {
        parent::__construct(...$args);
        $this->args = $args;
    }
    
    public function closeCursor()
    {
        $prop = (new ReflectionClass('\Yajra\Pdo\Oci8\Statement'))->getProperty('sth');
        $prop->setAccessible(true);
        
        $this->_sth = $prop->getValue(new \Yajra\Pdo\Oci8\Statement(...$this->args));
        
        //Because we use OCI8 functions, we don't need this.
        return oci_free_statement($this->_sth);
    }
}
