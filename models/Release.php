<?php

require_once 'generated/BaseRelease.php';

class Release extends BaseRelease
{

    public function preInsert( $event )
    {
        if ( empty( $this->publishDate ) )
        {
            $this->publishDate = gmdate( 'Y-m-d h:i:s' );
        }
    }
}