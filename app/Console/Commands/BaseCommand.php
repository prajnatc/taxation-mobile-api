<?php

namespace App\Console\Commands;


use Illuminate\Console\Command;
use App\Database\OnTheFly;
use App\Models\ClientConfiguration;

class BaseCommand extends Command
{
  protected function connectClient($clientConfiguration){

      return new OnTheFly($clientConfiguration);
  }

   protected function client_details($clientCode=null)
    {

        if(!is_null($client_details = ClientConfiguration::where('client_unique_key',$clientCode)->first()) && !empty($clientCode)){

            return $client_details;

        }

    }
}
