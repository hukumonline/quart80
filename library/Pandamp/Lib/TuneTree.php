<?php
class Pandamp_Lib_TuneTree
{
	var $children = array();
	
	/**
	 * A hack to support __construct() on PHP 4
	 *
	 * @access	public
	 * @return	object
	 */
	function Pandamp_Lib_TuneTree()
	{
		$args = func_get_args();
		call_user_func_array(array(&$this, '__construct'), $args);
	}
	
	/**
	 * Class constructor
	 *
	 * @param	array	$items	array of all objects (objects must contain id and parent fields)
	 * @access	protected
	 * @return	object
	 */
	function __construct( $items )
	{
		$this->children = array();
			
		foreach ($items as $v) {
			$pt = $v->getParent();
			$list = isset($this->children[$pt]) ? $this->children[$pt] : array();
			array_push($list, $v);
			$this->children[$pt] = $list;
		}
	}
	
	/**
	 * Recursive building tree
	 *
	 * @access	protected
	 * @return	array
	 */
	function _buildTree( $id, $list = array(), $maxlevel = 9999, $level = 0, $number = '' )
	{
		if (isset($this->children[$id]) && $level <= $maxlevel) {
			if ($number != '') {
				$number .= '.';
			}
				
			$i = 1;
				
			foreach ($this->children[$id] as $v) {
				$id = $v->getId();
				$list[$id] = $v;
				$list[$id]->level = $level;
				$list[$id]->number = $number . $i;
				$list[$id]->children = isset($this->children[$id]) ? count($this->children[$id]) : 0;
				$list = $this->_buildTree($id, $list, $maxlevel, $level + 1, $list[$id]->number);
				$i++;
			}
		}
		return $list;
	}
		
	/**
	 * Return objects tree
	 *
	 * @access	public
	 * @param	int	$node	node id (by default node id is 0 - root node)
	 * @return	array
	 */
	function get( $node = 0 )
	{
		return $this->_buildTree($node);
	}
}
?>