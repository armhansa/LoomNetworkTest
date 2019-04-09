<?php
include 'func/curl_func.php';

	// URL to get data from API
	$urlDecks = "https://api.loom.games/zb/v1/decks";
	$urlDeckByID = "https://api.loom.games/zb/v1/deck/{id}";
	$urlUserDecks = "https://api.loom.games/zb/v1/decks?user_id={user_id}";
	$urlMatches = "https://api.loom.games/zb/v1/matches";

	// test get response from API
	// echo getContent($urlDecks);

	// Data Store
	$totalMatches = 0;
	if (isset($_GET["user"])) {
		$isUserView = true;
		$userID = $_GET['user'];
	} else {
		$isUserView = false;
		// $decksStat['id'] = ['name'=>'', 'user_id'=>'', 'win'=>0, 'loss'=>0, 'draw'=>0]; and total (cal from win+loss+draw)
		// $cardsStat['id'] = ['name'=>'', 'win'=>0, 'loss'=>0, 'draw'=>0]; // and total (cal from win+loss+draw)
	}

	// Get API to Decks Store
	$decksTotal = getJson($urlDecks)['total'];
	$pages = ceil($decksTotal/100);
	for ($i=1; $i<=$pages; $i++) {
		$jsonDecksPage = getJson("$urlDecks?page=$i");
		foreach ($jsonDecksPage['decks'] as $deck) {
			$decksStat[$deck['user_id'].":".$deck['deck_id']] = [
				'id'=>$deck['id']
				, 'name'=>$deck['name']
				, 'win'=>0
				, 'loss'=>0
				, 'draw'=>0];
		}
	}

	// Get API to Matches Store
	// $matchesTotal = getJson($urlMatches)['total'];
	// $pages = ceil($matchesTotal/100);
	// for ($i=1; $i<=$pages; $i++) {
	// 	$jsonMatchesPage = getJson("$urlDecks?page=$i");
	// 	foreach ($jsonMatchesPage['decks'] as $match) {
	// 		if ($match['status'] == "Ended") {
	// 			$player[] = [$match['player1_id'], $match['player1_deck_id']];
	// 			$player[] = [$match['player2_id'], $match['player2_deck_id']];
	// 			if ($match['winner_id'] == "") {
	// 				$decksStat[$player[0][0].":".$player[0][1]]['draw']++;
	// 				$decksStat[$player[1][0].":".$player[1][1]]['draw']++;
	// 			} else if ($match['winner_id'] == $player[0][0]) {
	// 				$decksStat[$player[0][0].":".$player[0][1]]['win']++;
	// 				$decksStat[$player[1][0].":".$player[1][1]]['loss']++;
	// 			} else {
	// 				$decksStat[$player[0][0].":".$player[0][1]]['loss']++;
	// 				$decksStat[$player[1][0].":".$player[1][1]]['win']++;
	// 			}
	// 		}
	// 	}
	// }

?>



<!DOCTYPE html>
<html>
<head>
	<title>Cards Statics</title>
	<link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
	<?php
		if (count($decksStat) > 0) {
			echo "<table align='center'>";
			// echo "<tr><th class='bolder'>Eng Name</th><th class='bolder'>Section</th></tr>";
			echo "<tr> <th>USER_ID:Deck ID</th> <th>Deck ID</th> <th>Deck Name</th> <th>Win</th> <th>Loss</th> <th>Draw</th> <th>Total</th> </tr>";
			foreach ($decksStat as $key => $itemDeck) {
				echo "<tr>";
				echo "<td>".$key."</td>";
				echo "<td>".$itemDeck['id']."</td>";
				echo "<td>".$itemDeck['name']."</td>";
				echo "<td>".$itemDeck['win']."</td>";
				echo "<td>".$itemDeck['loss']."</td>";
				echo "<td>".$itemDeck['draw']."</td>";
				echo "<td>".($itemDeck['win']+$itemDeck['loss']+$itemDeck['draw'])."</td>";
				echo "<tr>";
			}

		} else {
			echo "Error Decks in Database is not availiable!";
			die();
		}
	?>
</body>
</html>