<?php

/**
 * Dumps database for backup purposes.
 */
class sfMySQLDumpTask extends sfBaseTask
{
  /**
   * @see sfTask
   */
  protected function configure()
  {
    $this->addOptions(array(
      new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'dev'),
      new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'doctrine'),
    ));

    $this->addArguments( array(
      new sfCommandArgument('application', sfCommandArgument::OPTIONAL, 'The email address to send to', 'backend'),
    ));

    $this->namespace = 'mysql';
    $this->name = 'dump';
    $this->briefDescription = 'Dumps a MySQL database using mysqldump';
  }

  /**
   * @see sfTask
   */
  protected function execute($arguments = array(), $options = array())
  {
    $configuration = ProjectConfiguration::getApplicationConfiguration($arguments['application'], $options['env'], true);
    sfContext::createInstance($configuration);
    $databaseManager = new sfDatabaseManager($this->configuration);
    $databaseConnection = $databaseManager->getDatabase( $options['connection'] );

    $username = $databaseConnection->getParameter( 'username' );
    $password = $databaseConnection->getParameter( 'password' );
    $dsnInfo = $this->parseDsn( $databaseConnection->getParameter( 'dsn' ) );

    // dump database
    $fileName = $dsnInfo['dbname'].'-sql-dump-'.date('Y').date('m').date('d').date('H').date('i').'.sql';
    $tmpfile = tempnam('/tmp', $fileName);
    $cmd = 'mysqldump '.$dsnInfo['dbname'].' --user='.$username.' --password='.$password.' --host='.$dsnInfo['host'].' > '.$tmpfile;
    system($cmd);
    system ( "gzip $tmpfile" );
    $tmpfile .= '.gz';
    
    copy($tmpfile, sfConfig::get('sf_root_dir').'/backups/'.$fileName.'.gz');
    unlink($tmpfile);
  }

  private function parseDsn( $dsn )
  {
    $dsnArray = array();
    $dsnArray['phptype'] = substr($dsn, 0, strpos($dsn, ':'));
    preg_match('/dbname=(\w+)/', $dsn, $dbname);
    $dsnArray['dbname'] = $dbname[1];
    preg_match('/host=(\w+)/', $dsn, $host);
    $dsnArray['host'] = $host[1];

    return $dsnArray;
  }
}
