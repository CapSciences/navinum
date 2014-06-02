  /**
   * Manages the visibility of fields in record collections and in relations.
   * This method will hide some fields, based on the configuration file
   *
   * @return  void
   */
  protected function setFieldVisibility()
  {
<?php
$display = $this->configuration->getValue('get.display');
$hide = $this->configuration->getValue('get.hide');
$embed_relations = $this->configuration->getValue('get.embed_relations');
$object_additional_fields = $this->configuration->getValue('get.object_additional_fields');
?><?php if (count($display) > 0): ?>
<?php if (count($hide) > 0): ?>
    $accepted_keys = <?php echo var_export(array_flip(array_merge(array_diff($display, $hide), $embed_relations, $object_additional_fields)), true); ?>;
<?php else: ?>
    $accepted_keys = <?php echo var_export(array_flip(array_merge($display, $embed_relations, $object_additional_fields)), true); ?>;
<?php endif; ?>

    foreach ($this->objects as $i => $object)
    {
      if (is_int($i))
      {
        $this->objects[$i] = array_intersect_key($object, $accepted_keys);
      }
    }
<?php elseif (count($hide) > 0): ?>

    $hidden_keys = <?php echo var_export(array_flip($hide), true); ?>;

    foreach ($this->objects as $i => $object)
    {
      if (is_int($i))
      {
        $this->objects[$i] = array_diff_key($object, $hidden_keys);
      }
    }
<?php endif; ?>
<?php $embedded_relations_hide = $this->configuration->getValue('get.embedded_relations_hide'); ?>
<?php if (count($embedded_relations_hide) > 0): ?>

    $embedded_relations_hide = <?php echo var_export($embedded_relations_hide, true); ?>;

    // relation types
    $relation_types = array(
<?php foreach (array_keys($embedded_relations_hide) as $relation_name): ?>
<?php
$relation = Doctrine::getTable($this->getModelClass())->getRelation($relation_name);
?>
  '<?php echo $relation_name ?>' => <?php echo var_export(Doctrine_Relation::MANY == $relation->getType()) ?>,
<?php endforeach; ?>
    );

    foreach ($this->objects as $i => $object)
    {
      // only filter objects, not additional fields
      if (is_int($i))
      {
        foreach ($embedded_relations_hide as $relation_name => $hidden_fields)
        {
          if (isset($object[$relation_name]))
          {
            if (false === $relation_types[$relation_name])
            {
                $object[$relation_name] = array_diff_key($object[$relation_name], $hidden_fields);
            }
            else
            {
              $related_objects = $object[$relation_name];

              foreach ($related_objects as $j => $related_object)
              {
                $related_objects[$j] = array_diff_key($related_object, $hidden_fields);
              }

              $object[$relation_name] = $related_objects;
            }
          }
        }

        $this->objects[$i] = $object;
      }
    }
<?php endif; ?>
  }
