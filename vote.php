<?php
header('Content-Type: application/json');

// 允許跨域請求（如果需要的話）
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, DELETE');

// 讀取投票數據
function getVotes() {
    if (!file_exists('votes.json')) {
        file_put_contents('votes.json', json_encode(['votes' => []]));
    }
    return json_decode(file_get_contents('votes.json'), true);
}

// 保存投票數據
function saveVotes($votes) {
    file_put_contents('votes.json', json_encode($votes, JSON_PRETTY_PRINT));
}

// 重置特定圖片的投票
function resetVote($imageId) {
    $votes = getVotes();
    if (isset($votes['votes'][$imageId])) {
        $votes['votes'][$imageId] = 0;
        saveVotes($votes);
    }
    return $votes['votes'][$imageId] ?? 0;
}

// 重置所有投票
function resetAllVotes() {
    $votes = ['votes' => []];
    saveVotes($votes);
    return $votes;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (isset($data['action']) && $data['action'] === 'reset') {
        if (isset($data['imageId'])) {
            // 重置特定圖片的投票
            $newCount = resetVote($data['imageId']);
            echo json_encode(['success' => true, 'votes' => $newCount]);
        } else {
            // 重置所有投票
            $votes = resetAllVotes();
            echo json_encode(['success' => true, 'votes' => $votes]);
        }
    } else {
        // 一般投票處理
        $imageId = $data['imageId'];
        $votes = getVotes();
        if (!isset($votes['votes'][$imageId])) {
            $votes['votes'][$imageId] = 0;
        }
        $votes['votes'][$imageId]++;
        saveVotes($votes);
        echo json_encode(['success' => true, 'votes' => $votes['votes'][$imageId]]);
    }
} else {
    // 獲取所有投票數據
    $votes = getVotes();
    echo json_encode($votes);
}
?>
