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

				    //translate the user into an actual doctrine object
				    $accounts = new App_Table_Account();
				    $auth->getStorage()->write( $accounts->find( $userdata->id ) );

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

		$form = new App_Form_Register();

		if ( !empty( $_POST ) && $form->isValid( $_POST ) ) {

		    if( $form->getValue( 'sellerAccount' )  ) {
		        $account = new Seller();
		    } else {
		        $account = new Buyer();
		    }

			$account->username 	    = $form->getValue( 'emailAddress' );
			$account->emailAddress  = $form->getValue( 'emailAddress' );
			$account->password		= $form->getValue( 'password' );
			$account->confirmed	    = false;
			$account->save();

			// send the confirmation, and redirect to the confirm page
			$this->_sendConfirmEmail( $account->emailAddress );

			$msg = "A confirmation code has been set to "
					. "<cite>{$account->emailAddress}</cite>.  Please check "
					. "your enter the confirmation into the field below.";

			$this->_flash->addMessage( $msg );
			$this->_redirector->gotoSimple( 'confirm', null, null, array(
			    'email' => $account->emailAddress
			) );
		}

		$form->setMethod( 'post' );
		$this->view->form = $form;
	}

	public function confirmAction()
	{
		$code 	= $this->_request->getParam( 'code' );
		$email	= $this->_request->getParam( 'email' );

		$form   = new App_Form_ConfirmRegister();
		$form->setMethod( 'post' );

		if( !empty( $_POST ) && $form->isValid( $_POST ) ) {

		    $confirms = new App_Table_AccountConfirm();
		    $code  = $form->getValue( 'code' );
		    $email = $form->getValue( 'emailAddress' );

		    //------------------------------------------
            // Attempt to confirm an email address
            //-----------------------------------------
            $confirm = $confirms->find( $email );

            if ( $confirm == false ) {

                $form->getElement( 'emailAddress' )
                    ->addError( 'This e-mail address is not pending confirmation' );

            } elseif( $code == $confirm->code ) {

                $confirm->Account->confirmed = true;
                $confirm->Account->save();
                $confirm->delete();
                $this->_flash->addMessage( 'Your e-mail address has been confirmed.  You may now log in.' );

                $this->_redirector->gotoSimple(
                    'login' );
            } else {
                $form->getElement( 'code' )
                    ->addError( 'The confirmation code is incorrect.' );
            }
		}

		$this->view->form = $form;
		$this->view->assign( 'email', $email );
		$this->view->assign( 'code', $code );
	}

	protected function _sendConfirmEmail( $emailAddress )
	{
	    $confirm = new AccountConfirm();
	    $confirm->emailAddress = $emailAddress;
	    $confirm->save();

		$this->view->layout()->setLayout( 'blank' );

		$this->view->assign( 'emailAddress', $confirm->emailAddress );
		$this->view->assign( 'confirmCode',  $confirm->code );

		$link = $this->_helper->url( 'confirm', 'account', 'default', array(
		    'email' => $confirm->emailAddress,
		    'code'  => $confirm->code
		) );

		$this->view->assign( 'link', "http://{$this->_config->hostname}/{$link}" );

		$mail = new Zend_Mail();
		$mail->addTo( $confirm->emailAddress );
		$mail->setBodyText( $this->view->render( 'mail/confirm-text.phtml' ) );
		$mail->setBodyHtml( $this->view->render( 'mail/confirm-html.phtml' ) );
		$mail->setFrom( 'accounts@' . $this->_config->hostname, 'BitNotion, M.D.' );
		$mail->setReturnPath( 'no-reply@' . $this->_config->hostname );
		$mail->setSubject( 'BitNotion | Please Confirm Your E-Mail Address' );
		$mail->send();
	}

}