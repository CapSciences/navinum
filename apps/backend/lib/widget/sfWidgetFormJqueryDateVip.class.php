<?php
class sfWidgetFormJqueryDateVip extends sfWidgetFormJQueryDate
{
  protected function configure($options = array(), $attributes = array())
  {
    parent::configure();
    $this->addOption('date_range', array());
    $this->addOption('date_widget_options', array());
    $this->setOption('image', '/images/calendar.png');
    if(isset($options['date_range'])){
	    $years = range($options['date_range']['min'], $options['date_range']['max']);
	    unset($options['date_range']);
    }
    else
    {
    	$years = range(2009, date('Y') + 2);
    }

    $date_widget_options = array(
      'culture'   => 'fr',
      'years'     =>  array_combine($years, $years),
      'can_be_empty' => true
    );
    if (isset($options['date_widget_options']))
    {
      $date_widget_options = array_merge($date_widget_options, $options['date_widget_options']);
    }

    $this->date_widget = new sfWidgetFormI18nDate($date_widget_options);
//    $this->setOption(
//      'date_widget',
//      $date_widget
//    );

    $this->setOption('culture', 'fr');
    $this->setOption('config', '{showMonthAfterYear: false, firstDay: 1}');
  }
  
  public function render($name, $value = null, $attributes = array(), $errors = array())
  {
    $prefix = $this->generateId($name);

    $image = '';
    if (false !== $this->getOption('image'))
    {
      $image = sprintf(', buttonImage: "%s", buttonImageOnly: true', $this->getOption('image'));
    }

    return $this->date_widget->render($name, $value, $attributes, $errors).
           $this->renderTag('input', array('type' => 'hidden', 'size' => 10, 'id' => $id = $this->generateId($name).'_jquery_control', 'disabled' => 'disabled')).
           sprintf(<<<EOF
<script type="text/javascript">
  function wfd_%s_read_linked()
  {
    jQuery("#%s").val(jQuery("#%s").val() + "-" + jQuery("#%s").val() + "-" + jQuery("#%s").val());

    return {};
  }

  function wfd_%s_update_linked(date)
  {
    jQuery("#%s").val(parseInt(date.substring(0, 4), 10));
    jQuery("#%s").val(parseInt(date.substring(5, 7), 10));
    jQuery("#%s").val(parseInt(date.substring(8), 10));
  }

  function wfd_%s_check_linked_days()
  {
    var daysInMonth = 32 - new Date(jQuery("#%s").val(), jQuery("#%s").val() - 1, 32).getDate();
    jQuery("#%s option:gt(28)").attr("disabled", "");
    jQuery("#%s option:gt(" + (%s) +")").attr("disabled", "disabled");

    if (jQuery("#%s").val() > daysInMonth)
    {
      jQuery("#%s").val(daysInMonth);
    }
  }

  jQuery(document).ready(function() {
    jQuery("#%s").datepicker(jQuery.extend({}, {
      minDate:    new Date(%s, 1 - 1, 1),
      maxDate:    new Date(%s, 12 - 1, 31),
      beforeShow: wfd_%s_read_linked,
      onSelect:   wfd_%s_update_linked,
      showOn:     "button",
      onClose: function(){%s}
      %s
    }, jQuery.datepicker.regional["%s"], %s, {dateFormat: "yy-mm-dd"}));
    //wfd_%s_check_linked_days();
  });

  jQuery("#%s, #%s, #%s").change(wfd_%s_check_linked_days);
</script>
EOF
      ,
      $prefix, $id,
      $this->generateId($name.'[year]'), $this->generateId($name.'[month]'), $this->generateId($name.'[day]'),
      $prefix,
      $this->generateId($name.'[year]'), $this->generateId($name.'[month]'), $this->generateId($name.'[day]'),
      $prefix,
      $this->generateId($name.'[year]'), $this->generateId($name.'[month]'),
      $this->generateId($name.'[day]'), $this->generateId($name.'[day]'),
      ($this->date_widget->getOption('can_be_empty') ? 'daysInMonth' : 'daysInMonth - 1'),
      $this->generateId($name.'[day]'), $this->generateId($name.'[day]'),
      $id,
      min($this->date_widget->getOption('years')), max($this->date_widget->getOption('years')),
      $prefix, $prefix, $this->getOption('onClose'), $image, $this->getOption('culture'), $this->getOption('config'),
      $prefix,
      $this->generateId($name.'[day]'), $this->generateId($name.'[month]'), $this->generateId($name.'[year]'),
      $prefix
    );
  }

}

?>
