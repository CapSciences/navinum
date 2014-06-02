[?php include_stylesheets_for_form($form) ?]
[?php include_javascripts_for_form($form) ?]

  [?php if (has_component_slot('show_header')) include_component_slot('show_header') ?]
  [?php include_partial('<?php echo $this->getModuleName() ?>/show_header', array( '<?php echo $this->getSingularName() ?>' => $<?php echo $this->getSingularName() ?>, 'helper' => $helper)) ?]
  
  
<div class="sf_admin_form">

    [?php foreach ($configuration->getFormFields($form, 'show') as $fieldset => $fields): ?]
<div class="content-box">
    <div class="content-box-header">
          <h3>[?php echo ('NONE' != $fieldset) ?   __($fieldset, array(), '<?php echo $this->getI18nCatalogue() ?>') : ''  ?]</h3>
    </div>
		<div id="sf_fieldset_[?php echo preg_replace('/[^a-z0-9_]/', '_', strtolower($fieldset)) ?]">
		
				[?php foreach ($fields as $name => $field): ?]
		    [?php $attributes = $field->getConfig('attributes', array()); ?]
			[?php if ($field->isPartial()): ?]
		      [?php include_partial('<?php echo $this->getModuleName() ?>/'.$name, array('<?php echo $this->getSingularName() ?>' => $<?php echo $this->getSingularName() ?>, 'form' => $form, 'attributes' => $attributes instanceof sfOutputEscaper ? $attributes->getRawValue() : $attributes)) ?]
		    [?php elseif ($field->isComponent()): ?]
		      [?php include_component('<?php echo $this->getModuleName() ?>', $name, array('<?php echo $this->getSingularName() ?>' => $<?php echo $this->getSingularName() ?>, 'form' => $form, 'attributes' => $attributes instanceof sfOutputEscaper ? $attributes->getRawValue() : $attributes)) ?]
		    [?php else: ?]
		    <div class="sf_admin_form_row">
		      <label>[?php echo $field->getConfig('label')? $field->getConfig('label'): $field->getName() ?]:</label>
			    [?php echo $form->getObject()->get($name) ? $form->getObject()->get($name) : "&nbsp;" ?]
		    </div>
		    [?php endif; ?]

		  [?php endforeach; ?]
		</div>
		</div>
    [?php endforeach; ?]

</div>


  [?php include_partial('<?php echo $this->getModuleName() ?>/show_footer', array('<?php echo $this->getSingularName() ?>' => $<?php echo $this->getSingularName() ?>, 'helper' => $helper)) ?]
  [?php if (has_component_slot('show_footer')) include_component_slot('show_footer') ?]
