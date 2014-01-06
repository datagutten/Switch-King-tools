<?Php
$dbfile='c:/Program Files (x86)/Switch King/Switch King Server/DB/switchKing.server.db3'; //Path to the Switch King database file
$where="WHERE DataSourceEngineeringUnit='°C' OR DataSourceEngineeringUnit='%'"; //Added to SQL query to filter what devices to dump data for
$logfolder='Switch King data source logs'; //Path to where the files will be placed