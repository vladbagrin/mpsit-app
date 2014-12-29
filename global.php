<?php
session_start();

error_reporting(E_ALL);
ini_set('display_errors', '1');

if (getenv('IS_HEROKU')) {
	$canvasUrl = "https://apps.facebook.com/birthday-thanker-heroku";
} else {
	$canvasUrl = "https://apps.facebook.com/birthday-thanker";
}

// list of patterns the message must have, for different languages
/*
Possible phrases:

Romanian
La multi ani
Sa ai parte de fericire
Felicitari cu ziua de nastere
Ziua nasterii (lol)

English
Happy Birthday
You have to get older, but you don't have to grow up
Take a day off to celebrate you birthday

French
Je te souhaite tout le succès possible
Passe une merveilleuse journee
Bonne fete
Bon anniversaire
Joyeux anniversaire
Passe une merveilleuse journee
Je te souhaite tout le succes possible
Je te souhaite beaucoup de sante, de bonheur, des jours ensoleilles et des moments inoubliables
*/
$roPatterns = array(
	"/(la\\W+)?mul(t|ts|tz)i\\W+ani(sori|shori)?/i",
	"/(urez|ai\\W+parte|doresc|multa)\\W.*(sanatate|bucurie|impliniri|fericire|iubire|respect)/i",
	"/(zi(ua)?)\\W.*(de\\W.*nastere|nasterii)/i",
	"/felicit(ari)?/i"
	);
$frPatterns = array(
	"/(joyeu(x|se)|bon(ne)?|merveilleu(x|se))\\W.*(anniversaire|journee|fete)/i",
	"/(je|nous)\\W.*((te|vous)\\W.*)?souhait/i"
);
$enPatterns = array(
	"/(happy\\W+)?birthday/i",
	"/\\bold(er)\\b/i",
	"/celebrat/i"
);

// %s stands for the name of the addressee
$commentChoicesAddressed = array(
	"EN" => array(
		"Thanks, %s!",
		"Thank you for your wishes, %s",
		"Thanks for the wishes, %s"
	),
	"FR" => array(
		"Merci, %s!",
		"Merci beaucoup %s",
		"Merci pour tes bienfaits, %s"
	),
	"RO" => array(
		"Multumesc, %s",
		"Multumesc pentru urari, %s",
		"Mersi mult, %s!"
	)
);

$commentChoices = array(
	"EN" => array(
		"Thanks!",
		"Thank you for your wishes",
		"Thanks for the wishes"
	),
	"FR" => array(
		"Merci",
		"Merci beaucoup",
		"Merci pour tes bienfaits"
	),
	"RO" => array(
		"Multumesc",
		"Multumesc pentru urari",
		"Mersi mult!"
	)
);
?>