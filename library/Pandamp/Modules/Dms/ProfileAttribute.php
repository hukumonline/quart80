<?php
class Pandamp_Modules_Dms_ProfileAttribute implements Pandamp_BeanContext, Serializable {
	
	private $guid;
	private $profileGuid;
	private $attributeGuid;
	private $defaultValues;
	private $viewOrder;
	
	public function __construct()
	{
		
	}
	
	
// -------- accessors    

	public function getId() { return $this->guid; }
	public function getCatalogId() { return $this->profileGuid; }
	public function getAttributeGuid() { return $this->attributeGuid; }
	public function getDefaultValues() { return $this->defaultValues; }
	public function getViewOrder() { return $this->viewOrder; }
	
	public function setGuid($guid) { return $this->guid=$guid; }
	public function setCatalogId($profileGuid) { return $this->profileGuid=$profileGuid; }
	public function setAttributeGuid($attributeGuid) { return $this->attributeGuid=$attributeGuid; }
	public function setDefaultValues($defaultValues) { return $this->defaultValues=$defaultValues; }
	public function setViewOrder($viewOrder) { return $this->viewOrder=$viewOrder; }
	
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
    		'profileGuid'		=> $this->profileGuid,
    		'attributeGuid'		=> $this->attributeGuid,
    		'defaultValues'		=> $this->defaultValues,
    		'viewOrder'			=> $this->viewOrder
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
            'profileGuid: '.$data['profileGuid'].', '.
            'attributeGuid: '.$data['attributeGuid'].', '.
            'defaultValues: '.$data['defaultValues'].', ';
            'viewOrder: '.$data['viewOrder'].';';
    }
}
?>