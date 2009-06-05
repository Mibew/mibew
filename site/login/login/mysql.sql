#
# Table structure for table 'members'
#


CREATE TABLE `members` (
  `member_id` int(11) unsigned NOT NULL auto_increment,
  `firstname` varchar(100) default NULL,
  `lastname` varchar(100) default NULL,
`email` varchar(100) default NULL,
 `city` varchar(100) default NULL,
`state` varchar(100) default NULL,
`country` varchar(100) default NULL,
`phone` varchar(100) default NULL,
`login` varchar(100) NOT NULL default '',
`passwd` varchar(32) NOT NULL default '',
  PRIMARY KEY  (`member_id`)
) TYPE=MyISAM;

