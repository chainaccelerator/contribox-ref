<?php
declare(strict_types = 1);
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

class Html {

    public static string $vars = '';
    public static string $method = '';
    public static string $methodLinks = '';
    public static string $sdk = '';
    public static string $sdkLinks = '';
    public static string $type = '';
    public static string $typeLinks = '';
    public static string $dir = 'rendered';

    public static function confGen(string $t, stdClass $c):bool {
    
        if(is_dir(Html::$dir) === false) mkdir(Html::$dir);

        $f =  Html::$dir.DIRECTORY_SEPARATOR.$t.'.json';
        $c = json_encode($c, JSON_PRETTY_PRINT);

        file_put_contents($f, $c);


        return true;
    }
    public static function renderedParamTypeList(array $list, stdClass $conf): string{

      $html = '<p>';
      $html .= '<strong><span class="paramName">Paramètre</span> <span class="paramType">Type</span></strong><br>'; 

      foreach($list as $paramType => $a) {
        
        $html .= '<span class="paramName">'.$paramType.':</span> <span class="paramType"><a href="#'.$a.'">'.$a.'</a></span><br>'; 
      }
      $html .= '</p>';

      return $html;
    }
    public static function renderedTypeParamTypeList(array|stdClass $list, stdClass $conf): string{

      $html = '<p>';
      $html .= '<strong><span class="paramName">Paramètre</span> <span class="paramName">Type</span> <span class="paramType">Validation</span></strong><br>'; 

      foreach($list as $paramType => $a) {

        if($a === "") $o = 'string';
        elseif($a === 0) $o = 'int';
        if(isset(Type::$list->$paramType) === true) {
          
          $o = 'Object <a href="#'.$paramType.'">'.$paramType.'</a>';
        }
        else {

          if(isset($conf->typeControle->$paramType) === true ) {
            
            $o = json_encode($conf->typeControle->$paramType);
          }
          else {

            $o = gettype($a);
          }
        }        
        $html .= '<span class="paramName">'.$paramType.' </span> <span class="paramType">'.$o.' </span><br>'; 
      }
      $html .= '</p>';

      return $html;
    }    
    public static function renderedMethod(stdClass $conf):bool {

      $html = '';
       
      foreach(Method::$list as $m) {

          $html .= '<h3><a name="'.$m->name.'"></a>'.$m->name.'</h3>';
          $html .= '<p>'.$m->description.'</p>';
          $html .= '<h4>Request</h4>';
          $html .= '<code><pre class="prettyprint">'.json_encode($m->request, JSON_PRETTY_PRINT).'</pre></code>';
          $html .= '<br><button onclick="rpctry(\''.$m->name.'\', '.$m->name.')">Try</button><br>';
          $html .= '<h4>ParamList types:</h4>';
          $html .= Html::renderedParamTypeList($m->paramTypeList, $conf);
          $html .= '<h4>Response</h4>';
          $html .= '<code><pre class="prettyprint" id="'.$m->name.'">'.json_encode($m->response, JSON_PRETTY_PRINT).'</pre></code>';
          $html .= '<h4>Resource types:</h4>';
          $html .= Html::renderedParamTypeList($m->responseTypeList, $conf);
          Html::$methodLinks .= '<p><a href="#'.$m->name.'">'.$m->name.'</a></p>';

          Html::$vars .= 'var '.$m->name.' = \''.json_encode($m->request).'\''."\r\n";
      }
      Html::$method = $html;

      return true;
    }
    public static function renderedSdk(stdClass $conf):bool {

      $html = '';  
        
      foreach(Sdk::$list as $s) {

        $html .= '<h3><a name="'.$s->name.'"></a>'.$s->name.'</h3>';
        $html .= '<p>'.$s->description.'</p>';
        $html .= '<h4>Request</h4>';
        $html .= '<code><pre class="prettyprint">'.json_encode($s->request, JSON_PRETTY_PRINT).'</pre></code>';
        $html .= '<h4>ParamList types:</h4>';
        $html .= Html::renderedParamTypeList($s->paramTypeList, $conf);
        $html .= '<h4>Response</h4>';
        $html .= '<code><pre class="prettyprint">'.json_encode($s->response, JSON_PRETTY_PRINT).'</pre></code>';
        $html .= '<h4>Resource types:</h4>';
        $html .= Html::renderedParamTypeList($s->responseTypeList, $conf);
        Html::$sdkLinks .= '<p><a href="#'.$s->name.'">'.$s->name.'</a></p>';
      }
      Html::$sdk = $html;  

      return true;
    }
    public static function renderedType(stdClass $conf):bool {

      $html = '';  
       
      foreach(Type::$list as $t) {

        $html .= '<h3><a name="'.$t->name.'"></a>'.$t->name.'</h3>';
        $html .= '<p>'.$t->description.'</p>';
        $html .= '<h4>Param list</h4>';
        $html .= '<code><pre class="prettyprint">'.json_encode($t->paramList, JSON_PRETTY_PRINT).'</pre></code>';
        $html .= Html::renderedTypeParamTypeList($t->paramList, $conf);
        Html::$typeLinks .= '<p><a href="#'.$t->name.'">'.$t->name.'</a></p>';
      }
      Html::$type = $html;

      return true;
    }
    public static function rendered(stdClass $conf){

        Html::renderedMethod($conf);
        Html::renderedSdk($conf);
        Html::renderedType($conf);

        $c = file_get_contents('htmlTemplate/index.html');

        $c = str_replace('$vars', Html::$vars, $c);       

        $c = str_replace('$methodLinks', Html::$methodLinks, $c);
        $c = str_replace('$typeLinks', Html::$typeLinks, $c);
        $c = str_replace('$sdkLinks', Html::$sdkLinks, $c);

        $c = str_replace('$method', Html::$method, $c);
        $c = str_replace('$sdk', Html::$sdk, $c);
        $c = str_replace('$type', Html::$type, $c);

        file_put_contents(Html::$dir.'/index.html', $c);
        copy('htmlTemplate/index.css', Html::$dir.'/index.css');
    }
}
class Hash {

    public string $name = '';
    public string $description = '';
    public stdClass $paramList;
    
    public function __construct(string $name, stdClass $def) {

        $this->name = 'hash';
        $this->description = '';
        $this->paramList = new stdClass();
        $this->paramList->left = hash('sha256', $name);
        $this->paramList->right = hash('sha256', json_encode($def));
        $this->paramList->hash = hash('sha256', $this->paramList->left.$this->paramList->right);
        $this->paramList->data = '';
    }
}
class Type {
    
    public static stdClass $conf;
    public static stdClass $list;

    public string $name = '';
    public string $description = '';
    public stdClass $paramList;

    public function __construct(string $name, stdClass $def, string $description = '') {

        $this->name = $name;
        $this->description = $description;
        $this->paramList = $def;
        $hash = new Hash($name, $def);
        $this->paramList->hash = $hash->paramList;
        $this->paramList->state = "mock";
    }
    public static function confGen(stdClass $conf):bool {

        Type::$list = new stdClass();

        $alias = array();
        $alias = $conf->alias;

        foreach($conf->type as $name => $def) {

            if($name === 'hash') Type::$list->$name = new Hash("", $def);
            else {

                $obj = new Type($name, $def);

                foreach($obj->paramList as $k => $v) {

                    $d = $k;                     
                    $d = str_replace('List', '', $d);

                    if(strstr($k, 'Hash') !== false ) $d = 'hash';

                    if(isset($alias->$d) === true) $d = $alias->$d;

                    if(strstr($k, 'List') !== false ) {

                        if(isset(Type::$list->$d) === true) $obj->paramList->$k[0] = Type::$list->$d->paramList;
                    }
                    else {

                        if(isset(Type::$list->$d) === true) $obj->paramList->$k = Type::$list->$d->paramList;
                    }
                }
                switch ($name) {
                    
                    case 'timestamp':

                            $conf->signature->timeout = $obj->paramList;
                            break;
                    case 'xPub':

                            foreach($conf->signature->userRoleList as $k => $v) {
                                
                                $conf->signature->userRoleList->$k->name = $k;
                                $conf->signature->userRoleList->$k->hash = Type::$list->hash->paramList;
                                $conf->signature->userRoleList->$k->xPubList = array();
                                $conf->signature->userRoleList->$k->xPubList[0] = $obj->paramList;
                                $conf->signature->userRoleList->$k->xPubYesList = array();
                                $conf->signature->userRoleList->$k->xPubYesList[0] = $obj->paramList;
                                $conf->signature->userRoleList->$k->xPubNoList = array();
                                $conf->signature->userRoleList->$k->xPubNoList[0] = $obj->paramList;
                            }
                        break;
                    case 'proof':
                            
                            $obj->paramList->userRole = $conf->signature->userRoleList->default;
                            $obj->paramList->groupRole = $conf->signature->groupRoleList->default;
                            $obj->paramList->actionRole = $conf->signature->roleActionList->default;
                        break;
                    case 'boarding':

                            $obj->paramList->userRole = $conf->signature->userRoleList->default;
                            $obj->paramList->groupRole = $conf->signature->groupRoleList->default;
                            $obj->paramList->actionRole = $conf->signature->roleActionList->default;
                        break;
                    case 'template':
                            
                            $obj->paramList->userRole = $conf->signature->userRoleList->default;
                            $obj->paramList->groupRole = $conf->signature->groupRoleList->default;
                            $obj->paramList->actionRole = $conf->signature->roleActionList->default;
                        break;
                    case 'cosig':

                            $obj->paramList->xPubS = Type::$list->xPub->paramList;
                            $obj->paramList->txId = Type::$list->txId->paramList;
                            $obj->paramList->txIssueAsset = Type::$list->txId->paramList;
                            $obj->paramList->txUtxo = Type::$list->utxo->paramList;
                            $obj->paramList->script = Type::$list->script->paramList;
                        break;
                    case 'sig':
                    
                            /*
                            $obj->paramList->voutSigList = array();
                            $obj->paramList->voutSigList[0] = Type::$list->cosig->paramList;                            
                            */
                        break;
                    case 'response':

                            $obj->paramList->resource = new stdClass();
                        break;
                }
                Type::$list->$name = $obj;
            }
        }    
        Html::confGen(get_called_class(), Type::$list);

        return true;
    }
}

class Method {

    public static string $jsonRoot = 'rpc';

    public static stdClass $conf;
    public static stdClass $list;

    public string $name = '';
    public string $description = ''; 
    public stdClass $request;
    public stdClass $response;
    public array $paramTypeList = array();
    public array $responseTypeList = array();

    public function __construct(string $name, stdClass $def, string $type, string $description = ''){

        $this->name = $name;
        $this->description = $description;
        $hash = new Hash($name, $def);

        $this->request = Type::$list->request->paramList;
        $this->request->methodHash = $hash->paramList;
        $this->request->method = $name;
        $this->request->paramList = new stdClass();
        $this->request->reason->state = "mock";
        $this->request->reason->type = $type;

        $this->response = Type::$list->response->paramList;
        $this->response->methodHash = $hash->paramList;
        $this->response->reason->state = "mock";
        $this->response->reason->type = $type;
    }
    public static function confGenParamList(Method|Sdk $obj, string $way, array $paramList, string $attribute, string $listName, string $alias): Method|Sdk {

        $obj->$way->$attribute->$listName[0] = new stdClass();
        $element = Type::$list->$alias->paramList;

        foreach($paramList as $attributeName) {
                                        
            $obj->$way->$attribute->$listName[0]->$attributeName = $element->$attributeName;
        }
        return $obj;
    }
    public static function confGenParam(Method|Sdk $obj, string $way, array $paramList, string $attribute, string $listName, string $alias): Method|Sdk {

        $obj->$way->$attribute->$listName = new stdClass();
        $element = Type::$list->$alias->paramList;

        foreach($paramList as $attributeName) {
                                        
            $obj->$way->$attribute->$listName->$attributeName = $element->$attributeName;
        }
        return $obj;
    }
    public static function aliasSet(string $paramType, stdClass  $conf): string{

        $alias = $conf->rpc->alias;
        $a = $paramType;

        if(strstr($paramType, 'Hash') !== false ) $a = 'hash';

        if(isset($alias->$paramType) === true)  $a = $alias->$paramType;

        $a = str_replace('List', '', $a);

        return $a;
    }
    public static function funcSet(string $paramType): string {

        $func = 'confGenParam';

        if(strstr($paramType, 'List') !== false) $func = 'confGenParamList';

        return $func;
    }
    public static function confGen(stdClass $conf):bool {

        Method::$list = new stdClass();

        foreach($conf->rpc->method as $type => $method) {

            switch ($type) {
                case 'authentification':
                    $t = '';
                    break;
                case 'key':
                    $t = 'share|backup|lock';
                    break;
                case 'boarding':
                    $t = 'default';
                    break;
                case 'contribution':
                    $t = 'default';
                    break;
                case 'public':
                    $t = 'default';
                    break;
                case 'peer':
                    $t = 'default';
                    break;
            }
            foreach($method as $name => $def) {

                $obj = new Method($name, $def, $t);

                foreach($def->paramList as $paramType => $attributeList) {

                    $a = Method::aliasSet($paramType, $conf);
                    $func = Method::funcSet($paramType);
                    $obj->paramTypeList[$paramType] = $a;
                    $obj = Method::$func($obj, 'request', $attributeList, 'paramList', $paramType, $a);
                }
                foreach($def->reason as $paramType => $attributeList) {
                 
                    $a = Method::aliasSet($paramType, $conf);
                    $func = Method::funcSet($paramType);                    
                    $obj->responseTypeList[$paramType] = $a;
                    $obj = Method::$func($obj, 'response', $attributeList, 'resource', $paramType, $a);
                }
                Method::$list->$name = $obj;
            }
        }
        Html::confGen('Method', Method::$list);

        return true;
    }
}    

class Sdk extends Method {

  public static string $jsonRoot = 'sdk';

  public static stdClass $conf;
  public static stdClass $list;

  public string $methodHash = '';

  public function __construct(string $name, stdClass $def, string $type, string $description = ''){

    $this->name = $name;
    $this->description = $description;
    $hash = new Hash($name, $def);
    $this->methodHash = $hash->paramList->hash;

    $this->request = new stdClass();
    $this->response = new stdClass();
  }
  public static function confGenParamList(Method|Sdk $obj, string $way, array $paramList, string $attribute, string $listName, string $alias): Method|Sdk {

      $obj->$way->$listName[0] = new stdClass();
      $element = Type::$list->$alias->paramList;

      foreach($paramList as $attributeName) {
                                      
          $obj->$way->$listName[0]->$attributeName = $element->$attributeName;
      }
      return $obj;
  }
  public static function confGenParam(Method|Sdk $obj, string $way, array $paramList, string $attribute, string $listName, string $alias): Method|Sdk {

      $obj->$way->$listName = new stdClass();
      $element = Type::$list->$alias->paramList;

      foreach($paramList as $attributeName) {
                                      
          $obj->$way->$listName->$attributeName = $element->$attributeName;
      }
      return $obj;
  }
  public static function aliasSet(string $paramType, stdClass  $conf): string{

      $alias = $conf->sdk->alias;
      $a = $paramType;

      if(strstr($paramType, 'Hash') !== false ) $a = 'hash';

      if(isset($alias->$paramType) === true)  $a = $alias->$paramType;

      $a = str_replace('List', '', $a);

      return $a;
  }
  public static function funcSet(string $paramType): string {

      $func = 'confGenParam';

      if(strstr($paramType, 'List') !== false) $func = 'confGenParamList';

      return $func;
  }
  public static function confGen(stdClass $conf):bool {

      Sdk::$list = new stdClass();

      foreach($conf->sdk->method as $type => $method) {

          switch ($type ) {
              case 'authentification':
                  $t = '';
                  break;
              case 'key':
                  $t = 'share|backup|lock';
                  break;
              case 'boarding':
                  $t = 'default';
                  break;
              case 'contribution':
                  $t = 'default';
                  break;
              case 'public':
                  $t = 'default';
                  break;
              case 'peer':
                  $t = 'default';
                  break;
          }
          foreach($method as $name => $def) {

              $obj = new Sdk($name, $def, $t);

              foreach($def->paramList as $paramType => $attributeList) {

                  $a = Sdk::aliasSet($paramType, $conf);
                  $func = Sdk::funcSet($paramType);
                  $obj->paramTypeList[$paramType] = $a;
                  $obj = Sdk::$func($obj, 'request', $attributeList, 'paramList', $paramType, $a);
              }
              foreach($def->reason as $paramType => $attributeList) {
               
                  $a = Sdk::aliasSet($paramType, $conf);
                  $func = Sdk::funcSet($paramType);                    
                  $obj->responseTypeList[$paramType] = $a;
                  $obj = Sdk::$func($obj, 'response', $attributeList, 'resource', $paramType, $a);
              }
              Sdk::$list->$name = $obj;
          }
      }
      Html::confGen('Sdk', Sdk::$list);

      return true;
  }
}
$input = file_get_contents('php://input');

if (strlen($input) > 0 && count($_POST) == 0 || count($_POST) > 0)  {

  $request = json_decode($input);
  $m = $request->method;

  $responses = json_decode(file_get_contents(Html::$dir.'/Method.json'));
  $r = $responses->$m->response;

  header('Content-Type: application/json');
  echo json_encode($r, JSON_PRETTY_PRINT);
  exit();
}
$conf = json_decode(file_get_contents('conf.json'));

Type::confGen($conf);
Method::confGen($conf);
Sdk::confGen($conf);
Html::rendered($conf);

echo '<a href="'.Html::$dir.'/index.html" target="_blank">'.Html::$dir.'/index.html</a><br>';
echo '<a href="'.Html::$dir.'/Method.json" target="_blank">'.Html::$dir.'/Method.json</a><br>';
echo '<a href="'.Html::$dir.'/Sdk.json" target="_blank">'.Html::$dir.'/Sdk.json</a><br>';
echo '<a href="'.Html::$dir.'/Type.json" target="_blank">'.Html::$dir.'/Type.json</a><br>';
