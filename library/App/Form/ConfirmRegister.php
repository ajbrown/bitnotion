<?php

require_once  'Zend/Dojo/Form.php';

class App_Form_ConfirmRegister extends Zend_Dojo_Form
{

	public function init()
	{
		parent::init();

		$this->setName( 'confirmRegisterForm' );
		$this->setAttrib( 'id', 'confirm-register-form' );
		$this->addElementPrefixPath( 'App_Validate', 'App/Validate/', 'validate' );

		$this->setDecorators( array(
			'FormElements',
			'DijitForm',
		) );

		$config = Initializer::getConfig();

		//--------------------------------------
		// Elements
		//--------------------------------------

		$this->addElement( 'ValidationTextBox', 'emailAddress', array(
			'label'		  => 'Your E-Mail Address',
			'description' => 'The E-mail address you wish you confirm',
			'required' 	 => true,
			'trim'		 => true,
			'filters'    => array( 'StringToLower' ),
			'validators' => array(
			    array( 'EmailAddress', false, array( 'validateMx' => true ) ),
			),
		) );

		$this->addElement( 'TextBox', 'code', array(
			'label'		    => 'Confirmation Code',
			'description'	=> 'This is the code you received from us.',
			'required'	    => true,
			'trim'		    => true,
		    'filters'		=> array( 'StringToUpper' ),
			'validators'	=> array( 'Alnum' ),
		) );

		$this->addElement( 'SubmitButton', 'submit',
			array(
				'ignore'	=> true,
				'label'		=> 'Confirm My E-Mail Address'
			)
		);
	}
}