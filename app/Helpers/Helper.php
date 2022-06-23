<?php

namespace App\Helpers;
use App\Models\Common;
use App\Models\Thoughtleadership;
use Session;
use Config;
use Redirect;
use DB;
class Helper
{
	/*public static function setConnection($params)
    {

        config(['connections.mysql_new' => [
            'driver' => $params['driver'],
            'host' => $params['host'],
			'database' => $params['dbname'],
            'username' => $params['username'],
            'password' => $params['password'],
			'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
        ]]);
        return DB::connection('mysql_new');
    }*/
	public static  function getUserName(){
		$data['User'] = Session::get('user');
		return $data['User']->user_name;
	}
	public static  function getUserData(){
		$data['User'] = Session::get('user');
		return $data['User'];
	}
	public static  function checkSession(){
		
		//print_r(Session::get('user')); exit;
		if (Session::has('user')){
			
			$data['User'] = Session::get('user');
			print_r($data['User']); exit;
			if(empty($data['User'])){
				return Redirect::to('/');
			}else{
				return Redirect::to('/dashboard');
			}
		  // do some thing if the key is exist
		}else{
			echo 'ddddd'; exit;
			return Redirect::to('/');
		  //the key does not exist in the session
		}
	}
   
	
    public static  function allow_permission($permission_name){
		//$permission_name = $response->permission_name;
		$data['User'] = Session::get('user');
		$RoleId = $data['User']->role_id;
		$UserId = $data['User']->id;
		$ListPermissionRole = Common::getRolePermission($RoleId);
		$ListPermissionUser = Common::getUserPermission($UserId);
		//print_r($ListPermissionUser); exit;
		$ListRole = Common::getListValue('roles', 'id', $RoleId);
		$AssociatedPermission = $ListRole[0]->all;
		if($AssociatedPermission==1){
			return 1;
		}else{
			if(count($ListPermissionUser)<=0){
				$RoleArr = array();
				foreach($ListPermissionRole as $key => $val){
					$RoleArr[]=$val->name;
				};
				if (in_array($permission_name, $RoleArr))
				  {
						//echo "Match found";
						return 1;
				  }
				else
				  {
					//echo "Match not found";
					return 0;
				  }
			}else{
				/*$RoleArr = array();
				foreach($ListPermissionRole as $key => $val){
					$RoleArr[]=$val->name;
				};*/
				
				$UserArr = array();
				foreach($ListPermissionUser as $keys => $vals){
					$UserArr[]=$vals->name;
				};
				//$result_merge = array_merge($RoleArr, $UserArr);
				//$result_unique = array_unique($result_merge);
				if (in_array($permission_name, $UserArr))
				  {
					//echo "Match found";
					return 1;
				  }
				else
				  {
					//echo "Match not found";
					return 0;
				  }
				/*echo '<pre>';
				print_r($RoleArr);
				print_r($UserArr);
				print_r($result_merge);
				print_r($result_unique);
				exit;*/
			}
		}
		
	
	}
	public static  function show_date_format($DefaultDate){
		return date('M Y', strtotime($DefaultDate));
	}
	public static  function show_upcoming_report_date($DefaultDate){
		//return date('M Y', strtotime($DefaultDate));
		return date('Y-m-d', strtotime("+2 months", strtotime($DefaultDate)));
	}
	public static  function all_category_menu($orderBy=''){
		$Categories = Common::getParentCategoryMenu(0, $orderBy);
		$categorydata = array();
        foreach ($Categories as $categoryDate) {
            $categorydata[$categoryDate->id]['name']= $categoryDate->name;
            //$categorydata[$categoryDate->id]['image']= $categoryDate->image;
            //$categorydata[$categoryDate->id]['image_path']= $categoryDate->image_path;
            $categorydata[$categoryDate->id]['slug'] = $categoryDate->slug;
            $categorydata[$categoryDate->id]['id'] = $categoryDate->id;
            $categorydata[$categoryDate->id]['single_feature_reports'] = Common::getLatestReportsBycategoryIdforMenu(1,$categoryDate->id);
            $categorydata[$categoryDate->id]['category_icon'] = $categoryDate->category_icon;
            //$categorydata[$categoryDate->id]['catdesc'] = $categoryDate->catdesc;

            $SubCategories = Common::getParentCategoryMenu($categoryDate->id);
            $count = 0;
            foreach ($SubCategories as $SubCategoriesData) {
                $thisindex = $count++;
                //$categorydata[$categoryDate->id][$thisindex]['image_path'] = $SubCategoriesData->image_path;
                $categorydata[$categoryDate->id][$thisindex]['name'] = $SubCategoriesData->name;
                //$categorydata[$categoryDate->id][$thisindex]['image'] = $SubCategoriesData->image;
                $categorydata[$categoryDate->id][$thisindex]['slug'] = $SubCategoriesData->slug;
            }
            $categorydata[$categoryDate->id]['countsubarray'] = $count;
        }
        return $categorydata;
		
	}
	public static  function getParentCategoryDetail($CatId){
		$Categories = Common::getListValue('mr_category', 'id', $CatId);
		return $Categories;
	}
	public static  function getReportSectionGraph($ReportId,$ReportSectionId){
		$WhrArr = array(
			'report_id'=>$ReportId,
			'report_section_id'=>$ReportSectionId,
		);
		$Categories = Common::getDataHelperFun('mr_report_graph', $WhrArr);
		return $Categories;
	}
	public static  function CTAbuttons(){
		$ArrCTA = array(
			'sample-enquiry-form'=>'Request A Sample',
			'request-customization-form'=>'Request for customisation',
			'download-summary-form'=>'Download Summary',
			'call-schedule'=>'Schedule a Call',
			'discount-enquiry-form'=>'Ask for Discount',
			'speak-to-analyst-form'=>'Speak to Analyst',
			'inquiry-before-buying'=>'Inquire Before Buying',
		);
		return $ArrCTA;
	}
	public static  function getCategoryLogo($name){
		$files = \File::allFiles('assets/category_logo/'.$name);
		$imagesArr = [];
		foreach ($files as $file) {
			$imagesArr[] = $file->getRelativePathname();
		}
		return $imagesArr;
	}
	public static  function getThoughtLeadershipSectionGraph($ReportId,$ReportSectionId){
		$WhrArr = array(
			'thought_leadership_id'=>$ReportId,
			'thought_leadership_section_id'=>$ReportSectionId,
		);
		$Categories = Common::getDataHelperFun('mr_thought_leadership_graph', $WhrArr);
		return $Categories;
	}
	public static  function getThoughtLeadershipSectionFetureImg($Id, $SectionType='',$limit=''){
		$WhrArr = array(
			'thought_leadership_id'=>$Id,
			'section_type'=>$SectionType,
		);
		$Categories = Thoughtleadership::getThoughtLeadershipSectionFetureImg('mr_thought_leadership_sections', $WhrArr,$limit,'section_sequence');
		return $Categories;
	}
	public static function redirectrdarray() {
		$array = array(
			'semiconductor-agv-&-mobile-robots-market'=>'semiconductor-agv-and-mobile-robots-market',
			'global-flame-retardant-cable-market-by-manufacturers-countries-type-and-application-forecast-to-2022'=>'flame-retardant-cable-market',
			'plastics-market'=>'plastic-market',
			'global-north-america-europe-and-asia-pacific-south-america-middle-east-and-africa-specialty-tire-market-2017-forecast-to-2022'=>'specialty-tire-market',
			'global-north-america-europe-and-asia-pacific-south-america-middle-east-and-africa-thermoplastic-polyester-elastomer-tpee-market-2017-forecast-to-2022'=>'thermoplastic-polyester-elastomer-tpee-market',
			'global-north-america-europe-and-asia-pacific-south-america-middle-east-and-africa-thoracic-catheters-market-2017-forecast-to-2022'=> 'thoracic-catheters-market',
			'global-north-america-europe-and-asia-pacific-south-america-middle-east-and-africa-travelers-vaccines-market-2017-forecast-to-2022'=>'travelers-vaccines-market',
			'global-matcha-tea-market-by-manufacturers-countries-type-and-application-forecast-to-2022'=>'matcha-tea-market',
			'global-fiber-optic-cable-market-by-manufacturers-countries-type-and-application-forecast-to-2022'=>'fiber-optic-cable-market',
			'global-medical-stethoscopes-market-by-manufacturers-countries-type-and-application-forecast-to-2022'=>'medical-stethoscopes-market',
			'global-bcg-vaccine-market-by-manufacturers-countries-type-and-application-forecast-to-2022'=>'bcg-vaccine-market',
			'global-botox-market-by-manufacturers-countries-type-and-application-forecast-to-2022'=>'botox-market',
			'global-electronic-expansion-valves-market-by-manufacturers-countries-type-and-application-forecast-to-2022'=>'electronic-expansion-valves-market',
			'global-low-e-glass-market-by-manufacturers-countries-type-and-application-forecast-to-2022'=>'low-e-glass-market',
			'global-luxury-vinyl-tile-lvt-market-by-manufacturers-countries-type-and-application-forecast-to-2022'=>'luxury-vinyl-tile-lvt-market',
			'global-plastic-injection-molding-machine-market-by-manufacturers-countries-type-and-application-forecast-to-2022'=>'plastic-injection-molding-machine-market',
			'global-ptfe-tapes-market-by-manufacturers-countries-type-and-application-forecast-to-2022'=>'ptfe-tapes-market',
			'global-rf-over-fiber-rfof-market-by-manufacturers-countries-type-and-application-forecast-to-2022'=>'rf-over-fiber-rfof-market',
			'global-rubidium-atomic-clock-market-by-manufacturers-countries-type-and-application-forecast-to-2022'=>'rubidium-atomic-clock-market',
			'global-vessel-traffic-services-vts-market-by-manufacturers-countries-type-and-application-forecast-to-2022'=>'vessel-traffic-services-vts-market',
			'global-fiber-optics-market-by-manufacturers-countries-type-and-application-forecast-to-2022'=>'fiber-optics-market',
			'global-solar-street-lights-market-by-manufacturers-countries-type-and-application-forecast-to-2022'=>'solar-street-lighting-market',
			'global-swim-fins-market-by-manufacturers-countries-type-and-application-forecast-to-2022'=>'swim-fins-market',
			'global-airport-charging-stations-market-by-manufacturers-countries-type-and-application-forecast-to-2022'=>'airport-charging-stations-market',
			'global-flash-point-tester-market-by-manufacturers-countries-type-and-application-forecast-to-2022'=>'flash-point-tester-market',
			'global-hot-work-die-steels-market-by-manufacturers-countries-type-and-application-forecast-to-2022'=>'hot-work-die-steels-market',
			'global-human-growth-hormone-market-by-manufacturers-countries-type-and-application-forecast-to-2022'=>'human-growth-hormone-hgh-market',
			'global-intra-aortic-balloon-pump-iabp-market-by-manufacturers-countries-type-and-application-forecast-to-2022'=>'intra-aortic-balloon-pump-iabp-market',
			'global-maldi-tof-mass-spectrometer-market-by-manufacturers-countries-type-and-application-forecast-to-2022'=>'maldi-tof-mass-spectrometer-market',
			'global-north-america-europe-and-asia-pacific-south-america-middle-east-and-africa-direct-methanol-fuel-cell-dmfc-market-2017-forecast-to-2022'=>'direct-methanol-fuel-cell-dmfc-market',
			'global-north-america-europe-and-asia-pacific-south-america-middle-east-and-africa-acoustic-doppler-current-profilers-adcp-market-2017-forecast-to-2022'=>'acoustic-doppler-current-profilers-adcp-market',
			'global-north-america-europe-and-asia-pacific-south-america-middle-east-and-africa-biological-safety-cabinet-market-2017-forecast-to-2022'=>'biological-safety-cabinet-market',
			'global-north-america-europe-and-asia-pacific-south-america-middle-east-and-africa-body-mist-market-2017-forecast-to-2022'=>'body-mist-market',
			'global-north-america-europe-and-asia-pacific-south-america-middle-east-and-africa-carbomer-market-2017-forecast-to-2022'=>'carbomer-market',
			'global-amenity-kits-market-by-manufacturers-countries-type-and-application-forecast-to-2022'=>'amenity-kits-market',
			'global-north-america-europe-and-asia-pacific-south-america-middle-east-and-africa-contemporary-height-adjustable-desk-market-2017-forecast-to-2022'=>'contemporary-height-adjustable-desk-market',
			'global-north-america-europe-and-asia-pacific-south-america-middle-east-and-africa-wallpaper-market-2017-forecast-to-2022'=>'wallpaper-market',
			'global-north-america-europe-and-asia-pacific-south-america-middle-east-and-africa-spectrometer-market-2017-forecast-to-2022'=>'spectrometer-market',
			'global-bone-harvester-market-by-manufacturers-countries-type-and-application-forecast-to-2022'=>'bone-harvester-market',
			'global-oil-water-separator-market-by-manufacturers-countries-type-and-application-forecast-to-2022'=>'oil-water-separator-market',
			'global-north-america-europe-and-asia-pacific-south-america-middle-east-and-africa-mosquito-control-market-2017-forecast-to-2022'=>'mosquito-control-market',
			'global-flash-point-tester-market-by-manufacturers-countries-type-and-application-forecast-to-2022'=>'flash-point-tester-market',
			'global-vinyl-ester-resins-market-by-manufacturers-countries-type-and-application-forecast-to-2022'=>'vinyl-ester-resins-market',
			'global-marine-scrubber-market-by-manufacturers-countries-type-and-application-forecast-to-2022'=>'marine-scrubber-market',
			'global-north-america-europe-and-asia-pacific-south-america-middle-east-and-africa-mobile-phone-insurance-ecosystem-market-2017-forecast-to-2022'=>'mobile-phone-insurance-ecosystem-market',
			'global-mram-market-by-manufacturers-countries-type-and-application-forecast-to-2022'=>'magneto-resistive-ram-random-access-memory-mram-market',
			'global-north-america-europe-and-asia-pacific-south-america-middle-east-and-africa-sprocket-market-2017-forecast-to-2022'=>'sprocket-market',
			'global-anti-caking-agents-for-fertilizer-market-by-manufacturers-countries-type-and-application-forecast-to-2022'=>'anti-caking-agents-for-fertilizer-market',
			'global-north-america-europe-and-asia-pacific-south-america-middle-east-and-africa-cmp-pad-conditioners-market-2017-forecast-to-2022'=>'cmp-pad-conditioners-market',
			'single-cylinder-diesel-engine-market'=>'china-single-cylinder-diesel-engine-market',
			'global-north-america-europe-and-asia-pacific-south-america-middle-east-and-africa-shape-memory-alloys-market-2017-forecast-to-2022'=>'shape-memory-alloys-market',
			'global-north-america-europe-and-asia-pacific-south-america-middle-east-and-africa-superhard-materials-market-2017-forecast-to-2022'=>'super-hard-materials-market',
			'global-north-america-europe-and-asia-pacific-south-america-middle-east-and-africa-two-wheeler-lighting-market-2017-forecast-to-2022'=>'two-wheeler-lighting-market',
			'global-automatic-shot-blasting-machine-market-by-manufacturers-countries-type-and-application-forecast-to-2022'=>'automatic-shot-blasting-machine-market',
			'global-ship-deck-market-research-report-2017'=>'ship-deck-machineries-market',
			'laboratory-equipment-services'=>'laboratory-equipment-services-market',
			'global-automotive-camera-module-market-by-manufacturers-countries-type-and-application-forecast-to-2022'=>'automotive-camera-module-market',
			'global-north-america-europe-and-asia-pacific-south-america-middle-east-and-africa-photo-printing-kiosk-market-2017-forecast-to-2022'=>'photo-printing-kiosk-market',
			'global-north-america-europe-and-asia-pacific-south-america-middle-east-and-africa-polarized-sunglasses-market-2017-forecast-to-2022'=>'polarized-sunglasses-market',
			'global-north-america-europe-and-asia-pacific-south-america-middle-east-and-africa-porous-filter-market-2017-forecast-to-2022'=>'porous-filter-market',
			'global-north-america-europe-and-asia-pacific-south-america-middle-east-and-africa-razor-blade-market-2017-forecast-to-2022'=>'razor-blade-market',
			'global-north-america-europe-and-asia-pacific-south-america-middle-east-and-africa-air-curtain-market-2017-forecast-to-2022'=>'air-curtain-market',
			'global-north-america-europe-and-asia-pacific-south-america-middle-east-and-africa-baby-bottles-market-2017-forecast-to-2022'=>'baby-bottles-market',
			'global-north-america-europe-and-asia-pacific-south-america-middle-east-and-africa-barcode-readers-market-2017-forecast-to-2022'=>'barcode-readers-market',
			'global-north-america-europe-and-asia-pacific-south-america-middle-east-and-africa-blood-bags-market-2017-forecast-to-2022'=>'blood-bags-market',
			'global-north-america-europe-and-asia-pacific-south-america-middle-east-and-africa-chlorhexidine-gluconate-solution-market-2017-forecast-to-2022'=>'chlorhexidine-gluconate-solution-market',
			'global-north-america-europe-and-asia-pacific-south-america-middle-east-and-africa-disposable-chemical-protective-clothing-market-2017-forecast-to-2022'=>'disposable-chemical-protective-clothing-market',
			'global-north-america-europe-and-asia-pacific-south-america-middle-east-and-africa-drawer-slides-market-2017-forecast-to-2022'=>'drawer-slides-market',
			'global-north-america-europe-and-asia-pacific-south-america-middle-east-and-africa-dripline-market-2017-forecast-to-2022'=>'dripline-market',
			'global-north-america-europe-and-asia-pacific-south-america-middle-east-and-africa-epinephrine-market-2017-forecast-to-2022'=>'epinephrine-market',
			'global-north-america-europe-and-asia-pacific-south-america-middle-east-and-africa-glucose-meter-market-2017-forecast-to-2022'=>'glucose-meter-market',
			'global-north-america-europe-and-asia-pacific-south-america-middle-east-and-africa-high-temperature-electrical-insulating-film-market-2017-forecast-to-2022'=>'high-temperature-electrical-insulating-film-market',
			'global-north-america-europe-and-asia-pacific-south-america-middle-east-and-africa-microplate-readers-market-2017-forecast-to-2022'=>'microplate-readers-market',
			'global-north-america-europe-and-asia-pacific-south-america-middle-east-and-africa-platform-screen-doors-psd-market-2017-forecast-to-2022'=>'platform-screen-doors-psd-market',
			'global-north-america-europe-and-asia-pacific-south-america-middle-east-and-africa-psa-test-market-2017-forecast-to-2022'=>'prostate-specific-antigen-psa-testing-market',
			'global-north-america-europe-and-asia-pacific-south-america-middle-east-and-africa-stand-up-paddle-board-market-2017-forecast-to-2022'=>'stand-up-paddle-board-market',
			'global-north-america-europe-and-asia-pacific-south-america-middle-east-and-africa-video-editing-software-market-2017-forecast-to-2022'=>'video-editing-software-market',
			'global-north-america-europe-and-asia-pacific-south-america-middle-east-and-africa-vitamin-k2-market-2017-forecast-to-2022'=>'vitamin-k2-market',
			'global-artificial-tears-market-by-manufacturers-countries-type-and-application-forecast-to-2022'=>'artificial-tears-market',
			'global-automotive-windshield-washer-fluid-market-by-manufacturers-countries-type-and-application-forecast-to-2022'=>'automotive-windshield-washer-fluid-market',
			'global-cut-and-stack-labels-market-by-manufacturers-countries-type-and-application-forecast-to-2022'=>'cut-and-stack-labels-market',
			'global-electronic-grade-phosphoric-acid-market-by-manufacturers-countries-type-and-application-forecast-to-2022'=>'electronic-grade-phosphoric-acid-market',
			'global-patient-temperature-management-devices-market-by-manufacturers-countries-type-and-application-forecast-to-2022'=>'patient-temperature-management-devices-market',
			'global-north-america-europe-and-asia-pacific-south-america-middle-east-and-africa-atomized-ferrosilicon-market-2017-forecast-to-2022'=>'atomized-ferrosilicon-market',
			'global-north-america-europe-and-asia-pacific-south-america-middle-east-and-africa-calcium-carbonate-from-oyster-shell-market-2017-forecast-to-2022'=>'calcium-carbonate-from-oyster-shell-market',
			'global-north-america-europe-and-asia-pacific-south-america-middle-east-and-africa-milled-ferrosilicon-market-2017-forecast-to-2022'=>'milled-ferrosilicon-market',
			'global-north-america-europe-and-asia-pacific-south-america-middle-east-and-africa-power-take-off-market-2017-forecast-to-2022'=>'power-take-off-market',
			'global-acrolein-market-by-manufacturers-countries-type-and-application-forecast-to-2022'=>'acrolein-market',
			'global-financial-leasing-market-by-manufacturers-countries-type-and-application-forecast-to-2022'=>'financial-leasing-market',
			'global-ibuprofen-market-by-manufacturers-countries-type-and-application-forecast-to-2022'=>'ibuprofen-market',
			'global-insurance-agency-software-market-by-manufacturers-countries-type-and-application-forecast-to-2022'=>'insurance-agency-software-market',
			'magnetic-field-sensors-market'=>'magnetic-field-sensor-market',
			'global-nursing-bras-market-by-manufacturers-countries-type-and-application-forecast-to-2022'=>'nursing-bras-market',
			'global-robot-battery-powered-lawn-mowers-market-by-manufacturers-countries-type-and-application-forecast-to-2022'=>'robotic-battery-powered-lawn-mowers-market',
			'global-washing-machine-market-by-manufacturers-countries-type-and-application-forecast-to-2022'=>'washing-machine-market',
			'global-diphenylamine-market-by-manufacturers-countries-type-and-application-forecast-to-2022'=>'diphenylamine-market',
			'global-fumed-silica-market-by-manufacturers-countries-type-and-application-forecast-to-2022'=>'fumed-silica-market',
			'global-glutamine-gln-market-by-manufacturers-countries-type-and-application-forecast-to-2022'=>'glutamine-gln-market',
			'transient-voltage-suppressor-tvs-market-'=>'transient-voltage-suppressor-tvs-market',
			'global-vitamin-d-testing-market-by-manufacturers-countries-type-and-application-forecast-to-2022'=>'vitamin-d-testing-market',
			'global-north-america-europe-and-asia-pacific-south-america-middle-east-and-africa-brown-sugar-market-2017-forecast-to-2022'=>'brown-sugar-market',
			'global-north-america-europe-and-asia-pacific-south-america-middle-east-and-africa-carbon-fiber-composite-heating-element-market-2017-forecast-to-2022'=>'carbon-fiber-composite-heating-element-market',
			'global-north-america-europe-and-asia-pacific-south-america-middle-east-and-africa-caustic-soda-prills-99-market-2017-forecast-to-2022'=>'caustic-soda-prills-99-market',
			'global-north-america-europe-and-asia-pacific-south-america-middle-east-and-africa-discrete-power-device-market-2017-forecast-to-2022'=>'discrete-power-device-market',
			'global-north-america-europe-and-asia-pacific-south-america-middle-east-and-africa-industrial-metal-detectors-market-2017-forecast-to-2022'=>'industrial-metal-detectors-market',
			'global-north-america-europe-and-asia-pacific-south-america-middle-east-and-africa-linear-encoders-market-2017-forecast-to-2022'=>'linear-encoders-market',
			'global-north-america-europe-and-asia-pacific-south-america-middle-east-and-africa-lithium-iodide-market-2017-forecast-to-2022'=>'lithium-iodide-market',
			'global-north-america-europe-and-asia-pacific-south-america-middle-east-and-africa-microcatheter-market-2017-forecast-to-2022'=>'microcatheter-market',
			'global-north-america-europe-and-asia-pacific-south-america-middle-east-and-africa-powered-data-buoy-market-2017-forecast-to-2022'=>'powered-data-buoy-market',
			'global-north-america-europe-and-asia-pacific-south-america-middle-east-and-africa-refrigerated-air-dryers-market-2017-forecast-to-2022'=>'refrigerated-air-dryers-market',
			'global-north-america-europe-and-asia-pacific-south-america-middle-east-and-africa-tft-lcd-photomask-market-2017-forecast-to-2022'=>'tft-lcd-photomask-market',
			'global-north-america-europe-and-asia-pacific-south-america-middle-east-and-africa-wifi-chipsets-market-2017-forecast-to-2022'=>'wifi-chipsets-market',
			'global-north-america-europe-and-asia-pacific-south-america-middle-east-and-africa-av-receiver-market-2017-forecast-to-2022'=>'av-receiver-market',
			'global-analog-timer-market-by-manufacturers-countries-type-and-application-forecast-to-2022'=>'analog-timer-market',
			'global-automotive-gear-shifter-market-by-manufacturers-countries-type-and-application-forecast-to-2022'=>'automotive-gear-shifter-market',
			'global-baby-clothing-market-by-manufacturers-countries-type-and-application-forecast-to-2022'=>'baby-clothing-market',
			'global-car-bumpers-market-by-manufacturers-countries-type-and-application-forecast-to-2022'=>'car-bumpers-market',
			'global-concrete-saw-market-by-manufacturers-countries-type-and-application-forecast-to-2022'=>'concrete-saw-market',
			'global-duty-free-retailing-market-by-manufacturers-countries-type-and-application-forecast-to-2022'=>'duty-free-retailing-market',
			'global-electric-vehicles-for-construction-agriculture-and-mining-market-by-manufacturers-countries-type-and-application-forecast-to-2022'=>'electric-vehicles-for-construction-agriculture-and-mining-market',
			'global-fireworks-market-by-manufacturers-countries-type-and-application-forecast-to-2022'=>'fireworks-market',
			'global-fishing-nets-and-aquaculture-cages-market-by-manufacturers-countries-type-and-application-forecast-to-2022'=>'fishing-nets-and-aquaculture-cages-market',
			'global-fluted-polypropylene-sheets-market-by-manufacturers-countries-type-and-application-forecast-to-2022'=>'fluted-polypropylene-sheets-market',
			'global-frozen-and-freeze-dried-pet-food-market-by-manufacturers-countries-type-and-application-forecast-to-2022'=>'frozen-and-freeze-dried-pet-food-market',
			'global-isolated-gate-drivers-market-by-manufacturers-countries-type-and-application-forecast-to-2022'=>'isolated-gate-drivers-market',
			'global-load-cell-market-by-manufacturers-countries-type-and-application-forecast-to-2022'=>'load-cell-market',
			'global-medium-voltage-switchgears-market-by-manufacturers-countries-type-and-application-forecast-to-2022'=>'medium-voltage-switchgears-market',
			'global-membrane-electrode-assemblies-market-by-manufacturers-countries-type-and-application-forecast-to-2022'=>'membrane-electrode-assemblies-market',
			'global-motorcycle-battery-market-by-manufacturers-countries-type-and-application-forecast-to-2022'=>'motorcycle-battery-market',
			'global-pallet-conveyor-market-by-manufacturers-countries-type-and-application-forecast-to-2022'=>'pallet-conveyor-market',
			'global-patient-engagement-software-market-by-manufacturers-countries-type-and-application-forecast-to-2022'=>'patient-engagement-software-market',
			'global-pharmaceutical-grade-sodium-chloride-market-by-manufacturers-countries-type-and-application-forecast-to-2022'=>'pharmaceutical-grade-sodium-chloride-market',
			'global-polymer-modified-bitumen-market-by-manufacturers-countries-type-and-application-forecast-to-2022'=>'polymer-modified-bitumen-market',
			'global-rotating-u-disk-market-by-manufacturers-countries-type-and-application-forecast-to-2022'=>'rotating-u-disk-market',
			'global-stainless-steel-sink-market-by-manufacturers-countries-type-and-application-forecast-to-2022'=>'stainless-steel-sink-market',
			'global-testosterone-replacement-therapy-market-by-manufacturers-countries-type-and-application-forecast-to-2022'=>'testosterone-replacement-therapy-market',
			'global-activated-alumina-spheres-market-by-manufacturers-countries-type-and-application-forecast-to-2022'=>'activated-alumina-spheres-market',
			'global-automotive-audio-market-by-manufacturers-countries-type-and-application-forecast-to-2022'=>'automotive-audio-market',
			'global-automotive-egr-system-market-by-manufacturers-countries-type-and-application-forecast-to-2022'=>'automotive-egr-system-market',
			'global-chromated-copper-arsenic-market-by-manufacturers-countries-type-and-application-forecast-to-2022'=>'chromated-copper-arsenic-market',
			'global-coding-and-marking-equipment-market-by-manufacturers-countries-type-and-application-forecast-to-2022'=>'coding-and-marking-equipment-market',
			'global-electrode-paste-market-by-manufacturers-countries-type-and-application-forecast-to-2022'=>'electrode-paste-market',
			'global-consumer-pressure-washers-market-by-manufacturers-countries-type-and-application-forecast-to-2022'=>'consumer-pressure-washers-market',
			'global-north-america-europe-and-asia-pacific-south-america-middle-east-and-africa-quantum-dots-display-qled-market-2017-forecast-to-2022'=>'quantum-dots-qd-display-market',
			'global-bfs-blow-fill-seal-products-market-by-manufacturers-countries-type-and-application-forecast-to-2022'=>'blow-fill-seal-bfs-products-market',
			'global-acsr-market-by-manufacturers-countries-type-and-application-forecast-to-2022'=>'aluminum-conductor-steel-reinforced-cable-acsr-market',
			'global-formwork-market-by-manufacturers-countries-type-and-application-forecast-to-2022'=>'formwork-market',
			'global-glass-mat-market-by-manufacturers-countries-type-and-application-forecast-to-2022'=>'glass-mat-market',
			'global-golf-gps-market-by-manufacturers-countries-type-and-application-forecast-to-2022'=>'golf-gps-market',
			'global-hfc-refrigerant-market-by-manufacturers-countries-type-and-application-forecast-to-2022'=>'hfc-refrigerant-market',
			'global-hydrogenated-mdi-market-by-manufacturers-countries-type-and-application-forecast-to-2022'=>'hydrogenated-mdi-market',
			'global-non-stick-pans-market-by-manufacturers-countries-type-and-application-forecast-to-2022'=>'non-stick-pans-market',
			'global-pediatric-hearing-aids-market-by-manufacturers-countries-type-and-application-forecast-to-2022'=>'pediatric-hearing-aids-market',
			'global-amebocyte-lysate-market-by-manufacturers-countries-type-and-application-forecast-to-2022'=>'amebocyte-lysate-market',
			'global-biofeedback-instrument-market-by-manufacturers-countries-type-and-application-forecast-to-2022'=>'biofeedback-instrument-market',
			'global-calibration-equipments-market-by-manufacturers-countries-type-and-application-forecast-to-2022'=>'calibration-equipments-market',
			'global-capecitabine-market-by-manufacturers-countries-type-and-application-forecast-to-2022'=>'capecitabine-market',
			'global-digital-scent-technology-market-by-manufacturers-countries-type-and-application-forecast-to-2022'=>'digital-scent-technology-market',
			'global-disposable-tableware-market-by-manufacturers-countries-type-and-application-forecast-to-2022'=>'disposable-tableware-market',
			'global-glucosamine-market-by-manufacturers-countries-type-and-application-forecast-to-2022'=>'glucosamine-market',
			'global-gypsum-board-market-by-manufacturers-regions-type-and-application-forecast-to-2022'=>'gypsum-board-market',
			'global-linbo3-crystal-market-by-manufacturers-countries-type-and-application-forecast-to-2022'=>'linbo3-crystal-market',
			'global-motor-spindles-market-by-manufacturers-countries-type-and-application-forecast-to-2022'=>'motor-spindles-market',
			'global-polymeric-membrane-for-separation-market-by-manufacturers-countries-type-and-application-forecast-to-2022'=>'polymeric-membrane-for-separation-market',
			'global-polyurethane-sealant-market-by-manufacturers-countries-type-and-application-forecast-to-2022'=>'polyurethane-sealant-market',
			'global-programmable-logic-controller-plc-market-by-manufacturers-countries-type-and-application-forecast-to-2022'=>'programmable-logic-controller-plc-market',
			'global-sodium-hypophosphite-market-by-manufacturers-countries-type-and-application-forecast-to-2022'=>'sodium-hypophosphite-market',
			'global-steel-grating-market-by-manufacturers-countries-type-and-application-forecast-to-2022'=>'steel-grating-market',
			'global-thickeners-market-by-manufacturers-countries-type-and-application-forecast-to-2022'=>'thickeners-market',
			'global-vacation-rental-software-market-by-manufacturers-countries-type-and-application-forecast-to-2022'=>'vacation-rental-software-market',
			'global-north-america-europe-and-asia-pacific-south-america-middle-east-and-africa-anti-acne-cleanser-market-2017-forecast-to-2022'=>'anti-acne-cleanser-market',
			'global-north-america-europe-and-asia-pacific-south-america-middle-east-and-africa-anti-reflective-glass-market-2017-forecast-to-2022'=>'anti-reflective-glass-market',
			'global-north-america-europe-and-asia-pacific-south-america-middle-east-and-africa-automotive-wire-market-2017-forecast-to-2022'=>'automotive-wire-market',
			'global-north-america-europe-and-asia-pacific-south-america-middle-east-and-africa-bioreactors-and-fermenters-market-2017-forecast-to-2022'=>'bioreactors-and-fermenters-market',
			'global-north-america-europe-and-asia-pacific-south-america-middle-east-and-africa-cervical-interbody-fusion-cages-market-2017-forecast-to-2022'=>'cervical-interbody-fusion-cages-market',
			'global-north-america-europe-and-asia-pacific-south-america-middle-east-and-africa-electric-utility-vehicles-market-2017-forecast-to-2022'=>'electric-utility-vehicles-market',
			'global-north-america-europe-and-asia-pacific-south-america-middle-east-and-africa-greaseproof-paper-market-2017-forecast-to-2022'=>'greaseproof-paper-market',
			'global-north-america-europe-and-asia-pacific-south-america-middle-east-and-africa-healthcare-workforce-management-system-market-2017-forecast-to-2022'=>'healthcare-workforce-management-system-market',
			'global-north-america-europe-and-asia-pacific-south-america-middle-east-and-africa-ingaas-image-sensors-market-2017-forecast-to-2022'=>'ingaas-image-sensors-market',
			'global-north-america-europe-and-asia-pacific-south-america-middle-east-and-africa-laser-capture-microdissection-market-2017-forecast-to-2022'=>'laser-capture-microdissection-market',
			'global-north-america-europe-and-asia-pacific-south-america-middle-east-and-africa-soft-ferrite-core-market-2017-forecast-to-2022'=>'soft-ferrite-core-market',
			'global-wire-wedge-bonder-equipment-market-2017-forecast-to-2022'=>'wire-wedge-bonder-equipment-market',
			'global-die-bonder-equipment-market-by-manufacturers-countries-type-and-application-forecast-to-2022'=>'die-bonder-equipment-market',
			'global-fabric-softener-market-by-manufacturers-countries-type-and-application-forecast-to-2022'=>'fabric-softener-market',
			'global-karl-fischer-titrators-market-by-manufacturers-countries-type-and-application-forecast-to-2022'=>'karl-fischer-titrators-market',
			'global-earth-leakage-protection-market-by-manufacturers-countries-type-and-application-forecast-to-2022'=>'earth-leakage-protection-market',
			'global-spices-and-seasonings-market-by-manufacturers-countries-type-and-application-forecast-to-2022'=>'spices-and-seasonings-market',
			'global-superalloy-market-by-manufacturers-countries-type-and-application-forecast-to-2022'=>'superalloys-market',
			'global-dj-equipment-market-by-manufacturers-countries-type-and-application-forecast-to-2022'=>'dj-equipment-market',
			'global-interior-glass-market-by-manufacturers-countries-type-and-application-forecast-to-2022'=>'interior-glass-market',
			'global-sim-card-market-by-manufacturers-countries-type-and-application-forecast-to-2022'=>'sim-card-market',
			'global-ticket-printers-market-by-manufacturers-countries-type-and-application-forecast-to-2022'=>'ticket-printers-market',
			'global-3d-time-of-flight-image-sensor-market-2017-forecast-to-2022'=>'3d-time-of-flight-image-sensor-market',
			'global-automotive-adhesive-tapes-market-2017-forecast-to-2022'=>'automotive-adhesive-tapes-market',
			'global-automotive-steering-wheel-switch-market-2017-forecast-to-2022'=>'automotive-steering-wheel-switch-market',
			'global-centerless-grinding-market-2017-forecast-to-2022'=>'centerless-grinding-market',
			'global-cesium-iodide-market-2017-forecast-to-2022'=>'cesium-iodide-market',
			'global-enteric-empty-capsules-market-2017-forecast-to-2022'=>'enteric-empty-capsules-market',
			'global-facial-mask-market-2017-forecast-to-2022'=>'facial-mask-market',
			'global-latex-powder-market-2017-forecast-to-2022'=>'latex-powder-market',
			'global-luxury-bag-market-2017-forecast-to-2022'=>'luxury-bag-market',
			'global-reciprocating-hermetic-compressors-market-2017-forecast-to-2022'=>'reciprocating-hermetic-compressors-market',
			'global-vegetable-hpmc-capsule-market-2017-forecast-to-2022'=>'vegetable-hpmc-capsule-market',
			'global-eas-systems-market-by-manufacturers-countries-type-and-application-forecast-to-2022'=>'eas-systems-market',
			'global-instant-coffee-market-by-manufacturers-countries-type-and-application-forecast-to-2022'=>'instant-coffee-market',
			'global-rubber-market-by-manufacturers-countries-type-and-application-forecast-to-2022'=>'rubber-market',
			'global-5-hydroxymethylfurfural-cas-67-47-0-market-2017-forecast-to-2022'=>'5-hydroxymethylfurfural-5-hmf-market',
			'global-active-toughening-agent-for-epoxy-resin-market-2017-forecast-to-2022'=>'active-toughening-agent-for-epoxy-resin-market',
			'global-assistive-technologies-for-visual-impairment-market-2017-forecast-to-2022'=>'assistive-technologies-for-visual-impaired-market',
			'global-automotive-labels-market-2017-forecast-to-2022'=>'automotive-labels-market',
			'global-automotive-sunroof-market-2017-forecast-to-2022'=>'automotive-sunroof-market',
			'global-chain-block-market-2017-forecast-to-2022'=>'chain-block-market',
			'global-climate-test-chamber-market-2017-forecast-to-2022'=>'environmental-test-chambers-market',
			'global-cto-distillation-market-2017-forecast-to-2022'=>'cto-distillation-market',
			'global-cyclohexane-dimethanol-chdm-market-2017-forecast-to-2022'=>'cyclohexane-dimethanol-chdm-market',
			'global-dextran-market-2017-forecast-to-2022'=>'dextran-market',
			'global-electric-heating-cable-market-2017-forecast-to-2022'=>'electric-heating-cable-market',
			'global-esr-analyzers-market-2017-forecast-to-2022'=>'erythrocyte-sedimentation-rate-esr-analyzers-market',
			'global-harmonic-drive-market-2017-forecast-to-2022'=>'harmonic-drive-market',
			'global-heat-cost-allocator-market-2017-forecast-to-2022'=>'heat-cost-allocator-market',
			'global-high-purity-aluminum-market-2017-forecast-to-2022'=>'high-purity-aluminum-market',
			'global-home-appliance-market-2017-forecast-to-2022'=>'home-appliances-market',
			'global-mannequins-market-2017-forecast-to-2022'=>'mannequins-market',
			'global-organic-dairy-products-market-2017-forecast-to-2022'=>'organic-dairy-products-market',
			'global-plga-market-2017-forecast-to-2022'=>'poly-lactic-co-glycolic-acid-plga-market',
			'global-ptfe-ccl-market-2017-forecast-to-2022'=>'ptfe-ccl-market',
			'global-riflescope-market-2017-forecast-to-2022'=>'riflescopes-market',
			'global-sausage-hotdog-casings-market-2017-forecast-to-2022'=>'sausage-hotdog-casings-market',
			'global-naphazoline-hydrochloridechlorphenamine-maleate-and-vitamin-b12-eye-drops-market-research-report-2017'=>'naphazoline-hydrochloride-chlorphenamine-maleate-and-vitamin-b12-eye-drops-market',
			'global-needle-free-injection-systems-market-research-report-2017'=>'needle-free-injection-systems-market',
			'global-network-security-cameras-market-research-report-2017'=>'network-security-cameras-market',
			'global-non-corticosteroid-anti-inflammatory-eyedrops-market-research-report-2017'=>'non-corticosteroid-anti-inflammatory-eyedrops-market',
			'global-ofloxacin-eye-drops-market-research-report-2017'=>'ofloxacin-eye-drops-market',
			'global-one-component-polyurethane-foam-market-research-report-2017'=>'one-component-polyurethane-foam-market',
			'global-optical-communication-and-networking-market-research-report-2017'=>'optical-communication-and-networking-market',
			'global-outdoor-camping-cooler-box-market-research-report-2017'=>'outdoor-camping-cooler-box-market',
			'global-paraformaldehyde-pfa-cas-30525-89-4-market-research-report-2017'=>'paraformaldehyde-pfa-market',
			'global-popcorn-popper-market-research-report-2017'=>'popcorn-popper-market',
			'port-redirector-market-'=>'port-redirector-market',
			'power-discrete-device-market-research-report-2017'=>'power-discrete-devices-market',
			'audiophile-headphone-sales-market'=>'earphones-and-headphones-market',
			'automated-passenger-counting-system-sales-market'=>'automated-passenger-counting-system-market',
			'automatic-lensmeter-sales-market'=>'automatic-lensmeter-market',
			'axial-leads-multilayer-ceramic-capacitors-sales-market'=>'axial-leads-multilayer-ceramic-capacitors-market',
			'global-cloud-service-brokerage-market'=>'cloud-services-brokerage-csb-market',
			'global-managed-security-services-market'=>'managed-security-services-market',
			'global-risk-management-market'=>'risk-management-market',
			'global-power-rental-market'=>'power-rental-market',
			'global-automotive-led-driver-market'=>'automotive-led-driver-market',
			'global-car-avn-audio-video-navigation-or-infotainment-system-or-in-car-entertainment-market'=>'car-avn-audio-video-navigation-or-infotainment-system-or-in-car-entertainment-market',
			'global-remote-starter-market'=>'remote-starter-market',
			'global-automotive-ultra-capacitor-market'=>'automotive-ultra-capacitor-market',
			'global-automotive-abs-market'=>'automotive-anti-lock-braking-abs-market',
			'global-power-discrete-module-market-research-report-2017'=>'power-discrete-module-market',
			'global-pvdc-resins-and-pvdc-latex-market-research-report-2017'=>'pvdc-resins-and-pvdc-latex-market',
			'global-residential-fully-automatic-washing-machine-market-research-report-2017'=>'residential-fully-automatic-washing-machine-market',
			'global-sic-and-gan-power-devices-market-research-report-2017'=>'sic-and-gan-power-device-market',
			'global-smartphones-camera-lenses-market-research-report-2017'=>'smartphones-camera-lenses-market',
			'global-sodium-hyaluronate-eye-drops-market-research-report-2017'=>'sodium-hyaluronate-eye-drops-market',
			'global-solar-sunlight-control-system-market-research-report-2017'=>'solar-sunlight-control-system-market',
			'global-special-and-extruded-graphite-market-research-report-2017'=>'special-and-extruded-graphite-market',
			'global-starter-solenoid-market-research-report-2017'=>'starter-solenoid-market',
			'global-sugar-excipients-market-research-report-2017'=>'sugar-excipients-market',
			'global-surface-mounted-fan-coil-market-research-report-2017'=>'surface-mounted-fan-coil-market',
			'global-synthetic-fibre-rope-market-research-report-2017'=>'synthetic-fibre-rope-market',
			'global-tyre-mixer-market-research-report-2017'=>'tyre-mixer-market',
			'global-urinary-stone-treatment-device-market-research-report-2017'=>'urinary-stone-treatment-devices-market',
			'global-usb-to-serial-converter-market-research-report-2017'=>'usb-to-serial-converter-market',
			'global-vanadium-redox-flow-battery-vrb-market-research-report-2017'=>'vanadium-redox-flow-battery-vrfb-market',
			'global-vertical-fan-coil-market-research-report-2017'=>'vertical-fan-coils-market',
			'global-vitamin-b4-market-research-report-2017'=>'vitamin-b4-market',
			'global-vitamin-pp-niacin-and-niacinamide-market-research-report-2017'=>'vitamin-pp-niacin-and-niacinamide-market',
			'global-wall-mounted-fan-coil-market-research-report-2017'=>'wall-mounted-fan-coil-market',
			'global-wbg-power-devices-market-research-report-2017'=>'wbg-power-devices-market',
			'global-x-ray-fluorescene-coating-thichness-gauge-market-research-report-2017'=>'x-ray-fluorescene-coating-thichness-gauge-market',
			'global-access-control-solutions-market-size-status-and-forecast-2022'=>'access-control-solutions-market',
			'global-ambulatory-software-market-size-status-and-forecast-2022'=>'ambulatory-software-market',
			'global-anti-fraud-management-system-market-size-status-and-forecast-2022'=>'anti-fraud-management-system-market',
			'global-application-modernization-services-market-size-status-and-forecast-2022'=>'application-modernization-services-market',
			'global-banking-and-financial-smart-cards-market-size-status-and-forecast-2022'=>'banking-and-financial-smart-cards-market',
			'global-blood-and-organ-bank-market-size-status-and-forecast-2022'=>'blood-and-organ-bank-market',
			'global-cerebral-thrombectomy-systems-market-size-status-and-forecast-2022'=>'cerebral-thrombectomy-systems-market',
			'global-clinical-trial-management-system-ctms-market-size-status-and-forecast-2022'=>'clinical-trial-management-system-ctms-market',
			'global-compensation-software-market-size-status-and-forecast-2022'=>'compensation-software-market',
			'global-courier-management-software-market-size-status-and-forecast-2022'=>'courier-management-software-market',
			'global-smart-oilfield-market-size-status-and-forecast-2022'=>'smart-oilfield-market',
			'global-integrated-building-management-systems-market-size-status-and-forecast-2022'=>'integrated-building-management-systems-market',
			'global-iris-recognition-in-access-control-market-size-status-and-forecast-2022'=>'iris-recognition-in-access-control-market',
			'global-professional-services-automation-market-size-status-and-forecast-2022'=>'professional-services-automation-market',
			'global-safety-pre-filled-syringe-system-market-size-status-and-forecast-2022'=>'safety-prefilled-syringe-system-market',
			'global-telemedicine-devices-and-software-market-size-status-and-forecast-2022'=>'telemedicine-devices-and-software-market',
			'global-test-data-management-market-size-status-and-forecast-2022'=>'test-data-management-market',
			'global-transit-cards-market-size-status-and-forecast-2022'=>'transit-cards-market',
			'global-workplace-managed-services-market-size-status-and-forecast-2022'=>'workplace-managed-services-market',
			'global-air-conditioning-electronic-expansion-valves-eev-market-research-report-2017'=>'air-conditioning-electronic-expansion-valves-eev-market',
			'global-anti-glaucoma-eyedrops-market-research-report-2017'=>'anti-glaucoma-eyedrops-market',
			'global-auto-weatherstripping-market-research-report-2017'=>'auto-weatherstripping-market',
			'global-bicycle-lighting-equipment-market-research-report-2017'=>'bicycle-lighting-equipment-market',
			'global-bucket-mining-shovel-market-research-report-2017'=>'bucket-mining-shovels-market',
			'global-camping-cooler-box-market-research-report-2017'=>'camping-cooler-box-market',
			'global-cassette-air-conditioner-market-research-report-2017'=>'cassette-air-conditioner-market',
			'global-chilled-products-transport-market-research-report-2017'=>'chilled-products-transport-market',
			'global-chlorine-compressors-market-research-report-2017'=>'chlorine-compressors-market',
			'global-citral-products-market-research-report-2017'=>'citral-products-market',
			'global-conceal-install-fan-coil-market-research-report-2017'=>'conceal-install-fan-coil-market',
			'global-contemporary-light-column-market-research-report-2017'=>'contemporary-light-column-market',
			'global-corrosion-resistant-alloys-market-research-report-2017'=>'corrosion-resistant-alloys-market',
			'global-diesel-electric-hybrid-mining-drills-market-research-report-2017'=>'diesel-electric-hybrid-mining-drills-market',
			'global-diesel-electric-hybrid-mining-shovel-market-research-report-2017'=>'diesel-electric-hybrid-mining-shovel-market',
			'global-diesel-electric-hybrid-motor-graders-market-research-report-2017'=>'diesel-electric-hybrid-motor-graders-market',
			'global-diesel-mechanical-mining-shovel-market-research-report-2017'=>'diesel-mechanical-mining-shovel-market',
			'global-digital-notepad-market-research-report-2017'=>'digital-notepad-market',
			'global-dragline-mining-shovel-market-research-report-2017'=>'dragline-mining-shovel-market',
			'global-drainage-bottle-market-research-report-2017'=>'drainage-bottle-market',
			'global-drug-blister-packaging-market-research-report-2017'=>'drug-blister-packaging-market',
			'global-e-liquids-market-research-report-2017'=>'e-liquid-market',
			'global-electric-and-acoustic-guitar-strings-market-research-report-2017'=>'electric-and-acoustic-guitar-strings-market',
			'global-endoscopy-and-laparoscopy-light-source-market-research-report-2017'=>'endoscopy-and-laparoscopy-light-source-market',
			'global-eyedrops-for-cataract-market-research-report-2017'=>'eyedrops-for-cataract-market',
			'global-fips-market-research-report-2017'=>'full-ice-protection-system-fips-market',
			'global-floor-mounted-air-conditioner-market-research-report-2017'=>'floor-mounted-air-conditioner-market',
			'global-floor-mounted-fan-coil-market-research-report-2017'=>'floor-mounted-fan-coil-market',
			'global-freeze-dried-vegetables-market-research-report-2017'=>'freeze-dried-vegetables-market',
			'global-gan-power-semiconductor-devices-market-research-report-2017'=>'gan-power-semiconductor-devices-market',
			'global-gcc-fire-extinguishers-market-research-report-2017'=>'gcc-fire-extinguishers-market',
			'global-glycosylated-hemoglobin-and-c-peptide-market-research-report-2017'=>'glycosylated-hemoglobin-and-c-peptide-market',
			'global-hifi-audio-products-market-research-report-2017'=>'hifi-audio-products-market',
			'global-high-performance-adhesives-market-research-report-2017'=>'high-performance-adhesives-market',
			'global-horizontal-fan-coil-market-research-report-2017'=>'horizontal-fan-coil-market',
			'global-hydraulic-mining-shovel-market-research-report-2017'=>'hydraulic-mining-shovels-market',
			'global-industrial-process-recorders-market-research-report-2017'=>'industrial-process-recorders-market',
			'global-infant-radiant-warmer-market-research-report-2017'=>'infant-radiant-warmer-market',
			'global-injection-stretch-blow-molding-machines-market-research-report-2017'=>'injection-stretch-blow-molding-machines-market',
			'global-lactate-norfloxacin-market-research-report-2017'=>'lactate-norfloxacin-market',
			'global-large-scale-reed-switch-market-research-report-2017'=>'large-scale-reed-switch-market',
			'global-laser-fiber-in-medical-market-research-report-2017'=>'laser-fiber-in-medical-market',
			'global-ligases-enzymes-market-research-report-2017'=>'ligases-enzyme-market',
			'global-light-duty-rollator-market-research-report-2017'=>'light-duty-rollator-market',
			'global-light-sources-for-endoscopy-market-research-report-2017'=>'light-sources-for-endoscopy-market',
			'global-lingual-braces-market-research-report-2017'=>'lingual-braces-market',
			'global-lithium-ion-battery-electrolyte-market-research-report-2017'=>'lithium-ion-battery-electrolyte-market',
			'global-load-transducers-market-research-report-2017'=>'load-transducers-market',
			'global-mainframes-market-research-report-2017'=>'mainframes-market',
			'global-manufacturing-industry-freezer-market-research-report-2017'=>'manufacturing-industry-freezer-market',
			'global-mdi-prepolymers-market-research-report-2017'=>'mdi-prepolymers-market',
			'global-miniature-circuit-breaker-market-research-report-2017'=>'miniature-circuit-breaker-market',
			'global-mobile-energy-storage-system-market-research-report-2017'=>'mobile-energy-storage-system-market',
			'global-metal-hoses-market-research-report-2017'=>'metal-hoses-market',
			'global-temperate-box-market'=>'temperate-box-market',
			'baby-diaper-machine-sales-market'=>'baby-diaper-machine-market',
			'cmp-slurry-sales-market'=>'chemical-mechanical-planarization-cmp-slurry-market',
			'detonation-synthesis-nanodiamond-powder-industry-2017-market'=>'detonation-synthesis-nanodiamond-powder-market',
			'global-non-metallic-gasket-market'=>'non-metallic-gasket-market',
			'global-water-hammer-arrestor-market'=>'water-hammer-arrestor-market',
			'global-electrical-explosion-proof-equipments-market'=>'electrical-explosion-proof-equipments-market',
			'infrared-filters-sales-market'=>'infrared-filters-market',
			'wall-charger-industry-market'=>'wall-charger-market',
			'global-wireless-router-market'=>'wireless-router-market',
			'online-jewelry-retail-report-on-global-and-dec-2017-market-status-and-forecast-by-players-types-and-applications-2017-2022-market'=>'online-jewelry-market',
			'global-3d-computer-graphics-software-sales-market'=>'3d-computer-graphics-software-market',
			'global-3d-nand-flash-memory-market'=>'3d-nand-flash-memory-market',
			'global-4g-lte-devices-market'=>'4g-lte-devices-market',
			'4k2k-tv-sales-market-report-2017'=>'4k2k-tv-market',
			'active-protection-system-aps-sales-market'=>'active-protection-system-market',
			'actuator-sensor-interface-as-interface-sales-market'=>'actuator-sensor-as-interface-market',
			'adsl-chipsets-sales-market'=>'adsl-chipsets-market',
			'aircraft-computers-sales-market'=>'aircraft-computers-market',
			'analog-linear-and-mixed-signal-devices-sales-market'=>'analog-linear-and-mixed-signal-devices-market',
			'analog-timer-sales-market'=>'digital-and-analog-timer-market',
			'angular-sensors-sales-market'=>'angular-sensors-market',
			'apd-avalanche-photodiode-sales-market'=>'avalanche-photodiode-apd-market',
			'audiophile-headphone-sales-market'=>'earphones-and-headphones-market',
			'global-aluminum-cookware-market-by-manufacturers-countries-type-and-application-forecast-to-2022'=>'aluminum-cookware-market',
			'global-north-america-europe-and-asia-pacific-south-america-middle-east-and-africa-carbon-monoxide-alarms-market-2017-forecast-to-2022'=>'carbon-monoxide-alarms-market',
			'global-north-america-europe-and-asia-pacific-south-america-middle-east-and-africa-chemical-polishing-slurry-market-2017-forecast-to-2022'=>'chemical-polishing-slurry-market',
			'global-north-america-europe-and-asia-pacific-south-america-middle-east-and-africa-commercial-microwave-ovens-market-2017-forecast-to-2022'=>'commercial-microwave-ovens-market',
			'global-north-america-europe-and-asia-pacific-south-america-middle-east-and-africa-fibrinogen-concentrate-market-2017-forecast-to-2022'=>'fibrinogen-concentrate-market',
			'global-north-america-europe-and-asia-pacific-south-america-middle-east-and-africa-food-for-special-medical-purpose-fsmp-market-2017-forecast-to-2022'=>'food-for-special-medical-purpose-fsmp-market',
			'global-north-america-europe-and-asia-pacific-south-america-middle-east-and-africa-interposer-market-2017-forecast-to-2022'=>'interposer-market',
			'global-north-america-europe-and-asia-pacific-south-america-middle-east-and-africa-mechanical-and-electronic-fuzes-market-2017-forecast-to-2022'=>'mechanical-and-electronic-fuzes-market',
			'global-north-america-europe-and-asia-pacific-south-america-middle-east-and-africa-medical-refrigerator-market-2017-forecast-to-2022'=>'medical-refrigerators-market',
			'global-north-america-europe-and-asia-pacific-south-america-middle-east-and-africa-wind-turbine-pitch-systems-market-2017-forecast-to-2022'=>'wind-turbine-pitch-systems-market',
			'global-north-america-europe-and-asia-pacific-south-america-middle-east-and-africa-accelerated-solvent-extraction-ase-market-2017-forecast-to-2022'=>'accelerated-solvent-extraction-ase-market',
			'global-north-america-europe-and-asia-pacific-south-america-middle-east-and-africa-ammonium-nitrate-explosive-market-2017-forecast-to-2022'=>'ammonium-nitrate-explosive-market',
			'global-north-america-europe-and-asia-pacific-south-america-middle-east-and-africa-automotive-cyber-security-market-2017-forecast-to-2022'=>'automotive-cyber-security-market',
			'global-north-america-europe-and-asia-pacific-south-america-middle-east-and-africa-automotive-evp-electric-vacuum-pump-market-2017-forecast-to-2022'=>'automotive-electric-vacuum-pump-evp-market',
			'global-north-america-europe-and-asia-pacific-south-america-middle-east-and-africa-ethyl-3-ethoxypropionate-market-2017-forecast-to-2022'=>'ethyl-3-ethoxypropionate-market',
			'global-north-america-europe-and-asia-pacific-south-america-middle-east-and-africa-floor-panel-market-2017-forecast-to-2022'=>'floor-panel-market',
			'global-north-america-europe-and-asia-pacific-south-america-middle-east-and-africa-led-lighting-driver-market-2017-forecast-to-2022'=>'led-lighting-driver-market',
			'global-north-america-europe-and-asia-pacific-south-america-middle-east-and-africa-m-xylylenediamine-market-2017-forecast-to-2022'=>'m-xylylenediamine-market',
			'global-north-america-europe-and-asia-pacific-south-america-middle-east-and-africa-massage-chair-market-2017-forecast-to-2022'=>'massage-chair-market',
			'global-north-america-europe-and-asia-pacific-south-america-middle-east-and-africa-obd-telematics-market-2017-forecast-to-2022'=>'obd-telematics-market',
			'global-north-america-europe-and-asia-pacific-south-america-middle-east-and-africa-pet-insurance-market-2017-forecast-to-2022'=>'pet-insurance-market',
			'global-north-america-europe-and-asia-pacific-south-america-middle-east-and-africa-veterinary-diagnostic-imaging-market-2017-forecast-to-2022'=>'veterinary-diagnostic-imaging-market',
			'global-north-america-europe-and-asia-pacific-south-america-middle-east-and-africa-circular-saw-blade-market-2017-forecast-to-2022'=>'circular-saw-blade-market',
			'global-automobile-engine-valve-market-by-manufacturers-countries-type-and-application-forecast-to-2022'=>'automobile-engine-valves-market',
			'global-bismaleimide-bmi-resins-market-by-manufacturers-countries-type-and-application-forecast-to-2022'=>'bismaleimide-bmi-resins-market',
			'global-cardiac-care-medical-equipment-market-by-manufacturers-countries-type-and-application-forecast-to-2022'=>'cardiac-care-medical-equipment-market',
			'global-composite-insulated-panels-market-by-manufacturers-countries-type-and-application-forecast-to-2022'=>'composite-insulated-panels-market',
			'global-construction-estimating-software-market-by-manufacturers-countries-type-and-application-forecast-to-2022'=>'construction-estimating-software-market',
			'global-hospital-furniture-market-by-manufacturers-countries-type-and-application-forecast-to-2022'=>'hospital-furniture-market',
			'global-poly-aluminium-chloride-pac-market-by-manufacturers-countries-type-and-application-forecast-to-2022'=>'poly-aluminium-chloride-pac-market',
			'global-chemotherapy-devices-market-by-manufacturers-countries-type-and-application-forecast-to-2022'=>'chemotherapy-devices-market',
			'global-n-bromosuccinimide-market-by-manufacturers-countries-type-and-application-forecast-to-2022'=>'n-bromosuccinimide-market',
			'global-surface-disinfectant-market-by-manufacturers-countries-type-and-application-forecast-to-2022'=>'surface-disinfectant-market',
			'global-targeted-rna-sequencing-market-by-manufacturers-countries-type-and-application-forecast-to-2022'=>'targeted-rna-sequencing-market',
			'global-north-america-europe-and-asia-pacific-south-america-middle-east-and-africa-butyl-rubber-market-2017-forecast-to-2022'=>'butyl-rubber-market',
			'global-north-america-europe-and-asia-pacific-south-america-middle-east-and-africa-ferrous-slag-market-2017-forecast-to-2022'=>'ferrous-slag-market',
			'global-north-america-europe-and-asia-pacific-south-america-middle-east-and-africa-french-door-refrigerators-market-2017-forecast-to-2022'=>'french-door-refrigerators-market',
			'global-north-america-europe-and-asia-pacific-south-america-middle-east-and-africa-hydrochloric-acid-market-2017-forecast-to-2022'=>'hydrochloric-acid-market',
			'global-north-america-europe-and-asia-pacific-south-america-middle-east-and-africa-injection-molded-plastics-market-2017-forecast-to-2022'=>'injection-molded-plastics-market',
			'global-north-america-europe-and-asia-pacific-south-america-middle-east-and-africa-large-circular-knitting-machine-market-2017-forecast-to-2022'=>'large-circular-knitting-machine-market',
			'global-north-america-europe-and-asia-pacific-south-america-middle-east-and-africa-muconic-acid-market-2017-forecast-to-2022'=>'muconic-acid-market',
			'global-north-america-europe-and-asia-pacific-south-america-middle-east-and-africa-sealing-gasket-market-2017-forecast-to-2022'=>'sealing-gasket-market',
			'global-north-america-europe-and-asia-pacific-south-america-middle-east-and-africa-sphygmomanometers-market-2017-forecast-to-2022'=>'sphygmomanometers-market',
			'global-north-america-europe-and-asia-pacific-south-america-middle-east-and-africa-telematics-control-unit-tcu-market-2017-forecast-to-2022'=>'telematics-control-unit-tcu-market',
			'global-north-america-europe-and-asia-pacific-south-america-middle-east-and-africa-viscose-fiber-market-2017-forecast-to-2022'=>'viscose-fiber-market',
			'global-north-america-europe-and-asia-pacific-south-america-middle-east-and-africa-pm25-monitors-market-2017-forecast-to-2022'=>'pm25-monitors-market',
			'global-north-america-europe-and-asia-pacific-south-america-middle-east-and-africa-sodium-sulfate-market-2017-forecast-to-2022'=>'sodium-sulfate-market',
			'global-appearance-boards-market-by-manufacturers-countries-type-and-application-forecast-to-2022'=>'appearance-boards-market',
			'global-bioresorbable-medical-material-market-by-manufacturers-countries-type-and-application-forecast-to-2022'=>'bioresorbable-medical-material-market',
			'global-chemotherapy-infusion-pumps-market-by-manufacturers-countries-type-and-application-forecast-to-2022'=>'chemotherapy-infusion-pumps-market',
			'global-flexographic-printing-machine-market-by-manufacturers-countries-type-and-application-forecast-to-2022'=>'flexographic-printing-machine-market',
			'global-game-engines-market-by-manufacturers-countries-type-and-application-forecast-to-2022'=>'game-engines-market',
			'global-pressure-vessels-market-by-manufacturers-countries-type-and-application-forecast-to-2022'=>'pressure-vessels-market',
			'global-soy-protein-market-by-manufacturers-countries-type-and-application-forecast-to-2022'=>'soy-protein-market',
			'global-thermal-printhead-market-by-manufacturers-countries-type-and-application-forecast-to-2022'=>'thermal-printhead-market',
			'global-architectural-membrane-market-by-manufacturers-countries-type-and-application-forecast-to-2022'=>'architectural-membrane-market',
			'global-data-center-rack-market-by-manufacturers-countries-type-and-application-forecast-to-2022'=>'data-center-rack-market',
			'global-fresh-sea-food-packaging-market-by-manufacturers-countries-type-and-application-forecast-to-2022'=>'fresh-seafood-packaging-market',
			'global-radio-modem-market-by-manufacturers-countries-type-and-application-forecast-to-2022'=>'radio-modem-market',
			'global-specialty-fats-market-by-manufacturers-countries-type-and-application-forecast-to-2022'=>'specialty-fats-market',
			'global-dairy-packaging-market-by-manufacturers-countries-type-and-application-forecast-to-2022'=>'dairy-packaging-market',
			'global-geothermal-power-generation-market-by-manufacturers-countries-type-and-application-forecast-to-2022'=>'geothermal-power-generation-market',
			'global-industrial-gas-regulator-market-by-manufacturers-countries-type-and-application-forecast-to-2022'=>'industrial-gas-regulators-market',
			'global-north-america-europe-and-asia-pacific-south-america-middle-east-and-africa-digital-radiography-market-2017-forecast-to-2022'=>'digital-radiography-market',
			'global-north-america-europe-and-asia-pacific-south-america-middle-east-and-africa-dna-microarray-market-2017-forecast-to-2022'=>'dna-microarray-market',
			'global-north-america-europe-and-asia-pacific-south-america-middle-east-and-africa-insulating-adhesive-tape-market-2017-forecast-to-2022'=>'insulating-adhesive-tape-market',
			'global-north-america-europe-and-asia-pacific-south-america-middle-east-and-africa-polyferric-sulfate-market-2017-forecast-to-2022'=>'polyferric-sulfate-market',
			'global-north-america-europe-and-asia-pacific-south-america-middle-east-and-africa-solar-encapsulation-materials-market-2017-forecast-to-2022'=>'solar-encapsulation-materials-market',
			'global-vaginal-pessary-market-2017-forecast-to-2022'=>'vaginal-pessary-market',
			'global-desalination-pumps-market-by-manufacturers-countries-type-and-application-forecast-to-2022'=>'desalination-pumps-market',
			'global-industrial-food-cutting-machines-market-by-manufacturers-countries-type-and-application-forecast-to-2022'=>'industrial-food-cutting-machines-market',
			'global-water-filters-market-by-manufacturers-countries-type-and-application-forecast-to-2022'=>'water-filters-market',
			'global-laminate-flooring-market-by-manufacturers-countries-type-and-application-forecast-to-2022'=>'laminate-flooring-market',
			'global-phenylketonuria-market-by-manufacturers-countries-type-and-application-forecast-to-2022'=>'phenylketonuria-market',
			'global-acrylic-rubber-market-2017-forecast-to-2022'=>'acrylic-rubber-market',
			'global-bone-allograft-and-xenograft-market-2017-forecast-to-2022'=>'bone-allograft-and-xenograft-market',
			'global-calcium-magnesium-carbonate-market-2017-forecast-to-2022'=>'calcium-magnesium-carbonate-market',
			'global-fiber-cement-board-market-2017-forecast-to-2022'=>'fiber-cement-board-market',
			'global-fluorine-aromatic-pi-film-market-2017-forecast-to-2022'=>'fluorine-aromatic-pi-film-market',
			'global-heavy-duty-conveyor-belts-market-2017-forecast-to-2022'=>'heavy-duty-conveyor-belts-market',
			'global-inorganic-zinc-chemicals-market-2017-forecast-to-2022'=>'inorganic-zinc-chemicals-market',
			'global-licorice-extract-market-2017-forecast-to-2022'=>'licorice-extract-market',
			'global-medical-alert-systems-market-2017-forecast-to-2022'=>'medical-alert-systems-market',
			'global-pentaerythritol-market-2017-forecast-to-2022 '=>'pentaerythritol-market',
			'global-screen-protective-film-market-2017-forecast-to-2022'=>'screen-protective-film-market',
			'global-tomato-seeds-market-2017-forecast-to-2022'=>'tomato-seeds-market',
			'global-wound-care-biologics-market-2017-forecast-to-2022'=>'wound-care-biologics-market',
			'global-3d-printing-and-additive-market-2017-forecast-to-2022'=>'3d-printing-additive-manufacturing-market',
			'global-aluminum-plate-market-2017-forecast-to-2022'=>'aluminum-plate-market',
			'global-aroma-chemicals-market-2017-forecast-to-2022'=>'aroma-chemicals-market',
			'global-boehmite-market-2017-forecast-to-2022'=>'boehmite-market',
			'global-caustic-soda-market-2017-forecast-to-2022'=>'caustic-soda-market',
			'global-intra-oral-scanners-for-digital-impression-market-2017-forecast-to-2022'=>'intra-oral-scanners-for-digital-impression-market',
			'global-logistics-robots-market-2017-forecast-to-2022'=>'logistics-robots-market',
			'global-precision-bearings-market-research-report-2017'=>'precision-bearings-market',
			'global-radar-simulator-market-research-report-2017'=>'radar-simulator-market',
			'global-refractories-market-research-report-2017'=>'refractories-market',
			'global-riboflavin-vitamin-b2-market-research-report-2017'=>'riboflavin-vitamin-b2-market',
			'global-serial-port-server-market-research-report-2017'=>'serial-port-server-market',
			'global-special-motors-market-research-report-2017'=>'special-motors-market',
			'global-sugar-based-excipients-market-research-report-2017'=>'sugar-based-excipients-market',
			'global-sun-uv-protective-product-market-research-report-2017'=>'sun-uv-protective-products-market',
			'global-tapentadol-palexia-market-research-report-2017'=>'tapentadol-palexia-market',
			'global-vapor-corrosion-inhibitor-market-research-report-2017'=>'vapor-corrosion-inhibitors-vci-market',
			'global-fingerprint-mobile-biometrics-market-size-status-and-forecast-2022'=>'fingerprint-mobile-biometrics-market',
			'global-healthcare-automatic-identification-and-data-capture-aidc-market-size-status-and-forecast-2022'=>'healthcare-automatic-identification-and-data-capture-aidc-market',
			'global-intraoperative-imaging-ioi-system-market-size-status-and-forecast-2022'=>'intraoperative-imaging-ioi-system-market',
			'global-law-enforcement-biometrics-market-size-status-and-forecast-2022'=>'law-enforcement-biometrics-market',
			'global-two-factor-biometrics-market-size-status-and-forecast-2022'=>'two-factor-biometrics-market',
			'global-active-pharmaceutical-ingredients-api-market-research-report-2017'=>'active-pharmaceutical-ingredients-api-market',
			'global-ceiling-air-conditioner-market-research-report-2017'=>'ceiling-air-conditioner-market',
			'global-ceiling-mounted-fan-coil-market-research-report-2017'=>'ceiling-mounted-fan-coil-market',
			'global-cement-admixture-market-research-report-2017'=>'cement-admixtures-market',
			'global-eeg-and-ecg-biometrics-market-research-report-2017'=>'eeg-and-ecg-biometrics-market',
			'global-electronic-expansion-valves-eev-market-research-report-2017'=>'electronic-expansion-valves-eev-market',
			'global-emergency-ventilator-market-research-report-2017'=>'emergency-ventilator-market',
			'global-greenhouse-heaters-market-research-report-2017'=>'greenhouse-heaters-market',
			'global-infusion-pumps-market-research-report-2017'=>'infusion-pump-market',
			'global-microbial-and-bacterial-cellulose-market-research-report-2017'=>'microbial-and-bacterial-cellulose-market',
			'global-north-america-europe-and-asia-pacific-south-america-middle-east-and-africa-pekk-market-2017-forecast-to-2022'=>'polyetherketoneketone-pekk-market',
			'global-vitamin-h-market-research-report-2017'=>'vitamin-h-biotin-market',
			'global-100-ton-200-ton-mobile-cranes-market-research-report-2017'=>'north-america-100-ton-200-ton-mobile-cranes-market',
			'global-corticosteroid-eyedrops-market-research-report-2017'=>'corticosteroid-eyedrops-market',
			'global-north-america-europe-and-asia-pacific-south-america-middle-east-and-africa-permethrin-market-2017-forecast-to-2022'=>'north-america-permethrin-market',
			'global-trailer-mounted-cranes-market-size-status-and-forecast-2022'=>'trailer-mounted-cranes-market',
			'sim-cards-market'=>'sim-card-market',
			'global-flame-retardant-cable-market-by-manufacturers-countries-type-and-application-forecast-to-2022'=>'flame-retardant-cable-market',
			'pre-engineered-building-market'=>'pre-engineered-buildings-market',
			'automotive-oil-&-air-filters-market'=>'automotive-oil-and-air-filters-market',
		);
		return $array;
	}
	public static function isMobile() {
		return preg_match("/(android|avantgo|blackberry|bolt|boost|cricket|docomo|fone|hiptop|mini|mobi|palm|phone|pie|tablet|up\.browser|up\.link|webos|wos)/i", $_SERVER["HTTP_USER_AGENT"]);
	}
}


