<?php
include ('connectDB.php');
/**
 * TODO:
 	colocar infos em Banco de dados
 	preparar 
 */
class RequestHandler
{
	public function getMood($mood){
		if($mood == 'joy'){
			return 0;
		}
		else if ($mood == 'surprise') {
			return 1;
		}
		else if ($mood == 'positive') {
			return 2;
		}
		else if ($mood == 'neutral') {
			return 3;
		}
		else if ($mood == 'negative') {
			return 4;
		}
		else if ($mood == 'sadness') {
			return 5;
		}
		else if ($mood == 'anger') {
			return 6;
		}
		else if ($mood == 'fear') {
			return 7;
		}
		return 10;
	}	
	public function score($ad, $scheduled){
		$score = 0;
		$mood = $this->getMood($scheduled['mood']);
		$moodscore = abs($this->getMood($ad->mood) - $mood);
		$moodscore = 7 - $moodscore;
		$neg = $scheduled['negative'];
		$celeb = $scheduled['celebridades'];
		$tagscore = 0;
		$mytags = json_decode($scheduled['tags']);
		$agr = $mytags->tags;
		#search for tags that match
		foreach ($ad->tags as $tags) {
			foreach ($agr as $scheduled_tags) {
				if($scheduled_tags == $tags){
					$tagscore += 1;
				}
			}
		}
		$celeb_score = 0;
		if($celeb == $ad->celebridade && $celeb != ''){
			$celeb_score -= 10000;
		}
		$score = $moodscore + $tagscore + $celeb_score;
		return $score;
	}
	public function matchAd($json_obj){
		$myConnect = new ConnectDB();
		$myConnect->Connect();
		$conn = $myConnect->conn;
		$last2days = 3600*24*2;
		$sql = "SELECT * FROM PROGRAMACAO ";
		$myscore = -10000;
		$maxroll = null;
		if($result = mysqli_query($conn,$sql)) {
			while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
				$score = $this->score($json_obj, $row);
				if($myscore < $score){
					$myscore = $score;
					$maxroll = $row;
				}
			}
			//baixo se tem comflito
			if($myscore < -1000){
				echo false;
			}
			else {
				echo json_encode($maxroll);
			}
		}
		else {
			echo false;
		}
	}
}
$myjson = json_decode(file_get_contents('php://input'));
$rh = new RequestHandler();
// $myjson = array(
//     'mood' => 'joy',
//     'tags' => array('shampoo', 'cabelo', 'beleza'),
//     'celebridade' => 'grazi massafera'
// );
// $payload = json_encode($myjson);
// $myjson = json_decode($payload);
$rh->matchAd($myjson);
// $myjson2 = array(
//     'mood' => 'neutral',
//     'tags' => array('flamengo', 'pedro', 'beleza'),
//     'celebridade' => ''
// );
// $payload = json_encode($myjson2);
// $myjson2 = json_decode($payload);
// $rh->matchAd($myjson2);

?>