<?php

class Entity implements Iterator
{
	protected $vars;

	public function __construct($data)
	{
		$this->vars = $data;
	}

	/**
	 * Implémentation des méthodes de l'interface iterator
	 */
	public function valid()
	{
		return array_key_exists(key($this->vars),$this->vars);
	}

	public function rewind()
	{
		reset($this->vars);
		return $this;
	}

	public function next() {
		next($this->vars);
		return $this;
	}

	public function key()
	{
		return key($this->vars);
	}

	public function current()
	{
		return current($this->vars);
	}
}