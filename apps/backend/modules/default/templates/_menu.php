<div class="menu">
	<ul class="sf-menu">
	

<li>
	<?php echo link_to('Accueil', 'default')?>
</li>

<?php if ($sf_user->hasPermission('commissaire') || $sf_user->hasPermission('admin')): ?>
<li>
<a href="#">Vip</a>
	<ul>
  	<li><?php echo link_to('Interactif', 'interactif')?>
  	<?php if ($sf_user->hasPermission('commissaire') || $sf_user->hasPermission('admin')): ?>
  	 <ul><li class="create"><?php echo link_to('Créer', 'interactif/new')?></li></ul>
  	<?php endif;?> 
  	</li>
  	<li><?php echo link_to('Parcours', 'parcours')?>
  	 <?php if ($sf_user->hasPermission('commissaire') || $sf_user->hasPermission('admin')): ?>
     <ul><li class="create"><?php echo link_to('Créer', 'parcours/new')?></li></ul>
    <?php endif;?> 
  	</li>
  	<li><?php echo link_to('Exposition', 'exposition')?>
  	  <?php if ($sf_user->hasPermission('commissaire') || $sf_user->hasPermission('admin')): ?>
  	   <ul><li class="create"><?php echo link_to('Créer', 'exposition/new')?></li></ul>
  	  <?php endif;?>
  	</li>
  </ul>
</li>

<li>
<a href="#">Visiteurs</a>
  <ul>
		<?php if ($sf_user->hasPermission('commissaire') || $sf_user->hasPermission('admin')): ?>
      <li><?php echo link_to('Visiteur', 'visiteur')?>
       <ul><li class="create"><?php echo link_to('Créer', 'visiteur/new')?></li></ul>
      </li>
    <?php endif; ?>
  	<?php if ($sf_user->hasPermission('commissaire') || $sf_user->hasPermission('admin')): ?>
      <li><?php echo link_to('Besoin profil visiteur', 'exposition_visiteur_needs')?>
       <ul><li class="create"><?php echo link_to('Créer', 'exposition_visiteur_needs/new')?></li></ul>
      </li>
    <?php endif; ?>
	</ul>
</li>  	

<li>
<a href="#">Gratification</a>
	<ul>
		<li><?php echo link_to('Xp', 'xp')?>
  	<?php if ($sf_user->hasPermission('commissaire') || $sf_user->hasPermission('admin')): ?>
  	 <ul><li class="create"><?php echo link_to('Créer', 'xp/new')?></li></ul>
  	<?php endif;?>
  	</li>
  	<li><?php echo link_to('Typologie', 'typologie')?>
  	<?php if ($sf_user->hasPermission('commissaire') || $sf_user->hasPermission('admin')): ?>
  	 <ul><li class="create"><?php echo link_to('Créer', 'typologie/new')?></li></ul>
  	<?php endif;?>
  	</li>


  	<li><?php echo link_to('Type médaille', 'medaille_type')?>
    <?php if ($sf_user->hasPermission('commissaire') || $sf_user->hasPermission('admin')): ?>
  	 <ul><li class="create"><?php echo link_to('Créer', 'medaille_type/new')?></li></ul>
    <?php endif;?> 
  	 </li>


  	<li><?php echo link_to('Médaille', 'medaille')?>
  	<?php if ($sf_user->hasPermission('commissaire') || $sf_user->hasPermission('admin')): ?>
  	 <ul><li class="create"><?php echo link_to('Créer', 'medaille/new')?></li></ul>
  	<?php endif;?> 
		</li>
		<li><?php echo link_to('Médaille Visiteur', 'visiteur_medaille')?>
  	<?php if ($sf_user->hasPermission('admin')): ?>
  	 <ul><li class="create"><?php echo link_to('Créer', 'visiteur_medaille/new')?></li></ul>
  	<?php endif;?> 
  	</li>
	</ul>
</li>
	
<li>
<a href="#">Rfid</a>
	<ul>
		<li><?php echo link_to('Rfid', 'rfid')?>
  	<?php if ($sf_user->hasPermission('commissaire') || $sf_user->hasPermission('admin')): ?>
  	 <ul><li class="create"><?php echo link_to('Créer', 'rfid/new')?></li></ul>
  	<?php endif;?> 
  	</li>
  	<li><?php echo link_to('Groupe rfid', 'rfid_groupe')?>
  	<?php if ($sf_user->hasPermission('commissaire') || $sf_user->hasPermission('admin')): ?>
  	 <ul><li class="create"><?php echo link_to('Créer', 'rfid_groupe/new')?></li></ul>
  	<?php endif;?> 
  	</li>
    <li><?php echo link_to('Groupe Visiteur rfid', 'rfid_groupe_visiteur')?>
    <?php if ($sf_user->hasPermission('commissaire') || $sf_user->hasPermission('admin')): ?>
     <ul><li class="create"><?php echo link_to('Créer', 'rfid_groupe_visiteur/new')?></li></ul>
    <?php endif;?> 
    </li>
	</ul>
</li>	


<li>
<a href="#">Flotte et périphériques</a>
	<ul>
		<li><?php echo link_to('Flotte', 'flotte')?>
  	<?php if ($sf_user->hasPermission('admin')): ?>
  	 <ul><li class="create"><?php echo link_to('Créer', 'flotte/new')?></li></ul>
  	<?php endif;?> 
  	</li>
  	<li><?php echo link_to('Peripherique', 'peripherique')?>
  	<?php if ($sf_user->hasPermission('admin')): ?>
  	 <ul><li class="create"><?php echo link_to('Créer', 'peripherique/new')?></li></ul>
  	<?php endif;?> 
  	</li>
	</ul>
</li>


<li>
<a href="#">Paramètres</a>
	<ul>
  	<?php if ($sf_user->hasPermission('admin')): ?>
	  	<li><?php echo link_to('Contexte', 'contexte')?>
	     <ul><li class="create"><?php echo link_to('Créer', 'contexte/new')?></li></ul>
	   </li>
	  <li><?php echo link_to('Organisateur', 'organisateur')?>
	     <ul><li class="create"><?php echo link_to('Créer', 'organisateur/new')?></li></ul>
	  </li>
    <?php endif; ?>
    <?php if ($sf_user->hasPermission('commissaire') || $sf_user->hasPermission('admin')): ?>
      <li><?php echo link_to('Csp', 'csp')?>
       <ul><li class="create"><?php echo link_to('Créer', 'csp/new')?></li></ul>
      </li>
    <?php endif; ?>
  	<?php if ($sf_user->hasPermission('commissaire') || $sf_user->hasPermission('admin')): ?>
      <li><?php echo link_to('Evenement', 'evenement')?>
       <ul><li class="create"><?php echo link_to('Créer', 'evenement/new')?></li></ul>
      </li>
    <?php endif; ?>
    <?php if ($sf_user->hasPermission('commissaire') || $sf_user->hasPermission('admin')): ?>
      <li><?php echo link_to('Langue', 'langue')?>
       <ul><li class="create"><?php echo link_to('Créer', 'langue/new')?></li></ul>
      </li>
    <?php endif; ?>
    <?php if ($sf_user->hasPermission('admin')): ?>
    <li><?php echo link_to('Utilisateur', 'sf_guard_user')?>
     <ul><li class="create"><?php echo link_to('Créer', 'sf_guard_user/new')?></li></ul>
    </li>
    <?php endif; ?>
    <?php if ($sf_user->hasPermission('commissaire') || $sf_user->hasPermission('admin')): ?>
  	<li><?php echo link_to('PreferenceMedia', 'preference_media')?>
       <ul><li class="create"><?php echo link_to('Créer', 'preference_media/new')?></li></ul>
  	</li>
  	<?php endif;?>
  	<?php if ($sf_user->hasPermission('commissaire') || $sf_user->hasPermission('admin')): ?>
    <li><?php echo link_to('Log synchro', 'sync_log')?></li>
    <?php endif;?>
    <?php if ($sf_user->hasPermission('commissaire') || $sf_user->hasPermission('admin')): ?>
    <li><?php echo link_to('Template Mail', 'template_mail')?>
       <ul><li class="create"><?php echo link_to('Créer', 'template_mail/new')?></li></ul>
    </li>
    <?php endif;?>
    
    <?php if ($sf_user->hasPermission('commissaire') || $sf_user->hasPermission('admin')): ?>
    <li><?php echo link_to('Règles notifs', 'rulerz')?>
       <ul><li class="create"><?php echo link_to('Créer', 'rulerz/new')?></li></ul>
    </li>
    <?php endif;?>
    
	</ul>
</li>
<li>
<a href="#">Vues</a>
	<ul>	
  	<li><?php echo link_to('Vue visites', 'visite')?></li>
  	<li><?php echo link_to('Vue log visites', 'log_visite')?></li>
	</ul>
</li>	  
<?php else: ?>
  <li>
<a href="#">Vip</a>
  <ul>
    <li><?php echo link_to('Interactif', 'interactif')?>
    <?php if ($sf_user->hasPermission('commissaire') || $sf_user->hasPermission('admin')): ?>
     <ul><li class="create"><?php echo link_to('Créer', 'interactif/new')?></li></ul>
    <?php endif;?> 
    </li>
  </ul>
</li>

<?php endif;?>
	</ul>
</div>
<div style="float:right;"><?php echo link_to('Déconnexion', 'sfGuardAuth/signout')?></div>
<div class="clear"></div>
<script>
jQuery(function(){
	jQuery('div.menu ul.sf-menu').superfish();
});
</script>