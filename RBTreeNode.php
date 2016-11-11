<?php

// External definitions: RED = 0, BLACK = 1

class RBTreeNode {
	// Default color is red
	private $nodeColor = RED;

	private $parentNode = false;
	public $leftChildNode = false;
	public $rightChildNode = false;

	private $nodeContent = false;

	public function __construct($nodeContent, $leafNode=false) {
		$this->nodeContent = $nodeContent;
		if ($leafNode === false) {
			$leftChild = new RBTreeNode(false, true);
			$leftChild->setNodeColor(BLACK);
			$rightChild = new RBTreeNode(false, true);
			$rightChild->setNodeColor(BLACK);
			$this->leftChildNode = &$leftChild;
			$this->rightChildNode = &$rightChild;
		}
	}


	public function nodeIsLeaf() {
		$leftChildIsMissing = ($this->leftChildNode === false);
		$rightChildIsMissing = ($this->rightChildNode === false);
		$currentNodeIsEmpty = ($this->getNodeContent() === false);
		$isLeafNode = ($leftChildIsMissing && $rightChildIsMissing && $currentNodeIsEmpty);
		return $isLeafNode;
	}


	public function setNodeContent($nodeContent) {
		$this->nodeContent = $nodeContent;
	}

	public function getNodeContent() {
		return $this->nodeContent;
	}


	public function setNodeColor($color) {
		$this->nodeColor = $color;
	}

	public function getNodeColor() {
		return $this->nodeColor;
	}


	public function setParentNode(&$parentNode) {
		$this->parentNode = $parentNode;
	}

	public function getParentNode() {
		return $this->parentNode;
	}

	public function getGrandparentNode() {
		$grandparent = false;
		$parent = &$this->getParentNode();
		if ($parent !== false) {
			$grandparent = &$parent->getParentNode();
		}
		return $grandparent;
	}

	public function getUncleNode() {
		$uncle = false;
		$grandparent = &$this->getGrandparentNode();
		if ($grandparent !== false) {
			$parent = &$this->getParentNode();
			$uncle = &$grandparent->leftChildNode;
			$right = &$grandparent->rightChildNode;
			if ($uncle === $parent) {
				$uncle = $right;
			}
		}
		return $uncle;
	}

	public function getSiblingNode() {
		$parent = &$this->parentNode();
		$sibling = &$parent->leftChildNode;
		if ($sibling === $this) {
			$sibling = &$parent->rightChildNode;
		}
		return $sibling;
	}


	public function getPredecessorChildNode(&$startingNode) {
		$predecessor = false;
		if ($startingNode->nodeIsLeaf() === false) {
			$predecessor = &$startingNode->leftChildNode;
			while ($predecessor->rightChildNode->nodeIsLeaf() === false) {
				$predecessor = &$predecessor->rightChildNode;
			}
		}
		return $predecessor;
	}


	public function getSuccessorChildNode(&$startingNode) {
		$successor = false;
		if ($startingNode->nodeIsLeaf() === false) {
			$successor = &$startingNode->rightChildNode;
			while ($successor->leftChildNode->nodeIsLeaf() === false) {
				$successor = &$successor->leftChildNode;
			}
		}
		return $successor;
	}
}