<?php 

class Reporters{
    //author: islay
    //music: Sub Focus and Dimension at Printworks Weekend X
    //tea: Yorkshire
    private $example_data = '/example_data/reporters.xml';
    private $default_data = '/data_xml/reporters.xml';
    private $data_loc;
    public $data;

    public function __construct(String $data_in = null) {
        switch($data_in){
            case "example": $this->data_loc = $this->example_data; break;
            case null: $this->data_loc = $this->default_data; break;
            default: $this->data_loc = $data_in;
        }

        $this->data = json_decode(
            json_encode(
                (array) simplexml_load_file($_SERVER["DOCUMENT_ROOT"].$this->data_loc)
            ), 1);
    }

    public function get_table(){
        return $this->data;
    }
}