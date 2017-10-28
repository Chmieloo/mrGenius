<?php

namespace AppBundle\Doctrine;

use Doctrine\DBAL\DBALException;
use Doctrine\DBAL\Driver\PDOMySql\Driver;
use PDOException;

class PDOMysqlDriver extends Driver
{
    /**
     * {@inheritdoc}
     */
    public function connect(array $params, $username = null, $password = null, array $driverOptions = array())
    {
        try {
            /*
             * To be able to use persistent connections we have to use the \PDO class directly, instead of
             * using the doctrine PDO wrapper object, since it can't be combined with ATTR_STATEMENT_CLASS.
             * The trade off is less specific exceptions from PDOStatement.
             */
            $conn = new \PDO(
                $this->constructPdoDsn($params),
                $username,
                $password,
                $driverOptions
            );
            $conn->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            throw DBALException::driverException($this, $e);
        }

        return $conn;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'pdo_mysql_persistent';
    }
}
