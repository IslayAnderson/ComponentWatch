<?php 

namespace reporters;

class Reporters{
    //author: islay
    //music: Sub Focus and Dimension at Printworks Weekend X
    //tea: Yorkshire
    private $example_data = '/example_data/reporters.xml';
    private $default_data = '/data_xml/reporters.xml';
    private $data_loc;
    public $data;

    public function __construct(Type $data_in = null) {
        switch($data_in){
            case "example": $this->data_loc = $this->example_data;
            case null: $this->data_loc = $this->default_data;
            default: $this->data_loc = $data_in;
        }

        $this->data = simplexml_load_file(__DIR__."/../..".$this->data_loc);
    }

    public function get_table(){
        return $this->data;
    }
}