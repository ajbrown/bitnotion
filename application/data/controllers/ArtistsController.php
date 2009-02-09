<?php
/**
 * ReleasesController
 *
 * @author
 * @version
 */
require_once 'Zend/Controller/Action.php';
class Data_ArtistsController extends ZForge_Controller_Action
{

    public function isPublic()
    {
        return true;
    }

    public function init()
    {
        $this->_helper->viewRenderer->setNeverRender( true );
        Zend_Layout::getMvcInstance()->setLayout( 'data' );
        parent::init();
    }

    /**
     * The default action - show the home page
     */
    public function infoAction()
    {
        $artist = $this->_request->getParam( 'artist' );
    }

    public function releasesAction()
    {
        $artistId  = $this->_request->getParam( 'id' );
        $published = $this->_request->getParam( 'published', false );

        $releaseTable = new App_Table_Release();
        if( $published ) {
            $releases = $releaseTable->findPublishedByArtist( $artistId )
                ->toArray( true );
        } else {
            $releases = $releaseTable->findByArtistId( $artistId );
        }

        $data = new Zend_Dojo_Data( 'id', $releases, 'releases' );
        echo $data->toJson();
    }

    public function tracksAction()
    {
        $artistId = $this->_request->getParam( 'id' );

        $releaseTable = new App_Table_Release();
        $releases = $releaseTable->findPublishedByArtist( $artistId, false )
            ->toArray( true );
        $data = new Zend_Dojo_Data( 'id', $releases, 'releases' );
        echo $data->toJson();
    }

}