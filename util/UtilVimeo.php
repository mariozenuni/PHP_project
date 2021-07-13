<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace app\util;
use backend\util\Image;
use Codeception\Util\Debug;
use Vimeo\Exceptions\VimeoRequestException;
use Vimeo\Exceptions\VimeoUploadException;
use Vimeo\Vimeo;
use Yii;
use yii\base\ErrorException;
use yii\web\UploadedFile;

class UtilVimeo {

	/* @var $client Vimeo */
	protected $client = null;
	/* @var $access_token string */
	protected $access_token = null;
	const SCOPE_PUBLIC = 'public';
	const SCOPE_PRIVATE = 'private';
	const SCOPE_PURCHASED = 'purchased';
	const SCOPE_VIDEO_FILES = 'video_files';
	const SCOPE_UPLOAD = 'upload';
	const SCOPE_INTERACT = 'interact';
	const SCOPE_DELETE = 'delete';
	const SCOPE_EDIT = 'edit';
	const SCOPE_CREATE = 'create';

	public function __construct()
	{
		$this->client = new Vimeo(
			\Yii::$app->params['VIMEO']['client_id'],
			\Yii::$app->params['VIMEO']['client_secret'],
			\Yii::$app->params['VIMEO']['access_token']
		);
	}

    public static function retrieveVideo($idVimeo,$field = 'html'){

        $client = new Vimeo(
			\Yii::$app->params['VIMEO']['client_id'],
			\Yii::$app->params['VIMEO']['client_secret'],
			\Yii::$app->params['VIMEO']['access_token']
        );

        $response = $client->request('/videos/'.$idVimeo,null ,'GET');

		$responseUrl= $response["body"]["embed"][$field];

        return $responseUrl;
    }



	public function getHlsLink($idVimeo)
	{


		$response = $this->client->request('/videos/'.$idVimeo ,[],'GET');

		if(!isset($response['body']['files'])){


			throw new ErrorException('Chiave files mancante, controlla di chiamare vimeo con un token con scope video_files');

		}

		$files = $response["body"]['files'];


		$hlsFiles = array_filter($files,function ($item){

			return $item['quality'] == 'hls';
		});

		$firstKey = array_key_first($hlsFiles);

		if(self::checkIfUploaded($idVimeo)){


			$hlsFile = $hlsFiles[$firstKey];

			return $hlsFile['link'];
		}



		return "Attendere il caricamento del video";


	}

	/* @return bool true se completato il caricamento, false altrimenti */
	public static function checkIfUploaded($idVimeo)
	{
		if(!empty($idVimeo)){


			$client = new Vimeo(
				\Yii::$app->params['VIMEO']['client_id'],
				\Yii::$app->params['VIMEO']['client_secret'],
				\Yii::$app->params['VIMEO']['access_token']
			);

			$response = $client->request('/videos/'.$idVimeo,[
				'fields'=>'uri,upload.status,transcode.status'
			] ,'GET');

			$transCode = $response['body']['transcode']['status'];
			$upload = $response['body']['upload']['status'];


			return $transCode=='complete' && $upload=='complete';
		}

		return true;
    }

	public function deleteVideo($url)
	{

		$response = $this->client->request('/videos/'.$url,[],'DELETE');
		$error= isset($response['body']['error']) ? $response['body']['error'] : null;
		$success= empty($response['body']) ? Yii::t('app','Eliminazione avvenuta con successo') : null;


		return [
			$error,
			$success,
			$response
		];

	}

	public function getIdFromUrl(string $url)
	{
		$explode = explode('/',$url);

		return end($explode);
	}

	/**
	 * @param $file UploadedFile|string risorsa da caricare su Vimeo, può essere un file recuperato dalla variabile $_FILES oppure un path
	 * @param string $folder cartella che verrà creata per il caricamento del file temporaneo da caricare su Vimeo
	 * */
	public function uploadVideo( $file,string $folder = 'uploads-vimeo')
	{

		$subFolder = '';
		$file_name = '';
		$folder_path = '';
		if($file instanceof UploadedFile){

			$subFolder = substr($file->name, 0, strpos($file->name, "."));
			$subFolder = str_replace(" ", "_", $subFolder);

			Image::createFolder($folder, $subFolder, true);
			$fileName = $file->name;
			$attachment = $file->baseName . date("YmdHis") . '.' . 'mp4';
			$attachment = str_replace('/','_',$attachment);
			$attachment = str_replace(" ", "", $attachment);
			$file->saveAs('../../media/' . $folder . '/' . $subFolder . '/' . $attachment);
			$folder_path = \Yii::getAlias('@app') . '/../media/' . $folder . '/' . $subFolder;
			$file_name = $folder_path . '/' . $attachment;
		} else {

			$file_name = $file;
			$name = explode('/',$file);
			$attachment = end($name);
			$attachment = str_replace('/','_',$attachment);
			$attachment = str_replace(" ", "_", $attachment);
		}

		// torna anche l'id vimeo(ultima posizione)
		try {
			$uri = $this->client->upload($file_name, array(
				"name" => $attachment,
				'embed' => [
					'buttons' => [
						'like' => false,
						'watchlater' => false,
						'share' => false,
						'embed' => false
					],
					'logos' => [
						'vimeo' => false
					],
					'title' => false
				],
				'privacy' => [
					'download' => false,
					'embed' => 'whitelist',
					'view' => 'disable'
				]
			));
		} catch (VimeoRequestException $e) {

			echo $e->getMessage();

			exit();
		} catch (VimeoUploadException $e) {

			echo $e->getMessage();

			exit();

		}

		if(!empty($folder_path)){
			//cancello il file temporaneamente salvato sul server per l'upload su vimeo
			GeneralUtil::deleteDirectory($folder_path);
		}


		$explodedUri = explode('/', $uri);

		$idVimeo = $explodedUri[count($explodedUri )- 1];

		return $idVimeo;
	}


	/**
	 * @param string $domain dominio alla quale concedere il privilegio di vedere il video
	 * @param int $idVimeo id video
	 * @return mixed
	 */
	public function addPrivacyDomain(string $domain, int $idVimeo)
	{

		$privacyUri = self::getPrivacyUri($domain,$idVimeo);

		$resultPrivacy = $this->client->request($privacyUri,[],'PUT');

		return $resultPrivacy;
	}

	private static function getPrivacyUri(string $domain,int $idVimeo)
	{
		return '/videos/'.$idVimeo.'/privacy/domains/'.$domain;
	}
}
?>