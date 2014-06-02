<?php

class changeNonUniquePseudoTask extends sfBaseTask
{
  protected function configure()
  {
    $this->addOptions(array(
      new sfCommandOption('application', null, sfCommandOption::PARAMETER_REQUIRED, 'api'),
      new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'prod', 'prod'),
      new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'doctrine', 'doctrine'),
      // add your own options here
    ));


      //$configuration = ProjectConfiguration::getApplicationConfiguration('api' , 'prod' , false);
      //$context = sfContext::createInstance($configuration);


    $this->namespace        = 'servervip';
    $this->name             = 'change-non-unique-pseudo';
    $this->briefDescription = '';
    $this->detailedDescription = <<<EOF
The [change-non-unique-pseudo|INFO] task change pseudo / password and send email to visitor.
Call it with:

  [php symfony create-user-media|INFO]
EOF;
  }

  protected function execute($arguments = array(), $options = array())
  {
    // initialize the database connection

    $databaseManager = new sfDatabaseManager($this->configuration);
    //
      $body = <<<EOF
<!DOCTYPE HTML PUBLIC>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title></title>
</head>
<body style="margin:5; padding:0;">
<table border="0" cellpadding="0" cellspacing="0" width="600px">
    <tbody>
    <tr style="margin:0; padding:0;">
        <td style ="height:40px; padding-bottom: 35px;">
            <img src="http://navinum2.cap-sciences.net/mail-cyou/logo-mail.jpg"/>
        </td>
    </tr>


    <tr>
        <td style="height:42px">
            <p><span style="color:black; font-family:Helvetica, sans-serif; font-weight:700; font-size:14px;">Bonjour <span style="color:#7FB6E4;">%s</span></span>,</p>
            <p><span style="color:black; font-family:Helvetica, sans-serif; font-weight:200; font-size:14px;">Vos identifiants Cap Sciences ont changé :</span><br/>
                <br/>

            <p><span style="color:black; font-family:Helvetica, sans-serif; font-weight:200; font-size:14px;"><b>Login :</b> %s</span><br/></p>
            <p><span style="color:black; font-family:Helvetica, sans-serif; font-weight:200; font-size:14px;"><b>Mot de passe :</b> %s</span><br/></p>
        </td>
    </tr>



<tr style="height:50px">
    <td style="height:30px">
        <p style="color:black; font-family:Helvetica, sans-serif; font-weight:200; font-size:14px;">
            &laquo; C.YOU soon &raquo; .</p>
        <p style="color:black; font-family:Helvetica, sans-serif; font-weight:200; font-size:14px;"><b>L’&eacute;quipe C.YOU </b></p></td>
</tr>
<tr style="height:100px">
    <td style="height:30px">
        <p style="color:black; font-family:Helvetica, sans-serif; font-weight:200; font-size:14px;">&nbsp;</p>
        <p style="color:black; font-family:Helvetica, sans-serif; font-weight:200; font-size:14px;">Pour toutes informations sur le dispositif C.YOU, envoyez-nous un mail &agrave; :</p><br/>
</tr>
<tr>
    <table border="0" cellpadding="0" cellspacing="0" width="600px" style=" margin-bottom:40px;">
        <tbody>
        <tr style="margin:0; padding:0;">
            <td style ="width:10px; background-color:#DE3346;"></td>
            <td style="width:405px;">
                <p style ="color:black; font-weight:200; font-family:Helvetica, sans-serif; margin-left:10px;">CONTACT: <a style="color:#DE3346; font-family:Helvetica, sans-serif; font-weight:200;">c.you@cap-sciences.net</a></p>
            </td>
            <td style ="width:10px; background-color:#DE3346;"></td>
            <td style="color:black; font-family:Helvetica, sans-serif; font-weight:200; font-size:14px; padding-left:10px;"><a href="http://navinum2.cap-sciences.net/cgu/cgu.html">Mentions l&eacute;gales</a></td>
        </tr>
        </tbody>
    </table>
<tr>
    <table border="0" cellpadding="0" cellspacing="0" width="600px" style=" margin-bottom:40px;">
        <tbody style="margin:0; padding-top:30px;">
        <tr style="margin:0; padding:0;">
            <td style="width:320px; border-right:solid 1px black;"><img src="http://navinum2.cap-sciences.net/mail-cyou/rond.png" style=" margin-left: 90px; margin-right: auto; position: relative;"/></td>
            <td style="padding-left: 45px;">
                <p style="color:grey; font-family:Helvetica, sans-serif; font-weight:200; font-size:18px;line-height:2px;">CAP SCIENCES</p>
                <p style="color:grey; font-family:Helvetica, sans-serif; font-weight:200; font-size:18px;line-height:2px;">HANGAR 20</p>
                <p style="color:grey; font-family:Helvetica, sans-serif; font-weight:200; font-size:18px; line-height:2px;">QUAI DE BACCALAN</p>
                <p style="color:grey; font-family:Helvetica, sans-serif; font-weight:200; font-size:18px; line-height:2px;">33300 BORDEAUX</p>
            </td>
        </tr>
        </tbody>
    </table>
</tr>
<tr>
    <table border="0" cellpadding="0" cellspacing="0" width="600px" style=" margin-bottom:40px;">
        <tbody style="margin:0; padding-top:30px;">
        <tr style="margin:0; padding:0;">
            <td height="30px"><p style="margin-left:80px; color:black; font-weight:200; font-family:Helvetica, sans-serif; font-size: 12px;">Pour vous désinscrire, veuillez nous envoyez un mail à <span style="color:#DE3346; font-family:Helvetica, sans-serif; font-weight:200;">c.you@cap-sciences.net</span></p></td>
        </tr>
        </tbody>
    </table>
</tr>
</tbody>
</table>
</body>
</html>
EOF;


      $this->logSection("INFO", "BEGIN Update visiteur pseudo");

      $query = 'select guid, pseudo_son,email, is_anonyme from visiteur where (is_anonyme = 0 OR  is_anonyme IS NULL) group by pseudo_son having count(pseudo_son) > 1';
      $resultset = Doctrine_Manager::getInstance()->getCurrentConnection()->fetchAssoc($query);

      foreach($resultset as $result){
        $query = 'SELECT guid, pseudo_son,email, is_anonyme from visiteur where (is_anonyme = 0 OR is_anonyme IS NULL) AND pseudo_son="'.$result['pseudo_son'].'" AND guid != "'.$result['guid'].'"';
        $res = Doctrine_Manager::getInstance()->getCurrentConnection()->fetchAssoc($query);
          $cpt = 1;
          foreach($res as $visiteur){
              $randomPass = strtolower(substr(Guid::generate(), 0, 4));

              $is_exist = true;
              while($is_exist == true){
                  $new_login = $visiteur['pseudo_son'].$cpt;
                  $query = 'SELECT guid FROM visiteur WHERE pseudo_son = "'.$new_login. '"';
                  $check_pseudo = Doctrine_Manager::getInstance()->getCurrentConnection()->fetchAssoc($query);
                  if(count($check_pseudo) > 0){
                      $this->logSection("WARNING", $new_login. ' already exists, will find another');
                      $is_exist = true;
                      $cpt++;
                  }else{
                      $is_exist = false;
                  }
              }

              // is no one have this pseudo, update it, else increments again
              $this->logSection("INFO", "Replace " .$visiteur['pseudo_son']. " with ".$new_login . ' : ' .$visiteur['guid'] );
              $query = 'UPDATE visiteur set pseudo_son = "'.$new_login.'", password_son = md5("'.$randomPass.'") WHERE guid = "'.$visiteur['guid'].'"';
              //$r = Doctrine_Manager::getInstance()->getCurrentConnection()->execute($query);

              $this->getMailer();

              // force receiver
              $visiteur['email'] = 's.etheve@cap-sciences.net';

              $message = sprintf($body, $visiteur['pseudo_son'], $randomPass, $new_login);

              $c = $this->getMailer()
                  ->compose('capsciences@capsciences.com', $visiteur['email'], 'Vos identifiants Cap Sciences ont changé');
              $c->setBody($message, 'text/html');
              //$this->getMailer()->send($c);

              $this->logSection("INFO", 'UPDATE visiteur set pseudo_son = "'.$new_login.'", password_son = md5("'.$randomPass.'") WHERE guid = "'.$visiteur['guid'].'"');

              //$this->logSection("INFO", $c->getBody());
              $this->logSection("INFO", "================");
              //exit;
          }

      }
      $this->logSection("INFO", "END Update visiteur pseudo");

  }


}
