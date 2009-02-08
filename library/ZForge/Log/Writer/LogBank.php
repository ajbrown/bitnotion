<?php


/**
 * Log writter for using LogBank service.
 *
 * @todo Support additional formatters
 *
 */
class ZForge_Log_Writer_LogBank extends Zend_Log_Writer_Abstract
{
    /**
     * @var ZForge_Service_LogBank
     */
    protected $_logBankClient;

    public function __construct( ZForge_Service_LogBank $client )
    {
        $this->_logBankClient = $client;

        require_once 'Zend/Log/Formatter/Simple.php';
        $this->_formatter = new Zend_Log_Formatter_Simple();
    }

    /**
     * Formatting is not possible on this writer
     *
     * @todo Support more formatters
     */
    public function setFormatter( $formatter )
    {
        require_once 'Zend/Log/Exception.php';
        throw new Zend_Log_Exception(get_class() . ' does not support formatting');
    }

    /**
     * Handles writing the log message to LogBank.
     *
     * @param array	$event
     * @return void
     */
    protected function _write( $event )
    {
        require_once 'ZForge/Service/LogBank/Message.php';
        $msg =  new ZForge_Service_LogBank_Message();

        foreach( $event as $key => $value ) {
            switch( $key ) {

                case 'appId':
                    $msg->setAppId( $value );
                    break;
                case 'message':
                    $msg->setDescription( $value );
                    break;
                case 'priorityName':
                    $msg->setPriority( $value );
                    break;
                default:
                    $msg->setCustomField( $key, $value );
            }
        }

    }
}