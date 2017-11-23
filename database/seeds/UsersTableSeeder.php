<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // $usersid = DB::table('users')->insertGetId([
        //     'name' => 'Admin User',
        //     'mobile_number' => '8050163393',
        //     'activated_on'=>date('Y-m-d H:i:s'),
        //     'created_at'=>date('Y-m-d H:i:s'),
        //     'updated_at'=>date('Y-m-d H:i:s')
        // ]);
        //
        // $parentsid = DB::table('parents')->insertGetId([
        //     'user_id' => $usersid
        // ]);
        //
        // $parent_students = DB::table('parent_students')->insert([
        //     'parent_id' => $parentsid,
        //     'student_id' => 1,
        //     'client_id' => 1,
        //     'created_at'=>date('Y-m-d H:i:s'),
        //     'updated_at'=>date('Y-m-d H:i:s')
        // ]);
        //
        //
        // $usersid = DB::table('users')->insertGetId([
        //     'name' => 'App User',
        //     'mobile_number' => '9738349780',
        //     'activated_on'=>date('Y-m-d H:i:s'),
        //     'created_at'=>date('Y-m-d H:i:s'),
        //     'updated_at'=>date('Y-m-d H:i:s')
        // ]);
        //
        // $parentsid = DB::table('parents')->insertGetId([
        //     'user_id' => $usersid
        // ]);
        //
        // $parent_students = DB::table('parent_students')->insert([
        //     'parent_id' => $parentsid,
        //     'student_id' => 1,
        //     'client_id' => 1,
        //     'created_at'=>date('Y-m-d H:i:s'),
        //     'updated_at'=>date('Y-m-d H:i:s')
        // ]);
    }
}
