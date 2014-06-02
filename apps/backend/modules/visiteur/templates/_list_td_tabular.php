

<td class="sf_admin_text sf_admin_list_td_nom">
  <?php echo $visiteur->getNom() ?>
</td>
<td class="sf_admin_text sf_admin_list_td_prenom">
  <?php echo $visiteur->getPrenom() ?>
</td>
<td class="sf_admin_text sf_admin_list_td_pseudo_son">
  <?php echo $visiteur->getPseudoSon() ?>
</td>
<td class="sf_admin_text sf_admin_list_td_password_son">
  <?php echo $visiteur->getPasswordSon() ?>
</td>
<td class="sf_admin_date sf_admin_list_td_date_naissance">
  <?php echo false !== strtotime($visiteur->getDateNaissance()) ? format_date($visiteur->getDateNaissance(), "dd/MM/yyyy") : '&nbsp;' ?>
</td>
<td class="sf_admin_text sf_admin_list_td_code_postal">
  <?php echo $visiteur->getCodePostal() ?>
</td>
<td class="sf_admin_text sf_admin_list_td_ville">
  <?php echo $visiteur->getVille() ?>
</td>
<td class="sf_admin_text sf_admin_list_td_type">
  <?php echo $visiteur->getType() ?>
</td>
<td class="sf_admin_text sf_admin_list_td_email">
  <?php echo $visiteur->getEmail() ?>
</td>
<td class="sf_admin_text sf_admin_list_td_genre">
  <?php echo $visiteur->getGenre() ?>
</td>
<td class="sf_admin_text sf_admin_list_td_has_newsletter">
  <?php echo sprintf('<span class="bloc bool_%s {field: \'%s\'}" title="%s"></span>', $visiteur->getHasNewsletter() ? 'tick' : 'cross', 'is_active', __('Cliquer pour modifier')) ?>
</td>
<td class="sf_admin_text sf_admin_list_td_csp">
  <?php echo $visiteur->getCsp() ?>
</td>
<td class="sf_admin_boolean sf_admin_list_td_has_photo">
  <?php echo sprintf('<span class="bloc bool_%s {field: \'%s\'}" title="%s"></span>', $visiteur->getHasPhoto() ? 'tick' : 'cross', 'has_photo', __('Cliquer pour modifier')) ?>
</td>
<td class="sf_admin_boolean sf_admin_list_td_is_active">
  <?php echo sprintf('<span class="bloc bool_%s {field: \'%s\'}" title="%s"></span>', $visiteur->getIsActive() ? 'tick' : 'cross', 'is_active', __('Cliquer pour modifier')) ?>
</td>
<td class="sf_admin_date sf_admin_list_td_created_at">
  <?php echo false !== strtotime($visiteur->getCreatedAt()) ? format_date($visiteur->getCreatedAt(), "dd/MM/yyyy HH:mm") : '&nbsp;' ?>
</td>
<td class="sf_admin_date sf_admin_list_td_updated_at">
  <?php echo false !== strtotime($visiteur->getUpdatedAt()) ? format_date($visiteur->getUpdatedAt(), "dd/MM/yyyy HH:mm") : '&nbsp;' ?>
</td>
