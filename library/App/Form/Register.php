<?php

require_once  'Zend/Dojo/Form.php';

class App_Form_Register extends Zend_Dojo_Form
{

	public function init()
	{
		parent::init();

		$this->setName( 'registerForm' );
		$this->setAttrib( 'id', 'register-form' );
		$this->setMethod( 'post' );

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
			'description' => 'Give us your valid E-mail address.  We check this, so make sure its valid and you can access it.',
			'required' 	 => true,
			'trim'		 => true,
			'filters'    => array( 'StringToLower' ),
			'validators' => array(
			    array( 'EmailAddress', false, array( 'validateMx' => true ) ),
			    array( 'UniqueAccountDetail', false, array( 'field' => 'emailAddress' ) ),
			),
		) );

		$this->addElement( 'PasswordTextBox', 'password', array(
			'label'		    => 'Choose a Password',
			'description'	=> 'Make it a good one. It must be at least 6 characters',
			'required'	    => true,
			'trim'		    => false,
			'validators'	=> array(
				array( 'StringLength', false, array( 6 ) ),
				array( 'PasswordConfirmation', true, array( 'field' => 'confirm_password' ) ),
			)
		) );

		$this->addElement( 'PasswordTextBox', 'confirm_password', array(
			'label'		    => 'Retype Your Password',
			'description'	=> 'Just to make sure you didn\'t mess up',
			'required'	    => true,
			'trim'		    => false
		) );

		$this->addElement( 'CheckBox', 'sellerAccount', array(
		    'label'		    => 'I want to sell music, too.',
		    'description'	=> 'Check this box if you want to sell music on BitNotion.  You will still be able to purchase music using this account.'
		) );

		$this->addElement( 'Captcha', 'captcha', array(
		    'label'			=> 'Are you human?',
		    'descripton'	=> 'Type the letters shown, and prove you\'re smarter than the average robot.',
		    'captcha'		=> array(
		    	'captcha' => 'ReCaptcha',
		    	'privkey' => $config->recaptcha->privateKey,
		    	'pubkey'  => $config->recaptcha->publicKey,
		    )
		) );

		$this->addElement( 'SubmitButton', 'submit',
			array(
				'ignore'	=> true,
				'label'		=> 'Create My Account'
			)
		);
	}
}