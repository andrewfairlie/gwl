<?php 

class Platform extends CI_Model {

    function __construct()
    {
        // Call the Model constructor
        parent::__construct();
    }

    // is platform in database?
    function isPlatformInDB($GBID)
    {
        $query = $this->db->get_where('platforms', array('GBID' => $GBID));

        return $query->num_rows() > 0 ? true : false;
    }

    // add platform to database
    function addPlatform($platform)
    {
        $data = array(
           'GBID' => $platform->id,
           'Name' => $platform->name,
           'Abbreviation' => $platform->abbreviation,
           'API_Detail' => $platform->api_detail_url
        );

        return $this->db->insert('platforms', $data); 
    }

    // get platform data from Giant Bomb API
    function getPlatform($gbID)
    {
        $url = $this->config->item('gb_api_root') . "/platform/" . $gbID . "?api_key=" . $this->config->item('gb_api_key') . "&format=json";
    
        $result = $this->Utility->getData($url);

        if(is_object($result) && $result->error == "OK" && $result->number_of_total_results > 0)
        {
            return $result->results;
        } else {
            return null;
        }
    }
}