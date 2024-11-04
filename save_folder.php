<?php
header('Content-Type: application/json');

// 接收 POST 數據
$input = json_decode(file_get_contents('php://input'), true);

if (!isset($input['id']) || !isset($input['name'])) {
    echo json_encode(['success' => false, 'message' => '缺少必要參數']);
    exit;
}

// 讀取當前的 config.js 文件
$configFile = 'config.js';
$content = file_get_contents($configFile);

// 在 FOLDERS 數組中添加新的資料夾
$newFolder = sprintf(
    "        {
            id: '%s',
            defaultName: '%s'
        }",
    $input['id'],
    $input['name']
);

// 在最後一個資料夾後添加新的資料夾
$content = preg_replace(
    '/(FOLDERS: \[\s*(?:.*?}\s*,\s*)*)(.*?}\s*)\]\s*};/s',
    "$1$2,\n$newFolder\n    ]};",
    $content
);

// 保存修改後的文件
if (file_put_contents($configFile, $content)) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => '保存失敗']);
}
?> 