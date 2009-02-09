<?php

require_once 'generated/BaseAccountConfirm.php';

class AccountConfirm extends BaseAccountConfirm
{
    public function preInsert( $event )
    {
        if( empty( $this->code ) ) {
            $this->code = substr( strtoupper( md5( $this->emailAddress ) ), 0, 6 );
        }
        parent::preInsert();
    }
}