<?php
class Pandamp_Modules_Extension_Vote implements Pandamp_BeanContext, Serializable {
	
	private $id;
	private $guid;
	private $userid;
	private $ip;
	private $counter;
	private $value;
	
    /**
     * Constructor.
     *
     */
    public function __construct()
    {

    }
    
// -------- accessors    

    public function getId() { return $this->id; }
    public function getGuid() { return $this->guid; }
    public function getUserId() { return $this->userid; }
    public function getIp() { return $this->ip; }
    public function getCounter() { return $this->counter; }
    public function getValue() { return $this->value; }
    
    public function setId($id) { return $this->id=$id; }
    public function setGuid($guid) { return $this->guid=$guid; }
    public function setUserId($userid) { return $this->userid=$userid; }
    public function setIp($ip) { return $this->ip=$ip; }
    public function setCounter($counter) { return $this->counter=$counter; }
    public function setValue($value) { return $this->value=$value; }
	
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
    		'id' => $this->id,
    		'guid'	=> $this->guid,
    		'userid'	=> $this->userid,
    		'ip' => $this->ip,
    		'counter' => $this->counter,
    		'value' => $this->value
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
            'id: '.$data['id'].', '.
            'guid: '.$data['guid'].', '.
            'userid: '.$data['userid'].', '.
            'ip: '.$data['ip'].', '.
            'counter: '.$data['counter'].', '.
            'value: '.$data['value'].';';
    }
}
?>