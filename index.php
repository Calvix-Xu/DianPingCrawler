<?php

ini_set('max_execution_time', '0');


//dealthAll();
//dealthList("http://localhost/crawler/list.html");

//getNodeData("http://localhost/crawler/test1.html");

//1、
getList();

//dealthList("http://localhost/crawler/list/list1.html");

//2、
//getALLShops();
//getNodeData("http://localhost/crawler/shop/shop1.html");

//3、
//dealthAll();
//$dom = getDom("http://www.dianping.com/shop/9073397");
//print_r($dom->saveHTML());
function getList() {
    //北京
//    $baseUrl="http://www.dianping.com/search/category/2/55/g167p";  //50
    //深圳 http://www.dianping.com/search/category/7/55/g167p1
    $baseUrl="http://www.dianping.com/search/category/7/55/g167p";  //17
    for ($i=1;$i<=17;$i++){
        $url=$baseUrl.$i;
        echo $url."</br>";
        $dom = getDom($url);
        $data=$dom->saveHTML();
        writeToFile("./shenzhen/list/list".$i.".html", $data);
        echo "成功保存 ".$url."</br>";
    }
}

function getALLShops() {
    $baseUrl="http://localhost:8080/crawler/shenzhen/list/list";
    $j=1;
    for ($i=1;$i<=17;$i++){
        $url=$baseUrl.$i.".html";
        print_r($url."</br>");
        $dom = getDom($url);
        print_r($dom->saveHTML());
        if (!$dom) {
            return;
        }
        $spans = $dom->getElementsByTagName("a");
//    print_r($spans);
        foreach ($spans as $span) {
            if ($span->hasAttributes())
            {
                $attrs = $span->attributes;
                foreach ($attrs as $attr){
                    if ($attr->name=="class" && $attr->value=="shopname"){
//                        if ($i<3) {
//                            ++$j;
//                            break;
//                        }
                        $result=$attrs->getNamedItem("href")->nodeValue;
                        print_r($result."</br>");
                        $shopUrl="http://www.dianping.com".$result;
                        print_r($shopUrl."</br>");
                        $dom=getDom($shopUrl);
                        $content=$dom->saveHTML();
                        writeToFile("./shenzhen/shop/shop".$j.".html", $content);
                        ++$j;
                        sleep(3);
                        break;
                    }
                }
//                $result[$attr->name] = $attr->value;
            }

        }
    }

}

function writeToFile($fileName, $data) {
    $file = fopen($fileName, "w") or die("打开文件失败！！");
    fwrite($file, $data);
    fclose($file);
}

function dealSeap($string) {
    $name=str_replace('(', '1', $string);
    $name=str_replace(')', '', $string);
    $name=str_replace(',', '', $string);
    $name=str_replace(';', '', $string);
    $name=str_replace(' ', '', $string);
//    echo $name."</br>";
    return $name;
}

function dealthAll() {
    $con = new mysqli("localhost","calvix","123456", "shop", "3306");
    if ($con->connect_error)
    {
        die('Could not connect: ' . mysqli_error());
    }
    for ($i=1;$i<=746;$i++){
        $url="http://localhost:8080/crawler/shopl/shop".$i.".html";
//        $url="http://localhost/crawler/shopp/shop119".".html";
//        echo $url."</br>";
        $data=getNodeData($url);
//        print_r($data);
        if ($data==null) {
            echo "获取数据失败 ".$url."</br>";
            continue;
        }
        if ($data->name=="403 Forbidden") {
            echo "403 Forbidden ".$url."</br>";
            continue;
        }
        if ($data->name=="") {
            echo "名字为空".$url."</br>";
            continue;
        }
//    print_r($data);
        $name=dealSeap($data->name);
        $address=dealSeap($data->address);
        $phone=dealSeap($data->phone);
        $cost=dealSeap($data->cost);
        $time=dealSeap($data->time);
        $sql="INSERT INTO shop (name, address, phone, cost, time)
                    VALUES (\"$name\", \"$address\", \"$phone\", \"$cost\", \"$time\")";
//    echo $sql;
        $result = $con->query($sql);
//        echo "++++++++++".$i."++++++++++++";
        if (!$result){
//            echo "fail------->".$i."</br>";
        }else {
//            echo $sql."</br>";
        }
//    print_r($result);
//        return;
    }
    echo "end----------------------------</br>";
    $con->close();
}

function dealthList($url) {
    $dom = getDom($url);
    if (!$dom) {
        return;
    }
    $spans = $dom->getElementsByTagName("a");
//    print_r($spans);
    foreach ($spans as $span) {
        if ($span->hasAttributes())
        {
            $attrs = $span->attributes;
            foreach ($attrs as $attr){
                if ($attr->name=="class" && $attr->value=="shopname"){
                    $result=$attrs->getNamedItem("href")->nodeValue;
                    print_r($result."</br>");
//                    $data=getNodeData("http://www.dianping.com".$result);

                }
            }
//                $result[$attr->name] = $attr->value;
        }

    }
}


function getDom($url) {
    $ch = curl_init();
//$url http://www.dianping.com/shop/4269436
// 设置 URL 和相应的选项 http://www.dianping.com/shop/21372207
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

    curl_setopt($ch, CURLOPT_COOKIE, "JSESSIONID=082AD71FA7702F39B99C1C63B6746070; wed_user_path=167|0; QRCodeBottomSlide=hasShown; _lxsdk_s=e1319cd1ddd4591571d63912a367%7C%7C50; Hm_lpvt_dbeeb675516927da776beeb1d9802bd4=1509034054; Hm_lvt_dbeeb675516927da776beeb1d9802bd4=1508930968; aburl=1; cy=7; cye=shenzhen; s_ViewType=10; __mta=242556221.1508930970959.1509033118942.1509033118947.16; _lxsdk_cuid=15f59606808c8-0772c002a84983-1c401429-fa000-15f59606808c8; wedchatguest=g-142937695730969955; _hc.v=b5f48209-73f5-dc3b-87da-264d2088b30b.1508930968; _lxsdk=15f534b5a64c8-0cacd2c013b8af-1c401429-fa000-15f534b5a64c8");
//    curl_setopt($ch, CURLOPT_COOKIE, "_hc.v=79ce45f1 - e1ba - 4fa2 - a0c0 - 78241671659a.1508984522; wedchatguest=g-191347814789017673; __utma=1.1110244649.1509003856.1509003856.1509003856.1; __utmc=1; __utmz=1.1509003856.1.1.utmcsr=(direct)|utmccn=(direct)|utmcmd=(none); Hm_lvt_dbeeb675516927da776beeb1d9802bd4=1508989015; Hm_lpvt_dbeeb675516927da776beeb1d9802bd4=1509016661; wed_user_path=27943|0; JSESSIONID=6FB314A7A34E6FF2CAD13C69175C1B5C; aburl=1; cy=2; cye=beijing");
        curl_setopt($ch, CURLOPT_ENCODING, "text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8");
        //Mozilla/1.0 (Windows NT 6.1) AppleWebKit/536.11 (KHTML, like Gecko) Chrome/20.0.1132.47 Safari/536.11
    $rad = rand(1, 100000000);
//    echo "rand:".$rad."</br>";
//    curl_setopt($ch, CURLOPT_USERAGENT, $rad);
    curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/1.0 (Windows NT 6.1) AppleWebKit/536.11 (KHTML, like Gecko) Chrome/20.0.1132.47 Safari/536.11");
// 抓取 URL 并把它传递给浏览器
    $out = curl_exec($ch);
// echo $out;
// print_r($out);
// 关闭 cURL 资源，并且释放系统资源
    curl_close($ch);

//print_r($out);

    $dom = new DOMDocument();
    $dom->loadHTML($out);

    return $dom;
}

function getNodeData($url) {
//    $url="http://www.dianping.com/shop/9073397";
    $dom=getDom($url);
    if (!$dom) {
        return;
    }

//    print_r($dom->saveHTML());
//    print_r($dom);
    $nodeData = new NodeData();
    $nodeData->name = getName($dom);
    $nodeData->address = getAddress($dom);
    $nodeData->phone = getPhone($dom);
    $nodeData->cost = getPerPersonPay($dom);
    $nodeData->time = getWorkTime($dom);
//    print_r($nodeData);
    return $nodeData;
}


function getName($dom) {
    $h1 = $dom->getElementsByTagName("h1");
//    print_r($h1[0]);
//    $w = $h1[0]->saveHTML();
//    echo  $w;
    $name = $h1[0]->nodeValue;
//    print_r($name);
    return $name;
}

function getAddress($dom) {
    $spans = $dom->getElementsByTagName("span");
//    print_r($spans);
    foreach ($spans as $span) {
        if ($span->hasAttributes())
        {
            $attrs = $span->attributes;
            foreach ($attrs as $attr){
                if ($attr->name=="class" && $attr->value=="fl road-addr"){
                    $result=$span->nodeValue;
//                    print_r($result."\n");
                    return $result;
                    break;
                }
            }
//                $result[$attr->name] = $attr->value;
        }

    }

    //第二种网页
    $spans = $dom->getElementsByTagName("span");
//    print_r($spans);
    foreach ($spans as $span) {
        if ($span->hasAttributes())
        {
            $attrs = $span->attributes;
            foreach ($attrs as $attr){
                if ($attr->name=="itemprop" && $attr->value=="street-address"){
                    $result=$span->nodeValue;
//                    print_r($result."\n");
                    return $result;
                    break;
                }
            }
//                $result[$attr->name] = $attr->value;
        }

    }
}

function getPhone($dom) {
    $spans = $dom->getElementsByTagName("span");
//    print_r($spans);
    foreach ($spans as $span) {
        if ($span->hasAttributes())
        {
            $attrs = $span->attributes;
            foreach ($attrs as $attr){
                if ($attr->name=="class" && $attr->value=="icon-phone"){
                    $result=$span->nodeValue;
//                    print_r($result);
                    return $result;
                    break;
                }
            }
//                $result[$attr->name] = $attr->value;
        }

    }

    //第二种网页
    $span = $dom->getElementById("J-showPhoneNumber");
    if (!$span) {
        return;
    }
    if ($span->hasAttributes())
    {
        $attrs = $span->attributes;
        foreach ($attrs as $attr){
            if ($attr->name=="data-real"){
                $result=$attr->value;
//                print_r($result);
                return $result;
                break;
            }
        }
//                $result[$attr->name] = $attr->value;
    }
}

function getPerPersonPay($dom) {
    $spans = $dom->getElementsByTagName("em");
//    print_r($spans);
    foreach ($spans as $span) {
        if ($span->hasAttributes())
        {
            $attrs = $span->attributes;
            foreach ($attrs as $attr){
                if ($attr->name=="class" && $attr->value=="average"){
                    $result=$span->nodeValue;
                    $result2=substr($result, 9, strlen($result)-6);
//                    print_r($result2."\n");
                    return $result2;
                }
            }
//                $result[$attr->name] = $attr->value;
        }

    }
//    echo "1231";
    //第二种网页
    $spans = $dom->getElementsByTagName("span");
//    print_r($spans);
    foreach ($spans as $span) {
        if ($span->hasAttributes())
        {
            $attrs = $span->attributes;
            foreach ($attrs as $attr){
                if ($attr->name=="class" && $attr->value=="Price"){
                    $result=$span->parentNode->nodeValue;
                    print_r($result."\n");
                    return $result;
                    break;
                }
            }
//                $result[$attr->name] = $attr->value;
        }

    }
}

function getWorkTime($dom) {
//    echo "工作时间 ";
    $tbody = $dom->getElementsByTagName("tbody");
//    print_r($tbody);
//    echo  count($tbody)."\n";
//    print_r($tbody);
    foreach ($tbody as $body) {
//        echo "2222";
        $trs = $body->getElementsByTagName("tr");
//        echo "2222";
//    print_r($trs);
        if ($trs->length >= 4) {
            $spans = $trs[3]->getElementsByTagName("div");
//    print_r($spans);
            foreach ($spans as $span) {
                if ($span->hasAttributes()) {
//                echo "122222222";
                    $attrs = $span->attributes;
                    foreach ($attrs as $attr) {
                        if ($attr->name == "class" && $attr->value == "cont") {
//                        echo "工作时间 第二种网页123122222222";
                            $result = $span->nodeValue;
//                        print_r($result);
                            return $result;
                            break;
                        }
                    }
//                $result[$attr->name] = $attr->value;
                }

            }
        }
    }


    //第二种网页
//    echo "工作时间 第二种网页";
    $spans = $dom->getElementsByTagName("dd");
//    print_r($spans);
    foreach ($spans as $span) {
        if ($span->hasAttributes())
        {
            $attrs = $span->attributes;
            foreach ($attrs as $attr){
                if ($attr->name=="data-info-type" && $attr->value=="bh"){
                    if ($span->childNodes->length >= 1) {
//                        print_r($span->childNodes);
                        $result=$span->childNodes[1]->nodeValue;
//                        print_r($result);
                        return $result;
                    }
                    return;
                }
            }
//                $result[$attr->name] = $attr->value;
        }

    }
}

class NodeData {
    public $name="";
    public $address="";
    public $phone="";
    public $time="";
    public $cost="";
}

?>