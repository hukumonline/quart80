<?php
class Pandamp_Modules_Dms_Folder implements Pandamp_BeanContext, Serializable {
	
	private $guid;
	private $title;
	private $description;
	private $parentGuid;
	private $path;
	private $type;
	private $viewOrder;
	private $cmsParams;
	
    /**
     * Constructor.
     *
     */
    public function __construct()
    {

    }
    
// -------- accessors    

    public function getId() { return $this->guid; }
    public function getTitle() { return $this->title; }
    public function getDescription() { return $this->description; }
    public function getParentGuid() { return $this->parentGuid; }
    public function getPath() { return $this->path; }
    public function getType() { return $this->type; }
    public function getViewOrder() { return $this->viewOrder; }
    public function getCmsParams() { return $this->cmsParams; }
    
    public function setGuid($guid) { return $this->guid=$guid; }
    public function setTitle($title) { return $this->title=$title; }
    public function setDescription($description) { return $this->description=$description; }
    public function setParentGuid($parentGuid) { return $this->parentGuid=$parentGuid; }
    public function setPath($path) { return $this->path=$path; }
    public function setType($type) { return $this->type=$type; }
    public function setViewOrder($viewOrder) { return $this->viewOrder=$viewOrder; }
    public function setCmsParams($cmsParams) { return $this->cmsParams=$cmsParams; }
	
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
    		'guid'	=> $this->guid,
    		'title'	=> $this->title,
    		'description' => $this->description,
    		'parentGuid' => $this->parentGuid,
    		'path' => $this->path,
    		'type' => $this->type,
    		'viewOrder' => $this->viewOrder,
    		'cmsParams' => $this->cmsParams
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

        $dto = new Pandamp_Modules_Dms_Folder_Dto();
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
            'title: '.$data['title'].', '.
            'description: '.$data['description'].', '.
            'parentGuid: '.$data['parentGuid'].', '.
            'path: '.$data['path'].', ';
            'type: '.$data['type'].', ';
            'viewOrder: '.$data['viewOrder'].', ';
            'cmsParams: '.$data['cmsParams'].';';
    }
}
?>