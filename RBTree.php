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
	public $rootNode = false;

	public function __construct() {
	}

	public function insertNode(&$rootNode, &$node, &$parentNode) {
		_::echoPlusPlus("rootNode:", $node);
		if ($rootNode !== false) {
			$isleaf = $rootNode->nodeIsLeaf();
			_::echoPlusPlus("rootNodeIsLeaf:", $isleaf);
		}
		if ($rootNode === false || $rootNode->nodeIsLeaf() !== false) {
			// There is no root node for the subtree yet; this is where the insert happens
			$node->setParentNode($parentNode);
			
			$rootNode = $node;
			$this->basicInsertion($node);
		} else {
			// This node must be part of one of this node's subtrees
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

	public function removeNodeWithContent($nodeContent) {
		$rootNode = &$this->rootNode;
		if ($rootNode !== false) {
			$speculativeNode = &$rootNode;
			while ($speculativeNode->nodeIsLeaf() === false) {
				$content = $speculativeNode->getNodeContent();
				if ($content == $nodeContent) {
					// If content matches the deletion content, delete node
					$this->removeNode($speculativeNode);
				} elseif ($content < $nodeContent) {
					// If content is less than the deletion content, go down right subtree
					$speculativeNode = &$speculativeNode->rightChildNode;
				} else {
					// If content is more than the deletion content, go down left subtree
					$speculativeNode = &$speculativeNode->leftChildNode;
				}
			}
		}
	}

	public function removeNode(&$node) {
		// Find if node has two, one, or no non-leaf children
		$numberOfNonleafNodeChildren = 0;
		if ($node->leftChildNode !== false && $node->leftChildNode->nodeIsLeaf() === false) {
			$numberOfNonleafNodeChildren++;
		}
		if ($node->rightChildNode !== false && $node->rightChildNode->nodeIsLeaf() === false) {
			$numberOfNonleafNodeChildren++;
		}


		if ($numberOfNonleafNodeChildren === 0) {
			// Remove node, by setting parent's child on this side to a leaf node
			$newParentChild = new RBTreeNode(false, true);
			$newParentChild->setNodeColor(BLACK);
			$parent = &$node->getParentNode();
			$parentChild = &$parent->leftChildNode;
			if ($node !== $parentChild) {
				$parentChild = &$parent->rightChildNode;
			}
			$parentChild = &$newParentChild;
		} elseif ($numberOfNonleafNodeChildren === 1) {
			// A complex case
			// TODO
		} else {
			// Pick a side based on the height of left/right subtrees, and subsequently choose and remove the successor/predecessor node as appropriate
			$leftSubtreeSize = $this->getLeftSubtreeSize($node);
			$rightSubtreeSize = $this->getRightSubtreeSize($node);
			$descendantNode = false;
			if ($rightSubtreeSize > $leftSubtreeSize) {
				$descendantNode = &$node->getSuccessorChildNode();
			} else {
				$descendantNode = &$node->getPredecessorChildNode();
			}
			$node->setNodeContent($descendantNode->getNodeContent());
			$this->removeNode($descendantNode);
		}
		return false;
	}


	// This is a function which introduces significant potential runtime to tree operations, but will suffice for goals such as determining which extreme child should replace a deleted node
	public function getImproperSubtreeSize(&$node) {
		$size = 0;
		if ($node !== false) {
			$size = 1;
			if ($node->nodeIsLeaf() === false) {
				// Add the value of the left subtree
				if ($node->leftChildNode->nodeIsLeaf() === false) {
					$size += $this->getImproperSubtreeSize($node->leftChildNode);
				}
				// Add the value of the right subtree
				if ($node->rightChildNode->nodeIsLeaf() === false) {
					$size += $this->getImproperSubtreeSize($node->rightChildNode);
				}
			}
		}
		return $size;
	}

	// Simple wrapper for getting the left-side subtree size of a given node
	public function getLeftSubtreeSize(&$node) {
		$size = 0;
		if ($node->leftChildNode->nodeIsLeaf() === false) {
			$size += $this->getImproperSubtreeSize($node->leftChildNode);
		}
		return $size;
	}

	// Simple wrapper for getting the right-side subtree size of a given node
	public function getRightSubtreeSize(&$node) {
		$size = 0;
		if ($node->rightChildNode->nodeIsLeaf() === false) {
			$size += $this->getImproperSubtreeSize($node->rightChildNode);
		}
		return $size;
	}
}