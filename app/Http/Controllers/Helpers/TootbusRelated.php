<?php
namespace App\Http\Controllers\Helpers;
use GuzzleHttp\Client;
use Carbon\Carbon;



class TootbusRelated
{
	private $token;
	private $base_uri;
	private $headers;
	private $diff_day;

	public function __construct(){
	$this->diff_day = 3;
	$this->base_uri = env('TOOTBUS_API_BASE_URI', 'https://api.ventrata.com');
  $this->token = env('TOOTBUS_API_KEY', 'f1cd14ce-6b6a-48ad-8786-39684380f16a');
	$this->headers = [
			        'Authorization' => 'Bearer '.$this->token,
			        'Accept' => 'application/json',
			        //'Octo-Env' => 'test',
			        //'Accept-Language' => 'fr',
			        //'Octo-Capabilities' => 'octo/content'

			    ];
	
    //$this->token = "f1cd14ce-6b6a-48ad-8786-39684380f16a";
	}
    

    public function checkProduct($productID, $optionID){


     	try {

  


    	$client = new Client(['base_uri' => $this->base_uri, 'headers' => $this->headers]);

    	

		   $response = $client->request('GET', '/octo/products/'.$productID);
       $body = json_decode($response->getBody()->getContents());

       //return $body->options[0]->id === $optionID;

       if(empty($body->id)){
       	return ["status" => false, "message" => "Product ID doesnt exists On Target Json Data"];
       
       }
       

       if(!empty($body->options)){

       	foreach ($body->options as $opt) {
       		 if($opt->id === $optionID){
       		 	return ["status" => true, "message" => json_encode($body)];
       		 }
       	}

       	return ["status" => false, "message" => "Option ID doesnt exists on Target Json Data"];
       	

       }else{
       	return ["status" => false, "message" => "There is No Regular Option Format on Target System"];
       	
       }




		} catch (\Exception $e) {
       return ["status" => false, "message" => $e->getMessage()];
		    
		}
    	


    
       
		 

     
    }



    public function checkAvailability($productID, $optionID, $startDate, $diff_day = null){

    	if($diff_day !== null){
    		$this->diff_day = $diff_day;
    	}
    	if(empty(trim($optionID))){
    		$optionID = "DEFAULT";
    	}

    	try{
    		$client = new Client(['base_uri' => $this->base_uri, 'headers' => $this->headers]);

    			   $response = $client->request('POST', '/octo/availability', [
			    
			     'json' => [
                'productId' => $productID,
                'optionId' => $optionID,
                'localDateStart' => Carbon::parse($startDate)->format('Y-m-d'),
                'localDateEnd' => Carbon::parse($startDate)->addDays($this->diff_day)->format('Y-m-d')
                /*'units' => [
                  ['id' => 'adult', 'quantity' => ],
                  ['id' => 'family', 'quantity' => 10],
                ]*/
               ]
			  ]);

    			 $body = json_decode($response->getBody()->getContents());

          return ["status" => true, "message" => json_encode($body)];

    	}catch(\Exception $e){
    		return ["status" => false, "message" => $e->getMessage()];

    	}

    }


    public function reserve($data){

    	try{


    			$client = new Client(['base_uri' => $this->base_uri, 'headers' => $this->headers]);

    	  $response = $client->request('POST', '/octo/bookings', [
			    
			     'json' => $data
			  ]);

			   $body = json_decode($response->getBody()->getContents());

          return ["status" => true, "message" => json_encode($body)];

    	}catch(\Exception $e){
    		return ["status" => false, "message" => $e->getMessage()];

    	}


    
     
    }


        public function reserveUpdate($id, $data){

    	try{


    			$client = new Client(['base_uri' => $this->base_uri, 'headers' => $this->headers]);

    	  $response = $client->request('PATCH', '/octo/bookings/'.$id, [
			    
			     'json' => $data
			  ]);

			   $body = json_decode($response->getBody()->getContents());

          return ["status" => true, "message" => json_encode($body)];

    	}catch(\Exception $e){
    		return ["status" => false, "message" => $e->getMessage()];

    	}


    
     
    }


            public function delete($id, $data){

    	try{


    			$client = new Client(['base_uri' => $this->base_uri, 'headers' => $this->headers]);

    	  $response = $client->request('DELETE', '/octo/bookings/'.$id, [
			    
			     'json' => $data
			  ]);

			   $body = json_decode($response->getBody()->getContents());

          return ["status" => true, "message" => json_encode($body)];

    	}catch(\Exception $e){
    		return ["status" => false, "message" => $e->getMessage()];

    	}


    
     
    }





  public function confirm($id, $data){

    	try{


    			$client = new Client(['base_uri' => $this->base_uri, 'headers' => $this->headers]);

    	  $response = $client->request('POST', '/octo/bookings/'.$id.'/confirm', [
			    
			     'json' => $data
			  ]);

			   $body = json_decode($response->getBody()->getContents());

          return ["status" => true, "message" => json_encode($body)];

    	}catch(\Exception $e){
    		return ["status" => false, "message" => $e->getMessage()];

    	}


    
     
    }





  public function extend($id, $data){

    	try{


    			$client = new Client(['base_uri' => $this->base_uri, 'headers' => $this->headers]);

    	  $response = $client->request('POST', '/octo/bookings/'.$id.'/extend', [
			    
			     'json' => $data
			  ]);

			   $body = json_decode($response->getBody()->getContents());

          return ["status" => true, "message" => json_encode($body)];

    	}catch(\Exception $e){
    		return ["status" => false, "message" => $e->getMessage()];

    	}


    
     
    }



   public function getBooking($id){

    	try{


    			$client = new Client(['base_uri' => $this->base_uri, 'headers' => $this->headers]);

    	  $response = $client->request('GET', '/octo/bookings/'.$id);

			   $body = json_decode($response->getBody()->getContents());

          return ["status" => true, "message" => json_encode($body)];

    	}catch(\Exception $e){
    		return ["status" => false, "message" => $e->getMessage()];

    	}


    
     
    }
}
