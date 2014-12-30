<?php

if (isset($_POST["type"])) {
	$pageType = $_POST["type"];
	switch ($pageType) {
		case "detect":
		if (!isset($_POST["checkedThank"]) ||
			empty($_POST["checkedThank"])) {		
			echo "No thanks to give<br/>";
		} else {
			$checked = $_POST["checkedThank"];
			foreach ($checked as $i) {
				echo $i;
				$checkedLike = $_POST["checkedLike"];
				foreach ($checkedLike as $j) {
					if($i == $j) {
						echo "-like</br>";
					}
				}
				echo "</br>";
			}
		}
			break;
		case "choose":
		if (!isset($_POST["checkedThank"]) ||
			empty($_POST["checkedThank"])) {		
			echo "No thanks to give<br/>";
		} else {
			if (isset($_POST["language"])){
				$language = $_POST["language"];
				echo $language;
				echo "</br>";
				$checked = $_POST["checkedThank"];
				foreach ($checked as $i) {
					echo $i;
					$checkedLike = $_POST["checkedLike"];
					foreach ($checkedLike as $j) {
						if($i == $j) {
							echo "-like</br>";
						}
					}
					echo "</br>";
				}
			}
			else{
				echo "Form error!";
			}
		}
			break;
		case "write":
			if (isset($_POST["customThank"])){
				$custom_thank = $_POST["customThank"];
				echo $custom_thank;
				echo "</br>";
				$checked = $_POST["checkedThank"];
				foreach ($checked as $i) {
					echo $i;
					$checkedLike = $_POST["checkedLike"];
					foreach ($checkedLike as $j) {
						if($i == $j) {
							echo "-like</br>";
						}
					}
					echo "</br>";			
				}
			}
			else{
				echo "Form error!";
			}
			break;
	}
}
else
{
	echo "Form error<br/>";
}
?>