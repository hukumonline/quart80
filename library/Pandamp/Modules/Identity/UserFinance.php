<?php

/**
 * A class representing table KutuUserFinance in the pandamp application.
 *
 * @uses       Pandamp_BeanContext
 * @category   Pandamp
 * @package    Pandamp
 * @subpackage User
 *
 * @author Nihki Prihadi <nihki@madaniyah.com>
 */
class Pandamp_Modules_Identity_UserFinance implements Pandamp_BeanContext, Serializable {
	
	//private $fid;
	private $userId;
	private $taxNumber;
	private $taxCompany;
	private $taxAddress;
	private $taxCity;
	private $taxProvince;
	private $taxCountryId;
	private $taxZip;
	
    /**
     * Constructor.
     *
     */
    public function __construct()
    {

    }
    
// -------- accessors    

    //public function getId() 						{ return $this->fid; 		}
    public function getUserId()						{ return $this->userId; 		} 
    public function getTaxNumber() 					{ return $this->taxNumber; 	}
    public function getTaxCompany() 				{ return $this->taxCompany; 	}
    public function getTaxAddress() 				{ return $this->taxAddress; 	}
    public function getTaxCity() 					{ return $this->taxCity; 	}
    public function getTaxProvince() 				{ return $this->taxProvince; 		}
    public function getTaxCountryId() 				{ return $this->taxCountryId; 		}
    public function getTaxZip() 					{ return $this->taxZip; 	}
    
    //public function setId($fid) 					{ $this->fid=$fid; 			}
    public function setUserId($userId) 				{ $this->userId=$userId;	 		}
    public function setTaxNumber($taxNumber) 		{ $this->taxNumber=$taxNumber; 	}
    public function setTaxCompany($taxCompany) 		{ $this->taxCompany=$taxCompany; 	}
    public function setTaxAddress($taxAddress) 		{ $this->taxAddress=$taxAddress; 	}
    public function setTaxCity($taxCity) 			{ $this->taxCity=$taxCity; 	}
    public function setTaxProvince($taxProvince) 	{ $this->taxProvince=$taxProvince; 			}
    public function setTaxCountryId($taxCountryId) 	{ $this->taxCountryId=$taxCountryId; 				}
    public function setTaxZip($taxZip) 				{ $this->taxZip=$taxZip; 		}
    
// -------- helper
    /**
     * Returns an associative array, which key/value pairs represent
     * the properties stored by this object.
     *
     * @return array
     */
    public function toArray()
    {
    	return array(
    		//'fid' 			=> $this->fid,
    		'userId'		=> $this->userId,
    		'taxNumber'		=> $this->taxNumber,
    		'taxCompany'	=> $this->taxCompany,
    		'taxAddress'	=> $this->taxAddress,
    		'taxCity'		=> $this->taxCity,
    		'taxProvince'	=> $this->taxProvince,
    		'taxCountryId'	=> $this->taxCountryId,
    		'taxZip'		=> $this->taxZip
    	);
    }
    
// -------- interface Pandamp_BeanContext
    /**
     * Serializes properties and returns them as a string which can later on
     * be unserialized.
     *
     * @return string
     */
    public function serialize()
    {
        $data = $this->toArray();

        return serialize($data);
    }

    /**
     * Unserializes <tt>$serialized</tt> and assigns the specific
     * values found to the members in this class.
     *
     * @param string $serialized The serialized representation of a former
     * instance of this class.
     */
    public function unserialize($serialized)
    {
        $str = unserialize($serialized);

         foreach ($str as $member => $value) {
            $this->$member = $value;
        }
    }

    /**
     * Returns a Dto for an instance of this class.
     *
     */
    public function getDto()
    {
        $data = $this->toArray();

        $dto = new Pandamp_Modules_Identity_UserFinance_Dto();
        foreach ($data as $key => $value) {
            if (property_exists($dto, $key)) {
                $dto->$key = $value;
            }
        }

        return $dto;
    }

    /**
     * Returns a textual representation of the current object.
     *
     * @return string
     */
    public function __toString()
    {
        $data = $this->toArray();
        return
            //'fid: '.$data['fid'].', '.
        	'userId: '.$data['userId'].', '.
            'taxNumber: '.$data['taxNumber'].', '.
            'taxCompany: '.$data['taxCompany'].', '.
            'taxAddress: '.$data['taxAddress'].', '.
            'taxCity: '.$data['taxCity'].', '.
            'taxProvince: '.$data['taxProvince'].', '.
            'taxCountryId: '.$data['taxCountryId'].', '.
            'taxZip: '.$data['taxZip'].';';
    }
    
}
?>