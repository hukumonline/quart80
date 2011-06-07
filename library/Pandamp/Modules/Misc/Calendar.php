<?php
class Pandamp_Modules_Misc_Calendar implements Pandamp_BeanContext, Serializable {
	
	private $id;
	private $uid;
	private $m;
	private $d;
	private $y;
	private $start_time;
	private $end_time;
	private $title;
	private $text;
	
    /**
     * Constructor.
     *
     */
    public function __construct()
    {

    }
    
// -------- accessors    

    public function getId() { return $this->id; }
    public function getUid() { return $this->uid; }
    public function getMonth() { return $this->m; }
    public function getDate() { return $this->d; }
    public function getYear() { return $this->y; }
    public function getStartTime() { return $this->start_time; }
    public function getEndTime() { return $this->end_time; }
    public function getTitle() { return $this->title; }
    public function getText() { return $this->text; }
    
    public function setId($id) { return $this->id=$id; }
    public function setUid($uid) { return $this->uid=$uid; }
    public function setMonth($m) { return $this->m=$m; }
    public function setDate($d) { return $this->d=$d; }
    public function setYear($y) { return $this->y=$y; }
    public function setStartTime($start_time) { return $this->start_time=$start_time; }
    public function setEndTime($end_time) { return $this->end_time=$end_time; }
    public function setTitle($title) { return $this->title=$title; }
    public function setText($text) { return $this->text=$text; }
	
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
    		'id'	=> $this->id,
    		'uid'	=> $this->uid,
    		'm' => $this->m,
    		'd' => $this->d,
    		'y' => $this->y,
    		'start_time' => $this->start_time,
    		'end_time' => $this->end_time,
    		'title' => $this->title,
    		'text' => $this->text
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

        $dto = new Pandamp_Modules_Misc_Agenda_Dto();
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
            'uid: '.$data['uid'].', '.
            'm: '.$data['m'].', '.
            'd: '.$data['d'].', '.
            'y: '.$data['y'].', ';
            'start_time: '.$data['start_time'].', ';
            'end_time: '.$data['end_time'].', ';
            'title: '.$data['title'].', ';
            'text: '.$data['text'].';';
    }
}
?>