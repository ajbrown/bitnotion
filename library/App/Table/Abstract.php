<?php
require_once 'Doctrine/Table.php';

abstract class App_Table_Abstract extends Doctrine_Table
{
    /**
     *
     *@throws Doctrine_Connection_Exception    if there are no opened connections
     *@param Doctrine_Connection $conn         the connection associated with this table
     */
    public function __construct( Doctrine_Connection $conn = null )
    {
        if( $conn === null ) {
            $conn = Doctrine_Manager::connection();
        }

        $name = str_replace( 'App_Table_', '', get_class( $this ) );
        parent::__construct($name, $conn, true);
    }
}