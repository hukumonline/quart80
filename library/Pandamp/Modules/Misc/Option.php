<?php
class Pandamp_Modules_Misc_Option implements Pandamp_BeanContext, Serializable {
	
	private $guid;
	private $pollGuid;
	private $text;
	private $hits;
	
    /**
     * Constructor.
     *
     */
    public function __construct()
    {

    }
    
// -------- accessors    

    public function getGuid() { return $this->guid; }
    public function getPollGuid() { return $this->pollGuid; }
    public function getText() { return $this->text; }
    public function getHits() { return $this->hits; }
    
    public function setGuid($guid) { return $this->guid=$guid; }
    public function setPollGuid($pollGuid) { return $this->pollGuid=$pollGuid; }
    public function setText($text) { return $this->text=$text; }
    public function setHits($hits) { return $this->hits=$hits; }
	
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
    		'pollGuid'	=> $this->pollGuid,
    		'text' => $this->text,
    		'hits' => $this->hits
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

        $dto = new Pandamp_Modules_Misc_Poll_Dto();
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
            'pollGuid: '.$data['pollGuid'].', '.
            'text: '.$data['text'].', '.
            'hits: '.$data['hits'].';';
    }
}
?>