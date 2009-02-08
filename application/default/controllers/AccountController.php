<?php

/**
 * AccountController
 *
 * @author
 * @version
 */

require_once 'ZForge/Controller/Action.php';

class AccountController extends ZForge_Controller_Action
{

	const METHOD_OPENID = 'openid';

	/**
	 * The default action - show the home page
	 */
	public function indexAction()
	{
	}

	/**
	 *
	 * @todo add openid authentication
	 *
	 */
	public function loginAction()
	{

		$form = new App_Form_Login();

		if ( !empty( $_POST ) && $form->isValid( $_POST ) ) {

		    $username = $form->getValue( 'username' );
		    $password = $form->getValue( 'password' );

			//------------------------------------
			// make sure the login form validates
			//------------------------------------
			if ( $form->isValid( $_POST ) ) {

				$auth = Zend_Auth::getInstance();

				//------------------------------------------
				// Attempt a standard database login
				//------------------------------------------
				$adapter = new ZendX_Doctrine_Auth_Adapter(
				    Doctrine_Manager::connection(), 'Account', 'username',
				    'password', 'MD5(?) AND enabled = 1 AND confirmed = 1'
				);

				$adapter->setIdentity( $username );
				$adapter->setCredential( $password );

				$result = $auth->authenticate( $adapter );

				if ( !$result->isValid() ) {
					$message = 'The username and password provided does not match our records';
					$this->_flash->addMessage( $message );
					$form->addError( $message );

				} else {

				    $userdata = $adapter->getResultRowObject( null, 'password' );
				    $auth->getStorage()->write( $userdata );

				    //audit the login
				    $login = new AccountLogin();
				    $login->accountId = $userdata->id;
				    $login->ip = ip2long( $_SERVER['REMOTE_ADDR'] );
				    $login->save();

					$this->_flash->addMessage(
						'Welcome back, ' . $result->getIdentity() );

					$this->_redirector->gotoSimple( 'profile' );
				}
			}
		}

		// force users to logout before they can try to login
		if( Zend_Auth::getInstance()->getIdentity() !== null ) {
			$this->_flash->addMessage(
				'You are already logged in!  You must log out before you can
				log into a different account.'
			);
			$this->_redirector->gotoSimple( 'profile' );
		}

		$form->setMethod( Zend_Form::METHOD_POST );
		$this->view->form = $form;

	}

	public function profileAction()
	{
		if ( Zend_Auth::getInstance()->getIdentity() === null ) {
			$this->_flash->addMessage( 'You must login to do that.' );
			$this->_redirector->gotoSimple( 'login' );
		}

		//load profile information

		$form = new forms_ProfileForm();
		$form->setMethod( Zend_Form::METHOD_POST );

		$this->view->form = $form;
	}


	public function logoutAction()
	{

		if ( Zend_Auth::getInstance()->getIdentity() ) {

			Zend_Auth::getInstance()->clearIdentity();
			$this->_flash->addMessage( 'You have been logged out.' );
		}

	}

	public function registerAction()
	{
		$username = $this->_getParam( 'emailAddress' );
		$regForm = new forms_RegisterForm();

		if ( !empty( $username ) && $regForm->isValid( $_POST ) ) {

			$params = $this->_getAllParams();
			$messages = array();
			$valid = true;

			// make sure the username isn't taken
			require_once 'models/handlers/AccountsHandler.php';
			$accounts = new AccountsHandler( Doctrine_Manager::connection() );
			$result = $accounts->findOneByEmailAddress( $params[ 'emailAddress' ] );

			if ( false !== $result ) {
				$messages[] = 'The username you specfied is already taken.';
				$valid = false;
			}

			// password confirmation must be the same
			if ( !$params['password'] == $params['passwordconfirm'] ) {
				$messages[] = 'The passwords you entered did not match';
				$valid = false;
			}


			if ( $valid ) {

				// save the user

			    $user =  $accounts->create();
				$user->username 	= $params[ 'emailAddress' ];
				$user->emailAddress = $params[ 'emailAddress' ];
				$user->password		= md5( $params[ 'password' ] );
				$user->confirmed	= false;
				$user->save();


				// send the confirmation, and redirect to the confirm page

				$this->_sendConfirmEmail( $user->emailAddress );


				$msg = "A confirmation code has been set to "
						. "<cite>{$user->emailAddress}</cite>.  Please check "
						. "your enter the confirmation into the field below.";

				$this->_flash->addMessage( $msg );
				$this->_redirect( '/account/confirm/' . $user->emailAddress );
				return;

			}

			// add error messages to flash
			foreach( $messages as $msg ) {
					$this->_flash->addMessage( $msg );
			}
		}

		$regForm->setMethod( 'post' );
		$this->view->form = $regForm;
	}

	public function confirmAction()
	{
		$code 	= $this->_getParam( 'code' );
		$email	= $this->_getParam( 'email' );
		$resend = $this->_getParam( 'resend' );

		$form   = new forms_ConfirmForm();
		$form->setMethod( 'get' );
		$form->setDefaults( array( 'email' => $email, 'code' => $code ) );

		if( !empty( $resend ) && !empty( $email ) ) {

			//------------------------------------------
			// Resend email confirmation
			//------------------------------------------

			//make sure the supplied email address is registered and not confirmed
			$accounts = new AccountsHandler();
			$result = $accounts->findOneByEmailAddress( $email );
			if( false !== $result ) {

				$this->_sendConfirmEmail( $email );
				$this->_flash->addMessage( 'A confirmation code has been resent' );

			} else {

				$this->_flash->addMessage(
					'The email address you entered is not pending confirmation.'
				);
			}
		} else if( !empty( $code ) ) {

			//------------------------------------------
			// Attempt to confirm an email address
			//------------------------------------------

			$confirmcodes = new AccountConfirmsHandler();

			if( $confirmcodes->confirm( $email, $code ) ) {

				$accounts = new AccountsHandler();
				$accounts->confirmByEmail( $email );

				$this->_flash->addMessage(
					'Your email address has been confirmed.  You may now log in.'
				);

				$this->_redirector->gotoSimple( 'login' );
			} else {

				$this->_flash->addMessage(
					'The confirmation code is incorrect for the email address '
					. 'you provided.  Please try again.'
				);
			}
		}

		$this->view->form = $form;
		$this->view->assign( 'email', $email );
		$this->view->assign( 'code', $code );
	}

	protected function _sendConfirmEmail( $emailaddress )
	{

	    $confirms = new AccountConfirmsHandler();
		$code =   $confirms->generate( $emailaddress );

		$view = new Zend_Layout();
		$view->setLayout( 'blank' );

		$view->getView()->assign( 'emailAddress', $emailaddress );
		$view->getView()->assign( 'confirmCode', $code );

		$mail = new Zend_Mail();
		$mail->addTo( $emailaddress );
		$mail->setBodyText( $view->render( 'mail/confirm-text' ) );
		$mail->setBodyHtml( $view->render( 'mail/confirm-html' ) );
		$mail->setFrom( 'accounts@zendforge.org' );
		$mail->setReturnPath( 'no-reply@zendforge.org' );
		$mail->setSubject( 'Please Confirm Your E-Mail Address for ZendForge' );
		$mail->send();
	}

}