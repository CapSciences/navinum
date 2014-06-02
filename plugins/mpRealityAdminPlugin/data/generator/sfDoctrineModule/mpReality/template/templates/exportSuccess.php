[?php use_helper('I18N', 'Date', 'JavascriptBase') ?]
[?php include_partial('<?php echo $this->getModuleName() ?>/assets') ?]

<h1>[?php echo __('Exportation', array(), 'mpRealityAdmin') ?]</h1>

  [?php echo form_tag('<?php echo $this->getModuleName() ?>/export?sf_format=xls') ?]


      [?php echo $form->renderHiddenFields() ?]
      [?php if ($form->hasGlobalErrors()): ?]
        [?php echo $form->renderGlobalErrors() ?]
      [?php endif; ?]
      [?php foreach ($form as $name => $field): ?]
        [?php if (!$form[$name]->isHidden()): ?]
          [?php include_partial('export_form', array('form' => $form, 'name' => $name, 'help' => null, 'class' => 'sf_admin_form_row sf_admin_export_form_row')) ?]
        [?php endif ?]
      [?php endforeach ?]
      <input type="submit" value="[?php echo __('Exporter', array(), 'mpRealityAdmin'); ?]"  id="sf_admin_exportation_form_submit" />
    </form>