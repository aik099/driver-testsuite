<?php
    $requestUri = $request->server->get('REQUEST_URI');
    $resp = new Symfony\Component\HttpFoundation\Response();
    $cook = Symfony\Component\HttpFoundation\Cookie::create('srvr_cookie', 'srv_var_is_set_sub_folder', 0, dirname($requestUri));
    $resp->headers->setCookie($cook);
?>
<!doctype html public "-//w3c//dtd xhtml 1.1//en" "http://www.w3.org/tr/xhtml11/dtd/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ru">
<head>
    <title>basic form</title>
    <meta http-equiv="content-type" content="text/html;charset=utf-8"/>
    <script>
    </script>
</head>
<body>
    basic page with cookie set from server side
</body>
</html>
