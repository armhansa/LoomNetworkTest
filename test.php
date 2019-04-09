<?php

$decksStat['test'] = ['win'=>0, 'loss'=>2];
$decksStat['test']['loss']++;
print_r($decksStat);
$decksStat['test2'] = ['win'=>0, 'loss'=>2];
print_r($decksStat);

?>