<?php

class DesmondModulesLoader {
	
	
	public function LoadCollection($collection) {

		if($collection == 'Models') {
			foreach(glob(Application::Path('models') . '*.php') as $model) {
				include_once($model);
			}
		}

		$collectionraw = $collection;
		
		$collection = str_replace('::', '/', $collection);
		
		$collection_path_parts = explode('/', $collection);
		
		//Are we including a widget
		if($collection_path_parts[0] == 'Widget') {
			$collection_path = Application::Path('controllers') . '/widgets/'. $collection_path_parts[1] . '/';
		}
		
		else {
		$collection_path = Application::Path('libraries');
	    }
	     
	     
		   if(is_dir($collection_path)) {
			   foreach (glob($collection_path . '*.php') as $filename) 
			   {
				  include_once($filename); 
				
			   }
		   }
		   
		    else {
				throw new DesmondModuleMissingException("Module Import failed - " . $collectionraw);
			 }
	}
	
	public function LoadSingle($file) {
		
		$fileraw = $file;


		
		//let's preserve the file ext
		$file = str_replace('.php' , '[php]', $file);
		
		$file = str_replace('::', '/', $file);
		
		//let's add the ext back to the file
		$file = str_replace('[php]', '.php' , $file);
		
		//are we importing a page
		$file_parts = explode('/', $file);

		if($file_parts[0] == 'Controller') {
			$file = str_replace('Controller/', '', $file);
			
			if(file_exists(Application::path('controllers') . $file)) {
				
				
				include_once(Application::path('controllers') . '/' . $file);
			}
			
			else {
				throw new DesmondModuleMissingException("Module Import failed - " . $fileraw);
			}
		}
		
		else {
			if(file_exists(Application::path('libraries') . $file)) {
				include_once(Application::path('libraries'). $file);
			}
			
			else {
				throw new DesmondModuleMissingException("Module Import failed - " . $fileraw);
			}
		}
	}
	
	public function LoadAll($namespace, $recursive=false) {
		
		$namespaceraw = $namespace;
		$namespace = str_replace('::*', '', $namespace);
		$namespace = str_replace('::', '/', $namespace);


	

		if($recursive == false) {   
		$dir = Application::path('libraries'). $namespace . '/';
		}
		
		else {
			$dir = $namespace;
		}
		
		if(is_dir($dir)) {
			 if ($handle = opendir($dir))
			{
				while (false !== ($dirItem = readdir($handle)))
				{
					$dirItem = $dir . $dirItem;

					if ($dirItem != $dir . '.' && $dirItem != $dir . '..' && is_file($dirItem)) 
					{
						$path = pathinfo($dirItem);

						 require_once($dirItem);
						
					  
					 } 
					elseif ($dirItem != $dir . '.' && $dirItem != $dir . '..' && is_dir($dirItem)) 
					{
						
						$this->LoadAll($dirItem, true);
					}
			   }
			   closedir($handle);
		 
			}
		}
		
		else {
			throw new DesmondModuleMissingException("Module Import failed - " . $namespaceraw);
		}
	}
}

?>
