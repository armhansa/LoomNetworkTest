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
	$decksStat = [];

	// Get API to Decks Store
	// $decksTotal = getJson($urlDecks)['total'];
	// $pages = ceil($decksTotal/100);
	// for ($i=1; $i<=$pages; $i++) {
	// 	$jsonDecksPage = getJson("$urlDecks?page=$i");
	// 	foreach ($jsonDecksPage['decks'] as $deck) {
	// 		$decksStat[$deck['user_id'].":".$deck['deck_id']] = [
	// 			'id'=>$deck['id']
	// 			, 'name'=>$deck['name']
	// 			, 'win'=>0
	// 			, 'loss'=>0
	// 			, 'draw'=>0];
	// 	}
	// }

	// Get API to Matches Store
	$matchesTotal = getJson($urlMatches)['total'];
	$pages = ceil($matchesTotal/100);
	$pages = 1; // for test
	for ($i=1; $i<=$pages; $i++) {
		$jsonMatchesPage = getJson("$urlMatches?page=$i");
		foreach ($jsonMatchesPage['matches'] as $match) {
			$player1 = $match['player1_id'].":".$match['player1_deck_id'];
			$player2 = $match['player2_id'].":".$match['player2_deck_id'];
			// dict created
			if (!array_key_exists($player1, $decksStat)) {
				$decksStat[$player1] = [
					'win'=>0
					, 'loss'=>0
					, 'draw'=>0];
			}
			if (!array_key_exists($player2, $decksStat)) {
				$decksStat[$player2] = [
					'win'=>0
					, 'loss'=>0
					, 'draw'=>0];
			}
			// Cal Stat
			if ($match['winner_id'] == "") {
				$decksStat[$player1]['draw']++;
				$decksStat[$player2]['draw']++;
			} else if ($match['winner_id'] == $match['player1_id']) {
				$decksStat[$player1]['win']++;
				$decksStat[$player2]['loss']++;
			} else {
				$decksStat[$player1]['loss']++;
				$decksStat[$player2]['win']++;
			}
			// echo "-------------------<End Game>--------------------<br>";
		}
	}

	$userWinrate = [];
	foreach ($decksStat as $key => $itemDeck) {
		$userID = explode(':', $key)[0];
		if (!array_key_exists($userID, $userWinrate)) {
			$userWinrate[$userID] = $itemDeck;
		}
		$userWinrate[$userID]['win'] += $itemDeck['win'];
		$userWinrate[$userID]['loss'] += $itemDeck['loss'];
		$userWinrate[$userID]['draw'] += $itemDeck['draw'];
	}


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
			// echo "<tr> <th>USER_ID:Deck ID</th> <th>Deck ID</th> <th>Deck Name</th> <th>Win</th> <th>Loss</th> <th>Draw</th> <th>Total</th> </tr>";
			echo "<tr> <th>USER_ID</th> <th>Win</th> <th>Loss</th> <th>Draw</th> <th>Total</th> </tr>";
			foreach ($userWinrate as $key => $itemDeck) {
				echo "<tr>";
				echo "<td>".$key."</td>";
				// echo "<td>".$itemDeck['id']."</td>";
				// echo "<td>".$itemDeck['name']."</td>";
				$total = $itemDeck['win']+$itemDeck['loss']+$itemDeck['draw'];
				echo "<td>".number_format($itemDeck['win']/$total*100, 2)." %</td>";
				echo "<td>".number_format($itemDeck['loss']/$total*100, 2)." %</td>";
				echo "<td>".number_format($itemDeck['draw']/$total*100, 2)." %</td>";
				echo "<td>$total</td>";
				echo "<tr>";
			}

		} else {
			echo "Error Decks in Database is not availiable!";
			die();
		}
	?>
</body>
</html>