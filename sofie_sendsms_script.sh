#!/bin/bash

for i in `seq 1 12`;
do

result=`ps aux | grep -i "sofie_sendsms_script.sh" | grep -v "grep" | wc -l`
if [ $result -ge 1 ]
   then
	echo "`date` Service sendsms is running"  >> /var/log/sofie_sendsms.log
   else
	echo "`date` Service sendsms is not running"  >> /var/log/sofie_sendsms.log 
        `cd /var/www/html/sofiev4/api/scripts/sofie_sendsms_script && php sofie_sendsms_script.php >> /var/log/sofie_sendsms.log &`

fi

sleep 5

done
