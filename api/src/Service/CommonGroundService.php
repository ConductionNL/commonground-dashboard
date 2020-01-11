<?php
// src/Service/HuwelijkService.php
namespace App\Service;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Cache\Adapter\AdapterInterface as CacheInterface;
use GuzzleHttp\Client ;
use GuzzleHttp\RequestOptions;

class CommonGroundService
{
	private $params;
	private $cache;
	private $client;
	private $session;
	
	public function __construct(ParameterBagInterface $params, SessionInterface $session, CacheInterface $cache)
	{
		$this->params = $params;
		$this->session = $session;
		$this->cash = $cache;
		
		// We might want to overwrite the guzle config, so we declare it as a separate array that we can then later adjust, merge or otherwise influence
		$this->guzzleConfig = [
				// Base URI is used with relative requests
				'http_errors' => false,
				//'base_uri' => 'https://wrc.zaakonline.nl/applications/536bfb73-63a5-4719-b535-d835607b88b2/',
				// You can set any number of default request options.
				'timeout'  => 4000.0,
				// To work with NLX we need a couple of default headers
				'headers' => [
						//'X-NLX-Request-User-Id' => '64YsjzZkrWWnK8bUflg8fFC1ojqv5lDn'				// the id of the user performing the request
						//'X-NLX-Request-Application-Id' => '64YsjzZkrWWnK8bUflg8fFC1ojqv5lDn' 		// the id of the application performing the request
						//'X-NLX-Request-Subject-Identifier' => '64YsjzZkrWWnK8bUflg8fFC1ojqv5lDn' 	// an subject identifier for purpose registration (doelbinding)
						//'X-NLX-Request-Process-Id' => '64YsjzZkrWWnK8bUflg8fFC1ojqv5lDn' 			// a process id for purpose registration (doelbinding)
						//'X-NLX-Request-Data-Elements' => '64YsjzZkrWWnK8bUflg8fFC1ojqv5lDn' 		// a list of requested data elements
						//'X-NLX-Request-Data-Subject' => '64YsjzZkrWWnK8bUflg8fFC1ojqv5lDn' 		// a key-value list of data subjects related to this request. e.g. bsn=12345678,kenteken=ab-12-fg
				]
		];
		
		// Lets start up a default client
		$this->client= new Client($this->guzzleConfig);
	}
	
	/*
	 * Get a single resource from a common ground componant
	 */
	public function getResourceList($url, $query = [], $force = false)
	{
	}
	
	/*
	 * Get a single resource from a common ground componant
	 */	
	public function getResource($url, $query = [], $force = false)
	{
		if(!$url){
			return false;
		}		
		
		$item = $this->cash->getItem('commonground_'.md5 ($url));
		if ($item->isHit() && !$force) {
			return $item->get();
		}	
		
		$response = $this->client->request('GET',$url, [
				'query' => $query
			]
		);
		
		$response = json_decode($response->getBody(), true);		
		
		$item->set($response);
		$item->expiresAt(new \DateTime('tomorrow'));
		$this->cash->save($item);
		
		return $response;
	}	
		
	/*
	 * Get a list of available commonground components
	 */
	public function getComponentList($url, $query = [], $force = false)
	{
		$components = [
				'vtc' => ['url'=>'vtc','authorization'=>''],
				'vrc' => ['url'=>'vrc','authorization'=>''],
				'pdc' => ['url'=>'pdc','authorization'=>''],
				'wrc' => ['url'=>'wrc','authorization'=>'']
		];		
				
		return $components;
	}
	
	/*
	 * Get the health of a commonground componant
	 */
	public function getComponentHealth($url, $query = [], $force = false)
	{
		$componentList = $this->getComponentList();
		
		//@todo trhow symfony error
		if(!in_array($component, $componentList)){
			
		}
		else{
			// Lets swap the component for a
			$component = $componentList[$component];
			// Then we like to know al the component endpoints
			$component = $this->getComponentResources($component);
		}
		
		$response =  $this->client->request('GET',$component['url'],  ['Headers' =>['Authorization' => $component['authorization'],'Accept' => $component['application/health+json']]]);
		if($response->getStatusCode() == 200){
			$component['health'] = json_decode($response->getBody(), true);
			
			// If we can get a health responce on the main endpoint (and it thus is healthy enough to provide a healthcheck) we are going to check the indivual endpoints
			$endpoints = [];
			foreach ($component['endpoints'] as $key->$endpoint){
				
				$endpoints[$endpoint]  = [];
				$response =  $this->client->request('GET',$component['url'],  ['Headers' =>['Authorization' => $component['authorization'],'Accept' => $component['application/health+json']]]);
				if($response->getStatusCode() == 200){
					$endpoints[$endpoint] = json_decode($response->getBody(), true);
				}
				else{
					$endpoints[$endpoint] = false;					
				}
			}
			
			$component['endpoints'] = $endpoints;
		}
		else{
			$component['health'] = false;			
		}
		
		return $component;
	}
	
	/*
	 * Get a list of available resources on a commonground componant
	 */
	public function getComponentResources(string $component)
	{
		$componentList = $this->getComponentList();
		
		//@todo trhow symfony error
		if(!in_array($component, $componentList)){
			
		}
		else{
			// Lets swap the component for a version that has an endpoint and authorization
			$component = $componentList[$component];
		}
				
		$response =  $this->client->request('GET',$component['url'],  ['Headers' =>['Authorization' => $component['authorization'],'Accept' => $component['application/ld+json']]]);
		$component['status'] = $response->getStatusCode();
		if($response->getStatusCode() == 200){
			$component['endpoints'] = json_decode($response->getBody(), true);
		}
		else{
			$component['endpoints'] = [];
		}
		
		
		return $component;
	}
}
