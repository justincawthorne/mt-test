<?php
// run this sql to create your bouncer table
// $conn = your/database/connection;
$bouncer_sql = "
CREATE TABLE IF NOT EXISTS `bouncer` (
  `memberID` smallint(4) unsigned NOT NULL auto_increment,
  `email` varchar(120) NOT NULL default '',
  `password` varchar(10) NOT NULL default '',
  `username` varchar(25) default NULL,
  `firstname` varchar(25) NOT NULL default '',
  `surname` varchar(25) NOT NULL default '',
  `date_joined` datetime NOT NULL default '0000-00-00 00:00:00',
  `sub_expiry` datetime NOT NULL default '0000-00-00 00:00:00',
  `guest_flag` tinyint(1) default NULL,
  `guest_areas` varchar(50) default NULL,
  `last_login` datetime NOT NULL default '0000-00-00 00:00:00',
  `last_ip` varchar(16) default NULL,
  `last_sess` varchar(32) default NULL,
  PRIMARY KEY  (`memberID`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1

"; 
mysql_query( $bouncer_sql, $conn );
?>