<?php
$mysql_host = "192.168.1.110";
$mysql_user = "sofie";
$mysql_password = "poiuyt";
$mysql_dbname = "db_sofiev3";

$sms_content = "";
$dbObject = "v_forage_hors_delai_reparation_minute";
$kannel_sendsms_uri = "http://localhost:13013/cgi-bin/sendsms?";
$sms_content .= "ALERT - ";//Message ?|  envoyer
$sms_content_part2 = ": HORS DELAI DE REPARATION!";

$sofie_SMS_SHORTCODE = "77789417";//Definir ici le numÃ©ro du modem

try {
    $dbh = new PDO("mysql:host=$mysql_host;dbname=$mysql_dbname", $mysql_user, $mysql_password);
    $result=$dbh->query("SELECT * FROM ".$dbObject);
    $rows =  $result->fetchAll(PDO::FETCH_OBJ);	
}
catch(PDOException $e)
{
   echo $e->getMessage();
}

if($result->rowCount()>0)
{
	foreach($rows as $row)
        {
		$result2=$dbh->query("SELECT f_getInfosOuvrageByCodeOuvrage(".$row->CODE_OUVRAGE.") AS INFOS_OUVRAGE");
		$label=$result2->fetch(PDO::FETCH_OBJ);
		$sofie_NUMERO = $row->NumAppel;//Numero de telephone
		$sms_content .= $label->INFOS_OUVRAGE;
		echo $kannel_sendsms_uri .= "username=modem1&password=458asn&smsc=modem1&from=".$sofie_SMS_SHORTCODE."&to=+".substr($sofie_NUMERO,-8)."&text=".urlencode($sms_content.": HORS DELAI DE REPARATION!");
		$send_state = file_get_contents($kannel_sendsms_uri);
		echo $send_state;
		if($send_state="status=0"){
			$UpdateQuery = "UPDATE t_panne SET ALERT=2 WHERE IDOuvrage=f_getIDOuvrageByCodeOuvrage(".$row->CODE_OUVRAGE.") AND ((ALERT=1) OR (ALERT=0))";
			$UpdateResult = $dbh->exec($UpdateQuery);
			
			echo $UpdateQuery2 = "INSERT INTO `t_notification` (DateHeureNotif,MotifNotif,IDPanne,IDNumAppel)VALUES(NOW(),6,f_getPanneIdByPanneTicket(".$row->NumPanne."),f_getIDNumAppelAgentByIDAgent(".$row->IDAgent."))";
                        $UpdateResult2 = $dbh->exec($UpdateQuery2);
		}
	}
}	
else
{
	echo "DATA NOT FOUND...";
	$dbh = null;
	exit;
}

?>
