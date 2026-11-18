<?php


namespace Modules\Itinerary\Controllers;


use Modules\AdminController;

class ItineraryAdminController extends AdminController {

    function __construct() {
        parent::__construct();
        require_once __DIR__ . "/../Helpers/Util.php";
        app()->setLocale('ja');
    }

}
