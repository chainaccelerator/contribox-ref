<?php
declare(strict_types = 1);
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

class Html {

    public static string $method = '';
    public static string $methodLinks = '';
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
    public static function rendered(){

        $html = '';
       
        foreach(Method::$list as $m) {

            $html .= '<h3><a name="'.$m->name.'"></a>'.$m->name.'</h3>';
            $html .= '<p>'.$m->description.'</p>';
            $html .= '<h4>Request</h4>';
            $html .= '<code><pre class="prettyprint">'.json_encode($m->request, JSON_PRETTY_PRINT).'</pre></code>';
            $html .= '<h4>ParamList types:</h4>';
            $html .= '<p>';
            $html .= '<strong><span class="paramName">Nom</span> <span class="paramType">Type</span></strong><br>'; 

            foreach($m->paramTypeList as $paramType => $a) {
              
              $html .= '<span class="paramName">'.$paramType.':</span> <span class="paramType"><a href="#'.$a.'">'.$a.'</a></span><br>'; 
            }
            $html .= '</p>';
            $html .= '<h4>Response</h4>';
            $html .= '<code><pre class="prettyprint">'.json_encode($m->request, JSON_PRETTY_PRINT).'</pre></code>';

            self::$methodLinks .= '<p><a href="#'.$m->name.'">'.$m->name.'</a></p>';
        }
        Html::$method = $html;
        
       
        foreach(Type::$list as $t) {

            $html .= '<h3><a name="'.$t->name.'"></a>'.$t->name.'</h3>';
            $html .= '<p>'.$t->description.'</p>';
            $html .= '<h4>Param list</h4>';
            $html .= '<code><pre>'.json_encode($t->paramList, JSON_PRETTY_PRINT).'</pre></code>';

            self::$methodLinks .= '<p><a href="#'.$t->name.'">'.$t->name.'</a></p>';
        }
        Html::$type = $html;
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

    public static stdClass $conf;
    public static stdClass $list;

    public string $name = '';
    public string $description = ''; 
    public stdClass $request;
    public stdClass $response;
    public array $paramTypeList = array();

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
    public static function confGenParamList(Method $obj, string $way, array $paramList, string $attribute, string $listName, string $alias): Method {

        $obj->$way->$attribute->$listName[0] = new stdClass();
        $element = Type::$list->$alias->paramList;

        foreach($paramList as $attributeName) {
                                        
            $obj->$way->$attribute->$listName[0]->$attributeName = $element->$attributeName;
        }
        return $obj;
    }
    public static function confGenParam(Method $obj, string $way, array $paramList, string $attribute, string $listName, string $alias): Method {

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

                $obj = new Method($name, $def, $t);

                foreach($def->paramList as $paramType => $attributeList) {

                    $a = self::aliasSet($paramType, $conf);
                    $func = self::funcSet($paramType);

                    $obj->paramTypeList[$paramType] = $a;
                    $obj = Method::$func($obj, 'request', $attributeList, 'paramList', $paramType, $a);
                }
                foreach($def->reason as $paramType => $attributeList) {
                 
                    $a = self::aliasSet($paramType, $conf);
                    $func = self::funcSet($paramType);
                    $obj = Method::$func($obj, 'response', $attributeList, 'resource', $paramType, $a);
                }
                Method::$list->$name = $obj;
            }
        }
        Html::confGen(get_called_class(), Method::$list);

        return true;
    }
}    
$conf = json_decode(file_get_contents("conf.json"));

Type::confGen($conf);
Method::confGen($conf);
Html::rendered();

$c = '<!DOCTYPE HTML>
<html>
  <head>
    <title>Title of the document</title>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <script src="https://cdn.jsdelivr.net/gh/google/code-prettify@master/loader/run_prettify.js?lang=json&skin=sunburst"></script>
    <style>
      html,
      body {
        height: 100%;
      }
      body {
        display: flex;
        flex-wrap: wrap;
        margin: 0;
      }
      .header-menu,
      footer {
        display: flex;
        align-items: center;
        width: 100%;
      }
      .header-menu {
        justify-content: flex-end;
        height: 60px;
        background: #1c87c9;
        color: #fff;
      }
      h2 {
        margin: 0 0 8px;
      }
      ul li {
        display: inline-block;
        padding: 0 10px;
        list-style: none;
      }
      aside {
        flex: 0.4;
        height: 165px;
        padding-left: 15px;
        border-left: 1px solid #666;
      }
      section {
        flex: 1;
        padding-right: 15px;
      }
      footer {
        padding: 0 10px;
        background: #ccc;
      }
      pre {
        height: 300px;
        overflow: scroll;
       } 
       .paramtype {
       } 
       .paramName {
         display: inline-block;
        width: 200px;
       }
    </style>
  </head>
  <body>
    <header class="header-menu">
      <nav>
        <ul>
          <li>Home</li>
          <li></li>
          <li></li>
        </ul>
      </nav>
    </header>
    <section>
      <article>
        <header>
            <h2>Documentation Contribox</h2>
            <p><a href="https://github.com/chainaccelerator/contribox-ref">Rendered with : https://github.com/chainaccelerator/contribox-ref</a></p>
            <p><a href="rendered/Method.json" target="_blank">Download Method.json</a></p>
            <p><a href="rendered/Types.json" target="_blank">Download Type.json</a></p>
        </header>
      </article>
      <article>
        <header>
          <h2><a name="method"><a>Methods</h2>
        </header>        
        '.Html::$method.'
      </article>      
      <article>
        <header>
          <h2><a name="type"><a>Types</h2>
        </header>        
        '. Html::$type.'
      </article>
    </section>
    <aside>
      <h2>Shortcuts</h2>
      <p><strong><a href="#method">Method</a></strong></p>
      '.Html::$methodLinks.'
      <p><strong><a href="#type">Types</a></strong></p>
      '.Html::$typeLinks.'
    </aside>
    <footer>
      <small>Company © Chain Accelerator. All rights reserved.</small>
    </footer>
  </body>
</html>';

file_put_contents(Html::$dir.'/index.html', $c);

echo '<a href="'.Html::$dir.'/index.html" targert="_blank">'.Html::$dir.'/index.html</a><br>';
echo '<a href="'.Html::$dir.'/Type.json" targert="_blank">'.Html::$dir.'/Type.json</a><br>';
echo '<a href="'.Html::$dir.'/Method.json" targert="_blank">'.Html::$dir.'/Method.json</a><br>';
