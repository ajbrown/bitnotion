<?php

class ZForge_LogBank_Server_Message
{
    private $_id;

    /**
     * @var string
     */
    public $appId;

    /**
     * @var string
     */
    public $priority;

    /**
     * @var string
     */
    public $description;

    /**
     * @desc Custom fields to include with this message.
     *
     * @var array
     */
    public $fields;

    /**
     * @var string
     */
    public $created;


    /**
     *
     * @return string|null the document id for this message
     */
    public function getId()
    {
        return $this->_id;
    }

    /**
     *
     * @param string $id a document id to use for this message
     */
    public function setId( $id )
    {
        $this->_id = $id;
    }

}