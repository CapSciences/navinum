<?php

/**
 * interactif actions.
 *
 * @package    sf_sandbox
 * @subpackage interactif
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z xavier $
 * @see        class::autointeractifActions
 */
class interactifActions extends autointeractifActions
{

  /**
   * Loads "Parcours" objects related to the currently
   * selected objects
   */
  public function embedManyToManyParcours()
  {
    // get the list of the object's ids
    // we assume there's only one primary key
    $list = array();

    foreach ($this->objects as $object)
    {
      $value = $object['guid'];

      if ($value !== null)
      {
        $list[] = $value;
      }
    }

    if (0 == count($list))
    {
      return;
    }

    // retrieve the objects related to these primary keys
    $relation_name = 'Parcours';
    $rel = Doctrine::getTable($this->model)->getRelation($relation_name);
    $query = $rel->getTable()->createQuery();
    $query->orderBy("ordre asc");
    $dql = $rel->getRelationDql(count($list), 'collection');
    $collection = $query->query($dql, $list, Doctrine_Core::HYDRATE_ARRAY);
    $local_key = $rel->getLocal();
    $related_class = $rel->getClass();
    $related = array();

    // and attach them to the right objects
    foreach ($collection as $relation)
    {
      if (!isset($related[$relation[$local_key]]))
      {
        $related[$relation[$local_key]] = array();
      }

      $related[$relation[$local_key]][] = $relation[$related_class];
    }

    foreach ($this->objects as $key => $object)
    {
      if ($object['guid'] && isset($related[$object['guid']]))
      {
        $this->objects[$key][$relation_name] = $related[$object['guid']];
      }
    }
  }

  
  /**
   * Returns the list of validators for a get request.
   * @return  array  an array of validators
   */
  public function getIndexValidators()
  {
  	$validators = array();
    $validators['guid'] = new sfValidatorString(array('max_length' => 255, 'required' => false));
    $validators['source_type'] = new sfValidatorChoice(array('choices' => array("html5","natif"),'required' => false));
    $validators['libelle'] = new sfValidatorString(array('max_length' => 255, 'required' => false));
    $validators['url_fichier_interactif'] = new sfValidatorString(array('max_length' => 255, 'required' => false));
    $validators['url_pierre_de_rosette'] = new sfValidatorString(array('max_length' => 255, 'required' => false));
    $validators['url_illustration'] = new sfValidatorString(array('max_length' => 255, 'required' => false));
    $validators['is_visiteur_needed'] = new sfValidatorBoolean(array('required' => false));
    $validators['is_logvisite_needed'] = new sfValidatorBoolean(array('required' => false));
    $validators['is_parcours_needed'] = new sfValidatorBoolean(array('required' => false));
    $validators['markets'] = new sfValidatorString(array('max_length' => 255, 'required' => false));
    $validators['date_diff'] = new sfValidatorDateTime(array('required' => false));
    $validators['created_at'] = new sfValidatorDateTime(array('required' => false));
    $validators['updated_at'] = new sfValidatorDateTime(array('required' => false));

    return $validators;
  }
  
  protected function configureFields()
  {
    foreach($this->objects as $key=> $object)
    {
    	if(isset($object['Parcours']))
    	{
	      foreach($object['Parcours'] as $key2 => $parcours)
	      {
	        unset($this->objects[$key]['Parcours'][$key2]['created_at'], $this->objects[$key]['Parcours'][$key2]['updated_at']);
	      }
    	}
    }
  }

  /**
   * Retrieves a  collection of Interactif objects
   * @param   sfWebRequest   $request a request object
   * @return  string
   */
  public function executeIndex(sfWebRequest $request)
  {
    $this->forward404Unless($request->isMethod(sfRequest::GET));
    $params = $request->getParameterHolder()->getAll();
    // notify an event before the action's body starts
    $this->dispatcher->notify(new sfEvent($this, 'sfDoctrineRestGenerator.get.pre', array('params' => $params)));

    $request->setRequestFormat('html');

    //get the exposition_id param
    if (isset($params['exposition_id']))
    {
      $exposition_id = $params['exposition_id'];
    }
    //get the parcours_id param
    if (isset($params['parcours_id']))
    {
      $parcours_id = $params['parcours_id'];
    }
    $params = $this->cleanupParameters($params);

    try
    {
      $format = $this->getFormat();
      $this->validateIndex($params);
    }
    catch (Exception $e)
    {
      $this->getResponse()->setStatusCode(406);
      $serializer = $this->getSerializer();
      $this->getResponse()->setContentType($serializer->getContentType());
      $error = $e->getMessage();

      // event filter to enable customisation of the error message.
      $result = $this->dispatcher->filter(
        new sfEvent($this, 'sfDoctrineRestGenerator.filter_error_output'),
        $error
      )->getReturnValue();

      if ($error === $result)
      {
        $error = array(array('message' => $error));
        $this->output = $serializer->serialize($error, 'error');
      }
      else
      {
        $this->output = $serializer->serialize($result);
      }

      return sfView::SUCCESS;
    }

    $this->queryExecute($params);
    $isset_pk = (!isset($isset_pk) || $isset_pk) && isset($params['guid']);
    if ($isset_pk && count($this->objects) == 0)
    {
      $request->setRequestFormat($format);
      $this->forward404();
    }

    $this->embedManyToManyParcours($params);

    foreach ($this->objects as $key => $object)
    {
      $this->embedAdditionalurl_visiteur_needed($key, $params);
      $this->embedAdditionalurl_logvisite_needed($key, $params);
      $this->embedAdditionalurl_logvisite_verbose_needed($key, $params);
      $this->embedAdditionalurl_parcours_needed($key, $params);

        if (isset($params['markets']) )
        {
            $this->embedAdditionalExposition($key, $params);
        }
    }

     //Filter by parcours_id
    if (isset($parcours_id)) {
      $new_object = array();

      foreach ($this->objects as $key => $object)
      {
        $filtered = true;
        if (isset($object['Parcours']))
        {
          foreach ($object['Parcours'] as $parcours)
          {
            if ($parcours['guid'] == $parcours_id)
            {
              $filtered = false;
            }
          }
        }
        if (!$filtered && isset($this->objects[$key])) {
           $new_object[]  = $this->objects[$key];
        }
      }
      $this->objects = $new_object;
    }

    //Filter by exposition_id
    if (isset($exposition_id)) {
      $new_object = array();
      foreach ($this->objects as $key => $object)
      {
        $filtered = true;
        if (isset($object['Parcours']))
        {
          foreach ($object['Parcours'] as $parcours)
          {
            if (isset($exposition_id))
            {
              $expositions = Doctrine_Query::create()
                                  ->from('ExpositionsParcours ep')
                                  ->andWhere('ep.parcours_id = ?', $parcours['guid'])
                                  ->andWhere('ep.exposition_id = ?', $exposition_id)
                                  ->execute(array(), Doctrine::HYDRATE_ARRAY);

              if (count($expositions) > 0)
              {
                $filtered = false;
              }
            }
          }
        }
        if (!$filtered && isset($this->objects[$key])) {
        	 $new_object[]  = $this->objects[$key];
        }
      }
      $this->objects = $new_object;
    }

    // configure the fields of the returned objects and eventually hide some
    $this->setFieldVisibility();
    $this->configureFields();

    $serializer = $this->getSerializer();
    $this->getResponse()->setContentType($serializer->getContentType());
    $this->output = $serializer->serialize($this->objects, $this->model);
    unset($this->objects);
  }

    public function embedAdditionalExposition($key, $params){
      if(isset($this->objects[$key]['Parcours']))
      {
          foreach ($this->objects[$key]['Parcours'] as $parcours)
          {
		$array_exposition = array();
          foreach ($this->objects[$key]['Parcours'] as $parcours)
          {
              $expositions = Doctrine_Query::create()
                              ->select('ep.exposition_id, ex.libelle')
                              ->from('ExpositionsParcours ep')
                              ->leftJoin('ep.Exposition ex')
                              ->andWhere('ep.parcours_id = ?', $parcours['guid'])
                              ->execute(array(), Doctrine::HYDRATE_ARRAY);
		if($expositions[0]['Exposition']){
                  $array_exposition[] = $expositions[0]['Exposition'];
              }	

          }
          $this->objects[$key]["Exposition"] = $array_exposition;
	}
      }
    }

  public function embedAdditionalurl_visiteur_needed($key, $params)
  {
    if ($this->objects[$key]['is_visiteur_needed'] == 1)
    {
      $this->objects[$key]['url_visiteur_needed'] = 'api.php/visiteur?guid=$visiteur_id';
    }
  }

  public function embedAdditionalurl_logvisite_needed($key, $params)
  {
    if ($this->objects[$key]['is_logvisite_needed'] == 1)
    {
      $this->objects[$key]['url_logvisite_needed'] = $this->getUrlLogVisite($key, $params);
    }
  }
  
  private function getUrlLogVisite($key, $params){
      $url = 'api.php/log_visite?';
      if ($this->objects[$key]['url_visiteur_type'] == 0)
      {
        $url .= 'visiteur_id=$visiteur_id&';
      }
      //unset($this->objects[$key]['url_visiteur_type']);
      
      if ($this->objects[$key]['url_interactif_type'] == 1)
      {
        $url .= 'interactif_id=$interactif_id&';
      }
      elseif ($this->objects[$key]['url_interactif_type'] == 0)
      {
        $url .= 'interactif_id='.$this->objects[$key]['url_interactif_choice'].'&';
      }
      //unset($this->objects[$key]['url_interactif_type'], $this->objects[$key]['url_interactif_choice']);

      if ($this->objects[$key]['url_start_at'] != '')
      {
        $url .= 'start_at=$'.$this->objects[$key]['url_start_at'].$this->objects[$key]['url_start_at_type'].'&';
      }
      if ($this->objects[$key]['url_end_at'] != '')
      {
        $url .= 'end_at=$'.$this->objects[$key]['url_end_at'].$this->objects[$key]['url_end_at_type'];
      }
      return $url;
  }

  public function embedAdditionalurl_logvisite_verbose_needed($key, $params)
  {
    if (isset($this->objects[$key]['is_logvisite_verbose_needed']) && $this->objects[$key]['is_logvisite_verbose_needed'] == 1)
    {
      $this->objects[$key]['url_logvisite_verbose_needed'] = $this->getUrlLogVisite($key, $params) . '&extract=full';
    }
  }

  public function embedAdditionalurl_parcours_needed($key, $params)
  {
    if (isset($this->objects[$key]['is_parcours_needed']) && $this->objects[$key]['is_parcours_needed'] == 1)
    {
      $this->objects[$key]['url_parcours_needed'] = 'api.php/parcours?visiteur_id=$visiteur_id';
    }
  }

    /**
   * Create the query for selecting objects, eventually along with related
   * objects
   *
   * @param   array   $params  an array of criterions for the selection
   */
  public function query($params)
  {
    $q = Doctrine_Query::create()->from($this->model.' '.$this->model);

    $sort = 'ordre asc';
    if (isset($params['sort_by']))
    {
      $sort = $params['sort_by'];
      unset($params['sort_by']);

      if (isset($params['sort_order']))
      {
        $sort .= ' '.$params['sort_order'];
        unset($params['sort_order']);
      }
    }

    if (isset($sort))
    {
      $q->orderBy($sort);
    }

    if (isset($params['guid']))
    {
      $values = explode(',', $params['guid']);

      if (count($values) == 1)
      {
        $q->andWhere($this->model.'.guid = ?', $values[0]);
      }
      else
      {
        $q->whereIn($this->model.'.guid', $values);
      }

      unset($params['guid']);
    }
    if (isset($params['markets']))
    {

        $q->andWhere($this->model.'.markets like ?', "%".$params['markets']."%");
        unset($params['markets']);
    }
/*    if (isset($params['date']))
    {

        $q->andWhere($this->model.'.markets like ?', "%".$params['markets']."%");
        unset($params['markets']);
    }*/

    foreach ($params as $name => $value)
    {
      $q->andWhere($this->model.'.'.$name.' = ?', $value);
    }

    return $q;
  }

 /**
   * Retrieves a  collection of Interactif objects
   * @param   sfWebRequest   $request a request object
   * @return  string
   */
  public function executeDownloadSource(sfWebRequest $request)
  {

    $this->forward404Unless($request->isMethod(sfRequest::GET));
    $params = $request->getParameterHolder()->getAll();
    // notify an event before the action's body starts
    $this->dispatcher->notify(new sfEvent($this, 'sfDoctrineRestGenerator.get.pre', array('params' => $params)));

    $request->setRequestFormat('html');

    //get the exposition_id param
    if (isset($params['guid']))
    {
      $interactif_id = $params['guid'];
    }
    $params = $this->cleanupParameters($params);

    try
    {
      $format = $this->getFormat();
      $this->validateIndex($params);
      $interactif = Doctrine_Core::getTable('Interactif')->findOneByGuid($interactif_id);
      $serializer = $this->getSerializer();
      if (!$interactif)
      {
        $this->getResponse()->setStatusCode(406);
        $result = array(array('message' => 'interactif unknown', 'code' => 'E_INT_00'));
        $this->output = $serializer->serialize($result, 'error');
      }

      $path = $interactif->getInteractifDataPath();
      $finder = sfFinder::type('file');
      $finder->name('*.zip');
      $files = $finder->in($path);
      $file = $files[0];
       foreach ($files as $file) {
        //$handle = fopen($file, "r");
      }


    $response = $this->getResponse();
    $response->setContentType('application/octet-stream');
    $response->setHttpHeader('Content-Disposition', 'attachment; filename="' . basename($file) . '"');
    $response->setContent(file_get_contents($file));

    return $this->renderText(file_get_contents($file));
    //return sfView::HEADER_ONLY;

    }
    catch (Exception $e)
    {
      $this->getResponse()->setStatusCode(406);
      $serializer = $this->getSerializer();
      $this->getResponse()->setContentType($serializer->getContentType());
      $error = $e->getMessage();

      // event filter to enable customisation of the error message.
      $result = $this->dispatcher->filter(
        new sfEvent($this, 'sfDoctrineRestGenerator.filter_error_output'),
        $error
      )->getReturnValue();

      if ($error === $result)
      {
        $error = array(array('message' => $error));
        $this->output = $serializer->serialize($error, 'error');
      }
      else
      {
        $this->output = $serializer->serialize($result);
      }

      return sfView::SUCCESS;
    }

    return sfView::HEADER_ONLY;
  }


    public function executeBrowseDocuments(sfWebRequest $request)
    {
        $request->setRequestFormat('html');
        $this->forward404Unless($request->isMethod(sfRequest::GET));
        $visiteurs = $request->getParameter('visiteur_id');
        $interactifs = $request->getParameter('interactif_id');


        $serializer = $this->getSerializer();
        $this->getResponse()->setContentType($serializer->getContentType());
        if(is_null($interactifs))
        {
            $this->getResponse()->setStatusCode(406);
            $error = array("message" => "parameter interactif_id mandatory");
            $this->output = $serializer->serialize(array($error), 'error');
            return sfView::SUCCESS;
        }
        $visiteur_keys = array();
        $interactif_keys = array();

        $interactif_keys = explode(',', $interactifs);
        if($visiteurs){
            $visiteur_keys = explode(',', $visiteurs);
        }


        $page = $request->getParameter('page');
        if (!$page)
        {
            $page = 1;
        }

        $page_size = $request->getParameter('page_size');
        if (!$page_size)
        {
            $page_size = 5000;
        }

        $offset = (($page - 1) * $page_size);
        $limit = $page_size;
        //echo("offset=$offset && page_size=$page_size");
        $results = array();
        foreach($interactif_keys as $interactif_id){

            $item = Doctrine::getTable($this->model)->findOneByGuid($interactif_id);
            if(!$item && count($interactif_keys) == 1)
            {
                $this->getResponse()->setStatusCode(406);
                $error = array("message" => "unknown interactif ".$interactif_id);
                $this->output = $serializer->serialize(array($error), 'error');
                return sfView::SUCCESS;
            }

            $query = 'SELECT e.guid AS guid, e.libelle AS libelle';
            $query .= ' FROM exposition e, exposition_parcours e2, parcours_interactif p, interactif i';
            $query .= ' WHERE (';
            $query .= ' e.guid = e2.exposition_id';
            $query .= ' AND e2.parcours_id = p.parcours_id';
            $query .= ' AND p.interactif_id = i.guid';
            $query .= ' AND i.guid = "'.$interactif_id.'"';
            $query .= ' ) group by guid';

            $exposition = Doctrine_Manager::getInstance()->getCurrentConnection()->fetchAssoc($query);

            $interactif_info = array();
            if($exposition){
                $interactif_info = array(
                    'libelle' =>   $item->getLibelle(),
                    'synopsis' =>   $item->getSynopsis(),
                    'description' => $item->getDescription(),
                    'logo' => $item->getLogo(),
                    'url_illustration' => $item->getUrlIllustration(),
                    'url_market_ios' => $item->getUrlMarketIos(),
                    'url_market_android' => $item->getUrlMarketAndroid(),
                    'image1' => $item->getImage1(),
                    'image2' => $item->getImage2(),
                    'image3' => $item->getImage3(),
                    'expositions' => $exposition
                );
            }

            $results[$interactif_id] = $interactif_info;

            if(count($visiteur_keys) > 0){
                $docs = array();
                foreach($visiteur_keys as $visiteur_id){
                    $doc = $item->browseDocuments($interactif_id, $visiteur_id, array('page_size' => $page_size, 'offset' => $offset));
                    if(count($doc)){
                        $docs[] = $doc;
                    }
                }

                if(count($docs)){
                    $results = array_merge($results, array("documents" => $docs));
                }

            }else{
                $doc = $item->browseDocuments($interactif_id, "", array('page_size' => $page_size, 'offset' => $offset));
                if(count($doc)){
                    $results = array_merge($results, array("documents" => $doc));
                }
            }
        }

        if(count($results) > 0){
            $results = array($results);
        }
        $this->output = $serializer->serialize($results , 'result');
        return sfView::SUCCESS;

    }


}
