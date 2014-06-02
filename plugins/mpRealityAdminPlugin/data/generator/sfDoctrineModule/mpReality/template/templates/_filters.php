[?php use_stylesheets_for_form($form) ?]
[?php use_javascripts_for_form($form) ?]
<div id="blocFilter" title="Filtrer">
<div class="sf_admin_filter">
<div class="sf_admin_filter_content">

  [?php if ($form->hasGlobalErrors()): ?]
    [?php echo $form->renderGlobalErrors() ?]
  [?php endif; ?]

  <form action="[?php echo url_for('<?php echo $this->getUrlForAction('collection') ?>', array('action' => 'filter')) ?]" method="post">
        [?php foreach ($configuration->getFormFilterFields($form) as $name => $field): ?]
        [?php if ((isset($form[$name]) && $form[$name]->isHidden()) || (!isset($form[$name]) && $field->isReal())) continue ?]
          [?php include_partial('<?php echo $this->getModuleName() ?>/filters_field', array(
            'name'       => $name,
            'attributes' => $field->getConfig('attributes', array()),
            'label'      => $field->getConfig('label'),
            'help'       => $field->getConfig('help'),
            'form'       => $form,
            'field'      => $field,
            'class'      => 'sf_admin_form_row sf_admin_'.strtolower($field->getType()).' sf_admin_filter_field_'.$name,
          )) ?]
        [?php endforeach; ?]
		
		[?php echo $form->renderHiddenFields() ?]
		
		<div class="sf_admin_form_row">
      <div class="label">&nbsp;
        
      </div>
      <div class="field">
	  [?php echo link_to(__('Reset', array(), 'sf_admin'), '<?php echo $this->getUrlForAction('collection') ?>', array('action' => 'filter'), array('query_string' => '_reset', 'class' => 'blocFilter_reset graybutton f_left')) ?]
        <input type="submit" value="[?php echo __('Filter', array(), 'sf_admin') ?]"  class="graybutton"/>
      </div>
    </div>
  </form>
</div>
</div>
</div>