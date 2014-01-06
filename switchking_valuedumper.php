<?Php
chdir(__DIR__);
require 'config.php';
if(!file_exists($dbfile))
	die("Could not find database file: $dbfile\n");
if(!isset($where))
	$where='';

$db=new pdo('sqlite:'.$dbfile);
$st_sources=$db->query("SELECT * FROM DataSources $where");
$sources=$st_sources->fetchAll(PDO::FETCH_ASSOC);

$time=time();
if(!isset($argv[1])) //If no start timestamp is specified, start today
	$timestamp=$time; 
else
{
	$timestamp=strtotime($argv[1]);
	$st_mindate=$db->query("SELECT min(DataSourceValueTimestamp) FROM DataSourceValues");
	$mindate=$st_mindate->fetch();
	$mindate=strtotime($mindate[0]);

	if($timestamp<$mindate) //Check if the specified date is earlier than the first saved value
	{
		echo "Earlist saved value is ".date('Y-m-d',$mindate)."\n";	
		$timestamp=$mindate;
	}
}
if(!isset($argv[2])) //If no end timestamp is specified, end today
	$endtimestamp=$time;
else
	$endtimestamp=strtotime($argv[2]);
while($timestamp<=$endtimestamp)
{
	$date=date('Y-m-d',$timestamp);
	echo $date."\n";
	$filepath=$logfolder.$date.'/';
	if(!file_exists($filepath))
		mkdir($filepath,0777,true);
	foreach($sources as $source)
	{
		$st_values=$db->query("SELECT * FROM DataSourceValues WHERE DataSourceID={$source['DataSourceID']} AND DataSourceValueTimestamp LIKE '$date%'");
		$values=$st_values->fetchAll(PDO::FETCH_ASSOC);
		if(count($values)==0) //Skip data sources with no values
		{
	
			continue;
		}
		echo $source['DataSourceName']."\n";

		$csv=implode(';',array_keys($values[0]))."\r\n";
		foreach($values as $value)
		{
			$csv.=implode(";",$value)."\r\n";
		}

		file_put_contents($filepath.$source['DataSourceName'].' '.$date.'.csv',$csv);
	}
	$timestamp=strtotime('+1 day',$timestamp);
}