#!/bin/bash


cd /etc/asterisk/misc/sofie

while [ 1 ]
do

echo "`date`"

result1=`ps aux | grep -i "alert_hors_delai_prise_charge_minute.php" | grep -v "grep" | wc -l`
if [ $result1 -ge 1 ]
   then
   		echo "alert_hors_delai_prise_charge_minute.php is running"
   else
        echo "salert_hors_delai_prise_charge_minute.php is not running" && `/usr/bin/php -f alert_hors_delai_prise_charge_minute.php >> /var/log/sofie_alert.log &`
  
fi

result2=`ps aux | grep -i "alert_hors_delai_reparation_minute.php" | grep -v "grep" | wc -l`
if [ $result2 -ge 1 ]
   then
   		echo "alert_hors_delai_reparation_minute.php is running"     
   else
        echo "alert_hors_delai_reparation_minute.php is not running" && `/usr/bin/php -f alert_hors_delai_reparation_minute.php >> /var/log/sofie_alert.log &`
fi


sleep 5
done
