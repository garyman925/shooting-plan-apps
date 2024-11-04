const CONFIG = {
    API_KEY: 'AIzaSyBKxuRVfj_d8EBT-v3kBgWNeCOvYfF1t50',
    FOLDERS: [
        {
            id: '1-97a7cI4gRbH2B-5mGC4TqR5lebayRb_',
            defaultName: 'Album 1'
        },
        {
            id: '1fqybsZNOl5tU967q5FD7T3-4hJWr3lA-',
            defaultName: 'Album 2'
        },
        {
            id: '1-9hc5N0TkssXi9I9sc2wGO3hmJbA0jnY',
            defaultName: 'Album 3'
        },
        {
            id: '1H8cK1O99k12fT0yM-iZa2MqtIevjCvlP',
            defaultName: 'Album 4'
        },
        {
            id: '1aRTp8jCwqo5WT7jnXXaE3DEEAsU-unBw',
            defaultName: 'Album 5' 
        }
    ]};

async function loadAllFolderNames() {
    const promises = CONFIG.FOLDERS.map(async folder => {
        try {
            const response = await fetch(`https://www.googleapis.com/drive/v3/files/${folder.id}?fields=name&key=${CONFIG.API_KEY}`);
            if (!response.ok) throw new Error('API request failed');
            const data = await response.json();
            return {
                id: folder.id,
                name: data.name
            };
        } catch (error) {
            console.error('Error fetching folder name:', error);
            return {
                id: folder.id,
                name: folder.defaultName || '未命名相冊'
            };
        }
    });

    return Promise.all(promises);
}

// 添加新資料夾到 config.js
async function addFolderToConfig(folder) {
    try {
        const response = await fetch('save_folder.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(folder)
        });
        
        if (!response.ok) {
            throw new Error('保存失敗');
        }
        
        // 添加到當前的 CONFIG 對象
        CONFIG.FOLDERS.push({
            id: folder.id,
            defaultName: folder.name
        });
        
        return true;
    } catch (error) {
        console.error('Error saving folder:', error);
        return false;
    }
}

function handleError(error) {
    console.error('Error loading album:', error);
    const loadingStatus = document.getElementById('loading-status');
    if (loadingStatus) {
        loadingStatus.innerHTML = '<span style="color: red;">載入失敗，請重試</span>';
    }
}
