<?php
require 'vendor/autoload.php';
use Tree\Node\Node;

const INDENT_SIZE = 2;

$valueCache = array();

function print_node($node, $indent = 0, $keys = array()) {
	global $valueCache;
	for ($i=0; $i<$indent*INDENT_SIZE; $i++)
		echo " ";
	echo $node->getValue() . ":";
	if ($node->isLeaf()) {
		$linear = implode(".", array_merge($keys, [$node->getValue()]));
		echo " " . $valueCache[$linear] . "\n";
	} else {
		echo "\n";
		foreach ($node->getChildren() as $n) {
			print_node($n, $indent + 1, array_merge($keys, [$node->getValue()]));
		}
	}
}

$root = new Node('root');
$nodeCache = array();

$handle = fopen($argv[1], "r");
if ($handle) {
    while (($line = fgets($handle)) !== false) {
		$line = trim($line);
		$parts = explode("=", $line, 2);
		if (count($parts) > 1) {
			$key = trim($parts[0]);
			$value = trim($parts[1]);
			
			$valueCache[$key] = $value;
			
			$current = null; $previous = $root; $id = "";
			foreach (explode(".", $key) as $p) {
				$id .= "-" . $p;
				if (isset($nodeCache[$id])) {
					$current = $nodeCache[$id];
				} else {
					$current = new Node($p);
					$previous->addChild($current);
					$nodeCache[$id] = $current;
				}
				$previous = $current;
			}
		}
    }
    fclose($handle);
	
	foreach ($root->getChildren() as $n) {
		print_node($n);
		echo "\n";
	}
} else {
	echo "Failure opening file!";
} 

?>