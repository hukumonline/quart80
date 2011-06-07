<?php

class Pandamp_Core_Number
{
	public function generateNumber_()
	{
		return rand();
	}
    //function generateNumber($digits_quantity, $string = false, $zero = 1)
    function generateNumber()
    {
    	/*
        $random_number = 0;
        $digits = 0;
    
        while($digits < $digits_quantity)
        {
            $rand_max .= "9";
            $digits++;
        }
        
        mt_srand((double) microtime() * 1000000); 
        $random_number = mt_rand($zero, intval($rand_max));
    
        if($string)
        {
            if(strlen(strval($random_number)) < $digits_quantity)
            {
                $zeros_quantity = $digits_quantity - strlen(strval($random_number));
                $digits = 0;
                while($digits < $zeros_quantity)
                {
                    $str_zeros .= "0";
                    $digits++;
                }
                $random_number = $str_zeros . $random_number;
            }
        }
        return '7'.$random_number;
        */
    	//$random_number = intval( "0" . rand(1,9) . rand(0,9) . rand(0,9) . rand(0,9) . rand(0,9) );
    	//$n=5;
    	//$random_number = rand(0, pow(10, $n));
    	$lenth=5;
	    $aZ09 = array_merge(range('A', 'Z'), range('a', 'z'),range(0, 9));
	    $random_number ='';
	    for($c=0;$c < $lenth;$c++) {
	       $random_number .= $aZ09[mt_rand(0,count($aZ09)-1)];
	    }
    
		return $random_number;
    }
}