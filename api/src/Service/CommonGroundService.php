<?php
// src/Service/HuwelijkService.php
namespace App\Service;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Cache\Adapter\AdapterInterface as CacheInterface;
use GuzzleHttp\Client ;
use GuzzleHttp\RequestOptions;

use Twig\Environment as Twig ; 

class CommonGroundService
{
	private $params;
	private $cache;
	private $client;
	private $session;
	private $twig;
	
	public function __construct(ParameterBagInterface $params, SessionInterface $session, CacheInterface $cache, Twig $twig)
	{
		$this->params = $params;
		$this->session = $session;
		$this->cash = $cache;
		$this->twig = $twig;

		// We might want to overwrite the guzle config, so we declare it as a separate array that we can then later adjust, merge or otherwise influence
		$this->guzzleConfig = [
				// Base URI is used with relative requests
				'http_errors' => false,
				//'base_uri' => 'https://wrc.zaakonline.nl/applications/536bfb73-63a5-4719-b535-d835607b88b2/',
				// You can set any number of default request options.
				'timeout'  => 4000.0,
				// To work with NLX we need a couple of default headers
				'headers' => [
						'Accept'  => 'application/ld+json',
						'Content-Type'  => 'application/json',
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
	public function getClient()
	{
		return  $this->client;
	}
	
	/*
	 * Get a single resource from a common ground componant
	 */
	public function getResourceList($url, $query = [], $force = false)
	{
		if(!$url){
			return false;
		}

		$item = $this->cash->getItem('commonground_'.md5 ($url));
		if ($item->isHit() && !$force) {
			//return $item->get();
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
	 * Get a single resource from a common ground componant
	 */
	public function getResource($url, $query = [], $force = false)
	{
		if(!$url){
			//return false;
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
	 * Get a single resource from a common ground componant
	 */
	public function updateResource($resource, $url = null)
	{
		if(!$url){
			return false;
		}

		unset($resource['_links']);
		unset($resource['_embedded']);		
		
		$response = $this->client->request('PUT', $url, [
				'body' => json_encode($resource)
			]
		);

		// Temp error hunting
		if($response->getStatusCode() != '200'){
			var_dump($response->getBody());
			die;
		}
			
		$response = json_decode($response->getBody(), true);
		
		
		// Lets cash this item for speed purposes
		$item = $this->cash->getItem('commonground_'.md5 ($url));
		$item->set($response);
		$item->expiresAt(new \DateTime('tomorrow'));
		$this->cash->save($item);

		return $response;
	}
	
	/*
	 * Get a single resource from a common ground componant
	 */
	public function createResource($resource, $url = null)
	{
		if(!$url){
			return false;
		}
		
		$response = $this->client->request('POST',$url, [
				'body' => json_encode($resource)
			]
		);
		
		// Temp error hunting
		if($response->getStatusCode() != '200'){
			var_dump($response->getBody());
			die;
		}
		
		$response = json_decode($response->getBody(), true);
		
		// Lets cash this item for speed purposes
		$item = $this->cash->getItem('commonground_'.md5 ($url.'/'.$response['id']));
		$item->set($response);
		$item->expiresAt(new \DateTime('tomorrow'));
		$this->cash->save($item);
		
		return $response;
	}	
	
	/*
	 * Get a single resource from a common ground componant
	 */
	public function clearCash($url)
	{

	}

	/*
	 * Get a list of available commonground components
	 */
	public function getComponentList()
	{
		$components = [
				'contacts' => 		['href'=>'http://cc.zaakonline.nl','authorization'=>'','icon'=>'flaticon-user-1','config'=>['hidden'=>['']]],
				'locations' => 		['href'=>'http://lc.zaakonline.nl','authorization'=>'','icon'=>'flaticon-location','config'=>['hidden'=>['']]],
				'linking table' => 	['href'=>'http://ltc.zaakonline.nl','authorization'=>'','icon'=>'','config'=>['hidden'=>['']]],
				'proces types' => 	['href'=>'http://ptc.zaakonline.nl','authorization'=>'','icon'=>'flaticon-diagram','config'=>['hidden'=>['']]],
				'employees' => 		['href'=>'http://mrc.zaakonline.nl','authorization'=>'','icon'=>'flaticon-user-6','config'=>['hidden'=>['']]],
				'request_types' => 	['href'=>'http://vtc.zaakonline.nl','authorization'=>'','icon'=>'flaticon-agenda','config'=>['hidden'=>['']]],
				'web_resources' => 	['href'=>'http://wrc.zaakonline.nl','authorization'=>'','icon'=>'flaticon-internet','config'=>['hidden'=>['']]],
				'calendar' => 		['href'=>'http://ac.zaakonline.nl','authorization'=>'','icon'=>'flaticon-calendar','config'=>['hidden'=>['']]],
				'messages' =>		['href'=>'http://bs.zaakonline.nl','authorization'=>'','icon'=>'flaticon-message','config'=>['hidden'=>['']]],
				'payment' =>		['href'=>'http://bc.zaakonline.nl','authorization'=>'','icon'=>'flaticon-credit-card','config'=>['hidden'=>['']]],				
				'brp' =>			['href'=>'http://brp.zaakonline.nl','authorization'=>'','icon'=>'','config'=>['hidden'=>['']]],
				'assents' => 		['href'=>'http://irc.zaakonline.nl','authorization'=>'','icon'=>'flaticon-hands','config'=>['hidden'=>['']]],				
				'requests' => 		['href'=>'http://vrc.zaakonline.nl','authorization'=>'','icon'=>'flaticon-agenda','config'=>['hidden'=>['id','referenceId','targetOrganization','submitterPerson','submitter','properties','organizations','requestCases','openCase','parent','children','confidential','archive','currentStage']]],
				'products' => 		['href'=>'http://pdc.zaakonline.nl','authorization'=>'','icon'=>'','config'=>['hidden'=>['']]],
				'processes' => 		['href'=>'http://prc.zaakonline.nl','authorization'=>'','icon'=>'','config'=>['hidden'=>['']]],
				'users' => 			['href'=>'http://uc.zaakonline.nl','authorization'=>'','icon'=>'','config'=>['hidden'=>['']]],
				'digispoof' => 		['href'=>'http://ds.zaakonline.nl','authorization'=>'','icon'=>'','config'=>['hidden'=>['']]],
				'single sign on' => ['href'=>'http://sso.zaakonline.nl','authorization'=>'','icon'=>'','config'=>['hidden'=>['']]],				
				'orders' => 		['href'=>'http://orc.zaakonline.nl','authorization'=>'','icon'=>'flaticon-agenda-1','config'=>['hidden'=>['']]],				
				'stuf' => 			['href'=>'http://stuf.zaakonline.nl','authorization'=>'','icon'=>'','config'=>['hidden'=>['']]],				
				'contact moments' => ['href'=>'http://crc.zaakonline.nl','authorization'=>'','icon'=>'','config'=>['hidden'=>['']]],
				
				'zaken' => 			['href'=>'https://zaken-api.vng.cloud/api/v1/schema/','authorization'=>'','icon'=>'','config'=>['hidden'=>['']]],
				'documenten' => 	['href'=>'https://documenten-api.vng.cloud/api/v1/schema/','authorization'=>'','icon'=>'','config'=>['hidden'=>['']]],
				'catalogi' => 		['href'=>'https://catalogi-api.vng.cloud/api/v1/schema/','authorization'=>'','icon'=>'','config'=>['hidden'=>['']]],
				'besluiten' =>		['href'=>'https://besluiten-api.vng.cloud/api/v1/schema/','authorization'=>'','icon'=>'','config'=>['hidden'=>['']]],
				'notificaties' => 	['href'=>'https://notificaties-api.vng.cloud/api/v1/schema/','authorization'=>'','icon'=>'','config'=>['hidden'=>['']]],
				'autorisaties' => 	['href'=>'https://autorisaties-api.vng.cloud/api/v1/schema/','authorization'=>'','icon'=>'','config'=>['hidden'=>['']]],
				'klantinteracties' => ['href'=>'https://klantinteracties-api.vng.cloud/api/v1/schema/','authorization'=>'','icon'=>'','config'=>['hidden'=>['']]],
				
				'mijnapp_backend' => ['href'=>'http://mijnapp_bo.zaakonline.nl','authorization'=>'','icon'=>'','config'=>['hidden'=>['']]],
				'mijnapp_frontend' => ['href'=>'http://mijnapp.zaakonline.nl','authorization'=>'','icon'=>'','config'=>['hidden'=>['']]]
		];		
		
		// Lets set the context
		foreach($components as $key => $component){
			$components[$key]['context'] = $this->getComponentContext($component);
			$components[$key]['resources'] = $this->getComponentResources($component);
			
		}
				
		return $components;
	}
	
	/*
	 * Get a list of available commonground components
	 */
	public function getComponent($id)
	{
		$components= $this->getComponentList();
		
		if(!array_key_exists($id, $components)){
			return false;
		}
				
		return $components[$id];		
	}

	/*
	 * Get the health of a commonground componant
	 */
	public function getComponentHealth(string $component, $force = false)
	{
		$componentList = $this->getComponentList();

		$item = $this->cash->getItem('componentHealth_'.md5 ($component));
		if ($item->isHit() && !$force) {
			//return $item->get();
		}

		//@todo trhow symfony error
		if(!array_key_exists($component, $componentList)){
			return false;
		}
		else{
			// Lets swap the component for a

			// Then we like to know al the component endpoints
			$component = $this->getComponentResources($component);
		}

		// Lets loop trough the endoints and get health (the self endpoint is included)
		foreach ($component['endpoints'] as $key=>$endpoint){

			//var_dump($component['endpoints']);
			//var_dump($endpoint);

			$response =  $this->client->request('GET', $component['href'].$endpoint["href"],  ['Headers' =>['Authorization' => $component['authorization'],'Accept' => 'application/health+json']]);
			if($response->getStatusCode() == 200){
				//$component['endpoints'][$key]['health'] = json_decode($response->getBody(), true);
				$component['endpoints'][$key]['health'] = false;
			}
		}

		$item->set($component);
		$item->expiresAt(new \DateTime('tomorrow'));
		$this->cash->save($item);


		return $component;
	}
	
	/*
	 * Get a list of available resources on a commonground componant
	 */
	public function getResourceContext(array $component, string $resource, $force = false)
	{
		//$component = $this->getComponent($component);
		
		$item = $this->cash->getItem('componentContext_'.md5 ($component['href']));
		if ($item->isHit() && !$force) {
			//var_dump($item->get());
			//return $item->get();
		}
		
		$resource= ltrim($resource, '/');
		$resource= ucfirst($resource);
		$resource= rtrim($resource, 's');
		
		$response = $this->client->request('GET',$component['href'].'/contexts/'.$resource);
		$response = json_decode($response->getBody(), true);
		
		//unset($response['@context']['@vocab']);
		//unset($response['@context']['hydra']);
		
		$item->set($response);
		$item->expiresAt(new \DateTime('tomorrow'));
		$this->cash->save($item);
		
		
		return $response;
	}
	
	/*
	 * Get a list of available resources on a commonground componant
	 */
	public function getComponentContext(array $component, $force = false)
	{
		//$component = $this->getComponent($component);

		$item = $this->cash->getItem('componentContext_'.md5 ($component['href']));
		if ($item->isHit() && !$force) {
			//var_dump($item->get());
			return $item->get();
		}
		
		$response = $this->client->request('GET',$component['href'].'/docs.jsonld');
		$response = json_decode($response->getBody(), true);
		
		$item->set($response);
		$item->expiresAt(new \DateTime('tomorrow'));
		$this->cash->save($item);


		return $response; 
	}
	
	/*
	 * Get a list of available resources on a commonground componant
	 */
	public function getComponentResources(array $component, $force = false)
	{
		//$component = $this->getComponent($component);
		
		$item = $this->cash->getItem('componentContext_'.md5 ($component['href']));
		if ($item->isHit() && !$force) {
			//var_dump($item->get());
			//return $item->get();
		}
		
		$response = $this->client->request('GET',$component['href']);
		$response = json_decode($response->getBody(), true);
		
		$item->set($response);
		$item->expiresAt(new \DateTime('tomorrow'));
		$this->cash->save($item);
		
		
		return $response;
	}
	
	/*
	 * Get a list of available resources on a commonground componant
	 */
	public function getTemplate(string $component,string $resourcetype, array $variables)
	{
		if ($this->twig->getLoader()->exists($component.$resourcetype.'.html.twig')) {
			// the template exists, do something
			return $this->twig->render($component.$resourcetype.'.html.twig', $variables);
		}
		
		// the template dosn't exists, return falses
		return false;
	}
	
	
}
