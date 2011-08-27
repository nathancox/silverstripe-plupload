<?php


class PluploadField extends FormField {
	
	public static $image_extensions = array('jpg','jpeg','gif','png');
	
	public $template = 'PluploadField';
		
	public $settings = array(
		'image-class' => 'Image',
		'file-class' => 'File',
		'upload-url' => '10mb',
		'max-filesize' => '10mb',
		'filter-title' => 'Images',
		'filter-extensions' => 'jpg,jpeg,gif,png',
		'upload-folder' => 'Uploads'
	);
	
	public function __construct($name, $title = null, $configuration = array(), $form = null) {
		parent::__construct($name, $title, null, $form);
		
		/*
		// A little hack to make things easier in the CMS
		$controller = Director::urlParam('Controller');
		if(is_subclass_of($controller,"LeftAndMain")) {
			self::$backend = true;
		}
		*/
		
	}
	/*
	// @TODO
	function getMaxFilesize() {
		return '10mb';
	}
	
	function getFilterTitle() {
		return 'Images';
	}
	
	function getFilterExtensions() {
		return 'jpg,jpeg,gif,png';
	}
	*/
	
	function setUploadFolder($folder) {
		$this->settings['upload-folder'] = $folder;
	}
	function getUploadFolder() {
		return $this->settings['upload-folder'];
	}
	
		
	
	function getUploadURL() {
		return Director::baseURL().Director::makeRelative($this->Link('upload'));
	}
	
	public function FieldHolder() {
	//	Requirements::javascript(THIRDPARTY_DIR."/jquery/jquery.js");
		Requirements::javascript(THIRDPARTY_DIR."/jquery-livequery/jquery.livequery.js");
		Requirements::javascript('plupload/javascript/thirdparty/plupload.full.js');
		
		Requirements::javascript('plupload/javascript/thirdparty/jquery.plupload.queue/jquery.plupload.queue.js');
		Requirements::css('plupload/javascript/thirdparty/jquery.plupload.queue/css/jquery.plupload.queue.css');
		
		Requirements::css('plupload/css/pluploadfield.css');
		
		Requirements::javascript('plupload/javascript/pluploadfield.js');
		
		return $this->renderWith($this->template);
	}
	
	function getSettings() {
		$settings = $this->settings;
		$settings['upload-url'] = $this->getUploadURL();
		
		$output = new DataObjectSet();
		
		foreach ($settings as $key => $value) {
			$output->push(new ArrayData(array(
				'Key' => $key,
				'Value' => $value,
				'AttributeName' => 'data-'.$key
			)));
		}
		
		return $output;
	}
	
	function getSetting($key) {
		return (isset($this->settings[$key]) ? $this->settings[$key] : null);
	}

	
	function upload() {
		if(isset($_FILES['file']) && is_uploaded_file($_FILES['file']['tmp_name'])) {
			$chunk = (isset($_REQUEST["chunk"]) ? $_REQUEST["chunk"] : 0);
			$chunks = (isset($_REQUEST["chunks"]) ? $_REQUEST["chunks"] : 0);
			$fileName = (isset($_REQUEST["name"]) ? $_REQUEST["name"] : '');
			
			echo(':'.$chunk.'/'.$chunks.':');
			
			// Clean the fileName for security reasons
			$fileName = preg_replace('/[^\w\._]+/', '', $fileName);
			
			$uploadDir = $this->getUploadFolder();
			
			$ext = strtolower(end(explode('.', $_FILES['file']['name'])));
			$class = in_array($ext, self::$image_extensions) ? $this->getSetting('image-class') : $this->getSetting('file-class');
			
			
			
			if (isset($_SERVER["HTTP_CONTENT_TYPE"])) {
				$contentType = $_SERVER["HTTP_CONTENT_TYPE"];
			}
			if (isset($_SERVER["CONTENT_TYPE"])) {
				$contentType = $_SERVER["CONTENT_TYPE"];
			}
			
			/*
			// Handle non multipart uploads older WebKit versions didn't support multipart in HTML5
			if (strpos($contentType, "multipart") !== false) {
			*/
				
				if (isset($_FILES['file']['tmp_name']) && is_uploaded_file($_FILES['file']['tmp_name'])) {
					
					if ($chunks < 2) {
						$file = new $class();
						$uploadObject = new Upload();
						$uploadObject->loadIntoFile($_FILES['file'], $file, $uploadDir);
				//	$file->write();
						$id = $file->ID;
					} else {
						
						//die('ERROR: CHUNKING NOT YET SUPPORTED');
						die('{"jsonrpc" : "2.0", "error" : {"code": 102, "message": "Chunking is not supported.  Please remove chunk_size from the JS parameters."}, "id" : "id"}');

						// Open temp file
						$out = fopen($uploadDir . DIRECTORY_SEPARATOR . $fileName, $chunk == 0 ? "wb" : "ab");
						if ($out) {
							// Read binary input stream and append it to temp file
							$in = fopen($_FILES['file']['tmp_name'], "rb");

							if ($in) {
								while ($buff = fread($in, 4096)) {
									fwrite($out, $buff);
								}
							} else
								die('{"jsonrpc" : "2.0", "error" : {"code": 101, "message": "Failed to open input stream."}, "id" : "id"}');
							fclose($in);
							fclose($out);
							@unlink($_FILES['file']['tmp_name']);
						} else {
							die('{"jsonrpc" : "2.0", "error" : {"code": 102, "message": "Failed to open output stream."}, "id" : "id"}');
						}
					}
					
					
				} else {
					die('{"jsonrpc" : "2.0", "error" : {"code": 103, "message": "Failed to move uploaded file."}, "id" : "id"}');
				}
			/*
			} else {
				if (isset($_FILES['file']['tmp_name']) && is_uploaded_file($_FILES['file']['tmp_name'])) {
					
					
						$file = new $class();
						$uploadObject = new Upload();
						
						$uploadObject->loadIntoFile($_FILES['file'], $file, $uploadDir);
				//	$file->write();
						$id = $file->ID;
					
				} else {
					die('{"jsonrpc" : "2.0", "error" : {"code": 103, "message": "Failed to move uploaded file."}, "id" : "id"}');
				}

			}
			*/
			

			// Return JSON-RPC response
			die('{"jsonrpc" : "2.0", "result" : null, "id" : "id"}');

			
			
		}
	}
	
	
}









