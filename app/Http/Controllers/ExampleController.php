<?php

namespace App\Http\Controllers;

use Pusher;

class ExampleController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function testpusher(){

      $options = array(
        'encrypted' => true
      );
      $pusher = new Pusher(
        'c8758c68efd438562458',
        '5034e5e9ebbaea713947',
        '260595',
        $options
      );

      $data['message'] = 'hello world';
      $pusher->trigger('test_channel', 'my_event', $data);

    }

    //
}
