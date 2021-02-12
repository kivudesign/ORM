<?php
    session_start();
    
    require_once 'global.php';
    //
    $GLOBALS['config']=array(
        'mysql'=>array(
            'host'=> HOST,
            'username'=> USER,
            'password'=> PASSWORD,
            'db'=> DATABASE
        ),
        'remender'=>array(),
        'session'=>array(
            "token_name"=>"token"
        )
    );
    
    function getSubDirectories($dir)    {
        $subDir = array();
        $directories = array_filter(glob($dir), 'is_dir');
        $subDir = array_merge($subDir, $directories);
        foreach ($directories as $directory) $subDir = array_merge($subDir, getSubDirectories($directory . '/*'));
        return $subDir;
    }
    /**
     * implement autoload module without composer
     */
    // will load all class from the class folder
    spl_autoload_register(function($className){
        $check_NameSpace_separator=explode("\\",$className); // explode to get all namespace defined, if there is
        $len=count($check_NameSpace_separator);//count how much namescpace exist  
        $n_class= $check_NameSpace_separator[$len-1]; // get the last name with is the reel class name
        $class=str_replace("\\", DIRECTORY_SEPARATOR, $n_class);
        $dirs = getSubDirectories("class");
        foreach($dirs as $dir){
            $file= $dir."/".str_replace('\\', '/',$class).".php";
            if (is_file($file)) { // check if the file exist
                include_once($file); // incluse the file request if it exist
            }
        }
    });