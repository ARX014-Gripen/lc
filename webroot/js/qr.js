var player = document.getElementById('video');
var canvas = document.getElementById('canvas');
var width = canvas.width;
var height = canvas.height;

var startScan = function (callback) {
    var canvasContext = canvas.getContext('2d');

    var intervalHandler = setInterval(function () {
        canvasContext.drawImage(player, 0, 0, width, height);
        var imageData = canvasContext.getImageData(0, 0, width, height);
        var scanResult = jsQR(imageData.data, imageData.width, imageData.height);

        if (scanResult) {
            clearInterval(intervalHandler);
            callback(scanResult);
        }
    });
};

var handleSuccess = function (stream) {
    player.srcObject = stream;

    startScan(function (scanResult) {
        document.forms[0].action = scanResult.data;
        document.forms[0].item_id.value = getParam('item_id',scanResult.data);
        document.forms[0].deliverer_id.value = getParam('deliverer_id',scanResult.data);
        document.forms[0].submit();
    });
};

if (navigator.mediaDevices) {
    navigator.mediaDevices.getUserMedia({
        video: {
            facingMode: 'environment',
            width: 640,
            height: 480
        },
        audio: false
    })
        .then(handleSuccess)
        .catch(function (err) {
            console.log(JSON.stringify(err));
        });
} else {
    alert('ビデオカメラを使用できません');
}

/**
 * Get the URL parameter value
 *
 * @param  name {string} パラメータのキー文字列
 * @return  url {url} 対象のURL文字列（任意）
 */
 function getParam(name, url) {
    if (!url) url = window.location.href;
    name = name.replace(/[\[\]]/g, "\\$&");
    var regex = new RegExp("[?&]" + name + "(=([^&#]*)|&|#|$)"),
        results = regex.exec(url);
    if (!results) return null;
    if (!results[2]) return '';
    return decodeURIComponent(results[2].replace(/\+/g, " "));
}
