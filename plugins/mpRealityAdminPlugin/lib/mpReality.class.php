<?php

/**
 * Description of mpReality
 *
 * @author mplavonil
 */
class mpReality
{

  public static function getCategories()
  {
    $categories = sfConfig::get('app_mp_reality_admin_categories', array());
    foreach ($categories as $key => $value)
    { 
      array_walk($categories[$key], 'mpReality::initCategorie');

//      $categories['name'] = isset($categories['name']) ? $categories['name'] :  sfConfig::get('app_mp_reality_admin_default_category_name', 'administration');
//
//    $categories['class'] = (isset($categories['class']) && in_array($categories['class'], array('large', 'medium', 'small'))) ? $categories['class'] :  'full';
      if (isset($value['items']))
      {
        array_walk($categories[$key]['items'], 'mpReality::initItem');
      }
    }

    return $categories;
  }

  public static function initCategorie(&$categorie, $key)
  {
    $categorie['name'] = isset($categorie['name']) ? $categorie['name'] :  sfConfig::get('app_mp_reality_admin_default_category_name', 'administration');

    $categorie['class'] = (isset($categorie['class']) && in_array($categorie['class'], array('large', 'medium', 'small'))) ? $categorie['class'] :  'full';

  }

  public static function initItem(&$item, $key)
  {
    $image = isset($item['image']) ? $item['image'] : sfConfig::get('app_mp_reality_admin_default_image', 'toto');
    $image = (substr($image, 0, 1) == '/') ? $image : (sfConfig::get('app_mp_reality_admin_image_dir') . $image);

    $item['image'] = $image;

    $item['name'] = isset($item['name']) ? $item['name'] : $key;

    $item['url'] = isset($item['url']) ? $item['url'] : $key;
  }

}

