
CREATE TABLE `amazoniTest`.`amazoni4_languages` (
  `langID` smallint(3) NOT NULL AUTO_INCREMENT,
  `language` char(49) DEFAULT NULL,
  `code` char(2) DEFAULT NULL,
  `active` tinyint(1) DEFAULT 0,	
  PRIMARY KEY (`langID`)
) ENGINE=InnoDB AUTO_INCREMENT=137 DEFAULT CHARSET=utf8;


INSERT INTO `amazoniTest`.`amazoni4_languages` VALUES ('1', 'English', 'en', 0);
INSERT INTO `amazoniTest`.`amazoni4_languages` VALUES ('2', 'Afar', 'aa', 0);
INSERT INTO `amazoniTest`.`amazoni4_languages` VALUES ('3', 'Abkhazian', 'ab', 0);
INSERT INTO `amazoniTest`.`amazoni4_languages` VALUES ('4', 'Afrikaans', 'af', 0);
INSERT INTO `amazoniTest`.`amazoni4_languages` VALUES ('5', 'Amharic', 'am', 0);
INSERT INTO `amazoniTest`.`amazoni4_languages` VALUES ('6', 'Arabic', 'ar', 0);
INSERT INTO `amazoniTest`.`amazoni4_languages` VALUES ('7', 'Assamese', 'as', 0);
INSERT INTO `amazoniTest`.`amazoni4_languages` VALUES ('8', 'Aymara', 'ay', 0);
INSERT INTO `amazoniTest`.`amazoni4_languages` VALUES ('9', 'Azerbaijani', 'az', 0);
INSERT INTO `amazoniTest`.`amazoni4_languages` VALUES ('10', 'Bashkir', 'ba', 0);
INSERT INTO `amazoniTest`.`amazoni4_languages` VALUES ('11', 'Byelorussian', 'be', 0);
INSERT INTO `amazoniTest`.`amazoni4_languages` VALUES ('12', 'Bulgarian', 'bg', 0);
INSERT INTO `amazoniTest`.`amazoni4_languages` VALUES ('13', 'Bihari', 'bh', 0);
INSERT INTO `amazoniTest`.`amazoni4_languages` VALUES ('14', 'Bislama', 'bi', 0);
INSERT INTO `amazoniTest`.`amazoni4_languages` VALUES ('15', 'Bengali/Bangla', 'bn', 0);
INSERT INTO `amazoniTest`.`amazoni4_languages` VALUES ('16', 'Tibetan', 'bo', 0);
INSERT INTO `amazoniTest`.`amazoni4_languages` VALUES ('17', 'Breton', 'br', 0);
INSERT INTO `amazoniTest`.`amazoni4_languages` VALUES ('18', 'Catalan', 'ca', 0);
INSERT INTO `amazoniTest`.`amazoni4_languages` VALUES ('19', 'Corsican', 'co', 0);
INSERT INTO `amazoniTest`.`amazoni4_languages` VALUES ('20', 'Czech', 'cs', 0);
INSERT INTO `amazoniTest`.`amazoni4_languages` VALUES ('21', 'Welsh', 'cy', 0);
INSERT INTO `amazoniTest`.`amazoni4_languages` VALUES ('22', 'Danish', 'da', 0);
INSERT INTO `amazoniTest`.`amazoni4_languages` VALUES ('23', 'German', 'de', 0);
INSERT INTO `amazoniTest`.`amazoni4_languages` VALUES ('24', 'Bhutani', 'dz', 0);
INSERT INTO `amazoniTest`.`amazoni4_languages` VALUES ('25', 'Greek', 'el', 0);
INSERT INTO `amazoniTest`.`amazoni4_languages` VALUES ('26', 'Esperanto', 'eo', 0);
INSERT INTO `amazoniTest`.`amazoni4_languages` VALUES ('27', 'Spanish', 'es', 0);
INSERT INTO `amazoniTest`.`amazoni4_languages` VALUES ('28', 'Estonian', 'et', 0);
INSERT INTO `amazoniTest`.`amazoni4_languages` VALUES ('29', 'Basque', 'eu', 0);
INSERT INTO `amazoniTest`.`amazoni4_languages` VALUES ('30', 'Persian', 'fa', 0);
INSERT INTO `amazoniTest`.`amazoni4_languages` VALUES ('31', 'Finnish', 'fi', 0);
INSERT INTO `amazoniTest`.`amazoni4_languages` VALUES ('32', 'Fiji', 'fj', 0);
INSERT INTO `amazoniTest`.`amazoni4_languages` VALUES ('33', 'Faeroese', 'fo', 0);
INSERT INTO `amazoniTest`.`amazoni4_languages` VALUES ('34', 'French', 'fr', 0);
INSERT INTO `amazoniTest`.`amazoni4_languages` VALUES ('35', 'Frisian', 'fy', 0);
INSERT INTO `amazoniTest`.`amazoni4_languages` VALUES ('36', 'Irish', 'ga', 0);
INSERT INTO `amazoniTest`.`amazoni4_languages` VALUES ('37', 'Scots/Gaelic', 'gd', 0);
INSERT INTO `amazoniTest`.`amazoni4_languages` VALUES ('38', 'Galician', 'gl', 0);
INSERT INTO `amazoniTest`.`amazoni4_languages` VALUES ('39', 'Guarani', 'gn', 0);
INSERT INTO `amazoniTest`.`amazoni4_languages` VALUES ('40', 'Gujarati', 'gu', 0);
INSERT INTO `amazoniTest`.`amazoni4_languages` VALUES ('41', 'Hausa', 'ha', 0);
INSERT INTO `amazoniTest`.`amazoni4_languages` VALUES ('42', 'Hindi', 'hi', 0);
INSERT INTO `amazoniTest`.`amazoni4_languages` VALUES ('43', 'Croatian', 'hr', 0);
INSERT INTO `amazoniTest`.`amazoni4_languages` VALUES ('44', 'Hungarian', 'hu', 0);
INSERT INTO `amazoniTest`.`amazoni4_languages` VALUES ('45', 'Armenian', 'hy', 0);
INSERT INTO `amazoniTest`.`amazoni4_languages` VALUES ('46', 'Interlingua', 'ia', 0);
INSERT INTO `amazoniTest`.`amazoni4_languages` VALUES ('47', 'Interlingue', 'ie', 0);
INSERT INTO `amazoniTest`.`amazoni4_languages` VALUES ('48', 'Inupiak', 'ik', 0);
INSERT INTO `amazoniTest`.`amazoni4_languages` VALUES ('49', 'Indonesian', 'in', 0);
INSERT INTO `amazoniTest`.`amazoni4_languages` VALUES ('50', 'Icelandic', 'is', 0);
INSERT INTO `amazoniTest`.`amazoni4_languages` VALUES ('51', 'Italian', 'it', 0);
INSERT INTO `amazoniTest`.`amazoni4_languages` VALUES ('52', 'Hebrew', 'iw', 0);
INSERT INTO `amazoniTest`.`amazoni4_languages` VALUES ('53', 'Japanese', 'ja', 0);
INSERT INTO `amazoniTest`.`amazoni4_languages` VALUES ('54', 'Yiddish', 'ji', 0);
INSERT INTO `amazoniTest`.`amazoni4_languages` VALUES ('55', 'Javanese', 'jw', 0);
INSERT INTO `amazoniTest`.`amazoni4_languages` VALUES ('56', 'Georgian', 'ka', 0);
INSERT INTO `amazoniTest`.`amazoni4_languages` VALUES ('57', 'Kazakh', 'kk', 0);
INSERT INTO `amazoniTest`.`amazoni4_languages` VALUES ('58', 'Greenlandic', 'kl', 0);
INSERT INTO `amazoniTest`.`amazoni4_languages` VALUES ('59', 'Cambodian', 'km', 0);
INSERT INTO `amazoniTest`.`amazoni4_languages` VALUES ('60', 'Kannada', 'kn', 0);
INSERT INTO `amazoniTest`.`amazoni4_languages` VALUES ('61', 'Korean', 'ko', 0);
INSERT INTO `amazoniTest`.`amazoni4_languages` VALUES ('62', 'Kashmiri', 'ks', 0);
INSERT INTO `amazoniTest`.`amazoni4_languages` VALUES ('63', 'Kurdish', 'ku', 0);
INSERT INTO `amazoniTest`.`amazoni4_languages` VALUES ('64', 'Kirghiz', 'ky', 0);
INSERT INTO `amazoniTest`.`amazoni4_languages` VALUES ('65', 'Latin', 'la', 0);
INSERT INTO `amazoniTest`.`amazoni4_languages` VALUES ('66', 'Lingala', 'ln', 0);
INSERT INTO `amazoniTest`.`amazoni4_languages` VALUES ('67', 'Laothian', 'lo', 0);
INSERT INTO `amazoniTest`.`amazoni4_languages` VALUES ('68', 'Lithuanian', 'lt', 0);
INSERT INTO `amazoniTest`.`amazoni4_languages` VALUES ('69', 'Latvian/Lettish', 'lv', 0);
INSERT INTO `amazoniTest`.`amazoni4_languages` VALUES ('70', 'Malagasy', 'mg', 0);
INSERT INTO `amazoniTest`.`amazoni4_languages` VALUES ('71', 'Maori', 'mi', 0);
INSERT INTO `amazoniTest`.`amazoni4_languages` VALUES ('72', 'Macedonian', 'mk', 0);
INSERT INTO `amazoniTest`.`amazoni4_languages` VALUES ('73', 'Malayalam', 'ml', 0);
INSERT INTO `amazoniTest`.`amazoni4_languages` VALUES ('74', 'Mongolian', 'mn', 0);
INSERT INTO `amazoniTest`.`amazoni4_languages` VALUES ('75', 'Moldavian', 'mo', 0);
INSERT INTO `amazoniTest`.`amazoni4_languages` VALUES ('76', 'Marathi', 'mr', 0);
INSERT INTO `amazoniTest`.`amazoni4_languages` VALUES ('77', 'Malay', 'ms', 0);
INSERT INTO `amazoniTest`.`amazoni4_languages` VALUES ('78', 'Maltese', 'mt', 0);
INSERT INTO `amazoniTest`.`amazoni4_languages` VALUES ('79', 'Burmese', 'my', 0);
INSERT INTO `amazoniTest`.`amazoni4_languages` VALUES ('80', 'Nauru', 'na', 0);
INSERT INTO `amazoniTest`.`amazoni4_languages` VALUES ('81', 'Nepali', 'ne', 0);
INSERT INTO `amazoniTest`.`amazoni4_languages` VALUES ('82', 'Dutch', 'nl', 0);
INSERT INTO `amazoniTest`.`amazoni4_languages` VALUES ('83', 'Norwegian', 'no', 0);
INSERT INTO `amazoniTest`.`amazoni4_languages` VALUES ('84', 'Occitan', 'oc', 0);
INSERT INTO `amazoniTest`.`amazoni4_languages` VALUES ('85', '(Afan)/Oromoor/Oriya', 'om', 0);
INSERT INTO `amazoniTest`.`amazoni4_languages` VALUES ('86', 'Punjabi', 'pa', 0);
INSERT INTO `amazoniTest`.`amazoni4_languages` VALUES ('87', 'Polish', 'pl', 0);
INSERT INTO `amazoniTest`.`amazoni4_languages` VALUES ('88', 'Pashto/Pushto', 'ps', 0);
INSERT INTO `amazoniTest`.`amazoni4_languages` VALUES ('89', 'Portuguese', 'pt', 0);
INSERT INTO `amazoniTest`.`amazoni4_languages` VALUES ('90', 'Quechua', 'qu', 0);
INSERT INTO `amazoniTest`.`amazoni4_languages` VALUES ('91', 'Rhaeto-Romance', 'rm', 0);
INSERT INTO `amazoniTest`.`amazoni4_languages` VALUES ('92', 'Kirundi', 'rn', 0);
INSERT INTO `amazoniTest`.`amazoni4_languages` VALUES ('93', 'Romanian', 'ro', 0);
INSERT INTO `amazoniTest`.`amazoni4_languages` VALUES ('94', 'Russian', 'ru', 0);
INSERT INTO `amazoniTest`.`amazoni4_languages` VALUES ('95', 'Kinyarwanda', 'rw', 0);
INSERT INTO `amazoniTest`.`amazoni4_languages` VALUES ('96', 'Sanskrit', 'sa', 0);
INSERT INTO `amazoniTest`.`amazoni4_languages` VALUES ('97', 'Sindhi', 'sd', 0);
INSERT INTO `amazoniTest`.`amazoni4_languages` VALUES ('98', 'Sangro', 'sg', 0);
INSERT INTO `amazoniTest`.`amazoni4_languages` VALUES ('99', 'Serbo-Croatian', 'sh', 0);
INSERT INTO `amazoniTest`.`amazoni4_languages` VALUES ('100', 'Singhalese', 'si', 0);
INSERT INTO `amazoniTest`.`amazoni4_languages` VALUES ('101', 'Slovak', 'sk', 0);
INSERT INTO `amazoniTest`.`amazoni4_languages` VALUES ('102', 'Slovenian', 'sl', 0);
INSERT INTO `amazoniTest`.`amazoni4_languages` VALUES ('103', 'Samoan', 'sm', 0);
INSERT INTO `amazoniTest`.`amazoni4_languages` VALUES ('104', 'Shona', 'sn', 0);
INSERT INTO `amazoniTest`.`amazoni4_languages` VALUES ('105', 'Somali', 'so', 0);
INSERT INTO `amazoniTest`.`amazoni4_languages` VALUES ('106', 'Albanian', 'sq', 0);
INSERT INTO `amazoniTest`.`amazoni4_languages` VALUES ('107', 'Serbian', 'sr', 0);
INSERT INTO `amazoniTest`.`amazoni4_languages` VALUES ('108', 'Siswati', 'ss', 0);
INSERT INTO `amazoniTest`.`amazoni4_languages` VALUES ('109', 'Sesotho', 'st', 0);
INSERT INTO `amazoniTest`.`amazoni4_languages` VALUES ('110', 'Sundanese', 'su', 0);
INSERT INTO `amazoniTest`.`amazoni4_languages` VALUES ('111', 'Swedish', 'sv', 0);
INSERT INTO `amazoniTest`.`amazoni4_languages` VALUES ('112', 'Swahili', 'sw', 0);
INSERT INTO `amazoniTest`.`amazoni4_languages` VALUES ('113', 'Tamil', 'ta', 0);
INSERT INTO `amazoniTest`.`amazoni4_languages` VALUES ('114', 'Tegulu', 'te', 0);
INSERT INTO `amazoniTest`.`amazoni4_languages` VALUES ('115', 'Tajik', 'tg', 0);
INSERT INTO `amazoniTest`.`amazoni4_languages` VALUES ('116', 'Thai', 'th', 0);
INSERT INTO `amazoniTest`.`amazoni4_languages` VALUES ('117', 'Tigrinya', 'ti', 0);
INSERT INTO `amazoniTest`.`amazoni4_languages` VALUES ('118', 'Turkmen', 'tk', 0);
INSERT INTO `amazoniTest`.`amazoni4_languages` VALUES ('119', 'Tagalog', 'tl', 0);
INSERT INTO `amazoniTest`.`amazoni4_languages` VALUES ('120', 'Setswana', 'tn', 0);
INSERT INTO `amazoniTest`.`amazoni4_languages` VALUES ('121', 'Tonga', 'to', 0);
INSERT INTO `amazoniTest`.`amazoni4_languages` VALUES ('122', 'Turkish', 'tr', 0);
INSERT INTO `amazoniTest`.`amazoni4_languages` VALUES ('123', 'Tsonga', 'ts', 0);
INSERT INTO `amazoniTest`.`amazoni4_languages` VALUES ('124', 'Tatar', 'tt', 0);
INSERT INTO `amazoniTest`.`amazoni4_languages` VALUES ('125', 'Twi', 'tw', 0);
INSERT INTO `amazoniTest`.`amazoni4_languages` VALUES ('126', 'Ukrainian', 'uk', 0);
INSERT INTO `amazoniTest`.`amazoni4_languages` VALUES ('127', 'Urdu', 'ur', 0);
INSERT INTO `amazoniTest`.`amazoni4_languages` VALUES ('128', 'Uzbek', 'uz', 0);
INSERT INTO `amazoniTest`.`amazoni4_languages` VALUES ('129', 'Vietnamese', 'vi', 0);
INSERT INTO `amazoniTest`.`amazoni4_languages` VALUES ('130', 'Volapuk', 'vo', 0);
INSERT INTO `amazoniTest`.`amazoni4_languages` VALUES ('131', 'Wolof', 'wo', 0);
INSERT INTO `amazoniTest`.`amazoni4_languages` VALUES ('132', 'Xhosa', 'xh', 0);
INSERT INTO `amazoniTest`.`amazoni4_languages` VALUES ('133', 'Yoruba', 'yo', 0);
INSERT INTO `amazoniTest`.`amazoni4_languages` VALUES ('134', 'Chinese', 'zh', 0);
INSERT INTO `amazoniTest`.`amazoni4_languages` VALUES ('135', 'Zulu', 'zu', 0);


UPDATE `amazoniTest`.`amazoni4_languages` SET `active`='1' WHERE `langID`='1';
UPDATE `amazoniTest`.`amazoni4_languages` SET `active`='1' WHERE `langID`='34';
UPDATE `amazoniTest`.`amazoni4_languages` SET `active`='1' WHERE `langID`='23';
UPDATE `amazoniTest`.`amazoni4_languages` SET `active`='1' WHERE `langID`='27';