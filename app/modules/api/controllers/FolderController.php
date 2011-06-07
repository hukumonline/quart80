<?php
class Api_FolderController extends Zend_Controller_Action
{
	function chdirEventAction()
	{
		$dir = ($this->_getParam('dir'))? $this->_getParam('dir') : '';
		$response = array('dirselect' => $this->_html_row_output($dir));
		echo Zend_Json::encode($response);
	}
	private function _html_row_output($id)
	{
		$tblFolder = new Pandamp_Modules_Dms_Folder_Model_Folder();
		// first we get the position chain
		if ($id != '/' && $id != 'root') {
			$position_chain = $this->_get_position($id);			
			$positions = explode("/" , $position_chain);
			$output = '<span><a style="font-size:10px; color:#666666; font-family:arial" href="javascript:void(0);" onclick="theDir=\'root\'; chDir(theDir);">Root</a>&nbsp;&bull;&nbsp;';
			foreach ($positions as $nid)
			{
				$body = "";
				if (!$nid) continue;
				$c = $tblFolder->find($nid)->current();
				if ($id == $nid)
				{
					$body .= '<span style="font-size:10px; text-decoration:none; color:red; font-family:arial; font-weight:bold" >'.$c->title.'</span>';
				} else {
					$body .= "<a style=\"font-size:10px; color:#666666; font-family:arial\" href='javascript:;' onclick='theDir=\"$c->guid\"; chDir(theDir);'>".$c->title."</a>";
					$body .= '&nbsp;&bull;&nbsp;';
				}
				// now lets replace the keys in the templates with the values
				foreach($c as $key => $value)
				{	
					$body = str_replace("[$key]" ,$value, $body);	
				}
				$output .= $body;		
			}
			$output .= '</span>';
			return $output;
		}
		else {
			$output = '<a style="font-size:10px; color:#666666; font-family:arial" href="javascript:void(0);" onclick="theDir=\'/\'; chDir(theDir);">Root</a>';
			return $output;
		}
	}
	private function _get_position($id)
	{
		if ($id == 'root') return "";
		$tblFolder = new Pandamp_Modules_Dms_Folder_Model_Folder();
		$result = $tblFolder->find($id)->current();
		return $result->path.$result->guid;
	}
}
?>