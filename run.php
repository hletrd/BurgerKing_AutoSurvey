<?php
$c = curl_init();
curl_setopt($c, CURLOPT_RETURNTRANSFER, TRUE);
curl_setopt($c, CURLOPT_USERAGENT, 'AutoBurgerking');
curl_setopt($c, CURLOPT_SSL_VERIFYPEER, FALSE);
curl_setopt($c, CURLOPT_FOLLOWLOCATION, TRUE);
unlink('cookie');
curl_setopt($c, CURLOPT_COOKIESESSION, true);
curl_setopt($c, CURLOPT_COOKIEFILE, 'cookie');
curl_setopt($c, CURLOPT_COOKIEJAR, 'cookie');
$auto = false;
if ($_GET['code'] == '') {
	$_GET['code'] = rand(1000000000000000, 9999999999999999);
	$auto = true;
}

curl_setopt($c, CURLOPT_URL, 'https://kor.tellburgerking.com/?AspxAutoDetectCookieSupport=1');
$data = curl_exec($c);

$post = '';
for($i = 0; $i < 6; $i++) {
	$post .= 'CN' . ($i+1) . '=' . substr($_GET['code'], $i*3, 3) . '&';
}

$print = true;
for($j = 0; $j < 100; $j++) {
	if ($print) echo 'Step ' . ($j+1) . ' done!<br />';
	preg_match('/action="([^"]+)"/', $data, $match);
	$each = explode('<input', $data);
	$postdata = '';
	$list = [];
	foreach($each as $i) {
		if (preg_match('/name="([^"]+)"/', $i, $match_name)) {
			preg_match('/value="([^"]+)"/', $i, $match_value);
			if ($match_name[1] === 'JavaScriptEnabled') $match_value[1] = '1';
			if (!$list[$match_name[1]]) $postdata .= $match_name[1] . '=' . urlencode($match_value[1]) . '&';
			$list[$match_name[1]] = true;
		}
	}
	if (strpos($postdata, 'CN1') !== false) {
		$postdata = str_replace('CN1=&CN2=&CN3=&CN4=&CN5=&CN6=&', $post, $postdata);
	}
	$postdata = str_replace('/=&', '', $postdata);
	curl_setopt($c, CURLOPT_URL, 'https://kor.tellburgerking.com/' . $match[1]);
	curl_setopt($c, CURLOPT_POSTFIELDS, $postdata);
	$data = curl_exec($c);
	if (strpos($data, '확인 코드') !== false) {
		preg_match('/확인 코드: ([0-9]{8})/', $data, $code);
		$print = false;
		$j = 195;
	}
}

echo '<strong>Survey code(';
if ($auto) {
	echo 'auto-generated';
} else {
	echo 'user-provided';
}
echo '): ' . $_GET['code'] . '<br /> Code: ' . $code[1] . '</strong>';
date_default_timezone_set("Asia/Seoul");
?>