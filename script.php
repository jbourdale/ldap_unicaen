<?php
  
  /*
  
  CE SCRIPT PEUT ETRE PRIT POUR UN TRUC SUSPECT MAIS C'ETAIT JUSTE
  POUR RECUPERER LES ADRESSES MAILS DES CONTACTS DE L'UNIVERSITE COMME
  ON PEUT AVOIR SUR ZIMBRA, POUR LES METTRES DANS UN CSV POUR POUVOIR
  LES IMPORTS DANS MA MESSAGERIE PERSO.
  
  MAIL : etudiant@jbourdale.fr
  
  */
  
  $username = "21502317";
  $ldappass = "MDPUNICAEN";
  
  
  $usernameLDAP = "uid=e21502317,ou=people,dc=unicaen,dc=fr";
  
// connect to ldap server
  $ldapconn = ldap_connect("ldap.unicaen.fr")
     or die("Could not connect to LDAP server.");
  ldap_set_option($ldapconn, LDAP_OPT_PROTOCOL_VERSION, 3);

  // on récup tous les dn de tout le monde
  $dn = "ou=people,dc=unicaen,dc=fr";
  $filter="(supannAliasLogin=*)";
  $data = array("dn");
  $sr=ldap_search($ldapconn, $dn, $filter, $data);
  $info = ldap_get_entries($ldapconn, $sr);

  /*echo "<pre>INFO : \n";
  print_r($info['count']);
  print_r($info);
  echo "</pre>";
  
  for($i=0; $i<$info['count'];$i++){
    echo $info[$i]["dn"]."\n";
  }*/
  
  // on recupere le dn final de l'utilisateur
  if (isset($info[0])){
    if ($ldapconn) {
      $ldapbind = ldap_bind($ldapconn, $usernameLDAP, $ldappass);
      
      if ($ldapbind) {
        
        $fp = fopen('mail.txt', "w+");
        echo "<pre>";
        
        for($i=0; $i<$info['count'];$i++){
        //for($i=0; $i<4;$i++){
          $filter="(objectclass=*)";
          $justthese = array("displayname", "mail", "uid");
          //echo "i = $i";
          $sr=ldap_read($ldapconn, $info[$i]["dn"], $filter, $justthese);
          $entry = ldap_get_entries($ldapconn, $sr);
          //echo "<pre>";print_r($entry);echo "</pre>";
          if(isset($entry[0]['displayname']) && isset($entry[0]['mail']) && isset($entry[0]['uid'])){
            //if ($entry[0]['uid'][0] >= 21000000){
              
              $line = '"'.$entry[0]['displayname'][0].'","'.$entry[0]['mail'][0].'"';
              echo $line."\n";
              fwrite($fp, $line."\n");
   
            //}
          }
        }       
        fclose($fp);
        echo "</pre>"; 
      }
    }
  }
  
  
?>


