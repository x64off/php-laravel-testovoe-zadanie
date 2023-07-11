<?php
return [
    'api/get-chat' =>['chatController', 'api'],
    'users' =>['usersController', 'index'],
    'users/{id}' =>['usersController', 'index'],
    '' =>['pagesController', 'index'],
    '{page}' =>['pagesController', 'index'],
    'get/{page}' =>['pagesController', 'getPage'],
];