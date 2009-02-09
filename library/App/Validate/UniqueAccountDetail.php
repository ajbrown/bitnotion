<?php

require_once 'Zend/Validate/Abstract.php';

class App_Validate_UniqueAccountDetail extends Zend_Validate_Abstract
{
    const NOT_UNIQUE= 'notUnique';

    protected $_messageTemplates = array(
        self::NOT_UNIQUE => 'This value is already in use.'
    );

    protected $_field;

    public function __construct( $field = null )
    {
        $this->setField( $field );
    }

    public function isValid($value, $context = null)
    {

        if( empty( $this->_field ) ) {
            require_once 'Zend/Validate/Exception.php';
            throw new Zend_Validate_Exception( 'Invalid field specified' );
        }

        $accounts = new App_Table_Account();
        $query = $accounts->createQuery()
            ->addWhere( $this->_field . ' = ?', array( $value ) );

        if ( $query->count() == 0 ) {
            return true;
        }

        $this->_error(self::NOT_UNIQUE);
        return false;
    }

    public function setField( $field )
    {
        $this->_field = $field;
    }
}