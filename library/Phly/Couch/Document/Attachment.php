<?php
class Phly_Couch_Document_Attachment
{

    public $name;
    protected $_data = array();

    public function __construct( array $json )
    {

    }

    public function setData( $contentType, $data )
    {
        $this->_data[ 'content_type' ] = strtolower( $contentType );
        $this->_data[ 'data' ] = base64_encode( $data );
    }

    public function getData()
    {
        if( !empty( $this->_data ) ) {
            return $this->_data;
        } else {
            return null;
        }
    }
}