<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Str;
use App\Models\ProfileUser;
use Carbon\Carbon;
use Artisan;
class ProfileImageController extends Controller
{

  public function __construct() {
    Artisan::call('cache:clear');
  }

   public function index(){
    $allusers = ProfileUser::count();
      return view('welcome',compact('allusers'));
    }

    public function uploads(Request $request){
        if(isset($_FILES["image"])){
            $sourceImg = @imagecreatefromstring(@file_get_contents($_FILES["image"]["tmp_name"]));
            if ($sourceImg === false){
              echo "images/default-profile-pic.png";
              exit;
            }
          
            $image = makeDP($_FILES["image"]["tmp_name"], (
              isset($_POST["design"]) ? $_POST["design"] : 0
            ));
            
            $image_extension = Str::random(10) . ".png";
            $loc = public_path('uploads/').$image_extension;
          
            file_put_contents($loc, $image);
            ProfileUser::insert([
                "image"=>$image_extension,
                "created_at"=> Carbon::now()
            ]);
            return $image_extension;
          }

    }


    




}
