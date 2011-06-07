<?php

/**
 * A class representing an user in the pandamp application.
 *
 * @uses       Pandamp_BeanContext
 * @category   Pandamp
 * @package    Pandamp
 * @subpackage User
 *
 * @author Nihki Prihadi <nihki@madaniyah.com>
 */
class Pandamp_Modules_Identity_User implements Pandamp_BeanContext, Serializable {
	
	private $guid;
	private $kopel;
	private $username;
	private $password;
	private $fullName;
	private $birthday;
	private $phone;
	private $fax;
	private $gender;
	private $email;
	private $openId;
	private $company;
	private $address;
	private $indexCol;
	private $billingAddress;
	private $emailBilling;
	private $picture;
	private $newArticle;
	private $weeklyList;
	private $monthlyList;
	private $packageId;
	private $promotionId;
	private $educationId;
	private $expenseId;
	private $paymentId;
	private $businessTypeId;
	private $periodeId;
	private $activationDate;
	private $isEmailSent;
	private $isEmailSentOver;
	private $createdDate;
	private $createdBy;
	private $updatedDate;
	private $updatedBy;
	private $isActive;
	private $isContact;
	
    /**
     * Constructor.
     *
     */
    public function __construct()
    {

    }
    
// -------- accessors    

    public function getId() 					{ return $this->guid; 		}
    public function getKopel() 					{ return $this->kopel; 		} 
    public function getUsername() 				{ return $this->username; 	}
    public function getPassword() 				{ return $this->password; 	}
    public function getFullname() 				{ return $this->fullName; 	}
    public function getBirthday() 				{ return $this->birthday; 	}
    public function getPhone() 					{ return $this->phone; 		}
    public function getFax() 					{ return $this->fax; 		}
    public function getGender() 				{ return $this->gender; 	}
    public function getEmail() 					{ return $this->email; 		}
    public function getOpenId() 				{ return $this->openId; 	}
    public function getCompany() 				{ return $this->company; 	}
    public function getAddress() 				{ return $this->address; 	}
    public function getIndexCol() 				{ return $this->indexCol; 	}
    public function getBillingAddress()			{ return $this->billingAddress; 	}
    public function getEmailBilling()			{ return $this->emailBilling; 	}
    public function getPicture()			{ return $this->picture; 	}
    public function getNewArticle()			{ return $this->newArticle; 	}
    public function getWeeklyList()			{ return $this->weeklyList; 	}
    public function getMonthlyList()			{ return $this->monthlyList; 	}
    public function getPackageId()			{ return $this->packageId; 	}
    public function getPromotionId()			{ return $this->promotionId; 	}
    public function getEducationId()			{ return $this->educationId; 	}
    public function getExpenseId()			{ return $this->expenseId; 	}
    public function getPaymentId()			{ return $this->paymentId; 	}
    public function getBusinessTypeId()			{ return $this->businessTypeId; 	}
    public function getPeriodeId()			{ return $this->periodeId; 	}
    public function getActivationDate()			{ return $this->activationDate; 	}
    public function getIsEmailSent()			{ return $this->isEmailSent; 	}
    public function getIsEmailSentOver()			{ return $this->isEmailSentOver; 	}
    public function getCreatedDate()			{ return $this->createdDate; 	}
    public function getCreatedBy()			{ return $this->createdBy; 	}
    public function getUpdatedDate()			{ return $this->updatedDate; 	}
    public function getUpdatedBy()			{ return $this->updatedBy; 	}
    public function getIsActive()			{ return $this->isActive; 	}
    public function getIsContact()			{ return $this->isContact; 	}
    
    public function setId($guid) 				{ $this->guid=$guid; 			}
    public function setKopel($kopel) 			{ $this->kopel=$kopel;	 		}
    public function setUsername($username) 		{ $this->username=$username; 	}
    public function setPassword($password) 		{ $this->password=$password; 	}
    public function setFullname($fullName) 		{ $this->fullName=$fullName; 	}
    public function setBirthday($birthday) 		{ $this->birthday=$birthday; 	}
    public function setPhone($phone) 			{ $this->phone=$phone; 			}
    public function setFax($phone) 				{ $this->fax=$fax; 				}
    public function setGender($gender) 			{ $this->gender=$gender; 		}
    public function setEmail($email) 			{ $this->email=$email; 			}
    public function setOpenid($openId) 			{ $this->openId=$openId;		}
    public function setCompany($company)		{ $this->company=$company;		}
    public function setAddress($address)		{ $this->address=$address;		}
    public function setIndexCol($indexCol)		{ $this->indexCol=$indexCol;	}
    public function setBillingAddress($billingAddress)		{ $this->billingAddress=$billingAddress;	}
    public function setEmailBilling($emailBilling)		{ $this->emailBilling=$emailBilling;	}
    public function setPicture($emailBilling)		{ $this->picture=$picture;	}
    public function setNewArticle($newArticle)		{ $this->newArticle=$newArticle;	}
    public function setWeeklyList($weeklyList)		{ $this->weeklyList=$weeklyList;	}
    public function setMonthlyList($monthlyList)		{ $this->monthlyList=$monthlyList;	}
    public function setPackageId($packageId)		{ $this->packageId=$packageId;	}
    public function setPromotionId($promotionId)		{ $this->promotionId=$promotionId;	}
    public function setEducationId($educationId)		{ $this->educationId=$educationId;	}
    public function setExpenseId($expenseId)		{ $this->expenseId=$expenseId;	}
    public function setPaymentId($paymentId)		{ $this->paymentId=$paymentId;	}
    public function setBusinessTypeId($businessTypeId)		{ $this->businessTypeId=$businessTypeId;	}
    public function setPeriodeId($periodeId)		{ $this->periodeId=$periodeId;	}
    public function setActivationDate($activationDate)		{ $this->activationDate=$activationDate;	}
    public function setIsEmailSent($isEmailSent)		{ $this->isEmailSent=$isEmailSent;	}
    public function setIsEmailSentOver($isEmailSentOver)		{ $this->isEmailSentOver=$isEmailSentOver;	}
    public function setCreatedDate($createdDate)		{ $this->createdDate=$createdDate;	}
    public function setCreatedBy($createdBy)		{ $this->createdBy=$createdBy;	}
    public function setUpdatedDate($updatedDate)		{ $this->updatedDate=$updatedDate;	}
    public function setUpdatedBy($updatedBy)		{ $this->updatedBy=$updatedBy;	}
    public function setIsActive($isActive)		{ $this->isActive=$isActive;	}
    public function setIsContact($isContact)		{ $this->isContact=$isContact;	}
    
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
    		'guid' 			=> $this->guid,
    		'kopel'			=> $this->kopel,
    		'username'		=> $this->username,
    		'fullName'		=> $this->fullName,
    		'birthday'		=> $this->birthday,
    		'phone'			=> $this->phone,
    		'fax'			=> $this->fax,
    		'gender'		=> $this->gender,
    		'email'			=> $this->email,
    		'company'		=> $this->company,
    		'address'		=> $this->address,
    		'indexCol'		=> $this->indexCol,
    		'billingAddress'		=> $this->billingAddress,
    		'emailBilling'		=> $this->emailBilling,
    		'picture'		=> $this->picture,
    		'newArticle'		=> $this->newArticle,
    		'weeklyList'		=> $this->weeklyList,
    		'monthlyList'		=> $this->monthlyList,
    		'packageId'		=> $this->packageId,
    		'promotionId'		=> $this->promotionId,
    		'educationId'		=> $this->educationId,
    		'expenseId'		=> $this->expenseId,
    		'paymentId'		=> $this->paymentId,
    		'businessTypeId'		=> $this->businessTypeId,
    		'periodeId'		=> $this->periodeId,
    		'activationDate'		=> $this->activationDate,
    		'isEmailSent'		=> $this->isEmailSent,
    		'isEmailSentOver'		=> $this->isEmailSentOver,
    		'createdDate'		=> $this->createdDate,
    		'createdBy'		=> $this->createdBy,
    		'updatedDate'		=> $this->updatedDate,
    		'updatedBy'		=> $this->updatedBy,
    		'isActive'		=> $this->isActive,
    		'isContact'		=> $this->isContact
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

        $dto = new Pandamp_Modules_Identity_User_Dto();
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
            'guid: '.$data['guid'].', '.
        	'kopel: '.$data['kopel'].', '.
            'username: '.$data['username'].', '.
            'fullName: '.$data['fullName'].', '.
            'birthday: '.$data['birthday'].', '.
            'phone: '.$data['phone'].', '.
            'fax: '.$data['fax'].', '.
            'gender: '.$data['gender'].', '.
            'email: '.$data['email'].', '.
            'address: '.$data['address'].', '.
            'company: '.$data['company'].', '.
            'indexCol: '.$data['indexCol'].', '.
            'billingAddress: '.$data['billingAddress'].', '.
            'emailBilling: '.$data['emailBilling'].', '.
            'picture: '.$data['picture'].', '.
            'newArticle: '.$data['newArticle'].', '.
            'weeklyList: '.$data['weeklyList'].', '.
            'monthlyList: '.$data['monthlyList'].', '.
            'packageId: '.$data['packageId'].', '.
            'promotionId: '.$data['promotionId'].', '.
            'educationId: '.$data['educationId'].', '.
            'expenseId: '.$data['expenseId'].', '.
            'paymentId: '.$data['paymentId'].', '.
            'businessTypeId: '.$data['businessTypeId'].', '.
            'periodeId: '.$data['periodeId'].', '.
            'activationDate: '.$data['activationDate'].', '.
            'isEmailSent: '.$data['isEmailSent'].', '.
            'isEmailSentOver: '.$data['isEmailSentOver'].', '.
            'createdDate: '.$data['createdDate'].', '.
            'createdBy: '.$data['createdBy'].', '.
            'updatedDate: '.$data['updatedDate'].', '.
            'updatedBy: '.$data['updatedBy'].', '.
            'isActive: '.$data['isActive'].', '.
            'isContact: '.$data['isContact'].';';
    }
    
}
?>