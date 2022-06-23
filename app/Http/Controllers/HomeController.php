<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Illuminate\Support\Facades\Validator;
use App\Models\Common;
class HomeController extends Controller
{
     protected $Common;
    public function __construct(Common $Common)
    {
		$this->Common = $Common;
    }
	
    function index(){
        $data['meta_title'] = "Sports";
		$data['meta_keywords'] = "Sports";
		$data['meta_description'] = "Sports";
		return view('frontend/pages/homePage',$data);
    }
	
}
