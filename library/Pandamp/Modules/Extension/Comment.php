<?php
class Pandamp_Modules_Extension_Comment implements Pandamp_BeanContext, Serializable {
	
	private $id;
	private $parent;
	private $objectId;
	private $userid;
	private $name;
	private $username;
	private $email;
	private $title;
	private $comment;
	private $ip;
	private $date;
	private $good;
	private $poor;
	private $published;
	private $checkedOutTime;
	
    /**
     * Constructor.
     *
     */
    public function __construct()
    {

    }
    
// -------- accessors    

    public function getId() { return $this->id; }
    public function getParent() { return $this->parent; }
    public function getobjectId() { return $this->objectId; }
    public function getuserid() { return $this->userid; }
    public function getName() { return $this->name; }
    public function getUsername() { return $this->username; }
    public function getEmail() { return $this->email; }
    public function getTitle() { return $this->title; }
    public function getComment() { return $this->comment; }
    public function getIp() { return $this->ip; }
    public function getDate() { return $this->date; }
    public function getgood() { return $this->good; }
    public function getpoor() { return $this->poor; }
    public function getPublished() { return $this->published; }
    public function getCheckedOutTime() { return $this->checkedOutTime; }
    
    public function setId($id) { return $this->id=$id; }
    public function setParent($parent) { return $this->parent=$parent; }
    public function setobjectId($objectId) { return $this->objectId=$objectId; }
    public function setuserid($userid) { return $this->userid=$userid; }
    public function setName($name) { return $this->name=$name; }
    public function setUsername($username) { return $this->username=$username; }
    public function setEmail($email) { return $this->email=$email; }
    public function setTitle($title) { return $this->title=$title; }
    public function setComment($comment) { return $this->comment=$comment; }
    public function setIp($ip) { return $this->ip=$ip; }
    public function setdate($date) { return $this->date=$date; }
    public function setgood($good) { return $this->good=$good; }
    public function setpoor($poor) { return $this->poor=$poor; }
    public function setPublished($published) { return $this->published=$published; }
    public function setCheckedOutTime($checkedOutTime) { return $this->checkedOutTime=$checkedOutTime; }
	
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
    		'parent'	=> $this->parent,
    		'objectId' => $this->objectId,
    		'userid' => $this->userid,
    		'name' => $this->name,
    		'username' => $this->username,
    		'email' => $this->email,
    		'title' => $this->title,
    		'comment' => $this->comment,
    		'ip' => $this->ip,
    		'date' => $this->date,
    		'good' => $this->good,
    		'poor' => $this->poor,
    		'published' => $this->published,
    		'checkedOutTime' => $this->checkedOutTime
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

        $dto = new Pandamp_Modules_Extension_Comment_Dto();
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
            'parent: '.$data['parent'].', '.
            'objectId: '.$data['objectId'].', '.
            'userid: '.$data['userid'].', '.
            'name: '.$data['name'].', '.
            'username: '.$data['username'].', '.
            'email: '.$data['email'].', '.
            'title: '.$data['title'].', '.
            'comment: '.$data['comment'].', '.
            'ip: '.$data['ip'].', '.
            'date: '.$data['date'].', '.
            'good: '.$data['good'].', '.
            'poor: '.$data['poor'].', '.
            'published: '.$data['published'].', '.
            'checkedOutTime: '.$data['checkedOutTime'].';';
    }
}