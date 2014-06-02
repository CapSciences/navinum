<td class="sf_admin_text sf_admin_list_td_libelle">
  <?php echo $interactif->getLibelle() ?>
</td>
<td class="sf_admin_text sf_admin_list_td_categorie">
  <?php echo $interactif->getCategorie() ?>
</td>
<td class="sf_admin_text sf_admin_list_td_source_type">
  <?php echo $interactif->getSourceType() ?>
</td>
<td class="sf_admin_text sf_admin_list_td_source_type">
  <?php echo $interactif->getMarkets() ?>
</td>
<td class="sf_admin_boolean sf_admin_list_td_ordre">
  <?php echo  $interactif->getOrdre();?>
</td>
<td class="sf_admin_date sf_admin_list_td_created_at">
  <?php echo false !== strtotime($interactif->getCreatedAt()) ? format_date($interactif->getCreatedAt(), "dd/MM/yyyy HH:mm") : '&nbsp;' ?>
</td>
<td class="sf_admin_date sf_admin_list_td_updated_at">
  <?php echo false !== strtotime($interactif->getUpdatedAt()) ? format_date($interactif->getUpdatedAt(), "dd/MM/yyyy HH:mm") : '&nbsp;' ?>
</td>
