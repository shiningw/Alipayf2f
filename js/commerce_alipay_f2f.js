

(function ($) {
	
  function genQrcode() {
	  
	  var options = {
             // render method: 'canvas', 'image' or 'div'
                render: 'image',

              // version range somewhere in 1 .. 40
                 minVersion: 1,
                 maxVersion: 40,

                // error correction level: 'L', 'M', 'Q' or 'H'
                 ecLevel: 'H',

                 // offset in pixel if drawn onto existing canvas
                 left: 0,
                 top: 0,

                 // size in pixel
                 size: 200,

                // code color or image element
                 fill: '#333333',

                 // background color or image element, null for transparent background
                   background: null,

                 // content
                text: Drupal.settings.commerce_alipay_f2f.ali_qrcode,

              // corner radius relative to module width: 0.0 .. 0.5
                radius: 0,

                 // quiet zone in modules
               quiet: 0,

				// modes
				// 0: normal
				// 1: label strip
				// 2: label box
				// 3: image strip
				// 4: image box
				mode: 2,

				mSize: 0.1,
				mPosX: 0.5,
				mPosY: 0.5,

				label: '扫描支付',
				fontname: 'sans',
				fontcolor: '#ff9818',

				image: null
			}

			$('.qrcode').qrcode(options);
   }
	
  Drupal.behaviors.commerce_alipay_f2f = {
	  attach:function (context, settings) {
		  
          genQrcode();
			
	  }
	}
})(jQuery);

