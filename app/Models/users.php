<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon ;

class users extends Model
{
    use HasFactory;

    public static function insert($data)
    {
        
        $data['created_at'] = date("Y-m-d H:i:s"); 

        $id = self::insertGetId($data);

        return $id;
    }

    public static function findByEmail($email)
    {
        $user = self::where('email' , $email )
        ->first();

        return $user;
    }

    public static function verifyUser($id)
    {
        self::where('id' , $id )
        ->update(['verified'=> 1]);
    }
}
