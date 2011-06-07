<?php
class Pandamp_Modules_Extension_Bbcode implements Pandamp_BeanContext, Serializable {
	
	private $idEmoticon;
	private $image;
	private $bbcode;
	
    /**
     * Constructor.
     *
     */
    public function __construct()
    {

    }
    
// -------- accessors    

    public function getIdEmoticon() { return $this->idEmoticon; }
    public function getImage() { return $this->image; }
    public function getBbCode() { return $this->bbcode; }
    
    public function setIdEmoticon($idEmoticon) { return $this->idEmoticon=$idEmoticon; }
    public function setImage($image) { return $this->image=$image; }
    public function setBbCode($bbcode) { return $this->bbcode=$bbcode; }
	
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
    		'idEmoticon' => $this->idEmoticon,
    		'image'	=> $this->image,
    		'bbcode' => $this->bbcode
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

        $dto = new Pandamp_Modules_Extension_Bbcode_Dto();
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
            'idEmoticon: '.$data['idEmoticon'].', '.
            'image: '.$data['image'].', '.
            'bbcode: '.$data['bbcode'].';';
    }
}
?>