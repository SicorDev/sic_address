#
# Table structure for table 'tx_sicaddress_domain_model_address'
#
CREATE TABLE tx_sicaddress_domain_model_address (

	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,

	categories int(11) unsigned DEFAULT '0' NOT NULL,

	tstamp int(11) unsigned DEFAULT '0' NOT NULL,
	crdate int(11) unsigned DEFAULT '0' NOT NULL,
	cruser_id int(11) unsigned DEFAULT '0' NOT NULL,
	deleted tinyint(4) unsigned DEFAULT '0' NOT NULL,
	hidden tinyint(4) unsigned DEFAULT '0' NOT NULL,
	starttime int(11) unsigned DEFAULT '0' NOT NULL,
	endtime int(11) unsigned DEFAULT '0' NOT NULL,
	sorting int(11) DEFAULT '0' NOT NULL,

	sys_language_uid int(11) DEFAULT '0' NOT NULL,
	l10n_parent int(11) DEFAULT '0' NOT NULL,
	l10n_diffsource mediumblob,

	
	firstName varchar(255) DEFAULT '' NOT NULL,
	
	lastName varchar(255) DEFAULT '' NOT NULL,
	
	place varchar(255) DEFAULT '' NOT NULL,
	

	PRIMARY KEY (uid),
	KEY parent (pid),

 KEY language (l10n_parent,sys_language_uid)

);

#
# Table structure for table 'sys_category'
#
CREATE TABLE sys_category (

);

#
# Table structure for table 'tx_sicaddress_domain_model_address'
#
CREATE TABLE tx_sicaddress_domain_model_address (
	categories int(11) unsigned DEFAULT '0' NOT NULL,
);

#
# Table structure for table 'tx_sicaddress_address_category_mm'
#
CREATE TABLE tx_sicaddress_address_category_mm (
	uid_local int(11) unsigned DEFAULT '0' NOT NULL,
	uid_foreign int(11) unsigned DEFAULT '0' NOT NULL,
	sorting int(11) unsigned DEFAULT '0' NOT NULL,
	sorting_foreign int(11) unsigned DEFAULT '0' NOT NULL,

	KEY uid_local (uid_local),
	KEY uid_foreign (uid_foreign)
);
