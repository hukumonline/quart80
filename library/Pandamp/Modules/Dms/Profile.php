<?php
class Pandamp_Modules_Dms_Profile implements Pandamp_BeanContext, Serializable {
	
	private $guid;
	private $title;
	private $description;
	private $profileType;
	
	public function __construct()
	{
		
	}
	
	
// -------- accessors    

	public function getId() { return $this->guid; }
	public function getTitle() { return $this->title; }
	public function getDescription() { return $this->description; }
	public function getProfileType() { return $this->ProfileType; }
	
	public function setGuid($guid) { return $this->guid=$guid; }
	public function setTitle($title) { return $this->title=$title; }
	public function setDescription($description) { return $this->description=$description; }
	public function setProfileType($profileType) { return $this->ProfileType=$profileType; }
	
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
    		'title'				=> $this->title,
    		'description'		=> $this->description,
    		'profileType'		=> $this->profileType
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
            'title: '.$data['title'].', '.
            'description: '.$data['description'].', '.
            'profileType: '.$data['profileType'].';';
    }
}
?>