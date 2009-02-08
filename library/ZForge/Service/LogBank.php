<?php
require_once 'Zend/Service/Abstract.php';

/**
 * An interface to ZendForge's LogBank service for exception and message
 * logging and reporting.
 *
 * @todo Does this really need to extend Zend_Service_Abstract?  No useful
 * functionality is provided, and it was originally only done as a matter of
 * convention.
 *
 * @uses Zend_Uri to validate endpoint URIs
 *
 * @author 		A.J. Brown <aj@zendforge.org>
 * @category 	ZendForge
 * @package 	ZForge_Service
 * @subpackage 	LogBank
 */
class ZForge_Service_LogBank extends Zend_Service_Abstract
{

    /**
     * Sets default options to use for instances, allowing configuration injection
     * @var array
     */
    protected static $defaultOpts = array(
        'wsdl' => 'http://api.zendforge.org/logbank/soap?wsdl'
    );

    /**
     * The API Key to use with all requests through this instance
     * @var string
     */
    protected $_apikey;

    /**
     * The soap client used to communicate with LogBank's servers
     * @var Zend_Soap_Client
     */
    protected $_soap;

    /**
     * Create a new instance
     *
     * @param string $apikey a valid ZForge LogBank API Key to use for all
     * 	requests
     * @param array	$options options to configure this instance
     */
    public function __construct( $apikey, array $options = null)
    {
        if ( null === $options ) $options = array();
        $options = array_merge( self::$defaultOpts, $options );
        $this->setOptions( $options );

        $this->_apikey = $apikey;
    }


    /**
     * Gets the currently assigned API Key
     *
     * @return string
     */
    public function getApiKey()
    {
        return $this->_apikey;
    }


    /**
     * Sets the API key associated with this account
     *
     * @param string $apikey
     * @return void
     */
    public function setApiKey( $apikey )
    {
        $this->_apikey = $apikey;
    }

    /**
     * Gets the soap client used by this instance
     * @return Zend_Soap_Client
     */
    public function getSoapClient()
    {
        return $this->_soap;
    }

    /**
     * Return the endpoint associated with the soap client
     *
     * @return string uri
     */
    public function getWsdl()
    {
        return $this->_soap->getWsdl();
    }

    /**
     * Sets the default endpoint uri.  URI shoud be full path to soap provider,
     * including the protocol designation. ex: http://api.zendforge.org/logbank/soap
     *
     * @throws ZForge_Service_LogBank_Exception if the specified URI is not
     * 	well formed.
     * @param string a valid URI
     * @return ZForge_Service_LogBank
     */
    public function setWsdl( $uri )
    {
        if( $this->_assertUri( $uri ) ) {
            $this->_soap = new Zend_Soap_Client( $uri );
        }
        return $this;
    }

    /**
     * Set options for this
     *
     * @param  array $options
     * @return Phly_Couch
     */
    public function setOptions(array $options)
    {
        foreach ($options as $key => $value) {
            $method = 'set' . ucfirst($key);
            if (method_exists($this, $method)) {
                $this->$method($value);
            }
        }
        return $this;
    }


    /**
     * Writes a message to the logbank service.
     *
     * @throws ZForge_Service_LogBank_Exception on SoapFault
     * @param ZForge_Service_LogBank_Message $message the message to send
     * @return string	the messageId of the newly created message
     */
    public function writeMessage( ZForge_Service_LogBank_Message $message )
    {
        $this->_assertApikey();

        try {
            $response = $this->_soap->writeMessage( $this->_apikey, $message->toArray() );
        } catch ( SoapFault $e ) {
            throw new ZForge_Service_LogBank_Exception( $e->getMessage(), $e->getCode() );
        }

        return $response;
    }

    /**
     * Retreives data for a message stored  in LogBank service.
     *
     * @throws ZForge_Service_LogBank_Exception on SoapFault
     * @param string $messageId
     * @return ZForge_LogBank_Server_Message|null
     */
    public function readMessage( $messageId )
    {
        $this->_assertApikey();

        try {
            $response = $this->_soap->readMessage( $this->_apikey, $messageId );
        } catch ( SoapFault $e ) {
            throw new ZForge_Service_LogBank_Exception( $e->getMessage(), $e->getCode() );
        }

        $msg = new ZForge_Service_LogBank_Message( $response->fields['_id'] );

        //unset known private keys, these are useless to the client
        unset( $response->fields[ '_id' ] );
        unset( $response->fields[ '_rev' ] );

        $msg->setAppId( $response->appId );
        $msg->setPriority( $response->priority );
        $msg->setDescription( $response->description );
        foreach( $response->fields as $key => $value ) {
            $msg->setCustomField( $key, $value );
        }

        return $msg;
    }

    /**
     * Verifies the API key against the LogBank service.
     *
     * @throws ZForge_Service_LogBank_Exception on SoapFault
     * @return bool true if the key is valid, otherwise false
     */
    public function verifyApiKey()
    {
        $this->_assertApikey();

        try {
            $response = $this->_soap->verifyApiKey( $this->_apikey );
        } catch ( SoapFault $e ) {
            throw new ZForge_Service_LogBank_Exception( $e->getMessage(), $e->getCode() );
        }

        return $response;
    }

    /**
     * Injects default configuration for future instances. Instances can use the
     * default options, or overwrite them.
     *
     * @param	array config options
     * @return	void
     */
    public static function configure( array $options )
    {
        self::$defaultOpts = $options;
    }

    /**
     * Ensures a uri is properly formed, and throws an exception otherwise
     *
     * @throws ZForge_Service_LogBank_Exception
     * @return boolean
     */
    protected function _assertUri( $uri ) {
        if( !Zend_Uri::check( $uri ) ) {
            require_once 'ZForge/Service/LogBank/Exception.php';
            throw new ZForge_Service_LogBank_Exception(
            	'The specified service URI does not exist'
            );
        }
        return true;
    }

    /**
     * Asserts that an API Key has been provided
     *
     * @throws ZForge_Service_LogBank_Exception
     * @return boolean
     */
    protected function _assertApikey() {
        if ( !isset( $this->_apikey ) ) {
            require_once 'ZForge/Service/LogBank/Exception.php';
            throw new ZForge_Service_LogBank_Exception(
            	'You must provide an API key to use this service'
            );
        }
        return true;
    }
}