<?php

namespace nlib\Orm\Classes;

use PDO;
use nlib\Orm\Interfaces\ConnectionInterface;

class Connection implements ConnectionInterface {

    private static $_i = null;

    private $_connection = null;
    private $_name;
    private $_user;
    private $_pwd;
    private $_host;

    private function __construct() {}

    public static function i(string $instance = 'i') : Connection { 
        if(empty(self::$_i) || !(array_key_exists($instance, self::$_i) && !empty(self::$_i[$instance])))
            self::$_i[$instance] = new Connection;

        return self::$_i[$instance];
    }

    public function init(array $parameters) : void {

        foreach($parameters as $key => $value)
            if(property_exists($this, $property = '_' . $key)) $this->{$property} = $value;

        try {
            ($connection = new PDO(
                'mysql:host=' . $this->_host . ';dbname=' . $this->_name,
                $this->_user,
                $this->_pwd,
                [PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8']
            ))->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            // \PDO::ERRMODE_WARNING

            $this->setConnection($connection);

        } catch (\Exception $e){
            die( 'PDO connexion error n° ' . $e->getCode() . ': ' . $e->getMessage() );
        }
    }

    #region Setter

    public function setConnection(?PDO $connection) : self { $this->_connection = $connection; return $this; }

    #endregion

    #region Getter

    public function getConnection() : ?PDO { return $this->_connection; }

    #endregion
}