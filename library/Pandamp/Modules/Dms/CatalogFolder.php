<?php
class Pandamp_Modules_Dms_CatalogFolder implements Pandamp_BeanContext, Serializable {
	
	private $catalogGuid;
	private $folderGuid;
	
	public function __construct()
	{
		
	}
	
	
// -------- accessors    

	public function getCatalogId() { return $this->catalogGuid; }
	public function getFolderGuid() { return $this->folderGuid; }
	
	public function setCatalogId($catalogGuid) { return $this->catalogGuid=$catalogGuid; }
	public function setFolderGuid($folderGuid) { return $this->folderGuid=$folderGuid; }
	
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
    		'catalogGuid'		=> $this->catalogGuid,
    		'folderGuid'		=> $this->folderGuid
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
    	/*
        $data = $this->toArray();

        $dto = new Pandamp_Modules_Dms_Catalog_Dto();
        foreach ($data as $key => $value) {
            if (property_exists($dto, $key)) {
                $dto->$key = $value;
            }
        }

        return $dto;
        */
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
            'catalogGuid: '.$data['catalogGuid'].', '.
            'folderGuid: '.$data['folderGuid'].';';
    }
}
?>