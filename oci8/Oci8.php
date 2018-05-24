<?php
/**
 * Created by PhpStorm.
 * User: isere_000
 * Date: 15.03.2015
 * Time: 12:59
 */

namespace lordkino\oci8pdo\oci8;

use yajra\Pdo\Oci8\Exceptions\Oci8Exception;
use lordkino\oci8pdo\oci8\Oci8Statement as Statement;

class Oci8 extends \yajra\Pdo\Oci8 {


    /**
     * Creates a PDO instance representing a connection to a database
     *
     * @param $dsn
     * @param $username [optional]
     * @param $password [optional]
     * @param array $options [optional]
     * @throws Oci8Exception
     */
    public function __construct($dsn, $username, $password, array $options = array())
    {
        if (strpos($dsn, ':') !== false) {
            $dsn = substr($dsn, strpos($dsn, ':') + 1);
            $dsn = explode(';',$dsn,1);
            $dsn = substr($dsn[0], strpos($dsn[0], '=') + 1);
        }
        parent::__construct($dsn, $username, $password, $options);
    }


    /**
     * Prepares a statement for execution and returns a statement object
     *
     * @param string $statement This must be a valid SQL statement for the
     *   target database server.
     * @param array $options [optional] This array holds one or more key=>value
     *   pairs to set attribute values for the PDOStatement object that this
     *   method returns.
     * @throws Oci8Exception
     * @return Statement
     */
    public function prepare($statement, $options = null)
    {
        // Get instance options
        if ($options == null)
        {
            $options = $this->_options;
        }

        // Skip replacing ? with a pseudo named parameter on alter/create table command
        if ( ! preg_match('/^alter+ +table/', strtolower(trim($statement)))
            and ! preg_match('/^create+ +table/', strtolower(trim($statement)))
        )
        {
            // Replace ? with a pseudo named parameter
            $newStatement = null;
            $parameter = 0;
            while ($newStatement !== $statement)
            {
                if ($newStatement !== null)
                {
                    $statement = $newStatement;
                }
                $newStatement = preg_replace('/\?/', ':autoparam' . $parameter, $statement, 1);
                $parameter++;
            }
            $statement = $newStatement;
        }

        // check if statement is insert function
        if (strpos(strtolower($statement), 'insert into') !== false)
        {
            preg_match('/insert into\s+([^\s\(]*)?/', strtolower($statement), $matches);
            // store insert into table name
            $this->_table = $matches[1];
        }

        // Prepare the statement
        $sth = @oci_parse($this->_dbh, $statement);

        if ( ! $sth)
        {
            $e = oci_error($this->_dbh);
            throw new Oci8Exception($e['message']);
        }

        if ( ! is_array($options))
        {
            $options = array();
        }

        return new Statement($sth, $this, $options);
    }


    /**
     * @param null $name
     * @return int|mixed
     */
    public function lastInsertId($name = null)
    {
        $sequence = $this->_table . "_" . $name . "_seq";

        if ( ! $this->checkSequence($sequence))
        {
            return 0;
        }

        $stmt = $this->query("select {$sequence}.currval from dual", \PDO::FETCH_COLUMN);
        $id = $stmt->fetch();

        return $id;
    }

}
