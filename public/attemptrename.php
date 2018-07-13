<?php
	require_once __DIR__ . '/../lib/bootstrap.php';

	if( !RA_ReadCookieCredentials( $user, $points, $truePoints, $unreadMessageCount, $permissions, \RA\Permissions::Developer ) )
	{
		//	Immediate redirect if we cannot validate user!	//TBD: pass args?
		header( "Location: " . APP_URL );
		exit;
	}
	
	$gameID = seekGET( 'g' );
	$errorCode = seekGET( 'e' );
	
	$achievementList = array();
	$gamesList = array();
	
	$gameIDSpecified = ( isset( $gameID ) && $gameID != 0 );
	if( $gameIDSpecified )
	{
		getGameMetadata( $gameID, $user, $achievementData, $gameData );
		if( $gameData == NULL )
		{
			//	Immediate redirect: this is pointless otherwise!
			header( "Location: " . APP_URL . "?e=unknowngame" );
		}
	}
	else
	{
		//	Immediate redirect: this is pointless otherwise!
		header( "Location: " . APP_URL );
	}
	
	$consoleName = $gameData['ConsoleName'];
	$consoleID = $gameData['ConsoleID'];
	$gameTitle = $gameData['Title'];
	$gameIcon = $gameData['ImageIcon'];
	
	$pageTitle = "Rename Game Entry ($consoleName)";
	
	//$numGames = getGamesListWithNumAchievements( $consoleID, $gamesList, 0 );
	//var_dump( $gamesList );
	RenderDocType();
?>

<head>
	<?php RenderSharedHeader( $user ); ?>
	<?php RenderTitleTag( $pageTitle, $user ); ?>
	<?php RenderGoogleTracking(); ?>
</head>
<body>

<?php RenderTitleBar( $user, $points, $truePoints, $unreadMessageCount, $errorCode ); ?>
<?php RenderToolbar( $user, $permissions ); ?>

<div id="mainpage">
	<div class='left'>
	
	<h2>Rename Game Entry</h2>

	<?php
	
	echo GetGameAndTooltipDiv( $gameID, $gameTitle, $gameIcon, $consoleName, FALSE, 96 );
	echo "</br></br>";
	
	echo "Renaming game entry <a href='/Game/$gameID'>$gameTitle</a> for $consoleName.<br/>";
	echo "Please enter a new name below:<br/><br/>";
	
	echo "<FORM method=post action='requestmodifygame.php'>";
	echo "<INPUT TYPE='hidden' NAME='u' VALUE='$user' />";
	echo "<INPUT TYPE='hidden' NAME='g' VALUE='$gameID' />";
	echo "<INPUT TYPE='hidden' NAME='f' VALUE='1' />";
	echo "New Name: <INPUT TYPE='text' NAME='v' VALUE=\"$gameTitle\" size='60' />";
	echo "&nbsp;<INPUT TYPE='submit' VALUE='Submit' />";
	echo "</FORM>";
	
	echo "<br/><div id='warning'><b>Warning:</b> PLEASE be careful with this tool. If in doubt, <a href='/createmessage.php?t=Scott&s=Attempt%20to%20Rename%20a%20title'>leave me a message</a> and I'll help sort it.</div>";
	?>
	<br/>
	</div>
</div>

<?php RenderFooter(); ?>	

</body>
</html>
