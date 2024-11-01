<?php
header('Content-Type: application/json');

// 允許跨域請求（如果需要的話）
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
header('Access-Control-Allow-Headers: Content-Type');

// 設置投票數據文件路徑
$votesFile = 'votes.json';

// 如果文件不存在，創建一個空的投票數據文件
if (!file_exists($votesFile)) {
    file_put_contents($votesFile, json_encode(['votes' => []]));
}

// 讀取現有投票數據
$votesData = json_decode(file_get_contents($votesFile), true);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    
    // 處理重置操作
    if (isset($input['action']) && $input['action'] === 'reset') {
        if (isset($input['imageId'])) {
            // 重置單張圖片的投票
            $votesData['votes'][$input['imageId']] = 0;
        } else {
            // 重置所有投票
            $votesData['votes'] = [];
        }
        file_put_contents($votesFile, json_encode($votesData));
        echo json_encode(['success' => true, 'votes' => $votesData['votes']]);
        exit;
    }
    
    // 處理投票操作
    if (isset($input['imageId'])) {
        $imageId = $input['imageId'];
        if (!isset($votesData['votes'][$imageId])) {
            $votesData['votes'][$imageId] = 0;
        }
        $votesData['votes'][$imageId]++;
        file_put_contents($votesFile, json_encode($votesData));
        echo json_encode(['success' => true, 'votes' => $votesData['votes'][$imageId]]);
        exit;
    }
}

// GET 請求返回所有投票數據
echo json_encode($votesData);
?>
