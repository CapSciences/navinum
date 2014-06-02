<?php

class mpDashboardReality
{
  public static function getCategories()
  {
    $categories = sfConfig::get('app_mp_reality_admin_categories', array());


    foreach ($categories as $key => $value)
    {
      $categories[$key]['label'] =  isset($categories[$key]['label']) ? $categories[$key]['label'] : $key;
      $categories[$key]['class'] =  isset($categories[$key]['class']) ? $categories[$key]['class'] : 'large';
      
      if (isset($value['items']))
      {
        array_walk($categories[$key]['items'], 'mpDashboardReality::initialize');
      }
    }

    return $categories;
  }

  public static function initialize(&$item, $key)
  {
    $image = isset($item['image']) ? $item['image'] : sfConfig::get('app_mp_reality_admin_default_image', 'default.png');
    $image = (substr($image, 0, 1) == '/') ? $image : sfConfig::get('app_mp_reality_admin_image_dir') . $image;

    $item['image'] = $image;

    $item['label'] = isset($item['label']) ? $item['label'] : $key;

    $item['url'] = isset($item['url']) ? $item['url'] : $key;
    if (isset($item['badge']))
    {
      $class = Doctrine::getTable($item['badge']['model']);
      $method = $item['badge']['table_method'];

      if (method_exists($class , $method))
      {
        $object = call_user_func(array($class, $method));
        $item['badge'] = $object->count();
      }
      else
      {
        unset ($item['badge']);
      }
    }
  }

  public static function checkUserAccess($item, $user)
  {
    if (!$user->isAuthenticated())
    {
      return false;
    }

    return isset($item['credentials']) ? $user->hasCredential($item['credentials']) : true;
  }
  
}