<?php

include "RBTreeNode.php";

const LEFT_DIRECTIONALITY = 0;
const RIGHT_DIRECTIONALITY = 1;

const RED = 0;
const BLACK = 1;


// Implementation uses a strict binary tree, rather than a 2-3 or 2-4 tree
// Taken from Wikipedia article (https://en.wikipedia.org/wiki/Red%E2%80%93black_tree)

// 2 public operations need to be supported: addition of nodes and deletion of nodes
class RBTree {
	// Instantiate new object peripherals
	private $rootNode = false;

	public function __construct() {
	}

	public function insertNode(&$rootNode, &$node, &$parentNode) {
		if ($rootNode === false) {
			// There is no root node for the subtree yet; this is where the insert happens
			_::echoPlusPlus("root");
			$node->setParentNode($parentNode);
			
			$rootNode = $node;
			$this->basicInsertion($node);
		} else {
			// This node must be part of one of this node's subtrees
			_::echoPlusPlus("notroot");
			$parent = $rootNode;
			$n = $node;
			$content = $node->getNodeContent();
			$rootContent = $rootNode->getNodeContent();
			if ($content < $rootContent) {
				$r = &$rootNode->leftChildNode;
			} else {
				$r = &$rootNode->rightChildNode;
			}
			$this->insertNode($r, $n, $parent);
		}
	}

	public function basicInsertion(&$node) {
		if ($node->getParentNode() === false) {
			$node->setNodeColor(BLACK);
		} else {
			$this->redNodeInsertion($node);
		}
	}

	public function redNodeInsertion(&$node) {
		if ($node->getParentNode()->getNodeColor() === BLACK) {
			return;
		} else {
			$this->bubbleUpRed($node);
		}
	}

	public function bubbleUpRed(&$node) {
		$uncle = &$node->getUncleNode();
		if ($uncle !== false && $uncle->getNodeColor() === RED) {
			$parent = &$node->getParentNode();
			$parent->setNodeColor(BLACK);
			$uncle->setNodeColor(BLACK);
			$grandparent = &$node->getGrandparentNode();
			$grandparent->setNodeColor(RED);
			$this->basicInsertion($grandparent);
		} else {
			$this->moveChildOutward($node);
		}
	}

	public function moveChildOutward(&$node) {
		$grandparent = &$node->getGrandparentNode();
		$parent = &$node->getParentNode();
		if ($parent->rightChildNode === $node && $parent === $grandparent->leftChildNode) {
			// Rotate left
			$this->rotateLeft($parent);
			// Finish rotation
			$node = &$node->leftChildNode;
		} elseif ($parent->leftChildNode === $node && $parent === $grandparent->rightChildNode) {
			// Rotate right
			$this->rotateRight($parent);
			// Finish rotation
			$node = &$node->rightChildNode;
		}
		$this->rollSubtree($node);
	}

	public function rollSubtree(&$node) {
		$grandparent = &$node->getGrandparentNode();
		$parent = &$node->getParentNode();
		$leftChildNode = &$parent->leftChildNode;
		$grandparent->setNodeColor(RED);
		$parent->setNodeColor(BLACK);
		if ($node === $leftChildNode) {
			// Rotate right
			$this->rotateRight($grandparent);
		} else {
			// Rotate left
			$this->rotateRight($grandparent);
		}
	}


	public function rotateLeft(&$node, &$grandparent) {
		$savedLeft = $node->leftChildNode;
		$parentSaved = $grandparent->leftChildNode;
		$grandparent->leftChildNode = &$node;
		$node->leftChildNode = &$parentSaved;
		$parentSaved->rightChildNode = &$savedLeft;
	}

	public function rotateRight(&$node, &$grandparent) {
		$savedRight = $node->rightChildNode;
		$parentSaved = $grandparent->rightChildNode;
		$grandparent->rightChildNode = &$node;
		$node->rightChildNode = &$parentSaved;
		$parentSaved->leftChildNode = &$savedRight;
	}


	public function addNode($nodeContent) {
		$node = new RBTreeNode($nodeContent);
		$parent = false;
		$this->insertNode($this->rootNode, $node, $parent);
	}


	public function removeNode() {
		
	}

}