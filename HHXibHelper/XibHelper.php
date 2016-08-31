<?php
    include('simple_html_dom.php');
    
    function radom_char() {
        $str = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz";
        return $str[mt_rand(0,strlen($str)-1)];
    }
    function radom_id() {
        return radom_char().radom_char().radom_char()."-".radom_char().radom_char()."-".radom_char().radom_char().radom_char();
    }
    function transClass($cls) {
        return "UI".ucfirst($cls);
    }
    function insertAfterTarget($filePath, $insertCont, $target) {
        $result = null;
        $fileCont = file_get_contents($filePath);
        $targetIndex = strpos($fileCont, $target); #查找目标字符串的坐标
        if ($targetIndex !== false) {
            #找到target的后一个换行符
            $chLineIndex = strpos(substr($fileCont, $targetIndex), "\n") + $targetIndex;
            if ($chLineIndex !== false) {
                #插入需要插入的内容
                $result = substr($fileCont, 0, $chLineIndex + 1) . $insertCont . "\n" . substr($fileCont, $chLineIndex + 1);
                $fp = fopen($filePath, "w+");
                fwrite($fp, $result);
                fclose($fp);
            }
        }
    }
    function insertBeforeTarget($filePath, $insertCont, $target) {
        $result = null;
        $fileCont = file_get_contents($filePath);
        $targetIndex = strpos($fileCont, $target); #查找目标字符串的坐标
        if ($targetIndex !== false) {
            $chLineIndex = strripos(substr($fileCont, 0, $targetIndex), "\n");
            if ($chLineIndex !== false) {
                $result = substr($fileCont, 0, $chLineIndex + 1) . $insertCont . "\n" . substr($fileCont, $chLineIndex + 1);
                $fp = fopen($filePath, "w+");
                fwrite($fp, $result);
                fclose($fp);
            }
        }
    }
    
    $classPath = $argv[1];
    $projPath = substr($classPath, 0, strripos($classPath, "/") + 1);
    $className = substr($classPath, strlen($projPath));
    $className = substr($className, 0, strripos($className, "."));
    
    
    $xibPath = $projPath.$className.'.xib';
    $mPath = $projPath.$className.'.m';
    
    $html = file_get_html($xibPath);
    if(!$html){
        die;
    }
    $connections = $html->find('connections');
    //如果没有拖过线
    if(!$connections){
        $obj = $html->find('[customClass='.$className.']');
        $obj[0]->innertext = $obj[0]->innertext.'<connections></connections>';
        $html->save($xibPath);
        $html = file_get_html($xibPath);
        $connections = $html->find('connections');
    }
    
    
    $views = $html->find('[userLabel]');
    $outlets = Array();
    foreach($views as $i=>$view) {
        $attributes = simplexml_load_string($view)->attributes();
        if(strlen($attributes['id']) < 10) continue;
        
        $outlets[$i]['destination'] = $attributes['id'];
        $outlets[$i]['property'] = $attributes['userLabel'];
        $outlets[$i]['cls'] = $view->tag;
    }
    
    
    foreach($outlets as $outlet) {
        
        $destination = $outlet['destination'];
        $property = $outlet['property'];
        $id = radom_id();
        
        //过滤重复
        if($html->find('outlet[destination='.$destination.']')){
            continue;
        }
        
        $newconne = "<outlet property=\"".$property."\" destination=\"".$destination."\" id=\"".$id."\"/>";
        $connections[0]->innertext = $connections[0]->innertext.$newconne;
    }
    
    $html->save($xibPath);
    
    
    //对应.m文件增加property
    $fileLines = array();
    $fp = fopen($mPath, "r");
    
    $interfaceIndex;
    $implementationIndex;
    
    $is_need_interface;
    while(!feof($fp))
    {
        $line = fgets($fp);
        array_push($fileLines, $line);
        
        if(strpos($line, 'interface '.$className.'()')){
            $propertyIndex = ftell($fp);
        }
        if(strpos($line, 'implementation '.$className)){
            $implementationIndex = ftell($fp);;
        }
        
        //缺少类定义
        if($implementationIndex && !$propertyIndex){
            $is_need_interface = 1;
        }
        
        //判断如果存在property则从数组中移除outlet
        if($propertyIndex && !$implementationIndex){
            foreach($outlets as $i=>$outlet) {
                $property = $outlet['property'].'';
                if(strlen($property) && strpos($line, $property)){
                    $outlets[$i]['property'] = '';
                }
            }
        }
    }
    
    
    if($is_need_interface){
        $str = '@interface '.$className.'()'."\n"."\n".'@end'."\n";
        insertBeforeTarget($mPath, $str, "implementation ".$className);
    }
    
    
    foreach(array_reverse($outlets) as $i=>$outlet) {
        $property = $outlet['property'].'';
        if(strlen($property) ){
            $class = transClass($outlet['cls'].'');
            $str = '@property (nonatomic, weak) IBOutlet '.$class.' *'.$property.';';
            insertAfterTarget($mPath, $str, "interface ".$className);
        }
    }
    
    fclose($fp);
    
    ?>
