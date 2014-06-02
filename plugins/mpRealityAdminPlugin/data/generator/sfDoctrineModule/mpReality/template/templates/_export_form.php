<div class="[?php echo $class ?][?php $form[$name]->hasError() and print ' errors' ?]">
  [?php $escapedField = $form[$name] instanceOf sfOutputEscaper? $form[$name]->getRawValue() : $form[$name] ?]
  [?php if (!$escapedField instanceOf sfFormFieldSchema) echo $form[$name]->renderError() ?]


    <div>
      [?php echo $form[$name]->renderLabel() ?]

      <div class="content">[?php echo $form[$name]->render() ?]</div>



  </div>
 
</div>
