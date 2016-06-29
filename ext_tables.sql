
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
	fe_group varchar(100) DEFAULT '0' NOT NULL,

	sys_language_uid int(11) DEFAULT '0' NOT NULL,
	l10n_parent int(11) DEFAULT '0' NOT NULL,
	l10n_diffsource mediumblob,

	
	gender varchar(255) DEFAULT '' NOT NULL,
	
	name varchar(255) DEFAULT '' NOT NULL,
	
	first_name varchar(255) DEFAULT '' NOT NULL,
	
	middle_name varchar(255) DEFAULT '' NOT NULL,
	
	last_name varchar(255) DEFAULT '' NOT NULL,
	
	birthday varchar(255) DEFAULT '' NOT NULL,
	
	title varchar(255) DEFAULT '' NOT NULL,
	
	email varchar(255) DEFAULT '' NOT NULL,
	
	phone varchar(255) DEFAULT '' NOT NULL,
	
	mobile varchar(255) DEFAULT '' NOT NULL,
	
	www varchar(255) DEFAULT '' NOT NULL,
	
	address varchar(255) DEFAULT '' NOT NULL,
	
	building varchar(255) DEFAULT '' NOT NULL,
	
	room varchar(255) DEFAULT '' NOT NULL,
	
	company varchar(255) DEFAULT '' NOT NULL,
	
	position varchar(255) DEFAULT '' NOT NULL,
	
	city varchar(255) DEFAULT '' NOT NULL,
	
	zip varchar(255) DEFAULT '' NOT NULL,
	
	region varchar(255) DEFAULT '' NOT NULL,
	
	country varchar(255) DEFAULT '' NOT NULL,
	
	image varchar(255) DEFAULT '' NOT NULL,
	
	fax varchar(255) DEFAULT '' NOT NULL,
	
	description varchar(255) DEFAULT '' NOT NULL,
	
	skype varchar(255) DEFAULT '' NOT NULL,
	
	twitter varchar(255) DEFAULT '' NOT NULL,
	
	facebook varchar(255) DEFAULT '' NOT NULL,
	
	linkedin varchar(255) DEFAULT '' NOT NULL,
	
	categories varchar(255) DEFAULT '' NOT NULL,
	
	latitude varchar(255) DEFAULT '' NOT NULL,
	
	longitude varchar(255) DEFAULT '' NOT NULL,
	
	hans int(11) unsigned DEFAULT '0' NOT NULL,
	

	PRIMARY KEY (uid),
	KEY parent (pid),

 KEY language (l10n_parent,sys_language_uid)

);

#
# Table structure for table 'tx_sicaddress_domain_model_domainproperty'
#
CREATE TABLE tx_sicaddress_domain_model_domainproperty (

	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,

	title varchar(255) DEFAULT '' NOT NULL,
	tca_label varchar(255) DEFAULT '' NOT NULL,
	tca_override text NOT NULL,
	settings text NOT NULL,
	is_list_label tinyint(1) unsigned DEFAULT '0' NOT NULL,
  external tinyint(4) unsigned DEFAULT '0' NOT NULL,
	type varchar(255) DEFAULT '' NOT NULL,

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

	PRIMARY KEY (uid),
	KEY parent (pid),

	KEY language (l10n_parent,sys_language_uid)

);

#
# Table structure for table 'sys_category'
#
CREATE TABLE sys_category (
	tx_extbase_type varchar(255) DEFAULT '0' NOT NULL,
);
