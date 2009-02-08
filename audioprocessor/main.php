#!/usr/bin/php
<?php

require_once '../application/bootstrap.php';

//TODO MP3 ID3 Tagging
//TODO

$init = new Initializer( isset( $argv[ 1 ] ) ? $argv[ 1 ] : 'production' );
$init->initDb();
$init->initApp();

$config = $init->getConfig();

$log = new Zend_Log();
$logwriter = new Zend_Log_Writer_Stream( 'ingestion.log' );
$logwriter->setFormatter( new Zend_Log_Formatter_Simple(
    '%timestamp% %priorityName% (%priority%): %process% [%track%] %message%' . PHP_EOL
) );
$log->addWriter( $logwriter );

$log->setEventItem( 'process', str_pad( rand(0, 999), 3, '0' ) );

define( 'WORKDIR', $config->injestion->workDir );


$s3 = new S3( $config->aws->accessKey, $config->aws->secretKey );

//------------------------------------

$log->log( '-- begin processing tracks --', Zend_Log::INFO );

$table = new Doctrine_Table( 'Track', Doctrine_Manager::connection(), true );
while ( $track = $table->findOneByEncodingstatus( 'UPLOADED' ) ) {

    $log->setEventItem( 'track', $track->id );

    $track->encodingStatus = 'PROCESSING';
    $track->save();

    $log->log( '- processing track', Zend_Log::INFO );

    // Make sure track has an original file id
    if ( intval( $track->originalFileId ) == 0 ) {
        $track->encodingStatus = 'ERROR';
        $track->save();
        $log->log( 'error: no source file to process', Zend_Log::ERR );
        continue;
    }

    $original = $track->Original;

    //make sure the file exists
    if( !is_readable( $original->filename ) ) {
        $track->encodingStatus = 'ERROR';
        $track->save();
        $log->log( 'error: source file is not readable', Zend_Log::ERR );
        continue;
    }

    $data = array(
        'type' => $original->mimeType,
        'file' => $original->filename
    );


    //build the s3 filename
    $path = '/originals/' . $track->releaseId . '/' . md5( $track->id ) . '.wav';
    $original->s3uri = $path;

    $result = $s3->putObject( $data, $config->aws->contentBucket, $path, S3::ACL_PRIVATE );

    if( !$result ) {
        $track->encodingStatus = 'ERROR';
        $track->save();
        continue;
    }

    $log->log( 's3 upload successful, original file', Zend_Log::INFO );

    $original->s3uri = $path;
    $original->save();


    $log->log( 'attempting to create preview file', Zend_Log::DEBUG );

    //create the preview file
    $preview = $track->Preview;
    $preview->filename = WORKDIR . '/' . md5( $track->id ) . '_preview.mp3';

    $cmd = sprintf( '%s -h -V%d %s %s',
        $config->injestion->lameExec,
        $config->injestion->samples->vbr,
        escapeshellarg($original->filename),
        escapeshellarg($preview->filename)
    );

    ob_start();
    system( $cmd, $status );
    ob_end_clean();

    if( $status !== 0 ) {
        $log->log( 'error: lame returned status ' . $status, Zend_Log::ERR );
        $track->encodingStatus = 'ERROR';
        $track->save();
        continue;
    }

    $log->log( 'attempting to upload preview file', Zend_Log::DEBUG );

    //upload the preview
    $path   = '/previews/' . $track->releaseId . '/' . basename( $preview->filename );
    $result = $s3->putObject( $data, $config->aws->publicBucket, $path, S3::ACL_PUBLIC_READ );

    if( !$result ) {
        $log->log( 'error: could not upload preview file', Zend_Log::ERR );
        $track->encodingStatus = 'ERROR';
        $track->save();
        continue;
    }

    $preview->s3uri = $path;
    $preview->save();

    //create the purchasable file
    $purchasable = $track->Purchasable;
    $purchasable->filename = WORKDIR . '/' . md5( $track->id ) . '.mp3';

    $cmd = sprintf( '%s -h %s %s',
        $config->injestion->lameExec,
        escapeshellarg( $original->filename ),
        escapeshellarg( $purchasable->filename )
    );

    //upload the purchasable file
    $path   = '/purchasables/' . $track->releaseId . '/' . basename( $purchasable->filename );
    $result = $s3->putObject( $data, $config->aws->contentBucket, $path, S3::ACL_PRIVATE );
    if( !$result ) {
        $log->log( 'error: could not upload purchasable file', Zend_Log::ERR );
        $track->encodingStatus = 'ERROR';
        $track->save();
        continue;
    }

    $purchasable->s3uri = $path;
    $purchasable->save();

    $log->log( 'finished processing track', Zend_Log::INFO );
    $track->encodingStatus = 'COMPLETE';
    $track->save();

    $log->setEventItem( 'track', null );
}