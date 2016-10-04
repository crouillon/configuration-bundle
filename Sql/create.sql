CREATE TABLE `bbx_configuration` (
  `uid` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `site` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `label` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `elements` longtext COLLATE utf8_unicode_ci NOT NULL COMMENT '(DC2Type:array)',
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`uid`),
  UNIQUE KEY `UKEY_SITE_LABEL` (`site`,`label`),
  KEY `KEY_SITE` (`site`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci