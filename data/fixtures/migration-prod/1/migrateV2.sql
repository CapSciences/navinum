SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL';

INSERT INTO `navinum_cs`.`contexte`(`guid`, `libelle`, `is_tosync`, `created_at`, `updated_at`) SELECT `guid`, `libelle`, `is_tosync`, `created_at`, `updated_at` FROM `navinum`.`contexte`;

INSERT INTO `navinum_cs`.`delete_log`(`guid`, `model_name`, `is_tosync`, `created_at`, `updated_at`) SELECT `guid`, `model_name`, `is_tosync`, `created_at`, `updated_at` FROM `navinum`.`delete_log`;

INSERT INTO `navinum_cs`.`exposition`(`guid`, `libelle`, `contexte_id`, `organisateur_editeur_id`, `organisateur_diffuseur_id`, `start_at`, `end_at`, `is_tosync`, `created_at`, `updated_at`) SELECT `guid`, `libelle`, `contexte_id`, `organisateur_editeur_id`, `organisateur_diffuseur_id`, `start_at`, `end_at`, `is_tosync`, `created_at`, `updated_at` FROM `navinum`.`exposition`;

INSERT INTO `navinum_cs`.`exposition_parcours`(`parcours_id`, `exposition_id`, `is_tosync`, `created_at`, `updated_at`) SELECT `parcours_id`, `exposition_id`, `is_tosync`, `created_at`, `updated_at` FROM `navinum`.`exposition_parcours`;

INSERT INTO `navinum_cs`.`exposition_visiteurneeds`(`guid`, `exposition_id`, `has_genre`, `has_date_naissance`, `has_code_postal`, `has_ville`, `has_adresse`, `has_prenom`, `has_nom`, `has_num_mobile`, `has_facebook_id`, `has_google_id`, `has_twitter_id`, `has_flickr_id`, `has_dailymotion_id`, `is_tosync`, `created_at`, `updated_at`) SELECT `guid`, `exposition_id`, `has_genre`, `has_date_naissance`, `has_code_postal`, `has_ville`, `has_adresse`, `has_prenom`, `has_nom`, `has_num_mobile`, `has_facebook_id`, `has_google_id`, `has_twitter_id`, `has_flickr_id`, `has_dailymotion_id`, `is_tosync`, `created_at`, `updated_at` FROM `navinum`.`exposition_visiteurneeds`;

INSERT INTO `navinum_cs`.`preferencemedia`(`guid`, `libelle`, `is_tosync`, `created_at`, `updated_at`) SELECT `guid`, `libelle`, `is_tosync`, `created_at`, `updated_at` FROM `navinum`.`handicapemedia`;

INSERT INTO `navinum_cs`.`interactif`(`guid`, `libelle`, `url_fichier_interactif`, `url_pierre_de_rosette`, `url_illustration`, `url_interactif_type`, `url_interactif_choice`, `url_visiteur_type`, `url_start_at`, `url_start_at_type`, `url_end_at`, `url_end_at_type`, `is_visiteur_needed`, `is_logvisite_needed`, `is_logvisite_verbose_needed`, `is_parcours_needed`, `is_tosync`, `created_at`, `updated_at`) SELECT `guid`, `libelle`, `url_fichier_interactif`, `url_pierre_de_rosette`, `url_illustration`, `url_intertactif_type`, `url_intertactif_choice`, `url_visiteur_type`, `url_start_at`, `url_start_at_type`, `url_end_at`, `url_end_at_type`, `is_visiteur_needed`, `is_logvisite_needed`, `is_logvisite_verbose_needed`, `is_parcours_needed`, `is_tosync`, `created_at`, `updated_at` FROM `navinum`.`interactif`;


INSERT INTO `navinum_cs`.`langue`(`guid`, `libelle`, `short_libelle`, `is_tosync`, `created_at`, `updated_at`) SELECT `guid`, `libelle`, `short_libelle`, `is_tosync`, `created_at`, `updated_at` FROM `navinum`.`langue`;


INSERT INTO `navinum_cs`.`log_visite`(`guid`, `interactif_id`, `visite_id`, `visiteur_id`, `start_at`, `end_at`, `resultats`, `is_tosync`, `created_at`, `updated_at`) SELECT md5( CURRENT_TIMESTAMP() * rand() *rand() * rand()), `interactif_id`, `visite_id`, `visiteur_id`, `start_at`, CASE WHEN `end_at`='0000-00-00 00:00:00' THEN NULL ELSE `end_at` END, `resultats`, `is_tosync`, `created_at`, `updated_at` FROM `navinum`.`log_visite`;

INSERT INTO `navinum_cs`.`organisateur`(`guid`, `libelle`, `is_tosync`, `created_at`, `updated_at`) SELECT `guid`, `libelle`, `is_tosync`, `created_at`, `updated_at` FROM `navinum`.`organisateur`;

INSERT INTO `navinum_cs`.`parcours`(`guid`, `libelle`, `is_tosync`, `created_at`, `updated_at`) SELECT `guid`, `libelle`, `is_tosync`, `created_at`, `updated_at` FROM `navinum`.`parcours`;

INSERT INTO `navinum_cs`.`parcours_interactif`(`interactif_id`, `parcours_id`, `num_order`, `is_tosync`, `created_at`, `updated_at`) SELECT `interactif_id`, `parcours_id`, `num_order`, `is_tosync`, `created_at`, `updated_at` FROM `navinum`.`parcours_interactif`;

INSERT IGNORE INTO `navinum_cs`.`parcours_interactif`(`interactif_id`, `parcours_id`, `num_order`, `is_tosync`, `created_at`, `updated_at`) SELECT `interactif_id`, `parcours_id`, `num_order`, `is_tosync`, `created_at`, `updated_at` FROM `navinum`.`parcours_interactif`;

INSERT IGNORE INTO `navinum_cs`.`parcours_interactif`(`interactif_id`, `parcours_id`, `num_order`, `is_tosync`, `created_at`, `updated_at`) SELECT `interactif_id`, `parcours_id`, `num_order`, `is_tosync`, `created_at`, `updated_at` FROM `navinum`.`parcours_interactif`;


INSERT INTO `navinum_cs`.`sf_guard_permission`(`id`, `name`, `description`, `is_tosync`, `created_at`, `updated_at`) SELECT `id`, `name`, `description`, `is_tosync`, `created_at`, `updated_at` FROM `navinum`.`sf_guard_permission`;

INSERT INTO `navinum_cs`.`sf_guard_remember_key`(`id`, `user_id`, `remember_key`, `ip_address`, `is_tosync`, `created_at`, `updated_at`) SELECT `id`, `user_id`, `remember_key`, `ip_address`, `is_tosync`, `created_at`, `updated_at` FROM `navinum`.`sf_guard_remember_key`;

INSERT INTO `navinum_cs`.`sf_guard_user`(`first_name`, `last_name`, `email_address`, `username`, `algorithm`, `salt`, `password`, `is_active`, `is_super_admin`, `last_login`, `id`, `is_tosync`, `created_at`, `updated_at`) SELECT `first_name`, `last_name`, `email_address`, `username`, `algorithm`, `salt`, `password`, `is_active`, `is_super_admin`, `last_login`, `id`, `is_tosync`, `created_at`, `updated_at` FROM `navinum`.`sf_guard_user`;


INSERT IGNORE INTO `navinum_cs`.`sync_log`(`guid`, `from_datetime_sync`, `to_datetime_sync`, `origin`, `is_done`) SELECT `guid`, `from_datetime_sync`, `to_datetime_sync`, `origin`, `is_done` FROM `navinum`.`sync_log`;

INSERT INTO `navinum_cs`.`user_exposition`(`user_id`, `exposition_id`, `is_tosync`, `created_at`, `updated_at`) SELECT `user_id`, `exposition_id`, `is_tosync`, `created_at`, `updated_at` FROM `navinum`.`user_exposition`;

INSERT INTO `navinum_cs`.`visite`(`guid`, `visiteur_id`, `navinum_id`, `exposition_id`, `parcours_id`, `is_tosync`, `created_at`, `updated_at`) SELECT `guid`, `visiteur_id`, `navinum_id`, `exposition_id`, `parcours_id`, `is_tosync`, `created_at`, `updated_at` FROM `navinum`.`visite`;

INSERT INTO `navinum_cs`.`visiteur`(`guid`, `password_son`, `email`, `contexte_creation_id`, `langue_id`, `genre`, `date_naissance`, `adresse`, `code_postal`, `ville`, `prenom`, `nom`, `pseudo_son`, `url_avatar`, `num_mobile`, `facebook_id`, `google_id`, `twitter_id`, `flickr_id`, `dailymotion_id`, `is_active`, `is_tosync`, `created_at`, `updated_at`) SELECT `guid`, `password_son`, `email`, `contexte_creation_id`, `langue_id`, `genre`, CASE WHEN `date_naissance`='0000-00-00' THEN NULL ELSE `date_naissance` END, `adresse`, `code_postal`, `ville`, `prenom`, `nom`, `pseudo_son`, `url_avatar`, `num_mobile`, `facebook_id`, `google_id`, `twitter_id`, `flickr_id`, `dailymotion_id`, `is_active`, `is_tosync`, `created_at`, `updated_at` FROM `navinum`.`visiteur`;

UPDATE visiteur SET password_son = md5( password_son );

SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;