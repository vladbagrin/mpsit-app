<?php
$pageType = $_POST["type"];
switch ($pageType) {
    case "detect":
		header("location:detect.php"); 
        break;
    case "choose":
        header("location:choose.html"); 
        break;
    case "write":
        header("location:write.html"); 
        break;
	case "like":
        header("location:like.html"); 
        break;
	case "about":
        header("location:about.html");  
        break;
}
?>