<f:if condition="{settings.ttAddressMapping}">
<f:then>
#
# Table structure for table 'tt_address'
#
CREATE TABLE tt_address (
  sorting int(11) DEFAULT '0' NOT NULL,
  
  t3ver_oid int(11) DEFAULT '0' NOT NULL,
  t3ver_wsid int(11) DEFAULT '0' NOT NULL,

	<f:for each="{properties}" as="field"><f:format.raw>{field.definition}</f:format.raw>,
	</f:for>
);
</f:then>
<f:else>
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
	l10n_state text,
    
	<f:for each="{properties}" as="field"><f:format.raw>{field.definition}</f:format.raw>,
	</f:for>

	PRIMARY KEY (uid),
	KEY parent (pid),
  KEY language (l10n_parent,sys_language_uid)
);
</f:else>
</f:if>

<f:for each="{properties}" as="field">
<f:if condition="{0: '{field.type.title}'} == {0: 'mmtable'}">
#
# Table structure for table 'tx_sicaddress_domain_model_{field.title}'
#
CREATE TABLE tx_sicaddress_domain_model_{field.title} (
	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,
	tstamp int(11) DEFAULT '0' NOT NULL,
	crdate int(11) DEFAULT '0' NOT NULL,
	cruser_id int(11) DEFAULT '0' NOT NULL,
	sys_language_uid int(11) DEFAULT '0' NOT NULL,
	l18n_parent int(11) DEFAULT '0' NOT NULL,
	l18n_diffsource mediumblob NOT NULL,
	deleted tinyint(4) DEFAULT '0' NOT NULL,
	hidden tinyint(4) DEFAULT '0' NOT NULL,
	starttime int(11) unsigned DEFAULT '0' NOT NULL,
	endtime int(11) unsigned DEFAULT '0' NOT NULL,
	title varchar(255) DEFAULT '' NOT NULL,

	PRIMARY KEY (uid),
	KEY parent (pid)
);

#
# Table structure for table 'tx_sicaddress_domain_model_address_{field.title}_mm'
#
CREATE TABLE tx_sicaddress_domain_model_address_{field.title}_mm (
  uid_local int(11) DEFAULT '0' NOT NULL,
  uid_foreign int(11) DEFAULT '0' NOT NULL,
  tablenames varchar(30) DEFAULT '' NOT NULL,
  sorting int(11) DEFAULT '0' NOT NULL,

  KEY uid_local (uid_local),
  KEY uid_foreign (uid_foreign)
);
</f:if>
</f:for>

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
	is_list_label SMALLINT UNSIGNED DEFAULT 0 NOT NULL,
	external SMALLINT UNSIGNED DEFAULT 0 NOT NULL,
	type varchar(255) DEFAULT '' NOT NULL,

	tstamp int(11) UNSIGNED DEFAULT '0' NOT NULL,
	crdate int(11) UNSIGNED DEFAULT '0' NOT NULL,
	cruser_id int(11) UNSIGNED DEFAULT '0' NOT NULL,
	deleted SMALLINT UNSIGNED DEFAULT 0 NOT NULL,
	hidden SMALLINT UNSIGNED DEFAULT 0 NOT NULL,
	starttime int(11) UNSIGNED DEFAULT '0' NOT NULL,
	endtime int(11) UNSIGNED DEFAULT '0' NOT NULL,
	sorting int(11) DEFAULT '0' NOT NULL,
	sys_language_uid int(11) DEFAULT '0' NOT NULL,
	l10n_parent int(11) DEFAULT '0' NOT NULL,
	l10n_diffsource mediumblob,
	l10n_state TEXT DEFAULT NULL,

	PRIMARY KEY (uid),
	KEY parent (pid),
	KEY language (l10n_parent,sys_language_uid)
);
