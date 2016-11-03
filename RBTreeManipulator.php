<?php

include "../PHPLibrary/Underscore.php";
include "RBTree.php";

$testTree = new RBTree();

$testTree->addNode("value5");
_::echoPlusPlus("1", $testTree);
$testTree->addNode("value4");
_::echoPlusPlus("2", $testTree);
$testTree->addNode("value6");
_::echoPlusPlus("3", $testTree);
$testTree->addNode("value45");
$testTree->addNode("value65");
$testTree->addNode("value35");
$testTree->addNode("value42");
_::echoPlusPlus("4", $testTree);