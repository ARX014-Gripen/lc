window.addEventListener('load', () => {
    
    const canvas = document.querySelector('#draw-area');
    const context = canvas.getContext('2d');
    const lastPosition = { x: null, y: null };
    let isDrag = false;
  
    // 現在の線の色を保持する変数(デフォルトは黒(#000000)とする)
    let currentColor = '#000000';
  
    function draw(x, y) {
      if(!isDrag) {
        return;
      }
      context.lineCap = 'round';
      context.lineJoin = 'round';
      context.lineWidth = 5;
      context.strokeStyle = currentColor;
      if (lastPosition.x === null || lastPosition.y === null) {
        context.moveTo(x, y);
      } else {
        context.moveTo(lastPosition.x, lastPosition.y);
      }
      context.lineTo(x, y);
      context.stroke();
  
      lastPosition.x = x;
      lastPosition.y = y;
    }

    function post() {
        // if(!window.confirm('署名を完了しますか？')){
        //     return;
        // }
        var base64 = canvas.toDataURL('image/png');
        var base64  = base64.replace("data:image/png;base64,", "");
        
        document.image_post.signature.value = base64;
        // document.image_post.submit();
    }
  
    function clear() {
      context.clearRect(0, 0, canvas.width, canvas.height);
    }
  
    function dragStart(event) {
      context.beginPath();
  
      isDrag = true;
    }
  
    function dragEnd(event) {
      context.closePath();
      isDrag = false;
      lastPosition.x = null;
      lastPosition.y = null;
    }
  
    function initEventHandler() {
      const postButton = document.querySelector('#post-button');
      postButton.addEventListener('click', post);

      const clearButton = document.querySelector('#clear-button');
      clearButton.addEventListener('click', clear);

      // Prevent scrolling when touching the canvas
      document.body.addEventListener("touchstart", function (e) {
        if (e.target == canvas) {
          e.preventDefault();
        }
      },  { passive: false });
      document.body.addEventListener("touchend", function (e) {
        if (e.target == canvas) {
          e.preventDefault();
        }
      },  { passive: false });
      document.body.addEventListener("touchmove", function (e) {
        if (e.target == canvas) {
          e.preventDefault();
        }
      },  { passive: false });

      canvas.addEventListener('mousedown', dragStart);
      canvas.addEventListener('touchstart', dragStart);
      canvas.addEventListener('mouseup', dragEnd);
      canvas.addEventListener('touchend', dragEnd);
      canvas.addEventListener('mouseout', dragEnd);
      canvas.addEventListener('touchcancel', dragEnd);
      canvas.addEventListener('mousemove', (event) => {
        draw(event.layerX, event.layerY);
      });
      canvas.addEventListener('touchmove', (event) => {
        if (event.layerX === undefined) {
            draw(event.touches[0].pageX - canvas.offsetLeft, event.touches[0].pageY - canvas.offsetTop);
          } else{
            draw(event.layerX, event.layerY);
          }
      });
    } 

    initEventHandler();

  });


  