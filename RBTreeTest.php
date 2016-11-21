<?php
// A unit testing library for PHP
use PHPUnit\Framework\TestCase;

// Underscore.php is a general utility script for things like outputting to the command line
include "../PHPLibrary/Underscore.php";
include "RBTree.php";



class RBTreeTest extends TestCase {
	public function testGeneralCases() {
		$testTree = new RBTree();
		$testTree->addNode("value5");
		$this->assertPropertiesAreAllObeyed($testTree);
		$testTree->addNode("value4");
		$this->assertPropertiesAreAllObeyed($testTree);
		$testTree->addNode("value6");
		$this->assertPropertiesAreAllObeyed($testTree);
		$testTree->addNode("value45");
		$this->assertPropertiesAreAllObeyed($testTree);
		$testTree->addNode("value65");
		$this->assertPropertiesAreAllObeyed($testTree);
		$testTree->addNode("value35");
		$this->assertPropertiesAreAllObeyed($testTree);
		$testTree->addNode("value42");
		$this->assertPropertiesAreAllObeyed($testTree);
		$testTree->removeNodeWithContent("value42");
		$this->assertPropertiesAreAllObeyed($testTree);
	}

	public function assertPropertiesAreAllObeyed(&$treeOfInterest) {
		$currentNode = &$treeOfInterest->rootNode;
		$blackMetric = getLeafBlackCountMetric($currentNode);
		// Confirm that all leaves are black
		$leavesAreBlack = checkLeafNodesAreBlack($currentNode);
		$this->assertTrue($leavesAreBlack);
		// Confirm that all children of red nodes are black
		$redNodesHaveBlackChildren = checkRedChildrenAreBlack($currentNode);
		$this->assertTrue($redNodesHaveBlackChildren);
		// Confirm that the distance from root to leaf covers a constant number of black nodes
		$blackDistanceIsUniform = checkLeafBlackDistance($currentNode, 0, $blackMetric);
		$this->assertTrue($blackDistanceIsUniform);
	}
}




// $testTree = new RBTree();

// $testTree->addNode("value5");
// _::echoPlusPlus("1", $testTree);
// $testTree->addNode("value4");
// _::echoPlusPlus("2", $testTree);
// $testTree->addNode("value6");
// _::echoPlusPlus("3", $testTree);
// $improperSubtreeSize = $testTree->getImproperSubtreeSize($testTree->rootNode);
// _::echoPlusPlus("$improperSubtreeSize", $improperSubtreeSize);
// $testTree->addNode("value45");
// $testTree->addNode("value65");
// $testTree->addNode("value35");
// $testTree->addNode("value42");
// _::echoPlusPlus("4", $testTree);
// $testTree->removeNodeWithContent("value42");

// _::echoPlusPlus("leftSubtreeSize", $leftSubtreeSize);
// _::echoPlusPlus("rightSubtreeSize", $rightSubtreeSize);
// _::echoPlusPlus("improperSubtreeSize", $improperSubtreeSize);

/*
1. A node is either red or black.

2. The root is black. This rule is sometimes omitted. Since the root can always be changed from red to black, but not necessarily vice versa, this rule has little effect on analysis.

3. All leaves (NIL) are black.

4. If a node is red, then both its children are black.

5. Every path from a given node to any of its descendant NIL nodes contains the same number of black nodes. Some definitions: the number of black nodes from the root to a node is the node's black depth; the uniform number of black nodes in all paths from root to the leaves is called the black-height of the red–black tree.
*/



function checkLeafNodesAreBlack(&$rootNode) {
	$returnValue = true;

	_::echoPlusPlus("LastRootNode:", $rootNode);
	if ($rootNode->nodeIsLeaf() === false) {
		$leftChildNode = &$rootNode->leftChildNode;
		$rightChildNode = &$rootNode->rightChildNode;
		$leftChildNodeResult = checkLeafNodesAreBlack($leftChildNode);
		$rightChildNodeResult = checkLeafNodesAreBlack($rightChildNode);
		if ($leftChildNodeResult === false || $rightChildNodeResult === false) {
			$returnValue = false;
		}
	} else {
		if ($rootNode->getNodeColor() === RED) {
			$returnValue = false;
		}
	}

	return $returnValue;
}

// Assumption made: all leaf nodes are black and thus above suspicion
function checkRedChildrenAreBlack(&$rootNode) {
	$returnValue = true;

	if ($rootNode->nodeIsLeaf() === false) {
		$leftChildNode = &$rootNode->leftChildNode;
		$rightChildNode = &$rootNode->rightChildNode;
		if ($rootNode->getNodeColor() === RED && ($leftChildNode->getNodeColor() === RED || $rightChildNode->getNodeColor() === RED)) {
			$returnValue = false;
		} else {
			$leftChildNodeResult = checkRedChildrenAreBlack($leftChildNode);
			$rightChildNodeResult = checkRedChildrenAreBlack($rightChildNode);
			if ($leftChildNodeResult === false || $rightChildNodeResult === false) {
				$returnValue = false;
			}
		}
	}

	return $returnValue;
}

function getLeafBlackCountMetric(&$rootNode) {
	$blackCount = 0;
	$currentNode = $rootNode;

	// _::echoPlusPlus("currentNode:", $currentNode);
	if ($currentNode->getNodeColor() === BLACK) {
		$blackCount++;
	}

	while ($currentNode->nodeIsLeaf() === false) {
		$currentNode = $currentNode->leftChildNode;
		if ($currentNode->getNodeColor() === BLACK) {
			$blackCount++;
		}
	}

	return $blackCount;
}

function checkLeafBlackDistance(&$rootNode, $currentBlackCount, $blackCountMetric) {
	$returnValue = true;

	if ($rootNode->getNodeColor() === BLACK) {
		$currentBlackCount++;
	}

	if ($rootNode->nodeIsLeaf() === false) {
		$leftChildNode = &$rootNode->leftChildNode;
		$rightChildNode = &$rootNode->rightChildNode;
		$leftChildNodeResult = checkLeafBlackDistance($leftChildNode, $currentBlackCount, $blackCountMetric);
		$rightChildNodeResult = checkLeafBlackDistance($rightChildNode, $currentBlackCount, $blackCountMetric);
		if ($leftChildNodeResult === false || $rightChildNodeResult === false) {
			$returnValue = false;
		}
	} else {
		if ($currentBlackCount != $blackCountMetric) {
			$returnValue = false;
		}
	}

	return $returnValue;
}