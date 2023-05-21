
#
# Table structure for table 'tx_sicaddress_domain_model_address'
#
CREATE TABLE tx_sicaddress_domain_model_address (

	categories int(11) unsigned DEFAULT '0' NOT NULL,
);


CREATE TABLE sys_category (
  sic_address_marker int(10) unsigned default '0',
);

#
# Table structure for table 'tx_sicaddress_domain_model_domainproperty'
#
CREATE TABLE tx_sicaddress_domain_model_domainproperty (

	title varchar(255) DEFAULT '' NOT NULL,
	tca_label varchar(255) DEFAULT '' NOT NULL,
	tca_override text NOT NULL,
	settings text NOT NULL,
	is_list_label SMALLINT UNSIGNED DEFAULT 0 NOT NULL,
	external SMALLINT UNSIGNED DEFAULT 0 NOT NULL,
	type varchar(255) DEFAULT '' NOT NULL,

);
