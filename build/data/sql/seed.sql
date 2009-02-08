/*
SQLyog Community Edition- MySQL GUI v7.14 
MySQL - 5.0.51a-3ubuntu5.4 : Database - coppermine
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;

CREATE DATABASE /*!32312 IF NOT EXISTS*/`coppermine` /*!40100 DEFAULT CHARACTER SET latin1 */;

USE `coppermine`;

/*Table structure for table `account_confirms` */

CREATE TABLE `account_confirms` (
  `emailaddress` char(64) NOT NULL default '',
  `code` char(8) default NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY  (`emailaddress`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Data for the table `account_confirms` */

/*Table structure for table `accounts` */

CREATE TABLE `accounts` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `username` char(16) NOT NULL,
  `password` char(32) NOT NULL,
  `emailaddress` char(64) NOT NULL,
  `enabled` tinyint(1) NOT NULL default '1',
  `confirmed` tinyint(1) NOT NULL default '0',
  `type` varchar(255) default NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

/*Data for the table `accounts` */

insert  into `accounts`(`id`,`username`,`password`,`emailaddress`,`enabled`,`confirmed`,`type`,`created_at`,`updated_at`) values (1,'aj@ajbrown.org','a46be2ddacd272de9814bbb9e4d086c3','aj@ajbrown.org',1,1,'9','0000-00-00 00:00:00','0000-00-00 00:00:00');

/*Table structure for table `accounts_logins` */

CREATE TABLE `accounts_logins` (
  `id` bigint(20) unsigned NOT NULL auto_increment,
  `accountId` int(10) unsigned NOT NULL,
  `ip` int(11) default NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `accountid_idx` (`accountId`),
  CONSTRAINT `accounts_logins_accountid_accounts_id` FOREIGN KEY (`accountid`) REFERENCES `accounts` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;

/*Data for the table `accounts_logins` */

insert  into `accounts_logins`(`id`,`accountId`,`ip`,`created_at`,`updated_at`) values (1,1,2130706433,'2009-02-03 03:09:04','2009-02-03 03:09:04'),(2,1,2130706433,'2009-02-04 02:13:20','2009-02-04 02:13:20');

/*Table structure for table `migration_version` */

CREATE TABLE `migration_version` (
  `version` int(11) default NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Data for the table `migration_version` */

insert  into `migration_version`(`version`) values (1);

/*Table structure for table `releases` */

CREATE TABLE `releases` (
  `id` bigint(20) NOT NULL auto_increment,
  `title` varchar(64) default NULL,
  `artistid` bigint(20) NOT NULL,
  `published` tinyint(1) NOT NULL default '1',
  `publishDate` datetime NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

/*Data for the table `releases` */

insert  into `releases`(`id`,`title`,`artistid`,`published`,`publishDate`,`created_at`,`updated_at`) values (1,'test release',1,1,'0000-00-00 00:00:00','2009-02-02 09:11:00','2009-02-02 09:11:00');

/*Table structure for table `tags` */

CREATE TABLE `tags` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `name` char(32) default NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Data for the table `tags` */

/*Table structure for table `track_files` */

CREATE TABLE `track_files` (
  `id` bigint(20) NOT NULL auto_increment,
  `filename` varchar(255) default NULL,
  `trackid` bigint(20) NOT NULL,
  `mimetype` varchar(20) default NULL,
  `purchasable` tinyint(1) NOT NULL default '0',
  `active` tinyint(1) NOT NULL default '1',
  `enabled` tinyint(1) NOT NULL default '1',
  `size` bigint(20) default NULL,
  `s3uri` varchar(255) default NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=latin1;

/*Data for the table `track_files` */

insert  into `track_files`(`id`,`filename`,`trackid`,`mimetype`,`purchasable`,`active`,`enabled`,`size`,`s3uri`,`created_at`,`updated_at`) values (1,'/tmp/encoding/incoming/15815__Howdy84__door_close_2.wav',2,'audio/x-wav',0,1,1,NULL,NULL,'2009-02-03 04:12:10','2009-02-03 04:12:10'),(2,'/tmp/encoding/incoming/15815__Howdy84__door_close_2.wav',3,'audio/x-wav',0,1,1,NULL,'/originals/1/eccbc87e4b5ce2fe28308fd9f2a7baf3.wav','2009-02-03 04:12:26','2009-02-03 09:22:08'),(3,'/tmp/encoding/incoming/15815__Howdy84__door_close_2.wav',4,'audio/x-wav',0,1,1,NULL,'/originals/1/a87ff679a2f3e71d9181a67b7542122c.wav','2009-02-03 04:13:27','2009-02-03 09:22:08'),(6,'/tmp/encoding/incoming/eccbc87e4b5ce2fe28308fd9f2a7baf3_preview.mp3',0,NULL,0,1,1,NULL,'/previews/1/eccbc87e4b5ce2fe28308fd9f2a7baf3_preview.mp3','2009-02-03 09:45:25','2009-02-03 09:46:57'),(7,'/tmp/encoding/incoming/a87ff679a2f3e71d9181a67b7542122c_preview.mp3',0,NULL,0,1,1,NULL,'/previews/1/a87ff679a2f3e71d9181a67b7542122c_preview.mp3','2009-02-03 09:45:27','2009-02-03 09:47:01');

/*Table structure for table `track_tags` */

CREATE TABLE `track_tags` (
  `trackid` bigint(20) NOT NULL default '0',
  `tagid` bigint(20) NOT NULL default '0',
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY  (`trackid`,`tagid`),
  CONSTRAINT `track_tags_trackid_tracks_id` FOREIGN KEY (`trackid`) REFERENCES `tracks` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Data for the table `track_tags` */

/*Table structure for table `tracks` */

CREATE TABLE `tracks` (
  `id` bigint(20) NOT NULL auto_increment,
  `title` varchar(64) default NULL,
  `artistid` bigint(20) NOT NULL,
  `releaseid` bigint(20) NOT NULL,
  `active` tinyint(1) NOT NULL default '0',
  `enabled` tinyint(1) NOT NULL default '1',
  `single` tinyint(1) NOT NULL default '0',
  `originalfileid` bigint(10) default NULL,
  `purchasefileid` bigint(10) default NULL,
  `previewfileid` bigint(10) default NULL,
  `publishdate` datetime default NULL,
  `encodingstatus` enum('UPLOADED','PROCESSING','ERROR','COMPLETE') default 'UPLOADED',
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `artistid_idx` (`artistid`),
  KEY `releaseid_idx` (`releaseid`),
  CONSTRAINT `tracks_releaseid_releases_id` FOREIGN KEY (`releaseid`) REFERENCES `releases` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;

/*Data for the table `tracks` */

insert  into `tracks`(`id`,`title`,`artistid`,`releaseid`,`active`,`enabled`,`single`,`originalfileid`,`purchasefileid`,`previewfileid`,`publishdate`,`encodingstatus`,`created_at`,`updated_at`) values (1,'tsetseet',1,1,1,1,1,NULL,NULL,NULL,'0000-00-00 00:00:00','ERROR','2009-02-03 04:11:47','2009-02-03 09:46:52'),(2,'tsetseet',1,1,1,1,1,NULL,NULL,NULL,'0000-00-00 00:00:00','ERROR','2009-02-03 04:12:10','2009-02-03 09:46:52'),(3,'tsetseet',1,1,1,1,1,2,NULL,6,'0000-00-00 00:00:00','COMPLETE','2009-02-03 04:12:25','2009-02-03 09:46:57'),(4,'tsetseet',1,1,1,1,1,3,NULL,7,'0000-00-00 00:00:00','COMPLETE','2009-02-03 04:13:27','2009-02-03 09:47:01');

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
