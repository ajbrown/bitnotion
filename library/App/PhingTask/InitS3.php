<?php
require_once 'phing/Task.php';

class App_Task_InitS3 extends Task {

    protected $_environment = 'production';

    public function init()
    {

    }

    public function setEnvironment( $environment )
    {
        $this->_environment = $environment;
    }

    public function main()
    {



    }

}