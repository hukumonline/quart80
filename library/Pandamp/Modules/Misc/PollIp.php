<?php
class Pandamp_Modules_Misc_PollIp implements Pandamp_BeanContext, Serializable {
	
	private $guid;
	private $dateOfPoll;
	private $ip;
	private $voteId;
	private $pollGuid;
	
    /**
     * Constructor.
     *
     */
    public function __construct()
    {

    }
    
// -------- accessors    

    public function getId() { return $this->guid; }
    public function getDateOfPoll() { return $this->dateOfPoll; }
    public function getIp() { return $this->ip; }
    public function getVoteId() { return $this->voteId; }
    public function getPollId() { return $this->pollGuid; }
    
    public function setId($guid) { return $this->guid=$guid; }
    public function setDateOfPoll($dateOfPoll) { return $this->dateOfPoll=$dateOfPoll; }
    public function setIp($ip) { return $this->ip=$ip; }
    public function setVoteId($voteId) { return $this->voteId=$voteId; }
    public function setPollGuid($pollGuid) { return $this->pollGuid=$pollGuid; }
	
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
    		'dateOfPoll'	=> $this->dateOfPoll,
    		'ip' => $this->ip,
    		'voteId' => $this->voteId,
    		'pollGuid' => $this->pollGuid
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
            'dateOfPoll: '.$data['dateOfPoll'].', '.
            'ip: '.$data['ip'].', '.
            'voteId: '.$data['voteId'].', ';
            'pollGuid: '.$data['pollGuid'].';';
    }
}
?>