<?php


namespace Modules\Itinerary\Controllers;




class ItineraryBaseController extends \Modules\FrontendController {

    function __construct() {
        parent::__construct();
        require_once __DIR__ . "/../Helpers/Util.php";
        app()->setLocale('ja');
    }

}
