<?php

/**
 * The Server-Side API for the LogBank service.
 *
 * @todo Memcached EVERYTHING.  LogBank's performace should be catered to writing
 * since our primary concern would be slowing down end-user applications.
 *
 */
class ZForge_LogBank_Server
{

    const DATE_FORMAT = 'Y-m-d H:i:s.u';

    /**
     * @var Phly_Couch
     */
    private $_couch;

    public function __construct()
    {
        if( is_null( $this->_couch ) ) {
            $this->_couch = new Phly_Couch( array(
            	'db' => 'zflogbank'
            ) );
        }
    }

    /**
     * Stores a message to the logbank
     *
     * @throws ZForge_LogBank_Exception if API Key is invalid
     * @param string $apiKey An active API key for this account
     * @param ZForge_LogBank_Server_Message the message to store
     * @return string the message id of the inserted message, or false on failure
     */
    public function writeMessage( $apiKey, ZForge_LogBank_Server_Message $msg )
    {
        if ( !$this->verifyApiKey( $apiKey ) ) {
            require_once 'ZForge/LogBank/Exception.php';
            throw new ZForge_LogBank_Exception( 'Provided API Key is invalid' );
        }

        $msg  = $this->_prepareMessage( $msg );
        $msg->apiKey = $apiKey;
        $doc =  new Phly_Couch_Document( $this->_messageToArray( $msg ) );
        $result = $this->_couch->docSave( $doc );

        return $result->id;
    }

    /**
     * Returns a single message by unique id
     *
     * @throws ZForge_LogBank_Exception if API Key is invalid
     * @param string $apiKey the api key to authenticate the message
     * @param string $message_uid the message uid to return
     * @return ZForge_LogBank_Server_Message|null
     */
    public function readMessage( $apiKey, $message_uid )
    {
        if ( !$this->verifyApiKey( $apiKey ) ) {
            require_once 'ZForge/LogBank/Exception.php';
            throw new ZForge_LogBank_Exception( 'Provided API Key is invalid' );
        }

        $doc = $this->_couch->docOpen( $message_uid, $options );

        if ( !is_null( $doc ) && $doc->apiKey == $apiKey ) {

            $msg = new ZForge_LogBank_Server_Message();
            $msg->setId( $doc->getId() );

            foreach( $doc->toArray() as $key => $value ) {
               switch( $key ) {

                   #standard fields, write directly
                   case 'apiKey': //no break
                   case 'appId':  //no break
                   case 'priority':  //no break
                   case 'description': //no break
                   case 'created':
                       $msg->$key = $value;
                       break;

                   #ignored fields, skip
                   case 'fields':  //duplicated 'fields' array bug
                   case 'type':    //a system value
                       continue;
                       break;

                   #non-standard fields, add to custom fields
                   default:
                       $msg->fields[ $key ] = $value;
               }
            }

            return $msg;
        }

        return null;
    }

    /**
     *
     * @param 	string $apiKey The api key to verify
     * @return 	boolean	true if the api key is valid, otherwise false
     */
    public function verifyApiKey( $apiKey )
    {
        $options = array( 'key' => "\"$apiKey\"" );
        $response = $this->_couch->openView( 'apikeys', 'active', $options );
        return $response->count() > 0;
    }

    protected function _messageToArray( ZForge_LogBank_Server_Message $msg )
    {
        return (array) $msg;
    }

    protected function _prepareMessage( ZForge_LogBank_Server_Message $msg )
    {
        $msg->type       = 'exception';
        $msg->created    = date( self::DATE_FORMAT, time() );
        $msg->clientip   = isset( $_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : null;
        return $msg;
    }

}