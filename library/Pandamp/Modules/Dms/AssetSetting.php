<?php
class Pandamp_Modules_Dms_AssetSetting implements Pandamp_BeanContext, Serializable {
	
	private $guid;
	private $contentType;
	private $application;
	private $part;
	private $detail;
	private $valueType;
	private $valueInt;
	private $valueFloat;
	private $valueText;
	private $valueDatetime;
	
    /**
     * Constructor.
     *
     */
    public function __construct()
    {

    }
    
// -------- accessors    

    public function getId() { return $this->guid; }
    public function getContentType() { return $this->contentType; }
    public function getApplication() { return $this->application; }
    public function getPart() { return $this->part; }
    public function getDetail() { return $this->detail; }
    public function getValueType() { return $this->valueType; }
    public function getValueInt() { return $this->valueInt; }
    public function getValueFloat() { return $this->valueFloat; }
    public function getValueText() { return $this->valueText; }
    public function getValueDatetime() { return $this->valueDatetime; }
    
    /*
     * @todo set[nama field database]
     * otherwise error message: Setter for [nama field database] not found
     */
    public function setGuid($guid) { return $this->guid=$guid; }
    public function setContentType($contentType) { return $this->contentType=$contentType; }
    public function setApplication($application) { return $this->application=$application; }
    public function setPart($part) { return $this->part=$part; }
    public function setDetail($detail) { return $this->detail=$detail; }
    public function setValueType($valueType) { return $this->valueType=$valueType; }
    public function setValueInt($valueInt) { return $this->valueInt=$valueInt; }
    public function setValueFloat($valueFloat) { return $this->valueFloat=$valueFloat; }
    public function setValueText($valueText) { return $this->valueText=$valueText; }
    public function setValueDatetime($valueDatetime) { return $this->valueDatetime=$valueDatetime; }

    
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
    		'guid' 			=> $this->guid,
    		'contentType'	=> $this->contentType,
    		'application'	=> $this->application,
    		'part'			=> $this->part,
    		'detail'		=> $this->detail,
    		'valueType'		=> $this->valueType,
    		'valueInt'		=> $this->valueInt,
    		'valueFloat'	=> $this->valueFloat,
    		'valueText'		=> $this->valueText,
    		'valueDatetime'	=> $this->valueDatetime
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

        $dto = new Pandamp_Modules_Dms_Catalog_Dto();
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
            'contentType: '.$data['contentType'].', '.
            'application: '.$data['application'].', '.
            'part: '.$data['part'].', '.
            'detail: '.$data['detail'].', '.
            'valueType: '.$data['valueType'].', '.
            'valueInt: '.$data['valueInt'].', '.
            'valueFloat: '.$data['valueFloat'].', '.
            'valueText: '.$data['valueText'].', '.
            'valueDatetime: '.$data['valueDatetime'].';';
    }
}
?>