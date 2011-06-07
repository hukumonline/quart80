<?php

class Pandamp_Controller_Action_Helper_GetClinicSource 
{
	public function getClinicSource($value)
	{
		switch ($value)
		{
			case '0':
			case 0:
				return 'Hukumonline';
				break;
			case 'lt4a0a7abee1f6e':
				return 'Ikatan Advokat Indonesia';
				break;
			case 'lt4a0a7a68885a2':
				return 'Advokat';
				break;
			case 'lt4a0a79cbcfdec':
				return 'LeIP';
				break;
			case 'lt4a0a795dc67d9':
				return 'LBH Jakarta';
				break;
			case 'lt4a0a78d8e8d19':
				return 'PSHK';
				break;
			case 'lt4a0a782de3e09':
				return 'IP Center';
				break;
			case 'lt4a0a77a35fef3':
				return 'TURC';
				break;
			case 'lt4a0a771a21161':
				return 'Ikatan Kekeluargaan Advokat Universitas Indonesia';
				break;
			case 'lt4a0a767fc1dba':
				return 'Suara Keadilan';
				break;
			case 'lt4a0a7548160b7':
				return 'LKHT';
				break;
		}
	}
}

?>