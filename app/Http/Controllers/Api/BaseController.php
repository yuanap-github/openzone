<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
class BaseController extends Controller
{
  public function __construct(){
   echo response()->json(1);
     exit;
  }

}
