<?php

class ServerVipSync
{
  protected $sync_log;
  protected $dump_intranet_path = '';
  protected $dump_internet_path = '';
  protected $type;
  protected $configuration;

  /**
   * Initialise la syncro
   * Récupération de la dernière date de synchro + création dateNowSync + génération Guid synchro
   * - Récupération de la dernière date de synchro dans le modèle syncLog
   * - On fige une date de synchro courante
   * - On génére un guid de synchro qui sera servira d'identifiant au modèle syncLog et de nom de fichier du dump
   * - Insère en BDD les paramètres de syncho dans le modèle syncLog
   * @param sfApplicationConfiguration $configuration
   * @param SyncLog $sync_log
   * @param string $type
   */
  public function __construct($configuration, $sync_log = null, $type = 'intranet')
  {
    if (!$sync_log)
    {
	    $this->log("Contruction des paramètres de la synchro $type");
      $this->sync_log = new SyncLog();
      $this->sync_log->setGuid(Guid::generate());
      $this->sync_log->setOrigin(sfConfig::get('app_sync_name'));
      $this->sync_log->setFromDatetimeSync($this->getLastSyncDate());
      $this->sync_log->setToDatetimeSync(date('Y-m-d H:i:s'));
      $this->log("Date du ".$this->getLastSyncDate(). " au ".$this->sync_log->getToDatetimeSync());
    }
    else
    {
      $this->sync_log = $sync_log;
    }

    $this->type = $type;
    $this->configuration = $configuration;
  }

  /**
   * Récupération de la dernière date de synchro dans le modèle syncLog
   * @return string
   */
  protected function getLastSyncDate()
  {
    $last_sync = Doctrine_Query::create()
                            ->select('sl.*')
                            ->from('SyncLog sl')
                            ->where('sl.origin = ?', $this->sync_log->getOrigin())
                            ->orderBy('sl.to_datetime_sync DESC')
                            ->limit(1)
                            ->fetchOne();

    if (!$last_sync)
    {
      return 0;
    }
    else
    {
      return $last_sync->getToDatetimeSync();
    }
  }
  
  /**
   * Démarage de la synchro depuis l'intranet
   */
  public function startSync()
  {
    if ($this->type == 'intranet')
    {
      //$this->updateSyncFordebug();

      $this->log("Démarrage de la synchro ".$this->type);
      $this->sync_log->save();
      $this->log("Préparation du dump intranet");
      $this->makeIntranetDump();

      $this->log("Appel au serveur internet");
      $this->callInternet();
      $this->log("Execution du dump internet");
      $this->executeInternetDump();
      $this->log("Synchro complète, inscription dans le log");
      $this->setIsSyncOk();
      $this->sync_log->setIsDone(1);
      $this->log("Fin.");
      $this->sync_log->save();
    }
    else
    {
      throw new sfException('La synchronisation ne peut être initialisée que depuis un Intranet.');
      exit;
    }
  }

    protected function updateSyncFordebug()
    {
        $tables = sfConfig::get('app_sync_tables');
        foreach ($tables as $table)
        {
            $table_name = Doctrine_Core::getTable($table)->getTableName();
            Doctrine_Query::create()
                ->update($table)
                ->set('is_tosync', 1)
                ->where('is_tosync = 0')
                ->orderBy('created_at ASC')
                ->limit(10000)
                ->execute(array(), Doctrine::HYDRATE_ARRAY);


            $this->log(" --> ".' update '.$table_name);

        }

    }

  /**
   * Synchro sur l'internet
   */
  public function internetSync()
  {
    if ($this->type == 'internet')
    {
      $this->sync_log->save();
      $this->makeInternetDump();
      $this->executeIntranetDump();
      $this->setIsSyncOk();
      $this->sync_log->setIsDone(1);
      $this->sync_log->save();
      return $this->dump_internet_path;
    }
    else
    {
      throw new sfException('La synchronisation internet ne peut être lancée depuis un intranet.');
      exit;
    }
  }

  /**
   * Création du dump de l'intranet
   * Modèles à dumper défini dans app.yml
   * Objets dont le is_tosync est à 1
   */
  protected function makeIntranetDump()
  {
    $tables = sfConfig::get('app_sync_tables');
    $this->dump_intranet_path = sfConfig::get('sf_root_dir').'/'.sfConfig::get('app_sync_dump_dir').'/'.$this->sync_log->getGuid().'.intranet.dump.gz';
    $dump = gzopen($this->dump_intranet_path, 'w');

    $delete_query = array();
    $insert_query = array();
    $delete_objects = array();
    foreach ($tables as $table)
    {
      $columns = Doctrine_Core::getTable($table)->getColumnNames();
      $table_name = Doctrine_Core::getTable($table)->getTableName();
      $primary_key = Doctrine_Core::getTable($table)->getIdentifier();
      $objects = Doctrine_Core::getTable($table)->findBy('is_tosync', true, Doctrine::HYDRATE_ARRAY);

    	$this->log(" -- ".count($objects).' enregistrements dans '.$table_name);
      if (count($objects) > 0)
      {
        $delete_query[] = $this->buildDeleteSqlFile($objects, $columns, $table_name, $primary_key);
        $insert_query[] = $this->buildInsertSqlFile($objects, $columns, $table_name, $primary_key);
        if ($table == 'DeleteLog')
        {
          $delete_objects[] = $this->buildDeleteObjects($objects);
        }
      }
    }
    $delete_query = implode("", array_reverse($delete_query));
    $insert_query = implode("", $insert_query);
    $delete_objects = implode("", $delete_objects);
    $header = '-- Généré le : '.date('d').'/'.date('m').'/'.date('Y').' à '.date('H').':'.date('i').':'.date('s')."\n";
    $header .= "SET foreign_key_checks = 0;\n";
    $end = "\nSET foreign_key_checks = 1;";
    $this->log("Enregistrement du fichier ".$this->dump_intranet_path);
    gzwrite($dump, $header.$delete_query . $insert_query . $delete_objects.$end);
    gzclose($dump);
    chmod($this->dump_intranet_path, 777);
  }

  /**
   * Appel de l'internet depuis l'intranet
   * Envoi du dump et des paramètres de synchro par curl
   * Récupération du dump internet
   */
  protected function callInternet()
  {
    $ch = curl_init();
    $this->log("Appel au serveur ".sfConfig::get('app_sync_internet_url'));
    $post_parameters = array(
        'dump_intranet'   => '@'.$this->dump_intranet_path,
        'guid'                   => $this->sync_log->getGuid(),
        'from_date_sync' => $this->sync_log->getFromDatetimeSync(),
        'to_date_sync'     => $this->sync_log->getToDatetimeSync(),
        'origin'                 => $this->sync_log->getOrigin()
    );
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_VERBOSE, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_URL, sfConfig::get('app_sync_internet_url'));
    curl_setopt($ch, CURLOPT_POST,1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post_parameters);
    $result = curl_exec ($ch);
    curl_close($ch);
//    var_dump($result);
    if (!$result)
    {
      throw new sfException('La connexion au serveur internet a échouée.');
      exit;
    }

    if (strlen($result) > 0)
    {
      $this->dump_internet_path = sfConfig::get('sf_root_dir').'/'.sfConfig::get('app_sync_dump_dir').'/'.$this->sync_log->getGuid().'.internet.dump.gz';
      $dump_internet = fopen($this->dump_internet_path, 'w');
      fwrite($dump_internet, $result);
      fclose($dump_internet);
    }
  }

  /**
   * Création du dump de l'internet
   * Modèles à dumper défini dans app.yml
   * Objects dont le updated date correspond aux dates de la synchro
   */
  protected function makeInternetDump()
  {
    $tables = sfConfig::get('app_sync_tables');
    $this->dump_internet_path = sfConfig::get('sf_root_dir').'/'.sfConfig::get('app_sync_dump_dir').'/'.$this->sync_log->getGuid().'.internet.dump.gz';
    $dump = gzopen($this->dump_internet_path, 'w');

    $delete_query = array();
    $insert_query = array();
    $delete_objects = array();
    foreach ($tables as $table)
    {
      $columns = Doctrine_Core::getTable($table)->getColumnNames();
      $table_name = Doctrine_Core::getTable($table)->getTableName();
      $primary_key = Doctrine_Core::getTable($table)->getIdentifier();
      $objects = Doctrine_Query::create()
                       ->from($table.' t')
                       ->where('t.updated_at > ?', $this->sync_log->getFromDatetimeSync())
                       ->andWhere('t.updated_at <= ?', $this->sync_log->getToDatetimeSync())
                       ->execute(array(), Doctrine::HYDRATE_ARRAY);
      $this->log(" -- ".count($objects).' enregistrements dans '.$table_name);
      if (count($objects) > 0)
      {
        $delete_query[] = $this->buildDeleteSqlFile($objects, $columns, $table_name, $primary_key);
        $insert_query[] = $this->buildInsertSqlFile($objects, $columns, $table_name, $primary_key);
        if ($table == 'DeleteLog')
        {
          $delete_objects[] = $this->buildDeleteObjects($objects);
        }
      }
    }
    $delete_query = implode("", array_reverse($delete_query));
    $insert_query = implode("", $insert_query);
    $delete_objects = implode("", $delete_objects);
    $this->log("Enregistrement du fichier ".$this->dump_intranet_path);
    $header = '-- Généré le : '.date('d').'/'.date('m').'/'.date('Y').' à '.date('H').':'.date('i').':'.date('s')."\n";
    $header .= "SET foreign_key_checks = 0;\n";
    $end = "\nSET foreign_key_checks = 1;";
    gzwrite($dump, $header.$delete_query . $insert_query . $delete_objects.$end);
    gzclose($dump);
    chmod($this->dump_internet_path, 777);
  }

  /**
   * Création des requètes de suppression
   * @param array $objects
   * @param array $columns
   * @param string $table_name
   * @param string or array $primary_key
   * @return string
   */
  protected function buildDeleteSqlFile($objects, $columns, $table_name, $primary_key)
  {
    if (is_array($primary_key))
    {
      $delete_query = 'DELETE FROM ' . $table_name . ' WHERE ';
      foreach ($primary_key as $k => $key)
      {
        $delete_query .= $key . ' IN (';
        foreach ($objects as $i => $object)
        {
          if ($i != 0)
          {
            $delete_query .= ', ';
          }
          $delete_query .= '"' . $object[$key] . '"';
        }
        if ($k < (count($primary_key) - 1))
        {
          $delete_query .= ' ) AND ';
        }
        else
        {
          $delete_query .= ");\n";
        }
      }
    }
    else
    {
      $delete_query = 'DELETE FROM ' . $table_name . ' WHERE ' . $primary_key . ' IN (';
      foreach ($objects as $key => $object)
      {
        if ($key != 0)
        {
          $delete_query .= ', ';
        }
        $delete_query .= '"' . $object[$primary_key] . '"';
      }
      $delete_query .= ");\n";
    }
    return $delete_query;
  }

  /**
   * Création des requètes d'insertion
   * @param array $objects
   * @param array $columns
   * @param string $table_name
   * @param string or array $primary_key
   * @return string
   */
  protected function buildInsertSqlFile($objects, $columns, $table_name, $primary_key)
  {
    $insert_query = 'INSERT INTO ' . $table_name . ' (';
    foreach ($columns as $key => $column)
    {
      if ($key != 0)
      {
        $insert_query .= ', ';
      }
      $insert_query .= '`' . $column . '`';
    }
    $insert_query .= ') VALUES ';

    foreach ($objects as $k => $object)
    {
      $insert_query .= "\n(";
      foreach ($columns as $key => $column)
      {
        if ($key != 0)
        {
          $insert_query .= ', ';
        }
        if ($column == 'updated_at')
        {
          $insert_query .= '"' . $this->sync_log->getToDatetimeSync() . '"';
        }
        else
        {
          if ($object[$column] == null) {
            $insert_query .= "null";
          }
          else
          {
            $insert_query .= '"' . addslashes($object[$column]) . '"';
          }
        }
      }
      $insert_query .= ")";
      if ($k < (count($objects) - 1))
      {
        $insert_query .= ",";
      }
    }
    $insert_query .= ";\n";
    return $insert_query;
  }

  /**
   * Création des requètes de suppression par rapport au DeleteLog
   * @param array $objects
   * @return string
   */
  protected function buildDeleteObjects($objects)
  {
    $query = "";
    foreach ($objects as $object)
    {
      $table_name = Doctrine_Core::getTable($object['model_name'])->getTableName();
      $query .= "DELETE FROM ".$table_name.' WHERE guid="'.$object['guid'].'";';
      $query .= "\n";
    }
    return $query;
  }

  /**
   * Execution du dump intranet sur l'internet
   */
  protected function executeIntranetDump()
  {
    $this->dump_intranet_path = sfConfig::get('sf_root_dir').'/'.sfConfig::get('app_sync_dump_dir').'/'.$this->sync_log->getGuid().'.intranet.dump.gz';
    $params = sfSyncContentTools::shellDatabaseParams(sfSyncContentTools::getDatabaseParams($this->configuration));
    if (file_exists($this->dump_intranet_path))
    {
      $fp = fopen($this->dump_intranet_path.'.tmp', "w");
      fwrite($fp, implode("", gzfile($this->dump_intranet_path)));
      fclose($fp);
      passthru("mysql $params --default-character-set=utf8 < ".$this->dump_intranet_path.".tmp", $result);
      unlink($this->dump_intranet_path.".tmp");
    }
    else
    {
			$this->log("Rien à dumper");
    }
  }

 /**
  * Execution du dump intranet sur l'internet
  */
  protected function executeInternetDump()
  {
    $this->dump_internet_path = sfConfig::get('sf_root_dir').'/'.sfConfig::get('app_sync_dump_dir').'/'.$this->sync_log->getGuid().'.internet.dump.gz';
    $params = sfSyncContentTools::shellDatabaseParams(sfSyncContentTools::getDatabaseParams($this->configuration));
    if (file_exists($this->dump_internet_path))
    {
      $file = gzfile($this->dump_internet_path);
    }
    if (isset($file) && $file)
    {
      $fp = fopen($this->dump_internet_path.'.tmp', "w");
      fwrite($fp, implode("", $file));
      fclose($fp);
      passthru("pv ".$this->dump_internet_path.".tmp | mysql $params --default-character-set=utf8", $result);
      unlink($this->dump_internet_path.".tmp");
    }
    else
    {
			$this->log("Rien à dumper");
    }
  }

  /**
   * Marque la synchro comme done
   */
  protected function setIsSyncOk()
  {
    $tables = sfConfig::get('app_sync_tables');
    foreach ($tables as $table)
    {
      Doctrine_Query::create()
           ->update($table)
           ->set('is_tosync', 0)
           ->where('is_tosync = 1')
           ->execute();
    }
  }
  
  public function log($msg){
  	if($this->type == 'intranet'){
	  	echo "# ".$msg."\n";
	  	$this->force_flush();
  	}
  }
  
  public function force_flush(){ 
  	ob_start(); 
  	flush(); 
  } 
}

