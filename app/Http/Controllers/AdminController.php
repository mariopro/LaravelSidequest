<?php namespace App\Http\Controllers;

use App\ApplicationSetting;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Auth;
use Request;

class AdminController extends Controller {

  /*
  |--------------------------------------------------------------------------
  | Admin Controller
  |--------------------------------------------------------------------------
  |
  | Every modern web application has some sort of administrative dashboard
  | that enables the people behind the application to modify content and
  | get a general overview. Is also nice for non-technical founders.
  |
  */

  protected $settings;

  public function __construct()
  {
    $this->middleware('admin');
    $this->settings = ApplicationSetting::findOrFail(1);
  }

  public function getIndex()
  {

    $title = 'Admin Dashboard';
    $settings = $this->settings;

    return view('admin.index', compact('title', 'settings'));
    
  }

  public function postUpdateSettings()
  {

    $setting = $this->settings;

    $exceptions = [
      '_token',
      'apple_touch_icon_152x152',
      'apple_touch_startup_image_640x920',
      'apple_touch_startup_image_640x1096',
      'apple_touch_startup_image_750x1334',
      'apple_touch_startup_image_1242x2208',
      'apple_touch_startup_image_1536x2008',
      'application_shortcut_icon_196x196',
      'application_favicon_ico_32x32',
      'application_favicon_png_32x32'
    ];

    $input = Request::except($exceptions);
    
    // Handle common settings
    foreach($input as $key => $value)
    {
      $setting[$key] = $value;
    }

    $setting->save();


    /**
    * Handle uploading meta images such as shortcut icons and
    * Apple touch images
    */
    function saveUploadedImages($images = [], $destinationPath)
    {
      foreach ($images as $key => $value) {
        if( Request::hasFile($key) ) {
          $image = Request::file($key);
          $image->move($destinationPath . "/", $value);
        }
      }
    }

    $icons_touch_images_path = base_path() . '/public/img/icons-touch';

    $icons_touch_images = [
      'application_shortcut_icon_196x196'   => "shortcut-icon-196x196.png",
      'application_favicon_ico_32x32'       => "shortcut-icon.ico",
      'application_favicon_png_32x32'       => "shortcut-icon.png",

      'apple_touch_icon_152x152'            => "apple-touch-icon.png",
      'apple_touch_startup_image_640x920'   => "apple-touch-startup-image-640x920.png",
      'apple_touch_startup_image_640x1096'  => "apple-touch-startup-image-640x1096.png",
      'apple_touch_startup_image_750x1334'  => "apple-touch-startup-image-750x1334.png",
      'apple_touch_startup_image_1242x2208' => "apple-touch-startup-image-1242x2208.png",
      'apple_touch_startup_image_1536x2008' => "apple-touch-startup-image-1536x2008.png"  
    ];

    saveUploadedImages($icons_touch_images, $icons_touch_images_path);

    return redirect()->back();

  }

  public function getImportSubscriptionPlans()
  {
    $plans = [];
    $plan_names = [];
    $ch = curl_init();
    
    curl_setopt_array($ch, [
      CURLOPT_RETURNTRANSFER => 1,
      CURLOPT_URL => 'https://api.stripe.com/v1/plans?key=' . env('SERVICE_STRIPE_SECRET_API_KEY'),
      CURLOPT_HTTPHEADER => ['Authorization: Bearer ' . env('SERVICE_STRIPE_SECRET_API_KEY')]
    ]);

    $result = curl_exec($ch);
    curl_close($ch);

    $result = json_decode($result);

    foreach( $result->data as $plan )
    {
      $plans[] = $plan;
    }

    foreach( $plans as $plan )
    {
      array_push($plan_names, $plan->name);
    }

    $plan_names = implode(',', $plan_names);

    $this->settings->subscription_plans_name = $plan_names;

    $this->settings->save();

    return redirect()->back();
  }
  
}