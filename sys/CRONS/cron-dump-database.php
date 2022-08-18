<?php

$fileName = '/home/u296526003/domains/portalsmart.com.br/public_html/app/sys/DUMPS-DATABASE/' . date('d-m-Y'). '_' .date('H:i') . '.sql';

exec("mysqldump -u u296526003_root -pMj05012018@ u296526003_app > $fileName");