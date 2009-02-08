#!/usr/bin/php
<?php
require_once '../application/Initializer.php';

// must specify the environment as the first variable
if ( ! count( $argv ) > 1 ) {
  echo 'You must specify an environment as the first argument';
  exit(-1);
}

$init = new Initializer( $argv[1] );
$config = $init->getConfig();

Zend_Loader::registerAutoload();

$s3 = new S3( $config->aws->accessKey, $config->aws->secretKey );

$buckets = array(
    $config->aws->publicBucket  => S3::ACL_PUBLIC_READ,
    $config->aws->contentBucket => S3::ACL_PRIVATE
);

$existingBuckets = $s3->listBuckets();

foreach( $buckets as $name => $acl ) {
    if ( !in_array( $name, $existingBuckets ) ) {
        $s3->putBucket( $name, $acl );
        echo "Bucket Added: $name\n";
    }
}