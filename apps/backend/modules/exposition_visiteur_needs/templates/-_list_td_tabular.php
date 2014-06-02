<td class="sf_admin_foreignkey sf_admin_list_td_exposition_id">
  <?php echo $exposition_visiteur_needs->getExposition()->getLibelle() ?>
</td>
<td class="sf_admin_text sf_admin_list_td_preference_media_list">
  <?php echo get_partial('exposition_visiteur_needs/langue_list', array('type' => 'list', 'exposition_visiteur_needs' => $exposition_visiteur_needs)) ?>
</td>
<td class="sf_admin_boolean sf_admin_list_td_has_genre">
  <?php echo sprintf('<span class="bloc bool_%s {field: \'%s\'}" title="%s"></span>', $exposition_visiteur_needs->getHasGenre() ? 'tick' : 'cross', 'has_genre', __('')) ?>
</td>
<td class="sf_admin_boolean sf_admin_list_td_has_date_naissance">
  <?php echo sprintf('<span class="bloc bool_%s {field: \'%s\'}" title="%s"></span>', $exposition_visiteur_needs->getHasDateNaissance() ? 'tick' : 'cross', 'has_date_naissance', __('')) ?>
</td>
<td class="sf_admin_boolean sf_admin_list_td_has_code_postal">
  <?php echo sprintf('<span class="bloc bool_%s {field: \'%s\'}" title="%s"></span>', $exposition_visiteur_needs->getHasCodePostal() ? 'tick' : 'cross', 'has_code_postal', __('')) ?>
</td>
<td class="sf_admin_boolean sf_admin_list_td_has_ville">
  <?php echo sprintf('<span class="bloc bool_%s {field: \'%s\'}" title="%s"></span>', $exposition_visiteur_needs->getHasVille() ? 'tick' : 'cross', 'has_ville', __('')) ?>
</td>
<td class="sf_admin_boolean sf_admin_list_td_has_adresse">
  <?php echo sprintf('<span class="bloc bool_%s {field: \'%s\'}" title="%s"></span>', $exposition_visiteur_needs->getHasAdresse() ? 'tick' : 'cross', 'has_adresse', __('')) ?>
</td>
<td class="sf_admin_boolean sf_admin_list_td_has_prenom">
  <?php echo sprintf('<span class="bloc bool_%s {field: \'%s\'}" title="%s"></span>', $exposition_visiteur_needs->getHasPrenom() ? 'tick' : 'cross', 'has_prenom', __('')) ?>
</td>
<td class="sf_admin_boolean sf_admin_list_td_has_nom">
  <?php echo sprintf('<span class="bloc bool_%s {field: \'%s\'}" title="%s"></span>', $exposition_visiteur_needs->getHasNom() ? 'tick' : 'cross', 'has_nom', __('')) ?>
</td>
<td class="sf_admin_boolean sf_admin_list_td_has_num_mobile">
  <?php echo sprintf('<span class="bloc bool_%s {field: \'%s\'}" title="%s"></span>', $exposition_visiteur_needs->getHasNumMobile() ? 'tick' : 'cross', 'has_num_mobile', __('')) ?>
</td>
<td class="sf_admin_boolean sf_admin_list_td_has_facebook_id">
  <?php echo sprintf('<span class="bloc bool_%s {field: \'%s\'}" title="%s"></span>', $exposition_visiteur_needs->getHasFacebookId() ? 'tick' : 'cross', 'has_facebook_id', __('')) ?>
</td>
<td class="sf_admin_boolean sf_admin_list_td_has_google_id">
  <?php echo sprintf('<span class="bloc bool_%s {field: \'%s\'}" title="%s"></span>', $exposition_visiteur_needs->getHasGoogleId() ? 'tick' : 'cross', 'has_google_id', __('')) ?>
</td>
<td class="sf_admin_boolean sf_admin_list_td_has_twitter_id">
  <?php echo sprintf('<span class="bloc bool_%s {field: \'%s\'}" title="%s"></span>', $exposition_visiteur_needs->getHasTwitterId() ? 'tick' : 'cross', 'has_twitter_id', __('')) ?>
</td>
<td class="sf_admin_boolean sf_admin_list_td_has_flickr_id">
  <?php echo sprintf('<span class="bloc bool_%s {field: \'%s\'}" title="%s"></span>', $exposition_visiteur_needs->getHasFlickrId() ? 'tick' : 'cross', 'has_flickr_id', __('')) ?>
</td>
<td class="sf_admin_boolean sf_admin_list_td_has_dailymotion_id">
  <?php echo sprintf('<span class="bloc bool_%s {field: \'%s\'}" title="%s"></span>', $exposition_visiteur_needs->getHasDailymotionId() ? 'tick' : 'cross', 'has_dailymotion_id', __('')) ?>
</td>
<td class="sf_admin_text sf_admin_list_td_preference_media_list">
  <?php echo get_partial('exposition_visiteur_needs/preference_media_list', array('type' => 'list', 'exposition_visiteur_needs' => $exposition_visiteur_needs)) ?>
</td>
<td class="sf_admin_date sf_admin_list_td_created_at">
  <?php echo false !== strtotime($exposition_visiteur_needs->getCreatedAt()) ? format_date($exposition_visiteur_needs->getCreatedAt(), "dd/MM/yyyy HH:mm") : '&nbsp;' ?>
</td>
<td class="sf_admin_date sf_admin_list_td_updated_at">
  <?php echo false !== strtotime($exposition_visiteur_needs->getUpdatedAt()) ? format_date($exposition_visiteur_needs->getUpdatedAt(), "dd/MM/yyyy HH:mm") : '&nbsp;' ?>
</td>
