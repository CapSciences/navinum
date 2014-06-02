ALTER TABLE visiteur_medaille
ADD COLUMN connection varchar(255);

ALTER TABLE visiteur_medaille
MODIFY connection varchar(255) default '';

ALTER TABLE exposition
MODIFY url_illustration varchar(255);

ALTER TABLE exposition
MODIFY url_studio varchar(255);

ALTER TABLE medaille_type
ADD COLUMN description LONGTEXT;


ALTER TABLE `medaille_type` CHANGE `description` `description` LONGTEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;

ALTER TABLE `visiteur_medaille` CHANGE `connection` `connection` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;

/*
ROLLBACK AU CAS OU
ALTER TABLE visiteur_medaille drop column connection;
ALTER TABLE exposition modify  url_illustration varchar(128);
ALTER TABLE exposition modify  url_studio varchar(128);
ALTER TABLE visiteur_medaille drop column medaille_type;
*/
