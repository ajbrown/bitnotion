<?php
require_once 'BaseController.php';

/**
 * AccountController
 *
 * @author
 * @version
 */

class Dashboard_CatalogController extends Dashboard_BaseController
{

    /**
     * @var Artist
     */
    protected $_artist;

    public function init()
    {
        parent::init();

        $artists = new App_Table_Artist();
        $this->_artist = $artists->findOneByAccountId( $this->_identity->id );

    }
    public function indexAction()
    {
        $releaseTable = new App_Table_Release();
        $releases = $releaseTable->findByArtistId( $this->_artist->id );

        $this->view->assign( 'releases', $releases );
    }

    public function addreleaseAction()
    {
        $form = new App_Form_Release();
        $form->addElement( 'Submit', 'continue', array(
        	'label'    => 'Save & Add Tracks',
            'value'	   => 'continue'
        ) );

        if( !empty( $_POST ) && $form->isValid( $_POST ) ) {

            //find the artistId for this account:
            $release = new Release();
            $release->title       = $form->getValue( 'title' );
            $release->artistId    = $this->_artist->id;
            $release->publishDate = $form->getValue( 'publishDate' );
            $release->save();

            $this->_flash->addMessage( 'Your release has been created.' );
            if( $form->getValue( 'continue' ) != null ) {
                $this->_redirector->gotoSimple( 'addtrack', null, null,
                    array( 'releaseId' => $release->id ) );
            }

            $form->setDefaults( array( 'publishDate' => date( 'm/d/Y' ) ) );
        }

        $form->setMethod( 'post' );
        $form->setAction( '/dashboard/catalog/addrelease' );
        $this->view->form = $form;
    }

    public function addtrackAction()
    {
        $form = new App_Form_NewTrack();

        if ( !empty( $_POST ) && $form->isValid( $_POST ) ) {

            $artists = new App_Table_Artist();

            //--------------------------------
            // Check the file info, and save
            //--------------------------------
            $track = new Track();
            $track->title        = $form->getValue( 'title' );
            $track->artistId     = $this->_artist->id;
            $track->releaseId    = $form->getValue( 'releaseId' );
            $track->single       = true;
            $track->publishDate  = $form->getValue( 'publishDate' );
            $track->save();

            $this->_flash->addMessage( 'Your track info has been saved.' );

            $file = new TrackFile();
            $file->trackId    = $track->id;
            $file->fileName   = $form->audioFile->getFileName();
            $file->mimeType   = mime_content_type( $file->fileName );
            $file->save();

            $track->originalFileId = $file->id;
            $track->save();

            $this->_flash->addMessage( 'The audio file for your track has been scheduled for injestion.  Once the file has been injested, it will be available on the site.' );

            $form = new App_Form_NewTrack();
        }

        $form->setDefault( 'releaseId', $this->_request->getParam( 'releaseId' ) );
        $form->setMethod( 'post' );
        $form->setAction( '/dashboard/catalog/addtrack' );
        $this->view->form = $form;
    }
}