<?php
include ('connectDB.php');
$myConnect = new ConnectDB();
$myConnect->Connect();
$conn = $myConnect->conn;
$sql = "SELECT * FROM PROGRAMACAO LIMIT 1";
$result = mysqli_query($conn,$sql);
if(!$result){
	$sql = 'CREATE TABLE PROGRAMACAO (
  `id` int,
  `programa` varchar(255) ,
  `tema` varchar(255),
  `mood` varchar(255) NOT NULL,
  `negative` varchar(255),
  `tags` JSON,
  `horario` BIGINT NOT NULL,
  `celebridades` varchar(255)
) ENGINE=InnoDB DEFAULT CHARSET=utf8';
	$result = mysqli_query($conn,$sql);
	echo 'creating DB <br>';
	$sql = "ALTER TABLE `PROGRAMACAO`
  ADD PRIMARY KEY (`ID`)";
  $result = mysqli_query($conn,$sql);
  $sql = "ALTER TABLE `PROGRAMACAO`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT";
  $result = mysqli_query($conn,$sql);

}
if (($h = fopen("prog3.csv", "r")) !== FALSE) 
{
	$fail = 0;
	$data = fgetcsv($h, 1000, ",");
  // Convert each line into the local $data variable
  while (($data = fgetcsv($h, 1000, ",")) !== FALSE) 
  {		
  	$progr = $data[0];
  	$horario = strtotime($data[1]);
  	$tema = $data[2];
  	$tags = explode(',',$data[3]);
  	foreach ($tags as $key => $value) {
  		$tags[$key] = ltrim($value);
  	}
  	$tags = json_encode($tags);
  	$negative = $data[4];
  	$mood = $data[5];
  	$celebridade = $data[6];
  	$sql = "INSERT INTO PROGRAMACAO (programa, tema, mood, negative, tags, horario, celebridades) VALUES ('$progr', '$tema', '$mood', '$negative', '$tags', '$horario', LOWER('$celebridade'))";
	if($result = mysqli_query($conn,$sql)) {
		echo 'success <br>';
	}
	else {
		$fail+=1;
		echo 'fail ', $fail;
		echo '<br>';
	}
  }
  // Close the file
  fclose($h);
}
?>