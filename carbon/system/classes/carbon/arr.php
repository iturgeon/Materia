<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Carbon
 *
 * Carbon is a fast, lightweight, community driven PHP5 framework.
 *
 * @package		Carbon
 * @version		1.0
 * @author		Dan Horrigan <http://dhorrigan.com>
 * @license		Apache License v2.0
 * @copyright	2010 Dan Horrigan
 */

class Carbon_Arr {
	
	/**
	 * Flattens a multi-dimensional associative array down into a 1 dimensional
	 * assoc. array.
	 *
	 * @access	public
	 * @param	array	The array to flatten
	 * @param	string	What to glue the keys together with
	 * @param	bool	Whether to reset and start over on a new array
	 * @return	array
	 */
	public static function flatten_assoc($array, $glue = ':', $reset = TRUE)
	{
		static $return = array();
		static $curr_key = array();

		if ($reset)
		{
			$return = array();
			$curr_key = array();
		}

		foreach ($array as $key => $val)
		{
			$curr_key[] = $key;
			if (is_array($val) AND array_values($val) !== $val)
			{
				Arr::flatten_assoc($val, $glue, FALSE);
			}
			else
			{
				$return[implode($glue, $curr_key)] = $val;
			}
			array_pop($curr_key);
		}
		return $return;
	}
}

/* End of file arr.php */