<?php
require_once 'generated/BaseAccount.php';

class Account extends BaseAccount
{
    const TYPE_BUYER    = 1;
    const TYPE_SELLER   = 2;

    public function setUp()
    {
        parent::setUp();
        $this->hasMutator( 'password', 'passwordMutator' );
    }

    public function passwordMutator( $value )
    {
        $this->_set( 'password', md5( $value ) );
    }
}