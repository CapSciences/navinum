<?php

/**
 * log_visite actions.
 *
 * @package    sf_sandbox
 * @subpackage log_visite
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z xavier $
 * @see        class::autolog_visiteActions
 */
class log_visiteActions extends autolog_visiteActions
{
    /**
     * Returns the list of validators for a get request.
     * @return  array  an array of validators
     */
    public function getTotalValidators()
    {
        $validators = array();
        $validators['visiteur_id'] = new sfValidatorDoctrineChoice(array(
            'model' => Doctrine_Core::getTable(
                    'LogVisite'
                )->getRelation('Visiteur')->getAlias(),
            'required' => false
        ));
        $validators['visite_id'] = new sfValidatorDoctrineChoice(array(
            'model' => Doctrine_Core::getTable(
                    'LogVisite'
                )->getRelation('Visite')->getAlias(),
            'required' => false
        ));
        $validators['interactif_id'] = new sfValidatorDoctrineChoice(array(
            'model' => Doctrine_Core::getTable(
                    'LogVisite'
                )->getRelation('Interactif')->getAlias(),
            'required' => false
        ));
        $validators['univers_id'] = new sfValidatorString(array('max_length' => 2550, 'required' => false));
        $validators['exposition_id'] = new sfValidatorString(array('max_length' => 2550, 'required' => false));

        return $validators;
    }

    /**
     * Returns the list of validators for a get request.
     * @return  array  an array of validators
     */
    public function getVisiteurExpositionsValidators()
    {
        $validators = array();
        $validators['visiteur_id'] = new sfValidatorDoctrineChoice(array(
            'model' => Doctrine_Core::getTable(
                    'LogVisite'
                )->getRelation('Visiteur')->getAlias(),
            'required' => false
        ));

        return $validators;
    }

    public function getVisiteurInteractifsExpositionValidators()
    {
        $validators = array();
        $validators['visiteur_id'] = new sfValidatorDoctrineChoice(array(
            'model' => Doctrine_Core::getTable(
                    'LogVisite'
                )->getRelation('Visiteur')->getAlias(),
            'required' => true
        ));
        $validators['exposition_id'] = new sfValidatorDoctrineChoice(array(
            'model' => Doctrine_Core::getTable(
                    'LogVisite'
                )->getRelation('Exposition')->getAlias(),
            'required' => true
        ));

        return $validators;
    }

    /**
     * Applies the get validators to the constraint parameters passed to the
     * webservice
     *
     * @param   array $params An array of criterions used for the selection
     */
    public function validateTotal($params)
    {
        $validators = $this->getTotalValidators();
        $this->validate($params, $validators);
    }

    /**
     * Applies the get validators to the constraint parameters passed to the
     * webservice
     *
     * @param   array $params An array of criterions used for the selection
     */
    public function validateVisiteurExpositions($params)
    {
        $validators = $this->getVisiteurExpositionsValidators();
        $this->validate($params, $validators);
    }

    public function validateVisiteurInteractifsExposition($params)
    {
        $validators = $this->getVisiteurInteractifsExpositionValidators();
        $this->validate($params, $validators);
    }


    /**
     * Create the query for selecting objects, eventually along with related
     * objects
     *
     * @param   array $params an array of criterions for the selection
     */
    public function queryTotal($params)
    {
        if (isset($params['visiteur_id']) && isset($params['univers_id'])) {

            $q = Doctrine_Query::create()
                ->select("SUM(score) as total")
                ->from($this->model . ' ' . $this->model)
                ->where($this->model . ".visiteur_id = ?", $params['visiteur_id'])
                ->andWhere($this->model . ".interactif_id = ?", $params['interactif_id']);

        } else {
            if (isset($params['visite_id']) && !isset($params['interactif_id'])) {
                $q = Doctrine_Query::create()
                    ->select("SUM(score) as total")
                    ->from($this->model . ' ' . $this->model)
                    ->where($this->model . ".visite_id = ?", $params['visite_id']);
            } else {
                if (isset($params['visite_id']) && isset($params['interactif_id'])) {
                    $q = Doctrine_Query::create()
                        ->select("SUM(score) as total")
                        ->from($this->model . ' ' . $this->model)
                        ->where($this->model . ".visite_id = ?", $params['visite_id'])
                        ->andWhere($this->model . ".interactif_id = ?", $params['interactif_id']);
                } else {
                    if (!isset($params['visite_id']) && isset($params['interactif_id']) && !isset($params['visiteur_id'])) {
                        $q = Doctrine_Query::create()
                            ->select("SUM(score) as total")
                            ->from($this->model . ' ' . $this->model)
                            ->where($this->model . ".interactif_id = ?", $params['interactif_id']);
                    } else {
                        if (isset($params['visiteur_id']) && isset($params['interactif_id'])) {
                            $q = Doctrine_Query::create()
                                ->select("SUM(score) as total")
                                ->from($this->model . ' ' . $this->model)
                                ->where($this->model . ".visiteur_id = ?", $params['visiteur_id'])
                                ->andWhere($this->model . ".interactif_id = ?", $params['interactif_id']);
                        } else {
                            if (isset($params['visiteur_id'])) {
                                $q = Doctrine_Query::create()
                                    ->select("SUM(score) as total")
                                    ->from($this->model . ' ' . $this->model)
                                    ->where($this->model . ".visiteur_id = ?", $params['visiteur_id']);
                            } else {
                                $q = Doctrine_Query::create()
                                    ->select("SUM(score) as total")
                                    ->from($this->model . ' ' . $this->model)
                                    ->where($this->model . ".visiteur_id = ?", "none");
                            }
                        }
                    }
                }
            }
        }

        return $q;
    }


    /**
     * Create the query for selecting objects, eventually along with related
     * objects
     *
     * @param   array $params an array of criterions for the selection
     */
    public function queryVisiteurExpositions($params)
    {

        if (isset($params['visiteur_id'])) {
            $q = Doctrine_Query::create()
                ->select($this->model . ".exposition_id, " . $this->model . ".interactif_id")
                ->addSelect("Exposition.libelle, Exposition.logo")
                ->from($this->model . ' ' . $this->model)
                ->where($this->model . ".visiteur_id = ?", $params['visiteur_id'])
                ->andWhere($this->model . ".exposition_id != ''")
                ->andWhere($this->model . ".interactif_id != ''")
                ->leftJoin($this->model . ".Exposition Exposition")
                ->groupBy($this->model . ".exposition_id")
                ->addGroupBy($this->model . ".interactif_id")
                ->orderBy('exposition_id');
        }

        return $q;
    }

    public function queryVisiteurInteractifsExposition($params)
    {
        if (isset($params['visiteur_id']) && isset($params['exposition_id'])) {
            $q = Doctrine_Query::create()
                ->select("interactif_id")
                ->from($this->model . ' ' . $this->model)
                ->where($this->model . ".visiteur_id = ?", $params['visiteur_id'])
                ->andWhereIn($this->model . ".exposition_id", $params['exposition_id'])
                ->andWhere($this->model . ".interactif_id != ''")
                ->groupBy('interactif_id')
                ->orderBy('exposition_id');
        }

        //die($q->getSqlQuery());
        return $q;
    }

    /**
     * Execute the query for selecting a collection of objects, eventually
     * along with related objects
     *
     * @param   array $params an array of criterions for the selection
     */
    public function queryVisiteurExpositionsExecute($params)
    {
        $this->objects = $this->dispatcher->filter(
            new sfEvent(
                $this,
                'sfDoctrineRestGenerator.filter_results',
                array()
            ),
            $this->queryVisiteurExpositions($params)->execute(array(), Doctrine::HYDRATE_ARRAY)
        )->getReturnValue();
    }


    public function queryVisiteurInteractifsExpositionExecute($params)
    {
        $this->objects = $this->dispatcher->filter(
            new sfEvent(
                $this,
                'sfDoctrineRestGenerator.filter_results',
                array()
            ),
            $this->queryVisiteurInteractifsExposition($params)->execute(array(), Doctrine::HYDRATE_ARRAY)
        )->getReturnValue();
    }

    /**
     * Execute the query for selecting a collection of objects, eventually
     * along with related objects
     *
     * @param   array $params an array of criterions for the selection
     */
    public function queryTotalExecute($params)
    {
        $this->objects = $this->dispatcher->filter(
            new sfEvent(
                $this,
                'sfDoctrineRestGenerator.filter_results',
                array()
            ),
            $this->queryTotal($params)->execute(array(), Doctrine::HYDRATE_ARRAY)
        )->getReturnValue();
    }

    /**
     * Retrieves a  collection of LogVisite objects
     * @param   sfWebRequest $request a request object
     * @return  string
     */
    public function executeTotal(sfWebRequest $request)
    {
        $this->forward404Unless($request->isMethod(sfRequest::GET));
        $params = $request->getParameterHolder()->getAll();

        // notify an event before the action's body starts
        $this->dispatcher->notify(new sfEvent($this, 'sfDoctrineRestGenerator.get.pre', array('params' => $params)));

        $request->setRequestFormat('html');
        $params = $this->cleanupParameters($params);

        try {

            if (count($params) == 0) {
                throw new Exception("At least one parameter (visiteur_id / visite_id / interactif_id / exposition_id / univers_id) is mandatory");
            }
            if (isset($params['univers_id'])) {
                $expositions = Doctrine_Query::create()
                    ->from('Exposition e')
                    ->where('e.univers_id = ?', $params['univers_id'])
                    ->fetchArray();

                if ($expositions && count($expositions)) {
                    $expos = array();
                    foreach ($expositions as $exposition) {
                        $expos[] = $exposition['guid'];
                    }
                    $params['exposition_id'] = implode(', ', $expos);
                } else {
                    throw new sfException(sprintf("no exposition relation with univers_id %s", $params['univers_id']));
                }

                unset($params['univers_id']);

            }

            if ((isset($params['exposition_id']) && !isset($params['visiteur_id'])) ||
                (isset($params['univers_id']) && !isset($params['visiteur_id']))
            ) {
                throw new Exception("visiteur_id is mandatory");
            }

            $format = $this->getFormat();
            $this->validateTotal($params);
        } catch (Exception $e) {
            $this->getResponse()->setStatusCode(406);
            $serializer = $this->getSerializer();
            $this->getResponse()->setContentType($serializer->getContentType());
            $error = $e->getMessage();

            // event filter to enable customisation of the error message.
            $result = $this->dispatcher->filter(
                new sfEvent($this, 'sfDoctrineRestGenerator.filter_error_output'),
                $error
            )->getReturnValue();

            if ($error === $result) {
                $error = array(array('message' => $error));
                $this->output = $serializer->serialize($error, 'error');
            } else {
                $this->output = $serializer->serialize($result);
            }

            return sfView::SUCCESS;
        }

        $this->queryTotalExecute($params);
        $isset_pk = (!isset($isset_pk) || $isset_pk) && isset($params['guid']);
        if ($isset_pk && count($this->objects) == 0) {
            $request->setRequestFormat($format);
            $this->forward404();
        }


        // configure the fields of the returned objects and eventually hide some
        $this->setFieldVisibility();
        $this->configureFields();

        $serializer = $this->getSerializer();
        $this->getResponse()->setContentType($serializer->getContentType());
        if (is_null($this->objects[0]["total"])) {
            $this->objects[0]["total"] = 0;
        }
        $this->output = $serializer->serialize($this->objects, $this->model);
        unset($this->objects);
    }


    /**
     * Retrieves a  collection of LogVisite objects
     * @param   sfWebRequest $request a request object
     * @return  string
     */
    public function executeVisiteurExpositions(sfWebRequest $request)
    {
        $this->forward404Unless($request->isMethod(sfRequest::GET));
        $params = $request->getParameterHolder()->getAll();

        // notify an event before the action's body starts
        $this->dispatcher->notify(new sfEvent($this, 'sfDoctrineRestGenerator.get.pre', array('params' => $params)));

        $request->setRequestFormat('html');
        $params = $this->cleanupParameters($params);

        try {

            if (count($params) == 0) {
                throw new Exception("visiteur_id is mandatory");
            }

            $format = $this->getFormat();
            $this->validateVisiteurExpositions($params);
        } catch (Exception $e) {
            $this->getResponse()->setStatusCode(406);
            $serializer = $this->getSerializer();
            $this->getResponse()->setContentType($serializer->getContentType());
            $error = $e->getMessage();

            // event filter to enable customisation of the error message.
            $result = $this->dispatcher->filter(
                new sfEvent($this, 'sfDoctrineRestGenerator.filter_error_output'),
                $error
            )->getReturnValue();

            if ($error === $result) {
                $error = array(array('message' => $error));
                $this->output = $serializer->serialize($error, 'error');
            } else {
                $this->output = $serializer->serialize($result);
            }

            return sfView::SUCCESS;
        }

        $this->queryVisiteurExpositionsExecute($params);

        $serializer = $this->getSerializer();

        $new_object = array();
        $exposition_name = array();
        $exposition_logo = array();
        foreach ($this->objects as $object) {
            if (array_key_exists($object['exposition_id'], $new_object)) {
                $new_object[$object['exposition_id']] = array_merge(
                    $new_object[$object['exposition_id']],
                    array($object['interactif_id'])
                );
            } else {
                $new_object[$object['exposition_id']] = array($object['interactif_id']);
            }
            $exposition_name[$object['exposition_id']] = $object['Exposition']['libelle'];
            $exposition_logo[$object['exposition_id']] = $object['Exposition']['logo'];
            $exposition_description[$object['exposition_id']] = $object['Exposition']['description'];
            $exposition_url_illustration[$object['exposition_id']] = $object['Exposition']['description'];
            $exposition_synopsis[$object['exposition_id']] = $object['Exposition']['synopsis'];

        }
        $this->objects = $new_object;

        $tabs = array();
        foreach ($this->objects as $exposition_id => $array_interactif) {
            $new_object = array(
                'exposition_id' => $exposition_id,
                'libelle' => $exposition_name[$exposition_id],
                'logo' => $exposition_logo[$exposition_id],
                'description' => $exposition_description[$exposition_id],
                'description' => $exposition_description[$exposition_id],
                'synopsis' => $exposition_synopsis[$exposition_id],
                'interactif_id' => $array_interactif
            );
            $tabs[] = $new_object;
        }

        $this->objects = $tabs;

        $this->getResponse()->setContentType($serializer->getContentType());
        $this->output = $serializer->serialize($this->objects, $this->model);
        unset($this->objects);
    }

    /**
     * Liste des interactifs joués par le visiteur sur une ou plusieurs expositions
     *
     * @param sfWebRequest $request
     * @return string
     * @throws Exception
     */

    public function executeVisiteurInteractifsExposition(sfWebRequest $request)
    {
        $this->forward404Unless($request->isMethod(sfRequest::GET));
        $params = $request->getParameterHolder()->getAll();

        // notify an event before the action's body starts
        $this->dispatcher->notify(new sfEvent($this, 'sfDoctrineRestGenerator.get.pre', array('params' => $params)));

        $request->setRequestFormat('html');
        $params = $this->cleanupParameters($params);

        try {
            $format = $this->getFormat();
            $this->validateVisiteurInteractifsExposition($params);
        } catch (Exception $e) {
            $this->getResponse()->setStatusCode(406);
            $serializer = $this->getSerializer();
            $this->getResponse()->setContentType($serializer->getContentType());
            $error = $e->getMessage();

            // event filter to enable customisation of the error message.
            $result = $this->dispatcher->filter(
                new sfEvent($this, 'sfDoctrineRestGenerator.filter_error_output'),
                $error
            )->getReturnValue();

            if ($error === $result) {
                $error = array(array('message' => $error));
                $this->output = $serializer->serialize($error, 'error');
            } else {
                $this->output = $serializer->serialize($result);
            }

            return sfView::SUCCESS;
        }

        $this->queryVisiteurInteractifsExpositionExecute($params);

        $serializer = $this->getSerializer();

        $new_object = array();
        $interactifs = array();
        foreach ($this->objects as $object) {
            $interactifs[] = $object['interactif_id'];
        }
        $this->objects = $interactifs;

        $this->getResponse()->setContentType($serializer->getContentType());
        $this->output = $serializer->serialize($this->objects, $this->model);
        unset($this->objects);
    }

    /**
     * Returns the list of validators for a get request.
     * @return  array  an array of validators
     */
    public function getHighScoreValidators()
    {
        $validators = array();
        $validators['interactif_id'] = new sfValidatorString(array('max_length' => 255, 'required' => false));
        $validators['visiteur_id'] = new sfValidatorString(array('max_length' => 255, 'required' => false));
        $validators['is_anonyme'] = new sfValidatorBoolean(array('required' => false));

        return $validators;
    }

    /**
     * Retrieves a  collection of LogVisite objects
     * @param   sfWebRequest $request a request object
     * @return  string
     */
    public function executeHighScore(sfWebRequest $request)
    {
        $this->forward404Unless($request->isMethod(sfRequest::GET));
        $params = $request->getParameterHolder()->getAll();

        // notify an event before the action's body starts
        $this->dispatcher->notify(new sfEvent($this, 'sfDoctrineRestGenerator.get.pre', array('params' => $params)));

        $request->setRequestFormat('html');
        $params = $this->cleanupParameters($params);

        try {
            $format = $this->getFormat();
            $this->validateHighScore($params);
        } catch (Exception $e) {
            $this->getResponse()->setStatusCode(406);
            $serializer = $this->getSerializer();
            $this->getResponse()->setContentType($serializer->getContentType());
            $error = $e->getMessage();

            // event filter to enable customisation of the error message.
            $result = $this->dispatcher->filter(
                new sfEvent($this, 'sfDoctrineRestGenerator.filter_error_output'),
                $error
            )->getReturnValue();

            if ($error === $result) {
                $error = array(array('message' => $error));
                $this->output = $serializer->serialize($error, 'error');
            }

            return sfView::SUCCESS;
        }

        $this->queryFetchOneHighScore($params);
        $isset_pk = (!isset($isset_pk) || $isset_pk) && isset($params['interactif_id']);
        $isset_pk = (!isset($isset_pk) || $isset_pk) && isset($params['visite_id']);
        if ($isset_pk && count($this->objects) == 0) {
            $request->setRequestFormat($format);
            $this->forward404();
        }

        //Extended list
        $extract = $request->getParameter('extract', null);
        foreach ($this->objects as $key => $object) {
            //Get Interactif
            $interactif = Doctrine_Query::create()
                ->from('Interactif i')
                ->where('i.guid = ?', $object['interactif_id'])
                ->execute(array(), Doctrine::HYDRATE_ARRAY);

            if (count($interactif) > 0) {
                unset($interactif[0]['is_tosync']);
                $this->objects[$key]['Interactif'] = $interactif[0];
            }

            //Get parcours
            $visite = Doctrine_Query::create()
                ->from('Visite v')
                ->leftJoin('v.Parcours p')
                ->leftJoin('p.Exposition e')
                ->leftJoin('e.Contexte c')
                ->leftJoin('e.OrganisateurDiffuseur od')
                ->leftJoin('e.OrganisateurEditeur oe')
                ->where('v.guid = ?', $object['visite_id'])
                ->execute(array(), Doctrine::HYDRATE_ARRAY);

            if (count($visite) > 0) {
                $this->objects[$key]['Parcours'] = $visite[0]['Parcours'];
            }
        }

        // configure the fields of the returned objects and eventually hide some
        $this->setFieldVisibility();
        $this->configureFields();
        $serializer = $this->getSerializer();
        $this->getResponse()->setContentType($serializer->getContentType());
        if (empty($this->objects[0]["guid"])) {
            $this->objects = array();
        }
        $this->output = $serializer->serialize($this->objects, $this->model);
        unset($this->objects);
    }

    /**
     * Applies the update validators to the payload posted to the service
     *
     * @param   string $payload A payload string
     */
    public function validateHighScore($payload)
    {
        $validators = $this->getHighScoreValidators();
        $this->validate($payload, $validators);
    }

    /**
     * Execute the query for selecting an object, eventually along with related
     * objects
     *
     * @param   array $params an array of criterions for the selection
     */
    public function queryFetchOneHighScore($params)
    {
        $this->objects = array(
            $this->dispatcher->filter(
                new sfEvent(
                    $this,
                    'sfDoctrineRestGenerator.filter_result',
                    array()
                ),
                $this->queryHighScore($params)->fetchOne(array(), Doctrine::HYDRATE_ARRAY)
            )->getReturnValue()
        );
    }

    /**
     * Create the query for selecting objects, eventually along with related
     * objects
     *
     * @param   array $params an array of criterions for the selection
     */
    public function queryHighScore($params)
    {
        $q = Doctrine_Query::create()
            ->select("score as highScore, interactif_id, visiteur_id, start_at, end_at, visite_id, Visiteur.*")
            ->from($this->model . ' ' . $this->model)
            ->addFrom('Visiteur v')
            ->andWhere($this->model . '.visiteur_id = v.guid')
            ->andWhere($this->model . '.visiteur_id IS NOT NULL')
            ->groupBy('score')
            ->having('max(highScore)')
            ->orderBy('highScore desc')
            ->addOrderBy('created_at desc')
            ->limit(1);


        if (isset($params['is_anonyme'])) {
            $q->andWhere('v.is_anonyme = ?', $params['is_anonyme']);
            unset($params['is_anonyme']);
        }

        foreach ($params as $name => $value) {
            $q->andWhere($this->model . '.' . $name . ' = ?', $value);
        }
//print_r($params);
        //      die($q->getSqlQuery());
        return $q;
    }


    /**
     * Returns the list of validators for a get request.
     * @return  array  an array of validators
     */
    public function getIndexValidators()
    {
        $validators = array();
        $validators['guid'] = new sfValidatorString(array('max_length' => 255, 'required' => false));
        $validators['interactif_id'] = new sfValidatorString(array('max_length' => 2550, 'required' => false));
        $validators['exposition_id'] = new sfValidatorString(array('max_length' => 2550, 'required' => false));
        $validators['univers_id'] = new sfValidatorString(array('max_length' => 2550, 'required' => false));
        $validators['visite_id'] = new sfValidatorString(array('max_length' => 2550, 'required' => false));
        $validators['visiteur_id'] = new sfValidatorString(array('max_length' => 2550, 'required' => false));
        $validators['start_at'] = new sfValidatorDateTime(array('required' => false));
        $validators['end_at'] = new sfValidatorDateTime(array('required' => false));
        $validators['resultats'] = new sfValidatorString(array('required' => false));
        $validators['created_at'] = new sfValidatorDateTime(array('required' => false));
        $validators['updated_at'] = new sfValidatorDateTime(array('required' => false));
        $validators['page'] = new sfValidatorInteger(array('min' => 1, 'required' => false));
        $validators['connection'] = new sfValidatorString(array('max_length' => 255, 'required' => false));
        $validators['sort_by'] = new sfValidatorString(array('max_length' => 255, 'required' => false));
        $validators['sort_order'] = new sfValidatorString(array('max_length' => 255, 'required' => false));
        $validators['page_size'] = new sfValidatorString(array('required' => false));
        $validators['extract'] = new sfValidatorPass(array('required' => false));
        $validators['limit'] = new sfValidatorInteger(array('min' => 1, 'required' => false));
        $validators['is_anonyme'] = new sfValidatorBoolean(array('required' => false));

        return $validators;
    }

    /**
     * Retrieves a  collection of LogVisite objects
     * @param   sfWebRequest $request a request object
     * @return  string
     */
    public function executeIndex(sfWebRequest $request)
    {
        $this->forward404Unless($request->isMethod(sfRequest::GET));
        $params = $request->getParameterHolder()->getAll();

        // notify an event before the action's body starts
        $this->dispatcher->notify(new sfEvent($this, 'sfDoctrineRestGenerator.get.pre', array('params' => $params)));

        $request->setRequestFormat('html');
        $params = $this->cleanupParameters($params);

        try {
            if (empty($params)) {
                throw new sfException("the service must have at least one parameter");
            }
            if (isset($params['univers_id'])) {
                $expositions = Doctrine_Query::create()
                    ->from('Exposition e')
                    ->where('e.univers_id = ?', $params['univers_id'])
                    ->fetchArray();

                if ($expositions && count($expositions)) {
                    $expos = array();
                    foreach ($expositions as $exposition) {
                        $expos[] = $exposition['guid'];
                    }
                    $params['exposition_id'] = implode(', ', $expos);
                } else {
                    throw new sfException(sprintf("no exposition relation with univers_id %s", $params['univers_id']));
                }

                unset($params['univers_id']);
            }
            if ((isset($params['exposition_id']) && !isset($params['visiteur_id'])) ||
                (isset($params['univers_id']) && !isset($params['visiteur_id']))
            ) {
                throw new Exception("visiteur_id is mandatory");
            }

            $format = $this->getFormat();
            $this->validateIndex($params);
        } catch (Exception $e) {
            $this->getResponse()->setStatusCode(406);
            $serializer = $this->getSerializer();
            $this->getResponse()->setContentType($serializer->getContentType());
            $error = $e->getMessage();

            // event filter to enable customisation of the error message.
            $result = $this->dispatcher->filter(
                new sfEvent($this, 'sfDoctrineRestGenerator.filter_error_output'),
                $error
            )->getReturnValue();

            if ($error === $result) {
                $error = array(array('message' => $error));
                $this->output = $serializer->serialize($error, 'error');
            } else {
                $this->output = $serializer->serialize($result);
            }

            return sfView::SUCCESS;
        }

        $this->objects = $this->dispatcher->filter(
            new sfEvent(
                $this,
                'sfDoctrineRestGenerator.filter_results',
                array()
            ),
            $this->query($params)->execute(array(), Doctrine::HYDRATE_ARRAY)
        )->getReturnValue();


        $q = $this->query($params);
        if (isset($params['is_anonyme'])) {
            $q->leftJoin($this->model . '.Visiteur v')->addWhere('v.is_anonyme = ?', $params['is_anonyme']);

        }


        $this->objects = $q->execute(array(), Doctrine::HYDRATE_ARRAY);

        $isset_pk = (!isset($isset_pk) || $isset_pk) && isset($params['interactif_id']);
        $isset_pk = (!isset($isset_pk) || $isset_pk) && isset($params['visite_id']);
        if ($isset_pk && count($this->objects) == 0) {
            $request->setRequestFormat($format);
            $this->forward404();
        }

        //Extended list
        $extract = $request->getParameter('extract', null);

        if ($extract == 'full') {
            foreach ($this->objects as $key => $object) {
                //Get Interactif
                $interactif = Doctrine_Query::create()
                    ->from('Interactif i')
                    ->where('i.guid = ?', $object['interactif_id'])
                    ->execute(array(), Doctrine::HYDRATE_ARRAY);

                if (count($interactif) > 0) {
                    unset($interactif[0]['is_tosync']);
                    $this->objects[$key]['Interactif'] = $interactif[0];
                }

                //Get parcours
                $visite = Doctrine_Query::create()
                    ->from('Visite v')
                    ->leftJoin('v.Parcours p')
                    ->leftJoin('p.Exposition e')
                    ->leftJoin('e.Contexte c')
                    ->leftJoin('e.OrganisateurDiffuseur od')
                    ->leftJoin('e.OrganisateurEditeur oe')
                    ->where('v.guid = ?', $object['visite_id'])
                    ->execute(array(), Doctrine::HYDRATE_ARRAY);

                if (count($visite) > 0) {
                    $this->objects[$key]['Parcours'] = $visite[0]['Parcours'];
                }
            }
        }

        // configure the fields of the returned objects and eventually hide some
        $this->setFieldVisibility();
        $this->configureFields();

        $serializer = $this->getSerializer();
        $this->getResponse()->setContentType($serializer->getContentType());
        $this->output = $serializer->serialize($this->objects, $this->model);
        unset($this->objects);
    }

    /**
     * Create the query for selecting objects, eventually along with related
     * objects
     *
     * @param   array $params an array of criterions for the selection
     */
    public function query($params)
    {
        $q = Doctrine_Query::create()
            ->from($this->model . ' ' . $this->model);

        if (!isset($params['page'])) {
            $params['page'] = 1;
        }

        $page_size = 10000;

        if (isset($params['page_size'])) {
            $page_size = $params['page_size'];
            unset($params['page_size']);
        }

        if (isset($params['limit'])) {
            $limit = $params['limit'];
            $sort = "start_at desc";
        } else {
            $limit = $page_size;
            $sort = 'start_at asc';
        }
        $q->offset(($params['page'] - 1) * $page_size);
        unset($params['page']);
        $q->limit($limit);

        if (isset($params['sort_by'])) {
            $sort = $params['sort_by'];
            unset($params['sort_by']);
            if (strpos($sort, ' ')) {
                unset($params['sort_order']);
            }
            if (isset($params['sort_order'])) {
                $sort .= ' ' . $params['sort_order'];
                unset($params['sort_order']);
            }
        }

        if (isset($sort)) {
            $q->orderBy($sort);
        }

        if (isset($params['start_at'])) {
            $start_at = explode(',', $params['start_at']);
            foreach ($start_at as $key => $date) {
                $start_at[$key] = date('Y-m-d H:i:s', $date);
            }
            unset($params['start_at']);
        }

        if (isset($params['end_at'])) {
            $end_at = explode(',', $params['end_at']);
            foreach ($end_at as $key => $date) {
                $end_at[$key] = date('Y-m-d H:i:s', $date);
            }
            unset($params['end_at']);
        }

        if (!isset($params['page'])) {
            $params['page'] = 1;
            $params['page_size'] = 10000;
        } elseif (!$params['page_size']) {
            $params['page_size'] = 10000;
        }

        if (isset($params['visite_id'])) {
            $values = explode(',', $params['visite_id']);

            if (count($values) == 1) {
                $q->andWhere($this->model . '.visite_id = ?', $values[0]);
            } else {
                $q->whereIn($this->model . '.visite_id', $values);
            }

            unset($params['visite_id']);
        }

        if (isset($params['visiteur_id'])) {
            $values = explode(',', $params['visiteur_id']);

            if (count($values) == 1) {
                $q->andWhere($this->model . '.visiteur_id = \'' . $values[0] . '\'');
            } else {
                $q->whereIn($this->model . '.visiteur_id', $values);
            }

            unset($params['visiteur_id']);
        }

        if (isset($start_at)) {
            if (count($start_at) == 2) {
                $q->andWhere($this->model . '.start_at >= \'' . $start_at[0] . '\'');
                $q->andWhere($this->model . '.start_at <= \'' . $start_at[1] . '\'');
            } elseif (count($start_at) == 1) {
                $q->andWhere($this->model . '.start_at >= \'' . $start_at[0] . '\'');
            }
        }
        if (isset($end_at)) {
            if (count($end_at) == 2) {
                $q->andWhere($this->model . '.end_at >= \'' . $end_at[0] . '\'');
                $q->andWhere($this->model . '.end_at <= \'' . $end_at[1] . '\'');
            } elseif (count($end_at) == 1) {
                $q->andWhere($this->model . '.end_at <= \'' . $end_at[0] . '\'');
            }
        }

        if (isset($params['interactif_id'])) {
            $values = explode(',', $params['interactif_id']);

            if (count($values) == 1) {
                $q->andWhere($this->model . '.interactif_id = \'' . $values[0] . '\'');
            } else {
                $q->whereIn($this->model . '.interactif_id', $values);
            }

            unset($params['interactif_id']);
        }

        if (isset($params['exposition_id'])) {
            $values = explode(',', $params['exposition_id']);

            if (count($values) == 1) {
                $q->andWhere($this->model . '.exposition_id = \'' . $values[0] . '\'');
            } else {
                $q->whereIn($this->model . '.exposition_id', $values);
            }

            unset($params['exposition_id']);
        }

        return $q;
    }

    /**
     * Applies a set of validators to an array of parameters
     *
     * @param array $params An array of parameters
     * @param array $validators An array of validators
     * @throw sfException
     */
    public function validate($params, $validators, $prefix = '')
    {

        $unused = array_keys($validators);
        foreach ($params as $name => $value) {

            if (!isset($validators[$name])) {
                throw new sfException(sprintf('Could not validate extra field "%s"', $prefix . $name));
            } else {
                if (is_array($validators[$name])) {
                    // validator for a related object
                    $this->validate($value, $validators[$name], $prefix . $name . '.');
                } else {
                    if ('start_at' == $name || 'end_at' == $name) {
                        $value = explode(',', $value);
                        foreach ($value as $val) {
                            $validators[$name]->clean($val);
                        }
                    } else {
                        $validators[$name]->clean($value);
                    }
                }

                unset($unused[array_search($name, $unused, true)]);
            }
        }

        // are non given values required?
        foreach ($unused as $name) {
            try {
                if (!is_array($validators[$name])) {
                    $validators[$name]->clean(null);
                }
            } catch (Exception $e) {
                throw new sfException(sprintf('Could not validate field "%s": %s', $prefix . $name, $e->getMessage()));
            }
        }
    }


    public function executeCreate(sfWebRequest $request)
    {
        $this->forward404Unless($request->isMethod(sfRequest::POST));
        $content = $request->getContent();

        // Restores backward compatibility. Content can be the HTTP request full body, or a form encoded "content" var.
        if (strpos($content, 'content=') === 0) {
            $content = $request->getParameter('content');
        }

        $request->setRequestFormat('html');

        $serializer = $this->getSerializer();
        $this->getResponse()->setContentType($serializer->getContentType());
        $parameters = $this->parsePayload($content, true);
        // $content = $serializer->serialize($parameters);
        $interactif_id = $parameters['interactif_id'];
        $visite_id = isset($parameters['visite_id']) ? $parameters['visite_id'] : '';

        $start_at = $parameters['start_at'];
        $end_at = $parameters['end_at'];

        $connection = $parameters['connection'];

        if ($start_at) {
            $start_at = date('Y-m-d H:i:s', $start_at);
            $parameters['start_at'] = $start_at;
        }

        if ($end_at) {
            $end_at = date('Y-m-d H:i:s', $end_at);
            $parameters['end_at'] = $end_at;
        }
        try {
            if (isset($connection) && $connection == 'insitu') {
                $this->validateCreateInsitu($content);
            } else {
                $this->validateCreate($content);
            }


        } catch (Exception $e) {
            $this->getResponse()->setStatusCode(406);
            $serializer = $this->getSerializer();
            $this->getResponse()->setContentType($serializer->getContentType());
            $error = $e->getMessage();

            // event filter to enable customisation of the error message.
            $result = $this->dispatcher->filter(
                new sfEvent($this, 'sfDoctrineRestGenerator.filter_error_output'),
                $error
            )->getReturnValue();

            if ($error === $result) {
                $error = array(array('message' => $error));
                $this->output = $serializer->serialize($error, 'error');
            } else {
                $this->output = $serializer->serialize($result);
            }

            return sfView::SUCCESS;
        }

        $this->object = $this->createObject();
        $this->updateObjectFromRequest($serializer->serialize($parameters));

        return $this->doSave();
    }

    protected function updateObjectFromRequest($content)
    {
        $this->object->importFrom('array', $this->parsePayload($content, true));
    }

    protected function createObject()
    {
        $object = new $this->model();
        $object->setGuid(Guid::generate());

        return $object;
    }

    /**
     * Applies the update validators to the payload posted to the service
     *
     * @param   string $payload A payload string
     */
    public function validateCreate($payload)
    {
        $validators = $this->getCreateValidators();
        $params = $this->parsePayload($payload);
        $this->validate($params, $validators);
    }

    public function validateCreateInsitu($payload)
    {
        $validators = $this->getCreateValidatorsInsitu();
        $params = $this->parsePayload($payload);
        $this->validate($params, $validators);
    }

    protected function doSave()
    {
        $this->object->save();

        Resque::enqueue('default', 'Job_LogVisite', array("log_visite_id" => $this->object->getGuid()));

        $serializer = $this->getSerializer();
        $object = Doctrine_Query::create()
            ->from('LogVisite v')
            ->where('v.guid = ?', $this->object->getGuid())
            ->execute(array(), Doctrine::HYDRATE_ARRAY);

        $this->getResponse()->setContentType($serializer->getContentType());
        $this->output = $serializer->serialize($object, $this->model);
        $this->setTemplate('index');

        return sfView::SUCCESS;
        $this->object->save();

    }

    public function getCreateValidatorsInsitu()
    {
        return array(
            'visiteur_id' => new sfValidatorDoctrineChoice(array(
                    'model' => Doctrine_Core::getTable(
                            'LogVisite'
                        )->getRelation('Visiteur')->getAlias(),
                    'required' => true
                )),
            'start_at' => new sfValidatorDateTime(array('required' => true)),
            'end_at' => new sfValidatorDateTime(array('required' => false)),
            'resultats' => new sfValidatorString(array('required' => false)),
            'parcours_id' => new sfValidatorString(array('required' => false)),
            'interactif_libelle' => new sfValidatorString(array('required' => false)),
            'interactif_id' => new sfValidatorDoctrineChoice(array(
                    'model' => Doctrine_Core::getTable(
                            'LogVisite'
                        )->getRelation('Interactif')->getAlias(),
                    'required' => true
                )),
            'exposition_id' => new sfValidatorDoctrineChoice(array(
                    'model' => Doctrine_Core::getTable(
                            'LogVisite'
                        )->getRelation('Exposition')->getAlias(),
                    'required' => true
                )),
            'visite_id' => new sfValidatorDoctrineChoice(array(
                    'model' => Doctrine_Core::getTable(
                            'LogVisite'
                        )->getRelation('Visite')->getAlias(),
                    'required' => true
                )),
            'score' => new sfValidatorInteger(array('required' => false)),
            'connection' => new sfValidatorString(array('required' => false)),
            'contexte_id' => new sfValidatorString(array('required' => false))
        );
    }

    /**
     * Method call from market apps or moduleWeb
     * Just not require exposition_id and visite_id vars
     *
     * @param   string $payload A payload string
     */
    public function getCreateValidators()
    {
        return array(
            'visiteur_id' => new sfValidatorDoctrineChoice(array(
                    'model' => Doctrine_Core::getTable(
                            'LogVisite'
                        )->getRelation('Visiteur')->getAlias(),
                    'required' => true
                )),
            'start_at' => new sfValidatorDateTime(array('required' => true)),
            'end_at' => new sfValidatorDateTime(array('required' => false)),
            'resultats' => new sfValidatorString(array('required' => false)),
            'parcours_id' => new sfValidatorString(array('required' => false)),
            'interactif_libelle' => new sfValidatorString(array('required' => false)),
            'interactif_id' => new sfValidatorDoctrineChoice(array(
                    'model' => Doctrine_Core::getTable(
                            'LogVisite'
                        )->getRelation('Interactif')->getAlias(),
                    'required' => true
                )),
            'exposition_id' => new sfValidatorDoctrineChoice(array(
                    'model' => Doctrine_Core::getTable(
                            'LogVisite'
                        )->getRelation('Exposition')->getAlias(),
                    'required' => false
                )),
            'visite_id' => new sfValidatorDoctrineChoice(array(
                    'model' => Doctrine_Core::getTable(
                            'LogVisite'
                        )->getRelation('Visite')->getAlias(),
                    'required' => false
                )),
            'score' => new sfValidatorInteger(array('required' => false)),
            'connection' => new sfValidatorString(array('required' => false)),
            'contexte_id' => new sfValidatorString(array('required' => false))
        );
    }

    protected function setFieldVisibility()
    {
        foreach ($this->objects as $i => $object) {
            unset(
            $this->objects[$i]['is_tosync'],
            $this->objects[$i]['Parcours']['is_tosync']
            );
            if (isset($this->objects[$i]['Parcours']['Exposition'])) {
                foreach ($this->objects[$i]['Parcours']['Exposition'] as $a => $object2) {
                    if (isset($this->objects[$i]['Parcours']['Exposition'][$a]['is_tosync'])) {
                        unset($this->objects[$i]['Parcours']['Exposition'][$a]['is_tosync']);
                    }
                }
                if (isset($this->objects[$i]['Parcours']['Exposition'][$a])) {
                    foreach ($this->objects[$i]['Parcours']['Exposition'][$a] as $b => $object3) {
                        if (is_array(
                                $this->objects[$i]['Parcours']['Exposition'][$a][$b]
                            ) && isset($this->objects[$i]['Parcours']['Exposition'][$a][$b]['is_tosync'])
                        ) {
                            unset($this->objects[$i]['Parcours']['Exposition'][$a][$b]['is_tosync']);
                        }
                    }
                }
            }
        }

    }
}
