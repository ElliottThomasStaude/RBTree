<?php

// Underscore.php is a general utility script for things like outputting to the command line
include "../PHPLibrary/Underscore.php";
include "RBTree.php";

$testTree = new RBTree();

$testTree->addNode("value5");
_::echoPlusPlus("1", $testTree);
$testTree->addNode("value4");
_::echoPlusPlus("2", $testTree);
$testTree->addNode("value6");
_::echoPlusPlus("3", $testTree);
$improperSubtreeSize = $testTree->getImproperSubtreeSize($testTree->rootNode);
_::echoPlusPlus("$improperSubtreeSize", $improperSubtreeSize);
$testTree->addNode("value45");
$testTree->addNode("value65");
$testTree->addNode("value35");
$testTree->addNode("value42");
_::echoPlusPlus("4", $testTree);
$testTree->removeNodeWithContent("value42");

_::echoPlusPlus("leftSubtreeSize", $leftSubtreeSize);
_::echoPlusPlus("rightSubtreeSize", $rightSubtreeSize);
_::echoPlusPlus("improperSubtreeSize", $improperSubtreeSize);