<?php
class sfWidgetScoreInteractifVip extends sfWidgetForm
{
  protected function configure($options = array(), $attributes = array())
  {
    parent::configure();
    $this->addOption('typologies', array());
  }

  public function render($name, $value = null, $attributes = array(), $errors = array())
  {
    $prefix = $this->generateId($name);

    $value = $value === null ? 'null' : $value;

    $output = "";
    foreach ($this->getOption('typologies') as $key => $option)
    {
      if ($key == $value)
      {
        $attributes['selected'] = 'selected';
      }

      $output .= $this->renderContentTag("label", $key).$this->renderTag(
        'input',
        array('type' => 'text', 'size' => 10, 'id' => $id = "interactif[score".$key."]"));
    }
$output .=            $this->renderTag('input', array('type' => 'hidden', 'size' => 10, 'id' => $id = 'interactif[score]'), $value);

    return $output;
  }

}

?>
