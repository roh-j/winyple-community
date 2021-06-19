-- MySQL dump 10.11
--
-- Host: localhost    Database: winyple
-- ------------------------------------------------------
-- Server version	5.1.45p1-log

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `artist`
--

DROP TABLE IF EXISTS `artist`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `artist` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(20) NOT NULL,
  `people` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=56 DEFAULT CHARSET=euckr;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `board`
--

DROP TABLE IF EXISTS `board`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `board` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(20) NOT NULL,
  `title` varchar(90) NOT NULL,
  `contents` text NOT NULL,
  `wdate` datetime NOT NULL,
  `view` int(9) NOT NULL DEFAULT '0',
  `img` varchar(100) NOT NULL,
  `bgimg` varchar(100) NOT NULL,
  `ip` varchar(15) NOT NULL,
  `total` double(9,1) NOT NULL DEFAULT '0.0',
  `today` int(2) NOT NULL DEFAULT '1',
  `commentupdate` int(2) NOT NULL DEFAULT '0',
  `stickerupdate` int(2) NOT NULL DEFAULT '0',
  `messenger` int(2) NOT NULL DEFAULT '0',
  `video` varchar(100) NOT NULL,
  `type` varchar(20) NOT NULL,
  `artist` varchar(50) NOT NULL,
  `feel_good` int(9) NOT NULL DEFAULT '0',
  `feel_soso` int(9) NOT NULL DEFAULT '0',
  `feel_bad` int(9) NOT NULL DEFAULT '0',
  `feel_view` int(9) NOT NULL DEFAULT '0',
  `feel_day` varchar(20) NOT NULL,
  `feel_type` varchar(20) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=238 DEFAULT CHARSET=euckr;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `board_sticker`
--

DROP TABLE IF EXISTS `board_sticker`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `board_sticker` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `bid` int(11) unsigned NOT NULL,
  `name` varchar(20) NOT NULL,
  `sticker` varchar(90) NOT NULL,
  `wdate` datetime NOT NULL,
  `writer` varchar(20) NOT NULL,
  `messenger` int(2) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `idx` (`bid`)
) ENGINE=MyISAM AUTO_INCREMENT=15 DEFAULT CHARSET=euckr;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `comment`
--

DROP TABLE IF EXISTS `comment`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `comment` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `bid` int(11) unsigned NOT NULL,
  `name` varchar(20) NOT NULL,
  `comment` text NOT NULL,
  `wdate` datetime NOT NULL,
  `ip` varchar(15) NOT NULL,
  `reply` int(2) NOT NULL DEFAULT '0',
  `writer` varchar(20) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx` (`bid`)
) ENGINE=MyISAM AUTO_INCREMENT=33 DEFAULT CHARSET=euckr;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `friends`
--

DROP TABLE IF EXISTS `friends`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `friends` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(20) NOT NULL,
  `mate` varchar(20) NOT NULL,
  `identify` int(2) NOT NULL DEFAULT '1',
  `boardup` int(2) NOT NULL DEFAULT '1',
  `talkup` int(2) NOT NULL DEFAULT '0',
  `view` int(9) NOT NULL DEFAULT '0',
  `intimacy` int(9) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=euckr;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `friendtalk`
--

DROP TABLE IF EXISTS `friendtalk`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `friendtalk` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `bid` int(11) unsigned NOT NULL,
  `name` varchar(20) NOT NULL,
  `comment` text NOT NULL,
  `wdate` datetime NOT NULL,
  `ip` varchar(15) NOT NULL,
  `writer` varchar(20) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx` (`bid`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=euckr;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `talkupdate`
--

DROP TABLE IF EXISTS `talkupdate`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `talkupdate` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `bid` int(11) unsigned NOT NULL,
  `name` varchar(20) NOT NULL,
  `talkup` int(2) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `idx` (`bid`)
) ENGINE=MyISAM AUTO_INCREMENT=241 DEFAULT CHARSET=euckr;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `term_compromise`
--

DROP TABLE IF EXISTS `term_compromise`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `term_compromise` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `profno` varchar(40) NOT NULL,
  `opponent` varchar(40) NOT NULL,
  `message` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=euckr;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `term_course`
--

DROP TABLE IF EXISTS `term_course`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `term_course` (
  `cno` varchar(40) NOT NULL,
  `cname` varchar(100) NOT NULL,
  `credit` int(1) NOT NULL,
  `examdate` varchar(40) DEFAULT NULL,
  `examtime` varchar(40) DEFAULT NULL,
  PRIMARY KEY (`cno`)
) ENGINE=MyISAM DEFAULT CHARSET=euckr;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `term_enrol`
--

DROP TABLE IF EXISTS `term_enrol`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `term_enrol` (
  `sno` varchar(40) NOT NULL,
  `cno` varchar(40) NOT NULL,
  `grade` char(1) DEFAULT NULL,
  PRIMARY KEY (`sno`,`cno`)
) ENGINE=MyISAM DEFAULT CHARSET=euckr;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `term_lecture`
--

DROP TABLE IF EXISTS `term_lecture`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `term_lecture` (
  `profno` varchar(40) NOT NULL,
  `cno` varchar(40) NOT NULL,
  `lectime` varchar(40) NOT NULL,
  `lecroom` varchar(40) NOT NULL,
  `lecdate` varchar(40) NOT NULL,
  PRIMARY KEY (`profno`,`cno`)
) ENGINE=MyISAM DEFAULT CHARSET=euckr;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `term_lms`
--

DROP TABLE IF EXISTS `term_lms`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `term_lms` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sno` varchar(40) NOT NULL,
  `cno` varchar(40) NOT NULL,
  `lmsdata` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=euckr;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `term_professor`
--

DROP TABLE IF EXISTS `term_professor`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `term_professor` (
  `profno` varchar(40) NOT NULL,
  `profname` varchar(100) NOT NULL,
  `dept` varchar(60) NOT NULL,
  PRIMARY KEY (`profno`)
) ENGINE=MyISAM DEFAULT CHARSET=euckr;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `term_student`
--

DROP TABLE IF EXISTS `term_student`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `term_student` (
  `sno` varchar(40) NOT NULL,
  `sname` varchar(100) NOT NULL,
  `year` int(1) DEFAULT NULL,
  `dept` varchar(60) NOT NULL,
  `issuedate` varchar(40) DEFAULT NULL,
  `issuetime` varchar(40) DEFAULT NULL,
  PRIMARY KEY (`sno`)
) ENGINE=MyISAM DEFAULT CHARSET=euckr;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `user`
--

DROP TABLE IF EXISTS `user`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `user` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(20) NOT NULL,
  `passwd` varchar(100) NOT NULL,
  `email` varchar(30) NOT NULL,
  `ip` varchar(15) NOT NULL,
  `original` varchar(20) NOT NULL,
  `identify` int(2) NOT NULL DEFAULT '1',
  `view_id` text NOT NULL,
  `view_total` int(9) NOT NULL DEFAULT '0',
  `view_like` text NOT NULL,
  `user_background` varchar(100) NOT NULL,
  `advertise` int(2) NOT NULL DEFAULT '0',
  `today_feel` varchar(20) NOT NULL,
  `feel_day` varchar(20) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=37 DEFAULT CHARSET=euckr;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `user_sticker`
--

DROP TABLE IF EXISTS `user_sticker`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `user_sticker` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(20) NOT NULL,
  `sticker` varchar(90) NOT NULL,
  `wdate` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=euckr;
SET character_set_client = @saved_cs_client;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2018-07-04  7:52:25
