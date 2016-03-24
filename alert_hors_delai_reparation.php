<?php
$mysql_host = "localhost";
$mysql_user = "sofie";
$mysql_password = "poiuyt";
$mysql_dbname = "db_sofie";

$dbObject = "v_forage_hors_delai_reparation";
$kannel_sendsms_uri = "http://localhost/sms/sendsms.php?";
//$sms_content = "ALERT - REPARATION HORS DELAI POUR LE FORAGE ";//Message à envoyer
$sms_content_part1 = "ALERT-";//Message à envoyer
$sms_content_part2 = ' HORS DELAI DE REPARATION!';
$sms_content = $sms_content_part1;
$sofie_SMS_SHORTCODE = "77789417";//Definir ici le numéro du modem
//$sofie_SMS_SHORTCODE = "1022";//Definir ici le numéro du modem

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
		$result=$dbh->query("SELECT f_getInfosOuvrageByCodeOuvrage(".$row->CODE_OUVRAGE.") AS INFOS_OUVRAGE");
		$label=$result->fetch(PDO::FETCH_OBJ);
		$sofie_NUMERO = $row->NUMERO;//Numero de telephone
		echo $kannel_sendsms_uri .= "SOA=".$sofie_SMS_SHORTCODE."&DA=".$sofie_NUMERO."&Modem=1&Content=".$sms_content.$label->INFOS_OUVRAGE."/OUVRAGE ".$row->CODE_OUVRAGE.$sms_content_part2;
		$send_state = file_get_contents($kannel_sendsms_uri);
		//echo $send_state;
		if($send_state="status=0"){
			$UpdateQuery = "UPDATE t_panne SET ALERT=2 WHERE IDOuvrage=f_getIDOuvrageByCodeOuvrage(".$row->CODE_OUVRAGE.") AND ALERT=1";
			$UpdateResult = $dbh->exec($UpdateQuery);
			
			$UpdateQuery2 = "INSERT INTO `t_notification` (DateHeureNotif,MotifNotif,IDPanne,IDNumAppel)VALUES(NOW(),6,f_getPanneIdByPanneTicket(".$row->NumPanne."),0)";
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
