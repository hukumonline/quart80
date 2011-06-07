<?php
class Pandamp_Modules_Dms_Catalog implements Pandamp_BeanContext, Serializable {
	
	private $guid;
	private $shortTitle;
	private $profileGuid;
	private $publishedDate;
	private $expiredDate;
	private $createdBy;
	private $modifiedBy;
	private $createdDate;
	private $modifiedDate;
	private $deletedDate;
	private $price;
	private $status;
	
    /**
     * Constructor.
     *
     */
    public function __construct()
    {

    }
    
// -------- accessors    

    public function getId() { return $this->guid; }
    public function getShortTitle() { return $this->shortTitle; }
    public function getProfile() { return $this->profileGuid; }
    public function getPublishedDate() { return $this->publishedDate; }
    public function getExpiredDate() { return $this->expiredDate; }
    public function getCreatedBy() { return $this->createdBy; }
    public function getModifiedBy() { return $this->modifiedBy; }
    public function getCreatedDate() { return $this->createdDate; }
    public function getModifiedDate() { return $this->modifiedDate; }
    public function getDeletedDate() { return $this->deletedDate; }
    public function getPrice() { return $this->price; }
    public function getStatus() { return $this->status; }
    
    /*
     * @todo set[nama field database]
     * otherwise error message: Setter for [nama field database] not found
     */
    public function setGuid($guid) { return $this->guid=$guid; }
    public function setShortTitle($shortTitle) { return $this->shortTitle=$shortTitle; }
    public function setProfileGuid($profileGuid) { return $this->profileGuid=$profileGuid; }
    public function setPublishedDate($publishedDate) { return $this->publishedDate=$publishedDate; }
    public function setExpiredDate($expiredDate) { return $this->expiredDate=$expiredDate; }
    public function setCreatedBy($createdBy) { return $this->createdBy=$createdBy; }
    public function setModifiedBy($modifiedBy) { return $this->modifiedBy=$modifiedBy; }
    public function setCreatedDate($createdDate) { return $this->createdDate=$createdDate; }
    public function setModifiedDate($modifiedDate) { return $this->modifiedDate=$modifiedDate; }
    public function setDeletedDate($deletedDate) { return $this->deletedDate=$deletedDate; }
    public function setPrice($price) { return $this->price=$price; }
    public function setStatus($status) { return $this->status=$status; }
	
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
    		'guid' 				=> $this->guid,
    		'shortTitle'		=> $this->shortTitle,
    		'profileGuid'		=> $this->profileGuid,
    		'publishedDate'		=> $this->publishedDate,
    		'expiredDate'		=> $this->expiredDate,
    		'createdBy'			=> $this->createdBy,
    		'modifiedBy'		=> $this->modifiedBy,
    		'createdDate'		=> $this->createdDate,
    		'modifiedDate'		=> $this->modifiedDate,
    		'deletedDate'		=> $this->deletedDate,
    		'price'				=> $this->price,
    		'status'			=> $this->status
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

        $dto = new Pandamp_Modules_Dms_Catalog_Dto();
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
            'shortTitle: '.$data['shortTitle'].', '.
            'profileGuid: '.$data['profileGuid'].', '.
            'publishedDate: '.$data['publishedDate'].', '.
            'expiredDate: '.$data['expiredDate'].', '.
            'createdBy: '.$data['createdBy'].', '.
            'modifiedBy: '.$data['modifiedBy'].', '.
            'createdDate: '.$data['createdDate'].', '.
            'modifiedDate: '.$data['modifiedDate'].', '.
            'deletedDate: '.$data['deletedDate'].', '.
        	'price: '.$data['price'].', ';
        	'status: '.$data['status'].';';
    }
}
?>