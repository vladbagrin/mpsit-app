<?php
//TODO: replace these with the actual data
session_start(); 
$_SESSION['PICTURE'] =  "images/thankyou.png";
$_SESSION['BIRTHDAY'] =  "1989/11/07";

function createElementTypeDetecting($name, $date, $message, $index, $isChecked, $language) {
	$string='<li> <div class="item"> <p class="message"> '.$message
	.' </br> '.$language.'<input  class="checkboxC" type="checkbox" name="checkedThank[]"  '.$isChecked.' value="'.$index.'">Thank&nbsp&nbsp&nbsp<input class="checkboxL" type="checkbox" name="checkedLike[]" value="'.$index.'">Like<br> </p> <p class="date"> '.$date
	.' </p> <p class="name"> '.$name
	.' </p> </div> </li>';
	return $string;
}

function createElementTypeChoosing($name, $date, $message, $index, $isChecked) {
	$string='<li> <div class="item"> <p class="message"> '.$message
	.' </br> <input  class="checkboxC" type="checkbox" name="checkedThank[]" '.$isChecked.' value="'.$index.'">Thank&nbsp&nbsp&nbsp<input class="checkboxL" type="checkbox" name="checkedLike[]" value="'.$index.'">Like<br> </p> <p class="date"> '.$date
	.' </p> <p class="name"> '.$name
	.' </p> </div> </li>';
	return $string;
}

function createElementTypeWriting($name, $date, $message, $index, $isChecked) {
	$string='<li> <div class="item"> <p class="message"> '.$message
	.' </br> <input  class="checkboxC" type="checkbox" name="checkedThank[]" '.$isChecked.' value="'.$index.'">Thank&nbsp&nbsp&nbsp<input class="checkboxL" type="checkbox" name="checkedLike[]" value="'.$index.'">Like<br> </p> <p class="date"> '.$date
	.' </p> <p class="name"> '.$name
	.' </p> </div> </li>';
	return $string;
}

function getCommentByDetectingLanguageOutput() {

$gigel = array("name"=>"Gigel", "date"=>"27.04.2013", "message"=>"Happy Birthday!", "language"=>"EN");
$dorel = array("name"=>"Dorel", "date"=>"27.12.2013", "message"=>"La multi ani!", "language"=>"RO");
$ionel = array("name"=>"Ionel", "date"=>"21.04.2014", "message"=>"Multi ani traiasca!", "language"=>"RO");
$wishesList = array($gigel, $dorel, $ionel);

echo '
	<div id="list4">
	<form method="post" action="thanks2.php">
    <ul>';
	
	foreach ($wishesList as $i => $wishData) {
			echo createElementTypeDetecting($wishData["name"], $wishData["date"], $wishData["message"], $i, "", $wishData["language"]);
	}
	
echo '</ul>
   <input type="hidden" name="type" value="detect">
   <button class="btn" id="thank_button" align="center" type="submit" value="Thank">Thank selected</button>
   </form>
   </div>';
}
function getCommentByChoosingLanguageOutput() {

$gigel = array("name"=>"Gigel", "date"=>"27.04.2013", "message"=>"Happy Birthday!", "language"=>"EN");
$dorel = array("name"=>"Dorel", "date"=>"27.12.2013", "message"=>"La multi ani!", "language"=>"RO");
$ionel = array("name"=>"Ionel", "date"=>"21.04.2014", "message"=>"Multi ani traiasca!", "language"=>"RO");
$wishesList = array($gigel, $dorel, $ionel);

echo '
	<div id="list4">
	<form method="post" action="thanks2.php">
	<div id="language">
	Choose language: <select name="language">
	  <option value="EN" selected>English</option>
	  <option value="RO">Romanian</option>
	  <option value="FR">French</option>
	</select>
	</div>
    <ul id="listaScr">';
	foreach ($wishesList as $i => $wishData) {
			echo createElementTypeChoosing($wishData["name"], $wishData["date"], $wishData["message"], $i, "");
	}	  
echo '</ul>
   <input type="hidden" name="type" value="choose">
   <button class="btn" id="thank_button" align="center" type="submit" value="Thank">Thank selected</button>
   </form>
   </div>';
}
function getWriteYourselfCommentOutput() {

$gigel = array("name"=>"Gigel", "date"=>"27.04.2013", "message"=>"Happy Birthday!", "language"=>"EN");
$dorel = array("name"=>"Dorel", "date"=>"27.12.2013", "message"=>"La multi ani!", "language"=>"RO");
$ionel = array("name"=>"Ionel", "date"=>"21.04.2014", "message"=>"Multi ani traiasca!", "language"=>"RO");
$gigel2 = array("name"=>"Gigel2", "date"=>"27.04.2013", "message"=>"Happy Birthday!", "language"=>"EN");
$dorel2 = array("name"=>"Dorel2", "date"=>"27.12.2013", "message"=>"La multi ani!", "language"=>"RO");
$ionel2 = array("name"=>"Ionel2", "date"=>"21.04.2014", "message"=>"Multi ani traiasca!", "language"=>"RO");
$wishesList = array($gigel, $dorel, $ionel, $gigel2, $dorel2, $ionel2);

echo '
	<div id="list4">
	<form method="post" action="thanks2.php">
	<textarea name="customThank" id="custom_thank" rows="2" cols="69" placeholder="Write here your custom message (e.g Thanks %s!)"></textarea>
    <ul>';

	foreach ($wishesList as $i => $wishData) {
		echo createElementTypeWriting($wishData["name"], $wishData["date"], $wishData["message"], $i, "");
	}	
	
 echo '</ul>
   <input type="hidden" name="type" value="write">
   <button class="btn" id="thank_button" align="center" type="submit" value="Thank">Thank selected</button>
   </form>
   </div>';
}

function getAboutPageOutput() {
	echo '<div class="author">Razvan Nitu - SSA</div> </br> <div class="email"> r.nitu7@gmail.com </div></br>
		 <div class="author"> Vlad Bagrin - SSA</div> </br> <div class="email"> vlad.bagrin@gmail.com </div></br>
		 <div class="project"> Proiect MPSIT </div></br>';
}

function getLikeAllPostsOutput() {
	echo "<div>TODO: like all feedback</div>";
}


?>