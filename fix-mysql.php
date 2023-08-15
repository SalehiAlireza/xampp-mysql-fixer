<?php 

const MYSQL_FOLDER_PATH = __DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'mysql';
const MYSQL_DATA_FOLDER_PATH = MYSQL_FOLDER_PATH.DIRECTORY_SEPARATOR.'data';
const MYSQL_BACKUP_FOLDER_PATH = MYSQL_FOLDER_PATH.DIRECTORY_SEPARATOR.'backup';

function deepCopy(
    string $sourceDirectory,
    string $destinationDirectory,
    string $childFolder = ''
): void {
    $directory = opendir($sourceDirectory);

    if (is_dir($destinationDirectory) === false) {
        mkdir($destinationDirectory);
    }

    if ($childFolder !== '') {
        if (is_dir("$destinationDirectory/$childFolder") === false) {
            mkdir("$destinationDirectory/$childFolder");
        }

        while (($file = readdir($directory)) !== false) {
            if ($file === '.' || $file === '..') {
                continue;
            }

            if (is_dir("$sourceDirectory/$file") === true) {
                deepCopy("$sourceDirectory/$file", "$destinationDirectory/$childFolder/$file");
            } else {
                copy("$sourceDirectory/$file", "$destinationDirectory/$childFolder/$file");
            }
        }

        closedir($directory);

        return;
    }

    while (($file = readdir($directory)) !== false) {
        if ($file === '.' || $file === '..') {
            continue;
        }

        if (is_dir("$sourceDirectory/$file") === true) {
            deepCopy("$sourceDirectory/$file", "$destinationDirectory/$file");
        }
        else {
            copy("$sourceDirectory/$file", "$destinationDirectory/$file");
        }
    }

    closedir($directory);
}

function copyHandler(array $arrayNames,$isFolder = false)
{

    foreach ($arrayNames as $fileName) 
    {
        if ($isFolder) 
        {
            deepCopy(MYSQL_BACKUP_FOLDER_PATH.DIRECTORY_SEPARATOR.$fileName,MYSQL_DATA_FOLDER_PATH.DIRECTORY_SEPARATOR.$fileName);
            echo "<p style='background: #7c828f;
            padding: 10px;margin : 5px;
            border-radius: 11px;'>{$fileName} copied<p/>";        
        }
        else
        {        
            if (file_exists(MYSQL_BACKUP_FOLDER_PATH.DIRECTORY_SEPARATOR.$fileName))
            {
                copy(MYSQL_BACKUP_FOLDER_PATH.DIRECTORY_SEPARATOR.$fileName,MYSQL_DATA_FOLDER_PATH.DIRECTORY_SEPARATOR.$fileName);
                echo "<p style='background: #0ba91e;
                padding: 10px;margin : 5px;
                border-radius: 11px;'>{$fileName} copied<p/>";        
            }
            else
            {
                echo "<p style='background: red;
                padding: 10px;margin : 5px;
                border-radius: 11px;'>{$fileName} does not exist !<p/>";
            }
      }
    }

}

deepCopy(MYSQL_DATA_FOLDER_PATH,MYSQL_DATA_FOLDER_PATH.'-old-fix');
echo "<p style='background: #7c828f;
padding: 10px;margin : 5px;
border-radius: 11px;'>---------- your data folder is copied in data-old-fix ---------<p/>";        

# copy backup folders
$folders = ['mysql','performance_schema','phpmyadmin','test'];
copyHandler($folders,true);

# copy backup files
// WARNING DONT ADD ibdata1 FILE IN THIS ARRAY
$backupFiles = [
    'aria_log.00000001','aria_log_control',
    'ib_buffer_pool','ib_logfile0',
    'ib_logfile1','ibtmp1',
    'multi-master.info','my.ini'
];
copyHandler($backupFiles);
