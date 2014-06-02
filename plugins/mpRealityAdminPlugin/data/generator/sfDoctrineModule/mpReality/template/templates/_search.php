<div class="module_search">
  <div class="filter_box" data-load-url="[?php echo url_for(array('module' => '<?php echo $this->getModuleName() ?>', 'action' => 'showFilters')) ?]"></div>
    <?php if ($this->configuration->hasFilterForm()): ?>
             <a class="advance_search f_left" title="[?php echo __('Recherche avancée') ?]"></a>
            <?php endif; ?>

  [?php    
    $currentSearch = $sf_user->getAttribute('<?php echo $this->getModuleName(); ?>'.'.search', '', 'admin_module');
    
    
    printf('<form action="%s" method="get">', url_for('@<?php echo $this->getUrlForAction('list') ?>'));
    printf('<input id="module_search_input" class="ui-corner-left  f_left" type="text" title="%s" value="%s" name="search"/>',
      __('Rechercher dans ') . '<?php echo $this->getModuleName() ?>',
      $currentSearch
    );
    printf('<input type="submit" id="button_module_search" class="mp_submit  no_margin graybutton f_left" value="%s" />', __('Rechercher'));
    if ($currentSearch)
    {
      printf('<a href="%s" class="cancel_search" title="%s">&nbsp;</a>', url_for('@<?php echo $this->getUrlForAction('list') ?>'.'?search='), __('Annuler la recherche'));
    }
  ?]
  </form>
  
</div>