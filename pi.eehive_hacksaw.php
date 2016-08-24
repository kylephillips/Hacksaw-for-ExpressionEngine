<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Hacksaw Class
 *
 * @package			ExpressionEngine
 * @category		Plugin
 * @author			Brett DeWoody - ee hive
 * @copyright		Copyright (c) 2010, Brett DeWoody
 * @link			http://www.ee-hive.com/add-ons/hacksaw
 */

class Eehive_hacksaw
{

	public $return_data = "";

	/**
	* Hacksaw
	*
	* This function strips HTML and cuts the content off at a specific character count, word or cutoff marker
	*
	* @access	public
	* @return	string
	*/
	public function __construct()
	{
		$this->EE =& get_instance();
	
		$tag_content = $this->EE->TMPL->tagdata;
	
		$chars = $this->EE->TMPL->fetch_param('chars');
		$chars_start = ($this->EE->TMPL->fetch_param('chars_start') ? $this->EE->TMPL->fetch_param('chars_start') : 0);
		$words = $this->EE->TMPL->fetch_param('words');
		$cutoff = $this->EE->TMPL->fetch_param('cutoff');
		$append = $this->EE->TMPL->fetch_param('append', '');
		$allow = $this->EE->TMPL->fetch_param('allow');
	
		if(isset($cutoff) && $cutoff != "") {
			$cutoff_content = $this->_truncate_cutoff($tag_content, $cutoff, $words, $allow, $append);
			// Strip the HTML
			$new_content = (strpos($tag_content, $cutoff) ? strip_tags($cutoff_content, $allow) : strip_tags($cutoff_content, $allow));
		} elseif (isset($chars) && $chars != "") {
			// Strip the HTML
			$stripped_content = strip_tags($tag_content, $allow);
			$new_content = (strlen($stripped_content) <= $chars ? $stripped_content : $this->_truncate_chars($stripped_content, $chars_start, $chars, $append));
		} elseif (isset($words) && $words != "") {
			// Strip the HTML
			$stripped_content = strip_tags($tag_content, $allow);
			$new_content = (str_word_count($stripped_content) <= $words ? $stripped_content : $this->_truncate_words($stripped_content, $words, $append));
		} else {
			// Strip the HTML
			$stripped_content = strip_tags($tag_content, $allow);
			$new_content = $stripped_content;
		}
	
		// Return the new content
		$this->return_data = $new_content; 
  }
  
	// Helper Function - Truncate by Word Limit
	private function _truncate_words($content, $limit, $append)
	{
		$num_words = str_word_count($content, 0);
		if ($num_words > $limit) {
			$words = str_word_count($content, 2);
			$pos = array_keys($words);
			$content = substr($content, 0, ($pos[$limit]-1)) . $append;
		}
		return $content;
	}
	
	// Helper Function - Truncate by Character Limit
	private function _truncate_chars($content, $chars_start, $limit, $append)
	{
		// Removing the below to see how it effect UTF-8. 
		$content = preg_replace('/\s+?(\S+)?$/', '', substr($content, $chars_start, ($limit+1))) . $append;
		return $content;
	}
  
	// Helper Function - Truncate by Cutoff Marker
	private function _truncate_cutoff($content, $cutoff, $words, $allow, $append)
	{
		$pos = strpos($content, $cutoff);
		if ($pos != FALSE) {
			$content = substr($content, 0, $pos) . $append;
		} elseif ($words != "") {
			$content = $this->_truncate_words(strip_tags($content, $allow), $words, '') . $append;
		}
		return $content;
	}
}
/* Location: ./system/expressionengine/third_party/eehive_buzzsaw/pi.eehive_buzzsaw.php */