<?php use_javascript('/mpRealityAdminPlugin/js/jquery-1.4.4.min.js') ?>
<?php use_javascript('/js/sync.js') ?>

<h1><?php echo __('Bienvenue sur l\'espace d\'administration du serveur vip')?></h1>

<?php if(sfConfig::get('app_type') == 'intranet'):?>
<pre id="synchro_result"></pre>
<button id="synchro_manuelle" onclick="return false" env="<?php echo sfConfig::get('sf_environment');?>">Synchroniser</button>
<?php endif;?>

<?php if($allowedApi==true): ?>
	<?php echo sprintf("Il vous reste %s SMS", $nbSms) ; ?>
<?php else: ?>
	<?php echo 'Le service SMS est disabled'; ?>
<?php endif; ?>

