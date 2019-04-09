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
		}
	}

	// Calculate User Winrate 
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
	<title>User Statics</title>
	<link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
	<?php
		if (count($decksStat) > 0) {
			echo "<table id='summary' align='center'>";
			echo "<tr> <th onclick='sortTable(0);' class='clickable'>USER_ID</th>";
			echo "<th onclick='sortTable(1);' class='clickable'>Win</th>";
			echo "<th onclick='sortTable(2);' class='clickable'>Loss</th>";
			echo "<th onclick='sortTable(3);' class='clickable'>Draw</th>";
			echo "<th onclick='sortTable(4);' class='clickable'>Total</th> </tr>";
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
				echo "</tr>";
			}

		} else {
			echo "Error Decks in Database is not availiable!";
			die();
		}
	?>

	<script>
	var increse = false;
	var last_index = -1;

	function sortTable(index) {
		if (index == last_index) {
	  	increse = !increse;
	  } else {
	  	increse = true;
	  }
	  var table, rows, switching, i, x, y, shouldSwitch;
	  table = document.getElementById("summary");
	  switching = true;
	  /*Make a loop that will continue until
	  no switching has been done:*/
	  while (switching) {
	    //start by saying: no switching is done:
	    switching = false;
	    rows = table.rows;
	    /*Loop through all table rows (except the
	    first, which contains table headers):*/
	    for (i = 1; i < (rows.length - 1); i++) {
	      //start by saying there should be no switching:
	      shouldSwitch = false;
	      /*Get the two elements you want to compare,
	      one from current row and one from the next:*/
	      x = rows[i].getElementsByTagName("TD")[index];
	      y = rows[i + 1].getElementsByTagName("TD")[index];
	      //check if the two rows should switch place:
	      if (increse && x.innerHTML.toLowerCase() < y.innerHTML.toLowerCase()) {
	        //if so, mark as a switch and break the loop:
	        shouldSwitch = true;
	        break;
	      }
	      if (!increse && x.innerHTML.toLowerCase() > y.innerHTML.toLowerCase()) {
	        //if so, mark as a switch and break the loop:
	        shouldSwitch = true;
	        break;
	      }
	    }
	    if (shouldSwitch) {
	      /*If a switch has been marked, make the switch
	      and mark that a switch has been done:*/
	      rows[i].parentNode.insertBefore(rows[i + 1], rows[i]);
	      switching = true;
	    }
	  }
	  last_index = index;
	}
	</script>
</body>
</html>