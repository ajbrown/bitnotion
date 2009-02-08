<?php

/**
 * Class representing a user message to be stored in ZendForge's LogBank service.
 *
 * @author 		A.J. Brown <aj@zendforge.org>
 * @category 	ZendForge
 * @package 	ZForge_Service
 * @subpackage 	LogBank
 */
class ZForge_Service_LogBank_Message
{
    protected $_id;
    protected $_appId;
    protected $_priority;
    protected $_description;
    protected $_fields = array();
    protected $_created;

    /**
     * Constructor
     *
     * @param string $messageId a messageId for this message.  This is only use
     * 		for responses which return messages.  If you try to write a message
     * 		containing a message id, the service will ignore your messageId.
     */
    function __construct( $messageId = null )
    {
        $this->_id = $messageId;
    }

    /**
     * Returns the 32char unique id for this message
     * @return string|null
     */
    public function getId()
    {
        return $this->_id;
    }

    /**
     * Set the application Id to pass to this message.  Application ID can be any
     * value, but using registered application IDs is highly recommended for
     * tracking and management purposes.
     *
     * @param string	$appId
     * @return ZForge_Service_LogBank_Message
     */
    public function setAppId( $appId )
    {
        $this->_appId = $appId;
        return $this;
    }

    /**
     * Sets message priority.  These may be any value, as the service does not
     * enforce any priorities.  Priority is only used to classify messages
     * on the reporting end, and is not used to inforce importance.
     *
     * @param	string $priority
     * @return	ZForge_Service_LogBank_Message
     */
    public function setPriority( $priority )
    {
        $this->_priority = $priority;
        return $this;
    }

    /**
     * Sets description
     *
     * @param string $description
     * @return ZForge_Service_LogBank_Message
     */
    public function setDescription( $description )
    {
        $this->_description = $description;
        return $this;
    }

    /**
     * Sets custom fields
     * @param string	$name
     * @param string	$value
     * @return ZForge_Service_LogBank_Message
     */
    public function setCustomField( $name, $value )
    {
        $this->_fields[ $name ] = $value;
        return $this;
    }

    /**
     * Represent the message as an array, compatible with the soap server
     * @return array
     */
    public function toArray()
    {
        return array(
            '_id'           => $this->_id,
            'appId'	        => $this->_appId,
            'priority'	    => $this->_priority,
            'description'	=> $this->_description,
            'created'		=> $this->_created,
            'fields'		=> $this->_fields
        );
    }
}