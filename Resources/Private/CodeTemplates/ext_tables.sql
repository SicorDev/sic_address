# Auto generated on '{now}'! Do NOT edit !!!
<f:if condition="{settings.ttAddressMapping}">
<f:then>
#
# Table structure for table 'tt_address'
#
CREATE TABLE tt_address (
  sorting int(11) DEFAULT '0' NOT NULL,

	<f:for each="{properties}" as="field"><f:format.raw>{field.definition}</f:format.raw>,
	</f:for>
);
</f:then>
<f:else>
#
# Table structure for table 'tx_sicaddress_domain_model_address'
#
CREATE TABLE tx_sicaddress_domain_model_address (
	categories int(11) unsigned DEFAULT '0' NOT NULL,
    
	<f:for each="{properties}" as="field"><f:format.raw>{field.definition}</f:format.raw>,
	</f:for>
);
</f:else>
</f:if>

CREATE TABLE sys_category (
  sic_address_marker int(10) unsigned default '0',
);

<f:for each="{properties}" as="field">
<f:if condition="{0: '{field.type.title}'} == {0: 'mmtable'}">
#
# Table structure for table 'tx_sicaddress_domain_model_{field.title}'
#
CREATE TABLE tx_sicaddress_domain_model_{field.title} (
	title varchar(255) DEFAULT '' NOT NULL,

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
    external SMALLINT UNSIGNED DEFAULT 0 NOT NULL,
    is_list_label SMALLINT UNSIGNED DEFAULT 0 NOT NULL,
    settings text NOT NULL,
    tca_label varchar(255) DEFAULT '' NOT NULL,
    tca_override text NOT NULL,
    title varchar(255) DEFAULT '' NOT NULL,
    type varchar(255) DEFAULT '' NOT NULL,
    l10n_parent int(11) DEFAULT '0' NOT NULL,
);
