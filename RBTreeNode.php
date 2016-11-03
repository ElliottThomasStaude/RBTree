<?php

class RBTreeNode {
	// Default color is red
	private $nodeColor = RED;

	private $parentNode = false;
	public $leftChildNode = false;
	public $rightChildNode = false;

	private $nodeContent = false;

	public function __construct($nodeContent) {
		$this->nodeContent = $nodeContent;
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
		$parent = $this->getParentNode();
		if ($parent !== false) {
			$grandparent = $parent->getParentNode();
		}
		return $grandparent;
	}

	public function getUncleNode() {
		$uncle = false;
		$parent = &$this->getParentNode();
		$grandparent = &$this->getGrandparentNode();
		if ($grandparent !== false) {
			$uncle = $grandparent->leftChildNode;
			$right = $grandparent->rightChildNode;
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


	public function getNodeContent() {
		return $this->nodeContent;
	}
}